<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->foreignId('room_type_id')->constrained('room_types', 'room_type_id')->onDelete('cascade');
            $table->string('room_no');
            $table->integer('floor')->nullable();
            $table->string('view')->nullable();
            $table->smallInteger('status')->default(0)->comment('0: Trống, 1: Bận, 2: Sửa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
