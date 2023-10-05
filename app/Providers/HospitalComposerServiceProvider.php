<?php

namespace App\Providers;

use App\Models\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class HospitalComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        view()->composer('*', function ($view) {

            if(Auth::user()){
                $hospital = Hospital::find(Auth::user()->hospital_id);
            }else{
                $hospital = [];
            }

            // Sharing the $hospital variable to all views
            $view->with('hospital', $hospital);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
