<?php

namespace Ysfkaya\ShipLog\Concerns;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;
use Ysfkaya\ShipLog\Filament\Pages\Changelog;

trait HasPage
{
    protected string $page = Changelog::class;

    protected string | UnitEnum | null $navigationGroup = null;

    protected ?int $navigationSort = null;

    protected string | BackedEnum | Htmlable | null $navigationIcon = null;

    protected ?string $navigationLabel = null;

    protected ?string $pageTitle = null;

    protected ?string $slug = null;

    public function usingPage(string $class): static
    {
        $this->page = $class;

        return $this;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function navigationGroup(string | UnitEnum | null $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): string | UnitEnum | null
    {
        return $this->navigationGroup;
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function navigationIcon(string | BackedEnum | Htmlable | null $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->navigationIcon;
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel;
    }

    public function pageTitle(?string $title): static
    {
        $this->pageTitle = $title;

        return $this;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function slug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
