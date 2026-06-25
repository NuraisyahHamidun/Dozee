<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id('promo_id');
            $table->foreignId('manager_id')->constrained('managers', 'manager_id')->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('association_rules', 'rule_id')->nullOnDelete();
            $table->string('promo_name');
            $table->enum('status', ['Active', 'Expired'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
