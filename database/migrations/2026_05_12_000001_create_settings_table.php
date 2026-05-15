<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('settings')->insert([
            [
                'key' => 'button_labels',
                'value' => json_encode(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'correct_buttons',
                'value' => json_encode([1, 2, 3]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
