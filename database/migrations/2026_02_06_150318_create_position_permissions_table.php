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
        Schema::create('position_permissions', function (Blueprint $table) {
            $table->foreignId('position_id')->constrained('positions', 'position_id')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions', 'permission_id')->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();

            // Composite primary key
            $table->primary(['position_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_permissions');
    }
};
