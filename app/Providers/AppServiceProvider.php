<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('FORCE_HTTPS') === true) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (env('FORCE_HTTPS', false)){
            URL::forceScheme('https');
        }    

        if ($this->app->environment('local') || $this->app->environment('development')) {
            Mail::alwaysTo(env('MAIL_DEV_TEST'));
        }

        // Share valid/latest semester with all views to avoid undefined variable errors in Mailables/Console
        try {
            $semester = \App\Models\Semester::getInEnrollmentPeriod() ?? \App\Models\Semester::getLatest();
            \Illuminate\Support\Facades\View::share('semester', $semester);
            
            // Fix for 'Undefined variable: errors' in Console/Mail
            \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\MessageBag());
        } catch (\Throwable $e) {
            // Ignore if DB not ready
        }
    }
}
