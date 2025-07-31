<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->updateForeignKeyWithCascade('rtc_production_farmers', 'user_id', 'users');
        $this->updateForeignKeyWithCascade('rtc_production_processors', 'user_id', 'users');
        $this->updateForeignKeyWithCascade('recruitments', 'user_id', 'users');
        $this->updateDecimals();
    }

    private function updateDecimals()
    {
        $database = DB::getDatabaseName();

        // Get all DECIMAL columns in the current database
        $decimalColumns = DB::select("
            SELECT TABLE_NAME, COLUMN_NAME, NUMERIC_PRECISION, NUMERIC_SCALE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND DATA_TYPE = 'decimal'
        ", [$database]);

        foreach ($decimalColumns as $column) {
            $table = $column->TABLE_NAME;
            $col = $column->COLUMN_NAME;

            // Optional: skip specific tables or columns
            // if ($table === 'audit_logs') continue;

            try {
                DB::statement("ALTER TABLE `$table` MODIFY `$col` DECIMAL(65,2)");
                echo "Updated $table.$col to DECIMAL(65,2)\n";
            } catch (\Exception $e) {
                echo "Failed to update $table.$col: " . $e->getMessage() . "\n";
            }
        }
    }


    private function revertDecimals()
    {

        Log::error("Reverting decimal columns to DECIMAL(18,2)");
    }

    private function updateDecimalColumn($tableName, $columnName, $precision, $scale)
    {
        Schema::table($tableName, function (Blueprint $table) use ($columnName, $precision, $scale) {
            $table->decimal($columnName, $precision, $scale)->change();
        });
    }

    private function revertDecimalColumn($tableName, $columnName)
    {
        Schema::table($tableName, function (Blueprint $table) use ($columnName) {
            $table->decimal($columnName, 18, 2)->change();
        });
    }

    private function updateForeignKeyWithCascade($tableName, $foreignKeyColumn, $referencedTable)
    {
        Schema::table($tableName, function (Blueprint $table) use ($foreignKeyColumn) {
            $table->dropForeign([$foreignKeyColumn]);
        });

        Schema::table($tableName, function (Blueprint $table) use ($foreignKeyColumn, $referencedTable) {
            $table->foreign($foreignKeyColumn)
                ->references('id')->on($referencedTable)
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        // Reverse the foreign keys (without cascade)
        $this->revertForeignKey('rtc_production_farmers', 'user_id', 'users');
        $this->revertForeignKey('rtc_production_processors', 'user_id', 'users');
        $this->revertForeignKey('recruitments', 'user_id', 'users');
        $this->revertDecimals();
    }

    private function revertForeignKey($tableName, $foreignKeyColumn, $referencedTable)
    {
        Schema::table($tableName, function (Blueprint $table) use ($foreignKeyColumn) {
            $table->dropForeign([$foreignKeyColumn]);
        });

        Schema::table($tableName, function (Blueprint $table) use ($foreignKeyColumn, $referencedTable) {
            $table->foreign($foreignKeyColumn)
                ->references('id')->on($referencedTable);
        });
    }
};
