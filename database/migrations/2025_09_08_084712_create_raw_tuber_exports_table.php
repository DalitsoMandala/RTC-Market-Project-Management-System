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
        Schema::create('raw_tuber_exports', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->date('reg_date')->nullable();
            $table->year('year')->nullable();
            $table->string('exporter_name')->nullable();
            $table->string('consignee_name')->nullable();
            $table->integer('quantity')->unsigned()->nullable();
            $table->string('package_kind')->nullable();
            $table->string('hs_code', 10)->nullable();
            $table->text('goods_description')->nullable();
            $table->text('commercial_goods_description')->nullable();
            $table->string('origin_country')->nullable();
            $table->string('exit_border')->nullable();
            $table->string('destination_country')->nullable();
            $table->decimal('netweight_kgs', 12, 2)->nullable();
            $table->decimal('export_value_mwk', 15, 2)->nullable();
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
        Schema::dropIfExists('raw_tuber_exports');
    }
};
