<?php

namespace App\Services;

use Illuminate\Support\Arr;

class Navigation
{
    /**
     * Return the menu tree for the given user, with `active` flags resolved.
     * Uses role priority — the first role in config('navigation.priority')
     * that the user has "wins". If you want the union of all roles' menus
     * instead, replace the body with a merge.
     */
    public static function forUser($user): array
    {
        if (! $user) {
            return [];
        }

        $roles   = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->all() : [];
        $menus   = config('navigation.menus', []);
        $priority = config('navigation.priority', array_keys($menus));

        foreach ($priority as $role) {
            if (in_array($role, $roles, true) && isset($menus[$role])) {
                return self::resolveActive($menus[$role]);
            }
        }

        return [];
    }

protected static function resolveActive(array $items): array
{
    foreach ($items as &$item) {

        if (!empty($item['children'])) {

            $item['children'] = self::resolveActive($item['children']);

            $item['active'] = collect($item['children'])
                ->contains(fn ($child) => $child['active'] ?? false);

        } else {

            $route = $item['route'] ?? null;

            $item['active'] = $route
                ? request()->routeIs($route, $route . '.*')
                : false;
        }
    }

    unset($item);

    return $items;
}

    /**
     * The role that "owns" the user's menu — first match in config priority.
     */
    public static function primaryRole($user): ?string
    {
        if (! $user) {
            return null;
        }

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->all()
            : [];

        foreach (config('navigation.priority', []) as $role) {
            if (in_array($role, $roles, true)) {
                return $role;
            }
        }

        return null;
    }
}
