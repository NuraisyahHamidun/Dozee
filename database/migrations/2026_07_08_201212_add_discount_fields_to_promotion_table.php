<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('final_discount');
            $table->decimal('discount_value', 8, 2)->nullable()->after('discount_type');
            $table->string('discount_apply_to')->default('all_selected_bundles')->after('discount_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotion', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_apply_to']);
        });
    }
};
