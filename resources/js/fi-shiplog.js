import styles from '../css/fi-shiplog.css'

const ICON = {
    megaphone: 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73s-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46',
    close: 'M6 18 18 6M6 6l12 12',
}

const svg = (path, className) =>
    `<svg class="${className}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="${path}"/></svg>`

const escape = (value) =>
    String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    })[character])

/**
 * A self contained changelog timeline.
 *
 * Everything lives in a shadow root, so the host application can be Blade,
 * React, Vue or Svelte and neither side can reach into the other's styles.
 */
class ShipLogElement extends HTMLElement {
    static observedAttributes = ['position', 'label', 'signature']

    #shadow
    #open = false
    #state = 'idle'
    #releases = []
    #returnFocus = null
    #themeObserver = null
    #media = null

    connectedCallback() {
        document.addEventListener('keydown', this.#onKeydown)

        if (this.#shadow) {
            return
        }

        this.#shadow = this.attachShadow({ mode: 'open' })
        this.#shadow.innerHTML = this.#template()

        this.#bind()
        this.#watchTheme()
        this.#paintFab()

        if (this.mode === 'inline') {
            this.#load()
        }
    }

    disconnectedCallback() {
        this.#themeObserver?.disconnect()
        this.#media?.removeEventListener('change', this.#syncTheme)
        document.removeEventListener('keydown', this.#onKeydown)
        this.#unlockScroll()
    }

    attributeChangedCallback() {
        if (this.#shadow) {
            this.#paintFab()
        }
    }

    get mode() {
        return this.getAttribute('mode') === 'inline' ? 'inline' : 'fab'
    }

    get src() {
        return this.getAttribute('src') || ''
    }

    get isOpen() {
        return this.#open
    }

    open() {
        if (this.#open || this.mode === 'inline') {
            return
        }

        this.#open = true
        this.#returnFocus = document.activeElement
        this.#overlay.setAttribute('data-open', '')
        this.#overlay.removeAttribute('aria-hidden')
        this.#lockScroll()
        this.#markSeen()
        this.#paintFab()
        this.#load()

        requestAnimationFrame(() => this.#shadow.querySelector('.sl-close')?.focus())
        this.dispatchEvent(new CustomEvent('shiplog:open', { bubbles: true, composed: true }))
    }

    close() {
        if (! this.#open) {
            return
        }

        this.#open = false
        this.#overlay.removeAttribute('data-open')
        this.#overlay.setAttribute('aria-hidden', 'true')
        this.#unlockScroll()

        if (this.#returnFocus instanceof HTMLElement) {
            this.#returnFocus.focus()
        }

        this.dispatchEvent(new CustomEvent('shiplog:close', { bubbles: true, composed: true }))
    }

    toggle() {
        this.#open ? this.close() : this.open()
    }

    /**
     * Forces the next open to fetch again, for apps that publish releases
     * without a full page reload.
     */
    refresh() {
        this.#state = 'idle'

        return this.#load()
    }

    get #overlay() {
        return this.#shadow.querySelector('.sl-overlay')
    }

    get #scroll() {
        return this.#shadow.querySelector('.sl-scroll')
    }

    #template() {
        const label = this.getAttribute('label') || 'What’s new'
        const title = this.getAttribute('heading') || label
        const subtitle = this.getAttribute('subheading') || ''

        return `<style>${styles}</style>
            ${this.mode === 'inline' ? '' : `
                <button class="sl-fab" type="button" part="fab" aria-haspopup="dialog">
                    ${svg(ICON.megaphone, 'sl-fab__icon')}
                    <span class="sl-fab__label">${escape(label)}</span>
                </button>`}
            <div class="sl-overlay" role="dialog" aria-modal="true" aria-label="${escape(title)}" aria-hidden="true">
                <div class="sl-backdrop"></div>
                <div class="sl-panel" part="panel">
                    <span class="sl-grip"></span>
                    <header class="sl-header">
                        <div class="sl-brand">
                            <strong>${escape(title)}</strong>
                            ${subtitle ? `<span>${escape(subtitle)}</span>` : ''}
                        </div>
                        <button class="sl-close" type="button" aria-label="Close">${svg(ICON.close, '')}</button>
                    </header>
                    <div class="sl-scroll">
                        <div class="sl-progress"><i></i></div>
                        <div class="sl-content"></div>
                    </div>
                </div>
            </div>`
    }

    #bind() {
        this.#shadow.querySelector('.sl-fab')?.addEventListener('click', () => this.toggle())
        this.#shadow.querySelector('.sl-close')?.addEventListener('click', () => this.close())
        this.#shadow.querySelector('.sl-backdrop')?.addEventListener('click', () => this.close())
        this.#scroll?.addEventListener('scroll', this.#onScroll, { passive: true })
    }

    #onKeydown = (event) => {
        if (event.key === 'Escape' && this.#open) {
            event.stopPropagation()
            this.close()
        }
    }

    #onScroll = () => {
        const element = this.#scroll
        const travelled = element.scrollHeight - element.clientHeight

        this.#shadow.host.style.setProperty(
            '--sl-scrolled',
            `${travelled > 0 ? Math.min(100, (element.scrollTop / travelled) * 100) : 0}%`,
        )
    }

    async #load() {
        if (this.#state === 'loading' || this.#state === 'loaded' || ! this.src) {
            return
        }

        this.#state = 'loading'
        this.#paint(`<div class="sl-state"><span class="sl-spinner"></span></div>`)

        try {
            const response = await fetch(this.src, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            })

            if (! response.ok) {
                throw new Error(`HTTP ${response.status}`)
            }

            const payload = await response.json()

            this.#releases = Array.isArray(payload.releases) ? payload.releases : []
            this.#state = 'loaded'
            this.#paintTimeline()
        } catch (error) {
            this.#state = 'idle'
            this.#paint(`<div class="sl-state">${escape(this.getAttribute('error-text') || 'The changelog could not be loaded.')}</div>`)
        }
    }

    #paint(html) {
        this.#shadow.querySelector('.sl-content').innerHTML = html
    }

    #paintTimeline() {
        if (this.#releases.length === 0) {
            this.#paint(`<div class="sl-state">${escape(this.getAttribute('empty-text') || 'Nothing shipped yet.')}</div>`)

            return
        }

        this.#paint(`<ol class="sl-timeline">${this.#releases.map(this.#release).join('')}</ol>`)
    }

    /**
     * Release bodies arrive as HTML that the server already rendered and
     * sanitised; everything else is escaped here.
     */
    #release = (release, index) => {
        const badges = (release.changes || [])
            .map((group) => `<span class="sl-badge" style="--accent:${escape(group.accent)}">${escape(group.label)} <b>${escape(group.count)}</b></span>`)
            .join('')

        return `<li class="sl-release" style="--i:${index}"${release.yanked ? ' data-yanked' : ''}>
            <span class="sl-node"></span>
            <div class="sl-meta">
                <span class="sl-version">${escape(release.version)}</span>
                ${release.released_at_label ? `<time class="sl-date" datetime="${escape(release.released_at)}">${escape(release.released_at_label)}</time>` : ''}
                ${release.yanked ? '<span class="sl-yanked">Yanked</span>' : ''}
            </div>
            ${release.title ? `<h3 class="sl-title">${escape(release.title)}</h3>` : ''}
            ${badges ? `<div class="sl-badges">${badges}</div>` : ''}
            <div class="sl-body">${release.body || ''}</div>
        </li>`
    }

    #paintFab() {
        const fab = this.#shadow.querySelector('.sl-fab')

        if (! fab) {
            return
        }

        fab.dataset.position = this.getAttribute('position') || 'bottom-right'
        fab.querySelector('.sl-fab__label').textContent = this.getAttribute('label') || 'What’s new'
        fab.querySelector('.sl-fab__dot')?.remove()

        if (this.#hasUnread()) {
            fab.insertAdjacentHTML('beforeend', '<span class="sl-fab__dot" aria-hidden="true"></span>')
        }
    }

    #storageKey() {
        return `shiplog:seen:${this.getAttribute('storage-key') || 'default'}`
    }

    #hasUnread() {
        const signature = this.getAttribute('signature')

        if (! signature) {
            return false
        }

        try {
            return window.localStorage.getItem(this.#storageKey()) !== signature
        } catch (error) {
            return false
        }
    }

    #markSeen() {
        const signature = this.getAttribute('signature')

        if (! signature) {
            return
        }

        try {
            window.localStorage.setItem(this.#storageKey(), signature)
        } catch (error) {
            // Private browsing: the dot simply comes back next time.
        }
    }

    #watchTheme() {
        if (this.hasAttribute('theme')) {
            this.dataset.theme = this.getAttribute('theme')

            return
        }

        this.#media = window.matchMedia('(prefers-color-scheme: dark)')
        this.#media.addEventListener('change', this.#syncTheme)

        this.#themeObserver = new MutationObserver(this.#syncTheme)
        this.#themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class', 'data-theme'],
        })

        this.#syncTheme()
    }

    /**
     * Follows whatever the host page already decided: a `dark` class, a
     * `data-theme` attribute, or the operating system preference.
     */
    #syncTheme = () => {
        const root = document.documentElement
        const declared = root.getAttribute('data-theme')

        const dark = declared
            ? declared === 'dark'
            : root.classList.contains('dark') || Boolean(this.#media?.matches)

        this.dataset.theme = dark ? 'dark' : 'light'
    }

    #lockScroll() {
        this.dataset.previousOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
    }

    #unlockScroll() {
        if ('previousOverflow' in this.dataset) {
            document.body.style.overflow = this.dataset.previousOverflow
            delete this.dataset.previousOverflow
        }
    }
}

if (! customElements.get('ship-log')) {
    customElements.define('ship-log', ShipLogElement)
}

/**
 * Lets any framework open the timeline without reaching for the DOM:
 * `window.ShipLog.open()`.
 */
window.ShipLog = {
    element: () => document.querySelector('ship-log'),
    open: () => window.ShipLog.element()?.open(),
    close: () => window.ShipLog.element()?.close(),
    toggle: () => window.ShipLog.element()?.toggle(),
    refresh: () => window.ShipLog.element()?.refresh(),
}
