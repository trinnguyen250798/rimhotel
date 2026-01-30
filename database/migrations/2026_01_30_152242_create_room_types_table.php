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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id('room_type_id');
            $table->foreignId('hotel_id')->constrained('hotels', 'hotel_id')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('bed_type')->nullable();
            $table->integer('area')->nullable();
            $table->integer('max_adult')->default(0);
            $table->integer('max_child')->default(0);
            $table->text('description')->nullable();
            $table->smallInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
