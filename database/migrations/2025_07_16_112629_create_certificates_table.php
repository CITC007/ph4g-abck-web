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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable(); // ให้เป็น nullable เผื่อกรณีข้อมูลนักเรียนถูกลบไปแล้ว แต่ควรป้องกันไม่ให้เกิด
            $table->string('student_name'); // เก็บชื่อนักเรียนโดยตรง เพื่อความเสถียรของข้อมูลใบประกาศ
            $table->string('class_room'); // เก็บห้องเรียนโดยตรง
            $table->integer('total_score'); // คะแนนรวมของเดือนนั้นที่ได้รับ
            $table->integer('month'); // เดือนของคะแนนที่ออกใบประกาศ (1-12)
            $table->integer('year'); // ปีของคะแนนที่ออกใบประกาศ (ค.ศ. เช่น 2025)
            $table->string('certificate_number')->unique(); // เลขที่ใบประกาศ (เช่น 001/2568)
            $table->timestamp('issued_at'); // วันที่ออกใบประกาศ (เวลาจริงที่กดปุ่ม)
            $table->timestamps();

            // เพิ่ม foreign key constraint ถ้าต้องการ แต่ควรใช้ soft delete ใน student table ด้วยถ้าทำ
            // $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};