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
        Schema::create('shop_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();

            // 1. Business Identity
            $table->string('cac_certificate')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('cac_status_report')->nullable();
            $table->string('owner_id_card')->nullable();

            // 2. Product Safety
            $table->string('nafdac_number')->nullable();
            $table->string('son_mancap_cert')->nullable();
            $table->string('lab_test_report')->nullable();
            $table->string('trademark_cert')->nullable();

            // 3. Operational
            $table->string('logistics_sla')->nullable();
            $table->text('production_address')->nullable();
            $table->string('production_capacity')->nullable();

            // Verification Statuses (JSON to store status for each item or individual columns)
            $table->json('verification_status')->nullable(); // e.g., {'cac': 'approved', 'nafdac': 'pending'}

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_verifications');
    }
};
