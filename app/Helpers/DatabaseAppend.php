<?php

namespace App\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseAppend
{
    public array $columns = [];
    public string $tableName;

    public function __construct(string $tableName, array $columns)
    {
        $this->tableName = $tableName;
        $this->columns = $columns;
    }

    public function append(): bool
    {
        // Check if table exists
        if (!Schema::hasTable($this->tableName)) {
            return false;
        }

        Schema::table($this->tableName, function (Blueprint $table) {

            foreach ($this->columns as $column => $callback) {

                if (Schema::hasColumn($this->tableName, $column)) {
                    dd('Column already exists');
                    continue;
                }

                $callback($table);
            }
        });

        return true;
    }


    public function updateData($uuid, $table, $data = [])
    {


    }
}
