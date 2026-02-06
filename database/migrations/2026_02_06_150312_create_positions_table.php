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
        Schema::create('positions', function (Blueprint $table) {
            $table->id('position_id');
            $table->foreignId('hotel_id')->nullable()->constrained('hotels', 'hotel_id')->onDelete('cascade')->comment('NULL = áp dụng cho tất cả hotels');

            $table->string('code', 50)->nullable()->comment('Mã chức vụ');
            $table->string('name')->comment('Tên chức vụ');
            $table->text('description')->nullable();
            $table->tinyInteger('level')->nullable()->comment('Cấp bậc (1: Cao nhất, 10: Thấp nhất)');

            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();

            // Index
            $table->index('hotel_id');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
