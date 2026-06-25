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
        Schema::create('promotion_association_rule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions', 'promo_id')->cascadeOnDelete();
            $table->foreignId('rule_id')->constrained('association_rules', 'rule_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_association_rule');
    }
};
