<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function showForm()
    {
        $classRoom = session('teacher_class_room');
        $students = [];

        if ($classRoom) {
            $students = Student::where('class_room', $classRoom)
                ->withSum('scores', 'point')
                ->get();
        }

        return view('score-entry', [
            'students' => $students,
            'classRoom' => $classRoom,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'class_room' => 'nullable|string',
            'keyword' => 'nullable|string',
        ]);

        $query = Student::query();

        if ($request->filled('class_room')) {
            $query->where('class_room', $request->class_room);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('student_name', 'like', "%{$keyword}%")
                    ->orWhere('student_code', 'like', "%{$keyword}%");
            });
        }

        $students = $query->withSum('scores', 'point')->get();

        return view('score-entry', [
            'students' => $students,
            'classRoom' => session('teacher_class_room'),
        ]);
    }

    public function save(Request $request)
    {
        // dd($request->all());

        $teacherId = session('teacher_id');
        $reason = $request->input('reason');
        $now = now();

        if (!$teacherId || !$reason) {
            return back()->withErrors('กรุณาล็อกอินและกรอกเหตุผลก่อนบันทึกคะแนน');
        }

        // เพิ่มคะแนนเดี่ยว
        if ($request->filled('student_id')) {
            Score::create([
                'student_id' => $request->student_id,
                'teacher_id' => $teacherId,
                'reason' => $reason,
                'point' => 1,
                'month' => $now->format('n'),
                'year' => $now->format('Y'),
            ]);
        }

        // เพิ่มคะแนนหลายคน
        if (is_array($request->selected_students)) {
            foreach ($request->selected_students as $studentId) {
                Score::create([
                    'student_id' => $studentId,
                    'teacher_id' => $teacherId,
                    'reason' => $reason,
                    'point' => 1,
                    'month' => $now->format('n'),
                    'year' => $now->format('Y'),
                ]);
            }
        }

        return redirect()->route('score-entry.form')->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
    }
}
