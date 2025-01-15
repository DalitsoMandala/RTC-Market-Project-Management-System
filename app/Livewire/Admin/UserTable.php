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

final class UserTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')
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
        return User::with([
            'organisation',
            'roles'
        ])->withTrashed();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('name_image', function ($model) {

                $image = $model->image;

                if (!$image) {
                    $image = asset('assets/images/users/usr.png');
                } else {
                    $image = asset('storage/profiles/' . $image);
                }
                return '<div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded p-1 me-2">
                                                        <img src="' . $image . '" alt="" class="img-fluid d-block">
                                                    </div>
                                                    <div>
                                                     ' . $model->name . '
                                                    </div>
  </div>';
            })
            ->add('email')
            ->add('phone_number')
            ->add('organisation_id')
            ->add('organisation', function ($model) {
                return $model->organisation->name;
            })
            ->add('image')
            ->add('role', function ($model) {

                $roles = $model->roles;


                $roleArray = $roles->map(function ($role) {


                    if ($role->name == 'external') {

                        $role->name = '<span class="badge bg-warning" style="font-size:11px">External user</span>';
                    }

                    if ($role->name == 'internal') {
                        $role->name = '<span class="badge bg-warning" style="font-size:11px">Internal user</span>';
                    }

                    if ($role->name == 'cip') {
                        $role->name = '<span class="badge bg-warning" style="font-size:11px">CIP</span>';
                    }


                    if ($role->name == 'desira') {
                        $role->name = '<span class="badge bg-warning" style="font-size:11px">DESIRA</span>';
                    }

                    if ($role->name == 'manager') {
                        $role->name = '<span class="badge bg-warning" style="font-size:11px">Organiser</span>';
                    }

                    if ($role->name == 'donor') {
                        $role->name = '<span class="badge bg-warning" style="font-size:11px">Donor</span>';
                    }

                    if ($role->name == 'admin') {
                        $role->name = '<span class="badge bg-success" style="font-size:11px">Administrator</span>';
                    }
                    if ($role->name == 'project_manager') {
                        $role->name = '<span class="badge bg-success" style="font-size:11px">Project Manager</span>';
                    }
                    if ($role->name == 'staff') {
                        $role->name = '<span class="badge bg-warning" style="font-size:11px">Staff</span>';
                    }


                    return $role;
                })->pluck('name');
                return implode(' ', $roleArray->toArray());
            })

            ->add('status', function ($model) {


                if (!$model->deleted_at) {
                    return '<span class="badge bg-success" style="font-size:11px">Active</span>';
                } else {
                    return '<span class="badge bg-theme-red" style="font-size:11px">Deleted</span>';
                }
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name_image', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Organisation', 'organisation')->searchable(),
            Column::make('Roles', 'role')->bodyAttribute('text-uppercase'),
            Column::make('Status', 'status', 'deleted_at')->sortable(),
            Column::action('Action')

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
            'organisation' => [ // relationship on dishes model
                'name', // column enabled to search
            ],
        ];
    }


    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i> Edit')
                ->id()
                ->class('btn btn-warning goUp')
                ->dispatch('edit', [
                    'rowId' => $row->id,
                    'name' => 'view-edit-modal'
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash"></i> Delete')
                ->id()
                ->class('btn btn-theme-red goUp')
                ->dispatch('showModal-delete', [
                    'rowId' => $row->id,
                    'name' => 'view-delete-modal'
                ]),

            Button::add('restore')
                ->slot('<i class="bx bx-recycle"></i>')
                ->id()
                ->class('btn btn-success goUp')
                ->dispatch('showModal-restore', [
                    'rowId' => $row->id,
                    'name' => 'view-restore-modal'
                ])
        ];
    }


    public function actionRules($row): array
    {
        $user = User::withTrashed()->find($row->id);
        return [
            // Hide the edit button for users with the 'admin' role
            Rule::button('edit')
                ->when(fn($row) => $user->hasAnyRole('admin'))
                ->hide(),

            Rule::button('edit')
                ->when(fn($row) => $user->deleted_at !== null)
                ->disable(),

            // Hide the delete button for users with the 'admin' role
            Rule::button('delete')
                ->when(fn($row) => $user->hasAnyRole('admin'))
                ->hide(),

            Rule::button('delete')
                ->when(fn($row) => $user->deleted_at !== null)
                ->disable(),

            // Hide the restore button for users with the 'admin' role
            Rule::button('restore')
                ->when(fn($row) => $user->hasAnyRole('admin'))
                ->hide(),
        ];
    }
}
