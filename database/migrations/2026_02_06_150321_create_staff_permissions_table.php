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
        Schema::create('staff_permissions', function (Blueprint $table) {
            $table->foreignId('staff_id')->constrained('staff', 'staff_id')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions', 'permission_id')->onDelete('cascade');

            $table->boolean('granted')->default(true)->comment('TRUE: Thêm quyền, FALSE: Thu hồi quyền');

            $table->timestamp('created_at')->useCurrent();

            // Composite primary key
            $table->primary(['staff_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_permissions');
    }
};
