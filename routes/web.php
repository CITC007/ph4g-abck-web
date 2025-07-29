<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ScoreHistoryController;

// หน้าแรก dashboard แสดงคะแนนสูงสุด
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


// รายงานคะแนนสูงสุด (หน้า 3)
Route::get('/report/top-scores', [ReportController::class, 'topScores'])->name('report.top_scores');
Route::post('/report/top-scores/download', [ReportController::class, 'downloadTopScores'])->name('report.top_scores.download');
Route::get('/report/top-scores/export', [ReportController::class, 'exportTopScores'])->name('report.top_scores_export');


// รายงานคะแนนรายชั้น (หน้า 4)
Route::get('/report/class-scores', [ReportController::class, 'classScores'])->name('report.class_scores');
// Route::post('/report/class-scores/download', [ReportController::class, 'downloadClassScores'])->name('report.class-scores.download');
Route::get('/report/class-scores/export', [ReportController::class, 'exportClassScores'])->name('report.class_scores_export');

// หน้าเพิ่มคะแนนนักเรียน (ต้องล็อกอินครูก่อน)
Route::get('/score-entry', [ScoreController::class, 'showForm'])->name('score-entry');
Route::get('/student-history', [ScoreController::class, 'showForm'])->name('score-entry');
Route::get('/score-entry', [ScoreController::class, 'showForm'])->name('score-entry.form');

// ค้นหานักเรียนในหน้าเพิ่มคะแนน (POST)
Route::match(['get', 'post'], '/score-entry/search', [ScoreController::class, 'search'])->name('score-entry.search');

// บันทึกคะแนน (เพิ่มคะแนนเดี่ยวหรือกลุ่ม)
Route::post('/score-entry/save', [ScoreController::class, 'save'])->name('score-entry.save');

// แสดงประวัตินักเรียน
Route::get('/student-history/{id}', [StudentController::class, 'showHistory'])->name('student.history');

// ระบบล็อกอินครู (จากฟอร์ม popup บนหน้า /score-entry)
Route::get('/teacher-login', [TeacherAuthController::class, 'showLoginForm'])->name('teacher.auth.form');
Route::post('/teacher-login', [TeacherAuthController::class, 'login'])->name('teacher.auth.login');


// ระบบ logout ครู (ล้าง session)
Route::post('/teacher/logout', [TeacherAuthController::class, 'logout'])->name('teacher.auth.logout');

//download Export file
// Route::get('/report/top-scores/export/pdf', [ReportController::class, 'exportTopScores'])->name('report.top_scores_export');

Route::get('/report/class-scores/download', [ReportController::class, 'exportClassScores'])->name('report.class-scores.download');

// --- Certificate Management Routes ---
Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
Route::post('/certificates/save', [CertificateController::class, 'saveAndGenerateCertificates'])->name('certificates.save'); // Adjusted name for consistency
Route::post('/certificates/download/selected', [CertificateController::class, 'downloadSelectedCertificates'])->name('certificates.downloadSelected'); // Adjusted name for consistency

// Route สำหรับแสดงฟอร์มกรอก PIN (ถ้ายังไม่ผ่านการยืนยัน)
Route::get('/certificates/pin', [CertificateController::class, 'showPinForm'])->name('certificates.pin.show');

// Route สำหรับรับค่า PIN ที่ส่งมาจากฟอร์ม (POST)
Route::post('/certificates/pin', [CertificateController::class, 'processPin'])->name('certificates.pin.process');

// Route สำหรับ Logout (จะล้างค่า Session การยืนยัน PIN)
Route::post('/certificates/logout', [CertificateController::class, 'logout'])->name('certificates.logout');

// --- Student History Route (สำหรับ QR Code) ---
// ตรวจสอบให้แน่ใจว่าคุณมี route นี้และ method ใน StudentController
Route::get('/students/{student}/history', [StudentController::class, 'showHistory'])->name('students.history');

Route::get('/certificate/{certificate}/score-history', [ScoreHistoryController::class, 'show'])
    ->name('certificates.score_history')
    ->middleware('signed');

Route::post('/score-entry/select-top-student', [ScoreController::class, 'selectTopStudent'])->name('score-entry.select-top-student');