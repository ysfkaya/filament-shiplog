<?php

namespace Ysfkaya\ShipLog;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ysfkaya\ShipLog\Contracts\ChangelogRepository;
use Ysfkaya\ShipLog\Http\FeedController;
use Ysfkaya\ShipLog\Markdown\ChangelogParser;
use Ysfkaya\ShipLog\Markdown\MarkdownRenderer;
use Ysfkaya\ShipLog\Support\Authorizer;
use Ysfkaya\ShipLog\Support\FabSettings;

class ShipLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('shiplog')
            ->hasConfigFile()
            ->hasViews('shiplog')
            ->hasTranslations()
            ->hasMigration('create_shiplog_releases_table');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MarkdownRenderer::class, fn ($app): MarkdownRenderer => new MarkdownRenderer(
            (bool) $app['config']->get('shiplog.markdown.allow_html', false),
        ));

        $this->app->singleton(ChangelogParser::class);
        $this->app->singleton(ShipLogManager::class);
        $this->app->singleton(Authorizer::class);
        $this->app->singleton(FabSettings::class);

        $this->app->bind(
            ChangelogRepository::class,
            fn ($app): ChangelogRepository => $app->make(ShipLogManager::class)->driver(),
        );
    }

    public function packageBooted(): void
    {
        $this->app->make(Authorizer::class)->registerGates();

        $this->registerRoutes();
        $this->registerAssets();
        $this->registerCacheInvalidation();

        Blade::component(View\ShipLog::class, 'shiplog');
    }

    protected function registerRoutes(): void
    {
        if (! config('shiplog.route.enabled', true)) {
            return;
        }

        Route::group([
            'prefix' => config('shiplog.route.prefix', 'shiplog'),
            'middleware' => config('shiplog.route.middleware', ['web']),
        ], function (): void {
            Route::get('feed', FeedController::class)->name('shiplog.feed');
        });
    }

    protected function registerAssets(): void
    {
        FilamentAsset::register([
            Js::make('fi-shiplog', __DIR__ . '/../resources/dist/fi-shiplog.js'),
        ], 'ysfkaya');
    }

    /**
     * Keeps the cached timeline honest without asking anyone to remember a
     * flush, no matter how a release was written.
     */
    protected function registerCacheInvalidation(): void
    {
        $model = config('shiplog.model', Models\Release::class);

        if (! is_string($model) || ! is_subclass_of($model, Model::class)) {
            return;
        }

        $flush = fn (): mixed => $this->app->make(ShipLogManager::class)->flush();

        $model::saved($flush);
        $model::deleted($flush);
    }
}
