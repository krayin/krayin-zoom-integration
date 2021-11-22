<?php

namespace Webkul\ZoomMeeting\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class ZoomMeetingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'zoom_meeting');

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/zoom-meeting/assets'),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'zoom_meeting');

        Event::listen('admin.layout.head', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('zoom_meeting::layouts.style');
        });

        Event::listen('admin.leads.view.informations.activity_actions.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('zoom_meeting::leads.view.activity-action.create');
        });

        Event::listen('admin.activities.edit.form_controls.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('zoom_meeting::activities.edit');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }
}