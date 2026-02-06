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
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id');
            $table->foreignId('hotel_id')->constrained('hotels', 'hotel_id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Link tới users nếu có tài khoản');
            $table->foreignId('department_id')->nullable()->constrained('departments', 'department_id')->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained('positions', 'position_id')->onDelete('set null');
            $table->foreignId('staff_group_id')->nullable()->constrained('staff_groups', 'staff_group_id')->onDelete('set null');

            // Thông tin cá nhân
            $table->string('full_name')->comment('Họ và tên');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();

            // Thông tin công việc
            $table->string('employee_code', 50)->unique()->nullable()->comment('Mã nhân viên');
            $table->date('hire_date')->nullable()->comment('Ngày vào làm');
            $table->enum('contract_type', ['full_time', 'part_time', 'contract', 'intern'])->nullable();
            $table->decimal('salary', 15, 2)->nullable();

            // Thông tin liên hệ khẩn cấp
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 100)->nullable();

            // Trạng thái
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive, 2: On Leave');

            // Metadata
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('hotel_id');
            $table->index('department_id');
            $table->index('position_id');
            $table->index('staff_group_id');
            $table->index('employee_code');
            $table->index('status');
        });

        // Add foreign key for manager_staff_id in departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_staff_id')->references('staff_id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['manager_staff_id']);
        });

        Schema::dropIfExists('staff');
    }
};
