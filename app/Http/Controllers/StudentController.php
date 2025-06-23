<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;

class StudentController extends Controller
{
    public function showHistory($id)
    {
        // ดึงข้อมูลนักเรียน
        $student = Student::findOrFail($id);

        // ดึงคะแนนทั้งหมดของนักเรียนคนนี้ (เรียงจากล่าสุด)
        $scores = Score::where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // คำนวณคะแนนรวมทั้งหมด
        $totalPoints = $scores->sum('point');

        // คำนวณคะแนนรวมของเดือนนี้
        $currentMonth = date('n');
        $currentYear = date('Y');
        $monthPoints = $scores->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('point');

        return view('student-history', compact('student', 'scores', 'totalPoints', 'monthPoints', 'currentMonth', 'currentYear'));
    }
}
