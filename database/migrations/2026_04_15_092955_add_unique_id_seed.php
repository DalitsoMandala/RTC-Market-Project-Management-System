<?php

use App\Models\SeedBeneficiary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add the column as nullable first
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
           $table->string('sd_id')->nullable()->after('id');
          $table->string('crop')->change();
        });

        // Initialize the counter.
        // If you want it to start from 1 regardless of ID gaps, use 1.
        $number = 1;

        // 2. Populate existing records
        // Use & to pass $number by reference so the increment persists across chunks
        DB::table('seed_beneficiaries')->orderBy('id')->chunkById(1000, function ($rows) use (&$number) {
            foreach ($rows as $row) {
                DB::table('seed_beneficiaries')
                    ->where('id', $row->id)
                    ->update([
                        'sd_id' => 'SD-' . str_pad($number++, 6, '0', STR_PAD_LEFT)
                    ]);

                    DB::table('seed_beneficiaries')
                    ->where('crop', 'OFSP')
                    ->update([
                        'crop' => 'Sweet potato'
                    ]);
            }
        });

        // 3. Modify the column to be NOT NULL and UNIQUE
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
            // Ensure you are modifying the correct column (sd_id)
            $table->string('sd_id')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seed_beneficiaries', function (Blueprint $table) {
            $table->dropColumn('sd_id');
        });
    }
};
