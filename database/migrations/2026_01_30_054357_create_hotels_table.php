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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id('hotel_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('hotel_name');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('website')->nullable();
            $table->unsignedTinyInteger('star_rating')->default(0);

            // Geographical info
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('google_map_url')->nullable();
            $table->decimal('distance_to_center', 8, 2)->nullable();

            // Legal & Contact
            $table->string('company_name')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('license_no')->nullable();
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();

            // Content
            $table->text('description')->nullable();
            $table->json('amenities')->nullable();
            $table->text('policies')->nullable();
            $table->json('languages')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
