<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

// หน้าแรก dashboard แสดงคะแนนสูงสุด
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// รายงานคะแนนสูงสุด (หน้า 3)
Route::get('/report/top-scores', [ReportController::class, 'topScores'])->name('report.top_scores');
Route::post('/report/top-scores/download', [ReportController::class, 'downloadTopScores'])->name('report.top_scores.download');
Route::get('/report/top-scores/export', [ReportController::class, 'exportTopScores'])->name('report.top_scores_export');


// รายงานคะแนนรายชั้น (หน้า 4)
Route::get('/report/class-scores', [ReportController::class, 'classScores'])->name('report.class_scores');
Route::post('/report/class-scores/download', [ReportController::class, 'downloadClassScores'])->name('report.class_scores.download');

// หน้าเพิ่มคะแนนนักเรียน (ต้องล็อกอินครูก่อน)
Route::get('/score-entry', [ScoreController::class, 'showForm'])->name('score-entry');

// ค้นหานักเรียนในหน้าเพิ่มคะแนน (POST)
Route::post('/score-entry/search', [ScoreController::class, 'search'])->name('score-entry.search');

// บันทึกคะแนน (เพิ่มคะแนนเดี่ยวหรือกลุ่ม)
Route::post('/score-entry/save', [ScoreController::class, 'save'])->name('score-entry.save');

// แสดงประวัตินักเรียน
Route::get('/student-history/{id}', [StudentController::class, 'showHistory'])->name('student.history');

// ระบบล็อกอินครู (จากฟอร์ม popup บนหน้า /score-entry)
Route::post('/teacher/login', [TeacherAuthController::class, 'login'])->name('teacher.auth.login');

// ระบบ logout ครู (ล้าง session)
Route::post('/teacher/logout', [TeacherAuthController::class, 'logout'])->name('teacher.auth.logout');
