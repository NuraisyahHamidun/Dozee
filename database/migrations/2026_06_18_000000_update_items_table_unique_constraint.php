<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('items', function (Blueprint $table) {
                $table->dropUnique('items_item_name_unique');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('items', function (Blueprint $table) {
                $table->dropUnique(['item_name']);
            });
        } catch (\Exception $e) {}

        Schema::table('items', function (Blueprint $table) {
            $table->unique(['item_name', 'volume']);
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            try {
                $table->dropUnique(['item_name', 'volume']);
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }
};
