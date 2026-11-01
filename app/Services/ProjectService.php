<?php
namespace App\Services;

use App\Models\Project;

class ProjectService
{

    public static function app_project()
    {
        return once(function () {
            $currentProject = config('app.currentProject');
            return Project::where('name', $currentProject)->where('is_active', true)->first();
        });
    }

    public static function app_project_id()
    {
        return self::app_project()->id;
    }

    public static function app_project_name()
    {
        return self::app_project()->name;
    }

    public static function app_cgiar_project()
    {
        return once(function () {
            $currentCgiarProject = config('app.currentCgiarProject');
            return Project::where('name', $currentCgiarProject)->where('is_active', true)->first();
        });
    }
}
