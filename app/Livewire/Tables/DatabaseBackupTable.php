<?php

namespace App\Livewire\tables;

use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DatabaseBackupTable extends PowerGridComponent
{

    use LivewireAlert;
    public $isRunning = false;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
  public function datasource(): ?Collection
{
    $files = Storage::disk('public_backups')->files(config('app.name'));

    // Map files into collection
    $collection = collect($files)->map(function ($file, $index) {
        return [
            'id'         => $index + 1,
            'name'       => basename($file),
            'path'       => $file, // relative path
            'size'       => round(Storage::disk('public_backups')->size($file) / 1024 / 1024, 2) . ' MB',
            'created_at' => date('Y-m-d H:i:s', Storage::disk('public_backups')->lastModified($file)),
        ];
    });

    // Sort by creation date descending (latest first)
    $collection = $collection->sortByDesc('created_at')->values();

    return $collection;
}

    public function setUp(): array
    {

        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('size')
            ->add('created_at_formatted', function ($entry) {

                return Carbon::parse($entry->created_at)->format('d/m/Y H:i:A');
            });
    }


    #[On('refreshDB')]
    public function refreshDB(){
        $this->refresh();
    }
    public function columns(): array
    {
        return [
            // Column::make('ID', 'id')
            //     ->searchable()
            //     ->sortable(),

            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),
            Column::make('Size', 'size')
                ->searchable()
                ->sortable(),


            Column::make('Last Saved', 'created_at_formatted', 'created_at'),

            Column::action('')
        ];
    }



    #[On('downloadBackup')]
    public function downloadBackup($file)
    {

        if (Storage::disk('public_backups')->exists($file)) {
            $this->redirect("/storage/backups/" . $file);
            $this->alert('success', 'Backup downloaded successfully',[
                'timer' => 5000,
                'toast' => false,
                'position' => 'center',
            ]);
        } else {
            $this->alert('error', 'File not found',[
                'timer' => 5000,
                'toast' => false,
                'position' => 'center',
            ]);
        }



        //  $absolutePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, Storage::disk('public_backups')->path($file));


    }



    public function actions($row): array
    {

        return [
            Button::add('download')
                ->slot('<i class="bx bx-download"></i>')
                ->id()
                ->tooltip('Download Backup')
                ->class('btn btn-warning goUp btn-sm custom-tooltip')
                ->dispatch('downloadBackup', ['file' => $row['path']]),


        ];
    }


}
