<?php

namespace App\Livewire\admin;

use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Pagination\Paginator;

final class UserTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        return [
            Exportable::make('Users_Export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->with([
            'organisation',
            'roles'
        ])->withTrashed()->select([
            'users.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('name_image', function ($model) {
                $image = $model->image
                    ? asset('storage/profiles/' . $model->image)
                    : asset('assets/images/users/usr.png');

                return '<div class="d-flex align-items-center">
                            <div class="mx-2">
                                <img src="' . $image . '" alt="" class="avatar-sm rounded-circle">
                            </div>
                            <div>' . $model->name . '</div>
                        </div>';
            })
            ->add('email')
            ->add('phone_number')
            ->add('organisation_id')
            ->add('organisation', fn($model) => $model->organisation?->name)
            ->add('image')
            ->add('role', fn($model) => $model->roles->pluck('name')->implode(', '))
            ->add('role_name', function ($model) {
                $roleArray = $model->roles->map(function ($role) {
                    $role->name = match ($role->name) {
                        'external'        => '<span class="badge bg-secondary-subtle text-secondary">External user</span>',
                        'manager'         => '<span class="badge bg-info-subtle text-info">Manager</span>',
                        'admin'           => '<span class="badge bg-success-subtle text-success">Administrator</span>',
                        'project-manager' => '<span class="badge bg-success-subtle text-success">Project Manager</span>',
                        'staff'           => '<span class="badge bg-warning-subtle text-warning">Staff</span>',
                        'enumerator'      => '<span class="badge bg-dark-subtle text-dark">' . $role->name . '</span>',
                        default           => '<span class="badge bg-danger-subtle text-danger">' . $role->name . '</span>',
                    };

                    return $role;
                })->pluck('name');

                return implode(' ', $roleArray->toArray());
            })
            ->add('status', fn($model) => !$model->is_active ? 'Deleted' : 'Active')
            ->add('status_set', function ($model) {
                return $model->is_active
                    ? '<span class="badge bg-success-subtle text-success">Active</span>'
                    : '<span class="badge bg-danger-subtle text-danger">Deleted</span>';
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'rn', 'rn')->sortable(),
            Column::make('Name', 'name_image', 'name')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),
            Column::make('Name', 'name')->hidden()->visibleInExport(true),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Organisation', 'organisation')->searchable(),
            Column::make('Roles', 'role_name')->bodyAttribute('text-uppercase')->visibleInExport(false),
            Column::make('Role', 'role')->hidden()->visibleInExport(true),
            Column::make('Status', 'status_set', 'deleted_at')->sortable()->visibleInExport(false),
            Column::make('Status', 'status')->hidden()->visibleInExport(true),

            Column::action('')->visibleInExport(false)
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function relationSearch(): array
    {
        return [
            'organisation' => [
                'name',
            ],
        ];
    }

    public function actions($row): array
    {
        $isAdmin = auth()->user()?->hasAnyRole('admin') ?? false;

        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-warning btn-sm goUp custom-tooltip')
                ->tooltip('Edit')
                ->can($isAdmin)
                ->dispatch('showModal-edit', [
                    'rowId' => $row->id,
                    'name'  => 'addUserModal'
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red btn-sm goUp custom-tooltip')
                ->tooltip('Delete')
                ->can($isAdmin)
                ->dispatch('showModal-delete', [
                    'rowId' => $row->id,
                    'name'  => 'view-delete-modal'
                ]),

            Button::add('restore')
                ->slot('<i class="bx bx-recycle"></i>')
                ->id()
                ->class('btn btn-success btn-sm goUp custom-tooltip')
                ->tooltip('Restore')
                ->can($isAdmin)
                ->dispatch('showModal-restore', [
                    'rowId' => $row->id,
                    'name'  => 'view-restore-modal'
                ])
        ];
    }

    public function actionRules($row): array
    {
        return [
            // --- EDIT BUTTON RULES ---
            Rule::button('edit')
                ->when(fn($row) => $row->hasAnyRole('admin'))
                ->disable(),

            Rule::button('edit')
                ->when(fn($row) => $row->is_active == 0)
                ->disable(),

            // --- DELETE BUTTON RULES ---
            // Disable if the row user is an admin (regardless of active status)
            Rule::button('delete')
                ->when(fn($row) => $row->hasAnyRole('admin'))
                ->disable(),

            // Disable if the user is already inactive/deleted
            Rule::button('delete')
                ->when(fn($row) => $row->is_active == 1)
                ->disable(),

            // --- RESTORE BUTTON RULES ---
            // Disable if the user is active (restore is only enabled when inactive)
            Rule::button('restore')
                ->when(fn($row) => $row->is_active == 1)
                ->disable(),
        ];
    }
}
