(()=>{var J=Object.defineProperty;var F=r=>{throw TypeError(r)};var Q=(r,i,e)=>i in r?J(r,i,{enumerable:!0,configurable:!0,writable:!0,value:e}):r[i]=e;var I=(r,i,e)=>Q(r,typeof i!="symbol"?i+"":i,e),T=(r,i,e)=>i.has(r)||F("Cannot "+e);var t=(r,i,e)=>(T(r,i,"read from private field"),e?e.call(r):i.get(r)),d=(r,i,e)=>i.has(r)?F("Cannot add the same private member more than once"):i instanceof WeakSet?i.add(r):i.set(r,e),n=(r,i,e,o)=>(T(r,i,"write to private field"),o?o.call(r,e):i.set(r,e),e),a=(r,i,e)=>(T(r,i,"access private method"),e);var P=`:host {
    --sl-font: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --sl-mono: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace;

    --sl-bg: #ffffff;
    --sl-bg-soft: #f8fafc;
    --sl-surface: rgba(255, 255, 255, 0.86);
    --sl-border: rgba(15, 23, 42, 0.1);
    --sl-border-strong: rgba(15, 23, 42, 0.16);
    --sl-text: #0f172a;
    --sl-text-soft: #475569;
    --sl-text-faint: #94a3b8;
    --sl-rail: linear-gradient(180deg, rgba(99, 102, 241, 0.9), rgba(168, 85, 247, 0.55), rgba(236, 72, 153, 0.15));
    --sl-accent: #6366f1;
    --sl-accent-2: #a855f7;
    --sl-shadow: 0 24px 60px -18px rgba(15, 23, 42, 0.35);
    --sl-backdrop: rgba(15, 23, 42, 0.45);
    --sl-code-bg: rgba(15, 23, 42, 0.05);

    position: fixed;
    inset: 0;
    z-index: 2147483000;
    pointer-events: none;
    font-family: var(--sl-font);
    color: var(--sl-text);
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
    contain: layout style;
}

:host([data-theme="dark"]) {
    --sl-bg: #0b1120;
    --sl-bg-soft: #0f172a;
    --sl-surface: rgba(15, 23, 42, 0.88);
    --sl-border: rgba(148, 163, 184, 0.16);
    --sl-border-strong: rgba(148, 163, 184, 0.28);
    --sl-text: #e2e8f0;
    --sl-text-soft: #94a3b8;
    --sl-text-faint: #64748b;
    --sl-shadow: 0 30px 80px -20px rgba(0, 0, 0, 0.75);
    --sl-backdrop: rgba(2, 6, 23, 0.7);
    --sl-code-bg: rgba(148, 163, 184, 0.14);
}

:host([mode="inline"]) {
    position: relative;
    inset: auto;
    display: block;
    pointer-events: auto;
    contain: none;
}

* {
    box-sizing: border-box;
}

/* ---------------------------------------------------------------- button */

.sl-fab {
    position: fixed;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border: 1px solid transparent;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--sl-accent), var(--sl-accent-2));
    color: #fff;
    font: inherit;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
    pointer-events: auto;
    box-shadow: 0 10px 30px -8px rgba(99, 102, 241, 0.75);
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
}

.sl-fab:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 16px 40px -10px rgba(99, 102, 241, 0.85);
}

.sl-fab:active {
    transform: translateY(0) scale(0.98);
}

.sl-fab:focus-visible {
    outline: 2px solid #fff;
    outline-offset: 3px;
}

.sl-fab[data-position^="top"] {
    top: max(1.25rem, env(safe-area-inset-top));
}

.sl-fab[data-position^="bottom"] {
    bottom: max(1.25rem, env(safe-area-inset-bottom));
}

.sl-fab[data-position$="left"] {
    left: max(1.25rem, env(safe-area-inset-left));
}

.sl-fab[data-position$="right"] {
    right: max(1.25rem, env(safe-area-inset-right));
}

.sl-fab__icon {
    width: 1.15rem;
    height: 1.15rem;
    flex: none;
}

.sl-fab__dot {
    position: absolute;
    top: 0.35rem;
    right: 0.35rem;
    width: 0.6rem;
    height: 0.6rem;
    border-radius: 999px;
    background: #f43f5e;
    box-shadow: 0 0 0 2px var(--sl-bg);
}

.sl-fab__dot::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: inherit;
    animation: sl-ping 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes sl-ping {
    75%, 100% { transform: scale(2.4); opacity: 0; }
}

/* --------------------------------------------------------------- overlay */

.sl-overlay {
    position: fixed;
    inset: 0;
    display: grid;
    place-items: stretch end;
    pointer-events: none;
    visibility: hidden;
}

.sl-overlay[data-open] {
    pointer-events: auto;
    visibility: visible;
}

.sl-backdrop {
    position: absolute;
    inset: 0;
    background: var(--sl-backdrop);
    opacity: 0;
    transition: opacity 0.32s ease;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
}

.sl-overlay[data-open] .sl-backdrop {
    opacity: 1;
}

.sl-panel {
    position: relative;
    display: flex;
    flex-direction: column;
    width: min(100%, 34rem);
    height: 100%;
    max-height: 100%;
    overflow: hidden;
    border: 0;
    border-left: 1px solid var(--sl-border);
    border-radius: 0;
    background: var(--sl-bg);
    box-shadow: var(--sl-shadow);
    opacity: 0;
    transform: translateX(100%);
    transition: opacity 0.28s ease, transform 0.44s cubic-bezier(0.22, 1, 0.36, 1);
}

.sl-overlay[data-open] .sl-panel {
    opacity: 1;
    transform: translateX(0);
}

:host([mode="inline"]) .sl-overlay {
    position: static;
    display: block;
    visibility: visible;
    pointer-events: auto;
}

:host([mode="inline"]) .sl-backdrop,
:host([mode="inline"]) .sl-grip {
    display: none;
}

:host([mode="inline"]) .sl-panel {
    width: 100%;
    height: auto;
    max-height: none;
    opacity: 1;
    transform: none;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

.sl-header {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--sl-border);
    background: var(--sl-surface);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

:host([mode="inline"]) .sl-header {
    display: none;
}

.sl-brand {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}

.sl-brand strong {
    font-size: 1.0625rem;
    font-weight: 650;
    letter-spacing: -0.01em;
}

.sl-brand span {
    font-size: 0.8125rem;
    color: var(--sl-text-soft);
}

.sl-grip {
    position: absolute;
    top: 0.55rem;
    left: 50%;
    width: 2.5rem;
    height: 0.25rem;
    transform: translateX(-50%);
    border-radius: 999px;
    background: var(--sl-border-strong);
}

.sl-close {
    margin-left: auto;
    display: grid;
    place-items: center;
    width: 2.25rem;
    height: 2.25rem;
    flex: none;
    border: 1px solid var(--sl-border);
    border-radius: 0.75rem;
    background: transparent;
    color: var(--sl-text-soft);
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
}

.sl-close:hover {
    background: var(--sl-bg-soft);
    color: var(--sl-text);
    transform: rotate(90deg);
}

.sl-close svg {
    width: 1.1rem;
    height: 1.1rem;
}

.sl-scroll {
    position: relative;
    flex: 1;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding: 1.75rem 1.5rem 4rem;
    scrollbar-width: thin;
}

:host([mode="inline"]) .sl-scroll {
    padding: 0.5rem 0 1rem;
    overflow: visible;
}

.sl-progress {
    position: absolute;
    right: 0;
    bottom: -1px;
    left: 0;
    height: 2px;
    background: transparent;
}

.sl-progress i {
    display: block;
    height: 100%;
    width: var(--sl-scrolled, 0%);
    background: linear-gradient(90deg, var(--sl-accent), var(--sl-accent-2));
    transition: width 0.1s linear;
}

:host([mode="inline"]) .sl-progress {
    display: none;
}

/* -------------------------------------------------------------- timeline */

.sl-timeline {
    position: relative;
    margin: 0;
    padding: 0 0 0 2.25rem;
    list-style: none;
}

.sl-timeline::before {
    content: "";
    position: absolute;
    top: 0.5rem;
    bottom: 2rem;
    left: 0.6875rem;
    width: 2px;
    border-radius: 999px;
    background: var(--sl-rail);
}

.sl-release {
    position: relative;
    padding-bottom: 2.25rem;
    content-visibility: auto;
    contain-intrinsic-size: auto 22rem;
    opacity: 0;
    transform: translateY(1.25rem);
    animation: sl-rise 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: calc(var(--i, 0) * 70ms);
}

@keyframes sl-rise {
    to { opacity: 1; transform: none; }
}

.sl-node {
    position: absolute;
    top: 0.45rem;
    left: -1.875rem;
    width: 0.875rem;
    height: 0.875rem;
    border-radius: 999px;
    background: var(--sl-bg);
    border: 2px solid var(--sl-accent);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--sl-accent) 18%, transparent);
}

.sl-release[data-yanked] .sl-node {
    border-color: #f43f5e;
    box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.18);
}

.sl-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.625rem;
    margin-bottom: 0.5rem;
}

.sl-version {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.6rem;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, color-mix(in srgb, var(--sl-accent) 16%, transparent), color-mix(in srgb, var(--sl-accent-2) 16%, transparent));
    border: 1px solid color-mix(in srgb, var(--sl-accent) 30%, transparent);
    font-family: var(--sl-mono);
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.sl-date {
    font-size: 0.8125rem;
    color: var(--sl-text-faint);
}

.sl-yanked {
    padding: 0.15rem 0.5rem;
    border-radius: 0.375rem;
    background: rgba(244, 63, 94, 0.14);
    color: #f43f5e;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.sl-title {
    margin: 0 0 0.65rem;
    font-size: 1.25rem;
    font-weight: 650;
    letter-spacing: -0.02em;
    line-height: 1.25;
}

.sl-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-bottom: 0.9rem;
}

.sl-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    background: color-mix(in srgb, var(--accent) 14%, transparent);
    border: 1px solid color-mix(in srgb, var(--accent) 32%, transparent);
    color: var(--accent);
    font-size: 0.75rem;
    font-weight: 600;
}

.sl-badge b {
    font-variant-numeric: tabular-nums;
    opacity: 0.75;
}

/* ------------------------------------------------------- rendered content */

.sl-body {
    font-size: 0.9375rem;
    line-height: 1.7;
    color: var(--sl-text-soft);
}

.sl-body > :first-child { margin-top: 0; }
.sl-body > :last-child { margin-bottom: 0; }

.sl-body p { margin: 0 0 0.85rem; }

.sl-body h3,
.sl-body h4 {
    margin: 1.4rem 0 0.6rem;
    color: var(--sl-text);
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.sl-body h3::before {
    content: "";
    display: inline-block;
    width: 0.4rem;
    height: 0.4rem;
    margin-right: 0.45rem;
    border-radius: 999px;
    background: var(--sl-accent);
    vertical-align: middle;
}

.sl-body ul,
.sl-body ol {
    margin: 0 0 0.9rem;
    padding-left: 1.15rem;
}

.sl-body li { margin-bottom: 0.35rem; }

.sl-body li::marker { color: var(--sl-accent); }

.sl-body a {
    color: var(--sl-accent);
    text-decoration: none;
    border-bottom: 1px solid color-mix(in srgb, var(--sl-accent) 40%, transparent);
    transition: border-color 0.2s ease;
}

.sl-body a:hover { border-bottom-color: var(--sl-accent); }

.sl-body code {
    padding: 0.1rem 0.35rem;
    border-radius: 0.35rem;
    background: var(--sl-code-bg);
    font-family: var(--sl-mono);
    font-size: 0.85em;
    color: var(--sl-text);
}

.sl-body pre {
    margin: 0 0 1rem;
    padding: 0.9rem 1rem;
    overflow-x: auto;
    border: 1px solid var(--sl-border);
    border-radius: 0.85rem;
    background: var(--sl-bg-soft);
}

.sl-body pre code {
    padding: 0;
    background: none;
    font-size: 0.8125rem;
    line-height: 1.6;
}

.sl-body table {
    width: 100%;
    margin: 0 0 1rem;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.sl-body th,
.sl-body td {
    padding: 0.5rem 0.65rem;
    border: 1px solid var(--sl-border);
    text-align: left;
}

.sl-body th {
    background: var(--sl-bg-soft);
    color: var(--sl-text);
    font-weight: 600;
}

.sl-img {
    display: block;
    max-width: 100%;
    height: auto;
    border: 1px solid var(--sl-border);
    border-radius: 0.85rem;
    background: var(--sl-bg-soft);
}

.sl-body p:has(> .sl-img:only-child) {
    margin: 1.1rem 0;
}

.sl-body p:has(> .sl-img:only-child) .sl-img {
    box-shadow: 0 18px 40px -22px rgba(15, 23, 42, 0.55);
    transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
}

.sl-body p:has(> .sl-img:only-child) .sl-img:hover {
    transform: scale(1.015);
}

.sl-body blockquote {
    margin: 0 0 1rem;
    padding: 0.25rem 0 0.25rem 1rem;
    border-left: 3px solid var(--sl-border-strong);
    color: var(--sl-text-faint);
}

/* ---------------------------------------------------------------- alerts */

.sl-alert {
    --alert: var(--sl-accent);
    display: flex;
    gap: 0.7rem;
    margin: 0 0 1rem;
    padding: 0.85rem 1rem;
    border: 1px solid color-mix(in srgb, var(--alert) 30%, transparent);
    border-left-width: 3px;
    border-radius: 0.85rem;
    background: color-mix(in srgb, var(--alert) 9%, transparent);
    color: var(--sl-text);
    font-size: 0.9rem;
}

.sl-alert--info { --alert: #0ea5e9; }
.sl-alert--success { --alert: #10b981; }
.sl-alert--warning { --alert: #f59e0b; }
.sl-alert--danger { --alert: #f43f5e; }
.sl-alert--important { --alert: #8b5cf6; }

.sl-alert__icon {
    width: 1.15rem;
    height: 1.15rem;
    flex: none;
    margin-top: 0.15rem;
    color: var(--alert);
}

.sl-alert__body > :first-child { margin-top: 0; }
.sl-alert__body > :last-child { margin-bottom: 0; }

/* -------------------------------------------------------------- tooltips */

.sl-tip {
    position: relative;
    border-bottom: 1px dashed color-mix(in srgb, var(--sl-accent) 60%, transparent);
    color: var(--sl-text);
    cursor: help;
}

.sl-tip::after {
    content: attr(data-sl-tip);
    position: absolute;
    bottom: calc(100% + 0.55rem);
    left: 50%;
    z-index: 5;
    width: max-content;
    max-width: 16rem;
    padding: 0.45rem 0.65rem;
    border-radius: 0.6rem;
    background: #0f172a;
    color: #f8fafc;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1.45;
    text-align: center;
    white-space: normal;
    opacity: 0;
    transform: translate(-50%, 0.35rem);
    pointer-events: none;
    transition: opacity 0.18s ease, transform 0.18s ease;
    box-shadow: 0 12px 30px -12px rgba(0, 0, 0, 0.6);
}

.sl-tip::before {
    content: "";
    position: absolute;
    bottom: calc(100% + 0.2rem);
    left: 50%;
    z-index: 5;
    border: 0.35rem solid transparent;
    border-top-color: #0f172a;
    opacity: 0;
    transform: translate(-50%, 0.35rem);
    pointer-events: none;
    transition: opacity 0.18s ease, transform 0.18s ease;
}

.sl-tip:hover::after,
.sl-tip:focus::after,
.sl-tip:hover::before,
.sl-tip:focus::before {
    opacity: 1;
    transform: translate(-50%, 0);
}

/* ------------------------------------------------------- states & motion */

.sl-sentinel {
    display: grid;
    place-items: center;
    padding: 1.5rem 0 0.5rem;
}

.sl-state {
    display: grid;
    place-items: center;
    gap: 0.5rem;
    padding: 4rem 1rem;
    text-align: center;
    color: var(--sl-text-faint);
    font-size: 0.9rem;
}

.sl-spinner {
    width: 1.6rem;
    height: 1.6rem;
    border: 2px solid var(--sl-border-strong);
    border-top-color: var(--sl-accent);
    border-radius: 999px;
    animation: sl-spin 0.7s linear infinite;
}

@keyframes sl-spin {
    to { transform: rotate(360deg); }
}

@media (min-width: 40rem) {
    .sl-grip { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .sl-backdrop,
    .sl-panel,
    .sl-release,
    .sl-fab,
    .sl-close,
    .sl-img {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-delay: 0ms !important;
    }

    .sl-release { opacity: 1; transform: none; }
}
`;var Y={megaphone:"M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73s-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46",close:"M6 18 18 6M6 6l12 12"},N=(r,i)=>`<svg class="${i}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="${r}"/></svg>`,m=r=>String(r??"").replace(/[&<>"']/g,i=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"})[i]),l,p,b,g,f,x,u,w,k,v,s,$,L,R,U,A,q,E,C,X,H,S,B,z,M,O,K,W,D,y,G,j,_=class extends HTMLElement{constructor(){super(...arguments);d(this,s);d(this,l);d(this,p,!1);d(this,b,"idle");d(this,g,[]);d(this,f,0);d(this,x,null);d(this,u,null);d(this,w,null);d(this,k,null);d(this,v,null);d(this,A,e=>{e.key==="Escape"&&t(this,p)&&(e.stopPropagation(),this.close())});d(this,q,()=>{let e=t(this,s,L),o=e.scrollHeight-e.clientHeight;t(this,l).host.style.setProperty("--sl-scrolled",`${o>0?Math.min(100,e.scrollTop/o*100):0}%`)});d(this,z,(e,o)=>{let c=(e.changes||[]).map(h=>`<span class="sl-badge" style="--accent:${m(h.accent)}">${m(h.label)} <b>${m(h.count)}</b></span>`).join("");return`<li class="sl-release" style="--i:${o}"${e.yanked?" data-yanked":""}>
            <span class="sl-node"></span>
            <div class="sl-meta">
                <span class="sl-version">${m(e.version)}</span>
                ${e.released_at_label?`<time class="sl-date" datetime="${m(e.released_at)}">${m(e.released_at_label)}</time>`:""}
                ${e.yanked?'<span class="sl-yanked">Yanked</span>':""}
            </div>
            ${e.title?`<h3 class="sl-title">${m(e.title)}</h3>`:""}
            ${c?`<div class="sl-badges">${c}</div>`:""}
            <div class="sl-body">${e.body||""}</div>
        </li>`});d(this,y,()=>{let e=document.documentElement,o=e.getAttribute("data-theme"),c=o?o==="dark":e.classList.contains("dark")||!!t(this,v)?.matches;this.dataset.theme=c?"dark":"light"})}connectedCallback(){document.addEventListener("keydown",t(this,A)),!t(this,l)&&(n(this,l,this.attachShadow({mode:"open"})),t(this,l).innerHTML=a(this,s,R).call(this),a(this,s,U).call(this),a(this,s,D).call(this),a(this,s,M).call(this),this.mode==="inline"&&a(this,s,E).call(this))}disconnectedCallback(){t(this,u)?.disconnect(),t(this,k)?.disconnect(),t(this,v)?.removeEventListener("change",t(this,y)),document.removeEventListener("keydown",t(this,A)),a(this,s,j).call(this)}attributeChangedCallback(){t(this,l)&&a(this,s,M).call(this)}get mode(){return this.getAttribute("mode")==="inline"?"inline":"fab"}get src(){return this.getAttribute("src")||""}get isOpen(){return t(this,p)}open(){t(this,p)||this.mode==="inline"||(n(this,p,!0),n(this,w,document.activeElement),t(this,s,$).setAttribute("data-open",""),t(this,s,$).removeAttribute("aria-hidden"),a(this,s,G).call(this),a(this,s,W).call(this),a(this,s,M).call(this),a(this,s,E).call(this),requestAnimationFrame(()=>t(this,l).querySelector(".sl-close")?.focus()),this.dispatchEvent(new CustomEvent("shiplog:open",{bubbles:!0,composed:!0})))}close(){t(this,p)&&(n(this,p,!1),t(this,s,$).removeAttribute("data-open"),t(this,s,$).setAttribute("aria-hidden","true"),a(this,s,j).call(this),t(this,w)instanceof HTMLElement&&t(this,w).focus(),this.dispatchEvent(new CustomEvent("shiplog:close",{bubbles:!0,composed:!0})))}toggle(){t(this,p)?this.close():this.open()}refresh(){return n(this,b,"idle"),n(this,f,0),n(this,g,[]),a(this,s,E).call(this)}};l=new WeakMap,p=new WeakMap,b=new WeakMap,g=new WeakMap,f=new WeakMap,x=new WeakMap,u=new WeakMap,w=new WeakMap,k=new WeakMap,v=new WeakMap,s=new WeakSet,$=function(){return t(this,l).querySelector(".sl-overlay")},L=function(){return t(this,l).querySelector(".sl-scroll")},R=function(){let e=this.getAttribute("label")||"What\u2019s new",o=this.getAttribute("heading")||e,c=this.getAttribute("subheading")||"";return`<style>${P}</style>
            ${this.mode==="inline"?"":`
                <button class="sl-fab" type="button" part="fab" aria-haspopup="dialog">
                    ${N(Y.megaphone,"sl-fab__icon")}
                    <span class="sl-fab__label">${m(e)}</span>
                </button>`}
            <div class="sl-overlay" role="dialog" aria-modal="true" aria-label="${m(o)}" aria-hidden="true">
                <div class="sl-backdrop"></div>
                <div class="sl-panel" part="panel">
                    <span class="sl-grip"></span>
                    <header class="sl-header">
                        <div class="sl-brand">
                            <strong>${m(o)}</strong>
                            ${c?`<span>${m(c)}</span>`:""}
                        </div>
                        <button class="sl-close" type="button" aria-label="Close">${N(Y.close,"")}</button>
                        <div class="sl-progress"><i></i></div>
                    </header>
                    <div class="sl-scroll">
                        <div class="sl-content"></div>
                    </div>
                </div>
            </div>`},U=function(){t(this,l).querySelector(".sl-fab")?.addEventListener("click",()=>this.toggle()),t(this,l).querySelector(".sl-close")?.addEventListener("click",()=>this.close()),t(this,l).querySelector(".sl-backdrop")?.addEventListener("click",()=>this.close()),t(this,s,L)?.addEventListener("scroll",t(this,q),{passive:!0})},A=new WeakMap,q=new WeakMap,E=async function(){if(t(this,b)==="loading"||t(this,b)==="loaded"||!this.src)return;n(this,b,"loading"),a(this,s,S).call(this,'<div class="sl-state"><span class="sl-spinner"></span></div>');let e=await a(this,s,C).call(this,0);if(!e){n(this,b,"idle"),a(this,s,S).call(this,`<div class="sl-state">${m(this.getAttribute("error-text")||"The changelog could not be loaded.")}</div>`);return}n(this,b,"loaded"),n(this,g,e.releases),n(this,f,e.next),a(this,s,B).call(this)},C=async function(e){let o=new URL(this.src,window.location.href);o.searchParams.set("cursor",e);try{let c=await fetch(o,{headers:{Accept:"application/json"},credentials:"same-origin"});if(!c.ok)throw new Error(`HTTP ${c.status}`);let h=await c.json();return{releases:Array.isArray(h.releases)?h.releases:[],next:h.next??null}}catch{return null}},X=async function(){if(t(this,f)===null||t(this,b)==="paging")return;n(this,b,"paging");let e=await a(this,s,C).call(this,t(this,f));if(n(this,b,"loaded"),!e)return;let o=t(this,g).length;t(this,g).push(...e.releases),n(this,f,e.next),t(this,l).querySelector(".sl-timeline")?.insertAdjacentHTML("beforeend",e.releases.map((c,h)=>t(this,z).call(this,c,o+h)).join("")),a(this,s,H).call(this)},H=function(){if(t(this,u)?.disconnect(),t(this,f)===null){t(this,l).querySelector(".sl-sentinel")?.remove();return}n(this,x,t(this,l).querySelector(".sl-sentinel")),t(this,x)&&(n(this,u,new IntersectionObserver(e=>e.some(o=>o.isIntersecting)&&a(this,s,X).call(this),{root:t(this,s,L),rootMargin:"600px"})),t(this,u).observe(t(this,x)))},S=function(e){t(this,l).querySelector(".sl-content").innerHTML=e},B=function(){if(t(this,g).length===0){a(this,s,S).call(this,`<div class="sl-state">${m(this.getAttribute("empty-text")||"Nothing shipped yet.")}</div>`);return}a(this,s,S).call(this,`<ol class="sl-timeline">${t(this,g).map(t(this,z)).join("")}</ol><div class="sl-sentinel" aria-hidden="true"><span class="sl-spinner"></span></div>`),a(this,s,H).call(this)},z=new WeakMap,M=function(){let e=t(this,l).querySelector(".sl-fab");e&&(e.dataset.position=this.getAttribute("position")||"bottom-right",e.querySelector(".sl-fab__label").textContent=this.getAttribute("label")||"What\u2019s new",e.querySelector(".sl-fab__dot")?.remove(),a(this,s,K).call(this)&&e.insertAdjacentHTML("beforeend",'<span class="sl-fab__dot" aria-hidden="true"></span>'))},O=function(){return`shiplog:seen:${this.getAttribute("storage-key")||"default"}`},K=function(){let e=this.getAttribute("signature");if(!e)return!1;try{return window.localStorage.getItem(a(this,s,O).call(this))!==e}catch{return!1}},W=function(){let e=this.getAttribute("signature");if(e)try{window.localStorage.setItem(a(this,s,O).call(this),e)}catch{}},D=function(){if(this.hasAttribute("theme")){this.dataset.theme=this.getAttribute("theme");return}n(this,v,window.matchMedia("(prefers-color-scheme: dark)")),t(this,v).addEventListener("change",t(this,y)),n(this,k,new MutationObserver(t(this,y))),t(this,k).observe(document.documentElement,{attributes:!0,attributeFilter:["class","data-theme"]}),t(this,y).call(this)},y=new WeakMap,G=function(){this.dataset.previousOverflow=document.body.style.overflow,document.body.style.overflow="hidden"},j=function(){"previousOverflow"in this.dataset&&(document.body.style.overflow=this.dataset.previousOverflow,delete this.dataset.previousOverflow)},I(_,"observedAttributes",["position","label","signature"]);customElements.get("ship-log")||customElements.define("ship-log",_);window.ShipLog={element:()=>document.querySelector("ship-log"),open:()=>window.ShipLog.element()?.open(),close:()=>window.ShipLog.element()?.close(),toggle:()=>window.ShipLog.element()?.toggle(),refresh:()=>window.ShipLog.element()?.refresh()};})();
