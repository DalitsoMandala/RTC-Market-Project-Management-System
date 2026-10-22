<?php

use App\Models\Project;

class ProjectService
{
    public static function app_project()
    {
        return once(function () {
            $currentProject = config('app.currentProject');
            return Project::where('name', $currentProject)->first();
        });
    }

    public static function app_cgiar_project()
    {
        return once(function () {
            $currentCgiarProject = config('app.currentCgiarProject');
            return Project::where('name', $currentCgiarProject)->first();
        });
    }
}