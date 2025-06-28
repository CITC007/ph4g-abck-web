<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;

class StudentController extends Controller
{
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
