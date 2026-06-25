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
        Schema::table('items', function (Blueprint $table) {
            $table->string('item_code')->nullable()->unique()->after('item_id');
        });

        // Populate existing items
        $items = Illuminate\Support\Facades\DB::table('items')->orderBy('item_id')->get();
        foreach ($items as $index => $item) {
            Illuminate\Support\Facades\DB::table('items')->where('item_id', $item->item_id)->update([
                'item_code' => sprintf('ITM-%04d', $index + 1)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('item_code');
        });
    }
};
