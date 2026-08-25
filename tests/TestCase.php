<?php

namespace Ysfkaya\ShipLog\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Ysfkaya\ShipLog\ShipLogServiceProvider;
use Ysfkaya\ShipLog\Tests\Fixtures\ShipLogPanelProvider;
use Ysfkaya\ShipLog\Tests\Fixtures\User;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            // Filament rebinds Livewire's DataStore, so its support provider has
            // to register before Livewire caches the mechanism instance.
            SupportServiceProvider::class,
            LivewireServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            ShipLogServiceProvider::class,
            ShipLogPanelProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        $migration = require __DIR__ . '/../database/migrations/create_shiplog_releases_table.php.stub';
        $migration->up();
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:fs7e0Hwi58EfBeSzcP7OuM1gJkUOOMTXdK+5e51umeA=');
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
