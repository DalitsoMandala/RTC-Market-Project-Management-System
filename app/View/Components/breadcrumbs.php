<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class Breadcrumbs extends Component
{
    public array $breadcrumbs = [];

    public function __construct()
    {
        $currentRoute = Route::currentRouteName();
        $navigation = config('navigation.menus', []); // Adjust config filename if yours isn't navigation.php

        // Find path for the user's current role or look through all role menus
        foreach ($navigation as $role => $menuItems) {
            $trail = $this->findRouteTrail($menuItems, $currentRoute);
            if (!empty($trail)) {
                $this->breadcrumbs = $trail;
                break;
            }
        }
    }

    private function findRouteTrail(array $items, string $targetRoute): array
    {
        foreach ($items as $item) {
            // Check if current item matches route
            if (isset($item['route']) && $item['route'] === $targetRoute) {
                return [['label' => $item['label'], 'route' => $item['route'] ?? null]];
            }

            // Check children / sub-menus recursively
            if (isset($item['children'])) {
                $result = $this->findRouteTrail($item['children'], $targetRoute);
                if (!empty($result)) {
                    // Prepend current parent label to the trail
                    array_unshift($result, [
                        'label' => $item['label'],
                        'route' => $item['route'] ?? null
                    ]);
                    return $result;
                }
            }
        }

        return [];
    }

    public function render(): View|Closure|string
    {
        return view('components.breadcrumbs');
    }
}
