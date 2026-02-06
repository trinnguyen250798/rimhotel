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
        Schema::create('departments', function (Blueprint $table) {
            $table->id('department_id');
            $table->foreignId('hotel_id')->constrained('hotels', 'hotel_id')->onDelete('cascade');

            $table->string('code', 50)->nullable()->comment('Mã phòng ban (FO, HK, FB...)');
            $table->string('name')->comment('Tên phòng ban');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('manager_staff_id')->nullable()->comment('Trưởng phòng');

            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();

            // Index
            $table->index('hotel_id');
            $table->index('manager_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
