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
        Schema::table('shop_verifications', function (Blueprint $table) {
            $table->string('product_images_sample')->nullable()->after('production_capacity');
            $table->text('product_descriptions_standard')->nullable()->after('product_images_sample');
            $table->string('pricing_list')->nullable()->after('product_descriptions_standard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_verifications', function (Blueprint $table) {
            $table->dropColumn(['product_images_sample', 'product_descriptions_standard', 'pricing_list']);
        });
    }
};
