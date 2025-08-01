<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;

class StudentController extends Controller
{
    public function __construct()
    {
        // ตรวจสอบว่า session 'teacher_id' มีค่าอยู่หรือไม่
        // If the 'teacher_id' session is not set, redirect to the login page.
        if (!Session::has('teacher_id')) {
            // หากไม่มี session teacher_id ให้ redirect ไปหน้า Login
            // This is a placeholder route name. You should use your actual login route name.
            redirect()->route('score-entry')->send();
        }
    }
    public function showHistory(Request $request, $id)
    {
        // ดึงข้อมูลนักเรียน
        $student = Student::findOrFail($id);

        // ดึงคะแนนทั้งหมดของนักเรียนคนนี้ (เรียงจากล่าสุด), paginate 30 รายการต่อหน้า
        $scores = Score::where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // คำนวณคะแนนรวมทั้งหมด (ยังใช้ get() แยกอีกครั้ง)
        $totalPoints = Score::where('student_id', $id)->sum('point');

        // คำนวณคะแนนรวมของเดือนนี้
        $currentMonth = date('n');
        $currentYear = date('Y');
        $monthPoints = Score::where('student_id', $id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('point');

        return view('student-history', compact('student', 'scores', 'totalPoints', 'monthPoints', 'currentMonth', 'currentYear'));
    }
}
