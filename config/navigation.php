<?php



return [
    /*
    |--------------------------------------------------------------------------
    | Role priority
    |--------------------------------------------------------------------------
    | When a user has multiple roles, the menu for the first matching role
    | in this list is shown. To show the union of all roles' menus instead,
    | change Navigation::forUser() to merge them.
    */
    'priority' => [
        'admin',
        'monitor',
        'manager',
        'project-manager',
        'staff',
        'external',
        'enumerator',
    ],

    'menus' => [
        'admin' => [
            ['label' => 'Dashboard',   'route' => 'admin-dashboard', 'icon' => 'bx-home-alt'],
            ['label' => 'Manage Users', 'route' => 'admin-users',     'icon' => 'bx-user'],
            [
                'label' => 'Data Management',
                'icon'  => 'bx-folder',
                'children' => [
                    ['label' => 'Project Management', 'children' => [
                        ['label' => 'Project Operations', 'route' => 'admin-period'],
                    ]],
                    ['label' => 'Indicator Management', 'children' => [
                        ['label' => 'Indicators',           'route' => 'admin-indicators'],
                        ['label' => 'Manage Baseline Data', 'route' => 'admin-baseline'],
                        ['label' => 'Indicator Targets',    'route' => 'admin-std-targets'],
                        ['label' => 'View Targets',         'route' => 'admin-targets'],
                        ['label' => 'Indicator Sources',    'route' => 'admin-sources'],
                    ]],
                    ['label' => 'Operations Management', 'children' => [
                        ['label' => 'Form Data',              'route' => 'admin-forms'],
                        ['label' => 'Submissions',            'route' => 'admin-submissions'],
                        ['label' => 'Submission Periods',     'route' => 'admin-submission-period'],
                        ['label' => 'Reports',                'route' => 'admin-reports'],
                        ['label' => 'Enterprise Management',  'route' => 'admin-enterprise-management'],
                        ['label' => 'Aggregated Reports',     'route' => 'admin-aggregated-reports'],
                    ]],
                    ['label' => 'Marketing Management', 'children' => [
                        ['label' => 'Manage Data',              'route' => 'admin-markets-manage-data'],
                        ['label' => 'Marketing Data Submission', 'route' => 'admin-markets-submit-data'],
                    ]],
                    ['label' => 'Gross Management', 'children' => [
                        ['label' => 'Manage Data',           'route' => 'admin-gross-margin-manage-data'],
                        ['label' => 'Gross Data Submission', 'route' => 'admin-gross-margin-add-data'],
                    ]],
                ],
            ],
            ['label' => 'Settings', 'route' => 'admin-setup', 'icon' => 'bx-cog'],
        ],

        'monitor' => [
            // similar to admin but monitor-* routes, no enterprise/aggregated
        ],

        'manager' => [
            ['label' => 'Dashboard', 'route' => 'cip-dashboard', 'icon' => 'bx-home-alt'],
            [
                'label' => 'Data Management',
                'icon'  => 'bx-folder',
                'children' => [
                    ['label' => 'Baseline Data',      'route' => 'cip-baseline'],
                    ['label' => 'Indicators',         'route' => 'cip-indicators'],
                    ['label' => 'Form Data',          'route' => 'cip-forms'],
                    ['label' => 'Submission Periods', 'route' => 'cip-submission-period'],
                    ['label' => 'View Targets',       'route' => 'cip-targets'],
                    ['label' => 'Submissions',        'route' => 'cip-submissions'],
                    ['label' => 'Marketing Management', 'children' => [
                        ['label' => 'Manage Data',               'route' => 'cip-markets-manage-data'],
                        ['label' => 'Marketing Data Submission', 'route' => 'cip-markets-submit-data'],
                    ]],
                    ['label' => 'Gross Management', 'children' => [
                        ['label' => 'Manage Data',           'route' => 'cip-gross-margin-manage-data'],
                        ['label' => 'Gross Data Submission', 'route' => 'cip-gross-margin-add-data'],
                    ]],
                ],
            ],
            ['label' => 'Reports', 'route' => 'cip-reports', 'icon' => 'bx-table'],
        ],

        'external' => [
            ['label' => 'Dashboard', 'route' => 'external-dashboard', 'icon' => 'bx-tachometer'],
            [
                'label' => 'Data Management',
                'icon'  => 'bx-folder',
                'children' => [
                    ['label' => 'Indicators',         'route' => 'external-indicators'],
                    ['label' => 'Form Data',          'route' => 'external-forms'],
                    ['label' => 'View Targets',       'route' => 'external-targets'],
                    ['label' => 'Submission Periods', 'route' => 'external-submission-period'],
                    ['label' => 'Submissions',        'route' => 'external-submissions'],
                ],
            ],
            ['label' => 'Reports', 'route' => 'external-reports', 'icon' => 'bx-table'],
        ],

        'staff' => [
            ['label' => 'Dashboard', 'route' => 'cip-staff-dashboard', 'icon' => 'bx-tachometer'],
            [
                'label' => 'Data Management',
                'icon'  => 'bx-folder',
                'children' => [
                    ['label' => 'Indicators',         'route' => 'cip-staff-indicators'],
                    ['label' => 'Form Data',          'route' => 'cip-staff-forms'],
                    ['label' => 'View Targets',       'route' => 'cip-staff-targets'],
                    ['label' => 'Submission Periods', 'route' => 'cip-staff-submission-period'],
                    ['label' => 'Submissions',        'route' => 'cip-staff-submissions'],
                    ['label' => 'Marketing Management', 'children' => [
                        ['label' => 'Manage Data',               'route' => 'staff-markets-manage-data'],
                        ['label' => 'Marketing Data Submission', 'route' => 'staff-markets-submit-data'],
                    ]],
                    ['label' => 'Gross Management', 'children' => [
                        ['label' => 'Manage Data',           'route' => 'staff-gross-margin-manage-data'],
                        ['label' => 'Gross Data Submission', 'route' => 'staff-gross-margin-add-data'],
                    ]],
                ],
            ],
            ['label' => 'Reports', 'route' => 'cip-staff-reports', 'icon' => 'bx-table'],
        ],

        'project-manager' => [
            ['label' => 'Dashboard', 'route' => 'project-manager-dashboard', 'icon' => 'bx-tachometer'],
            [
                'label' => 'Data Management',
                'icon'  => 'bx-folder',
                'children' => [
                    ['label' => 'Indicators',   'route' => 'project-manager-indicators'],
                    ['label' => 'Form Data',    'route' => 'project-manager-forms'],
                    ['label' => 'View Targets', 'route' => 'project-manager-targets'],
                    ['label' => 'Marketing Management', 'children' => [
                        ['label' => 'Manage Data', 'route' => 'project-manager-markets-manage-data'],
                    ]],
                    ['label' => 'Gross Management', 'children' => [
                        ['label' => 'Manage Data', 'route' => 'project-manager-gross-margin-manage-data'],
                    ]],
                ],
            ],
            ['label' => 'Reports', 'route' => 'project-manager-reports', 'icon' => 'bx-table'],
        ],

        'enumerator' => [
            ['label' => 'Dashboard',   'route' => 'enumerator-dashboard',   'icon' => 'bx-tachometer'],
            ['label' => 'Submissions', 'route' => 'enumerator-submissions', 'icon' => 'bx-bar-chart-alt-2'],
            [
                'label' => 'Data Management',
                'icon'  => 'bx-folder',
                'children' => [
                    ['label' => 'Marketing Management', 'children' => [
                        ['label' => 'Manage Data',               'route' => 'enumerator-markets-manage-data'],
                        ['label' => 'Marketing Data Submission', 'route' => 'enumerator-markets-submit-data'],
                    ]],
                ],
            ],
        ],
    ],


    /*
|--------------------------------------------------------------------------
| Dashboard switch tabs (top-row buttons)
|--------------------------------------------------------------------------
| Keyed by role. Any tab whose route name doesn't exist is silently skipped,
| so it's safe to list extras here for roles that don't have them yet.
*/
    'dashboard_tabs' => [
        'admin' => [
            ['route' => 'admin-dashboard',   'label' => 'Project Report', 'icon' => 'bx-file'],
            ['route' => 'admin-dashboard-2', 'label' => 'Market Data',    'icon' => 'bx-bar-chart'],
            ['route' => 'admin-dashboard-3', 'label' => 'Gross Margins',  'icon' => 'bx-trending-up'],
        ],
        'monitor' => [
            ['route' => 'monitor-dashboard',   'label' => 'Project Report', 'icon' => 'bx-file'],
            ['route' => 'monitor-dashboard-2', 'label' => 'Market Data',    'icon' => 'bx-bar-chart'],
            ['route' => 'monitor-dashboard-3', 'label' => 'Gross Margins',  'icon' => 'bx-trending-up'],
        ],
        'manager' => [
            ['route' => 'cip-dashboard',   'label' => 'Project Report', 'icon' => 'bx-file'],
            ['route' => 'cip-dashboard-2', 'label' => 'Market Data',    'icon' => 'bx-bar-chart'],
            ['route' => 'cip-dashboard-3', 'label' => 'Gross Margins',  'icon' => 'bx-trending-up'],
        ],
        'staff' => [
            ['route' => 'cip-staff-dashboard',   'label' => 'Project Report', 'icon' => 'bx-file'],
            ['route' => 'cip-staff-dashboard-2', 'label' => 'Market Data',    'icon' => 'bx-bar-chart'],
            ['route' => 'cip-staff-dashboard-3', 'label' => 'Gross Margins',  'icon' => 'bx-trending-up'],
        ],
        'project-manager' => [
            ['route' => 'project-manager-dashboard',   'label' => 'Project Report', 'icon' => 'bx-file'],
            ['route' => 'project-manager-dashboard-2', 'label' => 'Market Data',    'icon' => 'bx-bar-chart'],
            ['route' => 'project-manager-dashboard-3', 'label' => 'Gross Margins',  'icon' => 'bx-trending-up'],
        ],
        'enumerator' => [
            ['route' => 'enumerator-dashboard', 'label' => 'Project Report', 'icon' => 'bx-file'],
        ],
        // 'external' has no dashboard-switch tabs
    ],

    /*
|--------------------------------------------------------------------------
| Profile route per role
|--------------------------------------------------------------------------
| Falls back to a generic `profile` route, then to no link, if not defined.
*/
    'profile_routes' => [
        'admin'           => 'admin-profile',
        'monitor'         => 'monitor-profile',
        'manager'         => 'cip-profile',
        'staff'           => 'cip-staff-profile',
        'project-manager' => 'project-manager-profile',
        'enumerator'      => 'enumerator-profile',
        'external'        => 'external-profile',
    ],
];
