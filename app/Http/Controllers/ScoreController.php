<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function showForm()
    {
        $classRoom = session('teacher_class_room');
        $students = [];
        $teachers = Teacher::orderBy('teacher_name')->get();

        $orderedRooms = [
            'ป.1/1',
            'ป.1/2',
            'ป.1/3',
            'ป.1/4',
            'ป.2/1',
            'ป.2/2',
            'ป.2/3',
            'ป.2/4',
            'ป.3/1',
            'ป.3/2',
            'ป.3/3',
            'ป.3/4',
            'ป.4/1',
            'ป.4/2',
            'ป.4/3',
            'ป.4/4',
            'ป.5/1',
            'ป.5/2',
            'ป.5/3',
            'ป.5/4',
            'ป.6/1',
            'ป.6/2',
            'ป.6/3',
            'ป.6/4',
        ];

        // ดึงครูทั้งหมดและจัดเรียงตาม class_room
        $teachers = Teacher::all()->sortBy(function ($t) use ($orderedRooms) {
            $index = array_search($t->class_room, $orderedRooms);
            return $index !== false ? $index : count($orderedRooms); // ถ้าไม่พบ ให้ไปไว้ท้าย
        });

        if ($classRoom) {
            $students = Student::where('class_room', $classRoom)
                ->withSum('scores', 'point')
                ->get();
        }

        return view('score-entry', [
            'students' => $students,
            'classRoom' => $classRoom,
            'teachers' => $teachers,
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

        // ตรวจสอบว่ามาจากหน้า student-history หรือไม่
        if (str_contains(url()->previous(), '/student-history/')) {
            return redirect(url()->previous())->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
        } else {
            // ดึงค่า keyword และ class_room จาก request ที่ส่งมาจากหน้าฟอร์มค้นหา
            $currentKeyword = $request->input('keyword');
            $currentClassRoom = $request->input('class_room');

            // ถ้ามีการค้นหา (มี keyword หรือ class_room) ให้ redirect กลับไปที่ route search พร้อม parameter เดิม
            if (!empty($currentKeyword) || !empty($currentClassRoom)) {
                return redirect()->route('score-entry.search', [
                    'keyword' => $currentKeyword,
                    'class_room' => $currentClassRoom
                ])->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
            }
            // หากไม่มีการค้นหา (หรือมาจากการเข้าหน้า score-entry.form โดยตรง)
            else {
                return redirect()->route('score-entry.form')->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
            }
        }
    }
}
