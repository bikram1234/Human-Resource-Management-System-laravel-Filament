<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Filament\Actions\DownloadFileAction;
use Filament\Facades\Filament;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Filament::serving(function () {
        //     // ...
        //     $primaryColor = '#FF8834'; // For example, put your tenant primary color here
        //     $secondaryColor = '#BBAA87'; // For example, put your tenant secondary color here
    
        //     Filament::pushMeta([
        //         new HtmlString('<meta name="theme-primary-color" id="theme-primary-color" content="' . $primaryColor . '">' .
        //             '<meta name="theme-secondary-color" id="theme-secondary-color" content="' . $secondaryColor . '">'),
        //     ]);
        // });
    }
}
