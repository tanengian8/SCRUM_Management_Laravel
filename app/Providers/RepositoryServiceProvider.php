<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register() {
        $pages = array(
            'User',
            'Project',
            'Backlog',
            'PlanningPoker',
        );

        foreach ($pages as $page) {
            $this->app->bind("App\Repositories\Contracts\\{$page}RepositoryContract", "App\Repositories\\{$page}Repository");
        }
    }
}
