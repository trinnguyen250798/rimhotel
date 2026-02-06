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
        Schema::create('staff_groups', function (Blueprint $table) {
            $table->id('staff_group_id');
            $table->foreignId('hotel_id')->constrained('hotels', 'hotel_id')->onDelete('cascade');

            $table->string('name')->comment('Tên nhóm');
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable()->comment('Màu sắc để phân biệt (#FF5733)');

            $table->timestamps();

            // Index
            $table->index('hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_groups');
    }
};
