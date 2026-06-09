<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Map polymorphic types to actual models
        Relation::enforceMorphMap([
            'tenant' => 'App\Models\User',
            'owner'  => 'App\Models\Owner',
        ]);
    }

    public function register(): void
    {
        //
    }
}