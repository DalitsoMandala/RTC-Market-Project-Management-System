<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('raw_tuber_imports', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('entry_border')->nullable();
            $table->date('reg_date')->nullable();
            $table->string('tpin', 20)->nullable();
            $table->string('importer_name')->nullable();
            $table->year('year')->nullable();
            $table->string('hs_code', 10)->nullable();
            $table->text('tariff_description')->nullable();
            $table->text('commercial_description')->nullable();
            $table->string('package_kind')->nullable();
            $table->integer('packages')->unsigned()->nullable();
            $table->string('origin')->nullable();
            $table->string('exporter')->nullable();
            $table->decimal('netweight_kgs', 12, 2)->nullable();
            $table->decimal('foreign_currency', 15, 2)->nullable();
            $table->string('currency', 5)->nullable();
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->decimal('value_for_duty_mwk', 15, 2)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->onDelete('cascade')->onUpdate('cascade');
            $table->string('description')->nullable();
             $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_tuber_imports');
    }
};
