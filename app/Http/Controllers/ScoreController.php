<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Score;
use App\Models\Teacher;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale('th');
    }

    /**
     * แสดงฟอร์มการเพิ่มคะแนนและส่วนสำหรับเลือกนักเรียนดีเด่น
     */
    public function showForm()
    {
        $classRoom = session('teacher_class_room');
        $teacherId = session('teacher_id');
        $students = collect();

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
        $teachers = Teacher::all()->sortBy(function ($t) use ($orderedRooms) {
            $index = array_search($t->class_room, $orderedRooms);
            return $index !== false ? $index : count($orderedRooms);
        });

        $topStudentsInClass = collect();
        $topStudentAwarded = false;


        // ** ส่วนที่ถูกแก้ไข: สำหรับ เดือนปัจจุบัน **
        $currentCarbon = Carbon::now();
        $currentMonth = $currentCarbon->month; // ยังใช้สำหรับ Query
        $currentYear = $currentCarbon->year;   // ยังใช้สำหรับ Query
        $currentMonthYear = $currentCarbon->translatedFormat('F') . ' ' . ($currentCarbon->year + 543);
        // ผลลัพธ์: "กรกฎาคม 2568"

        // ** ส่วนที่ถูกแก้ไข: สำหรับ เดือนที่ผ่านมา **
        $previousMonthCarbon = Carbon::now()->subMonth();
        $displayMonthForTopStudents = $previousMonthCarbon->month; // ยังใช้สำหรับ Query
        $displayYearForTopStudents = $previousMonthCarbon->year;   // ยังใช้สำหรับ Query
        $previousMonthYearForDisplay = $previousMonthCarbon->translatedFormat('F') . ' ' . ($previousMonthCarbon->year + 543);
        // ผลลัพธ์: "มิถุนายน 2568"
        if ($classRoom && $teacherId) {
            // ดึงนักเรียนในห้องของครูผู้ใช้งาน
            // ใช้ withSum เพื่อรวมคะแนนของเดือนปัจจุบันโดยตรง
            $students = Student::where('class_room', $classRoom)
                ->withSum([
                    'scores' => function ($query) use ($currentMonth, $currentYear) {
                        $query->where('month', $currentMonth) // <-- เปลี่ยนเป็น field 'month'
                            ->where('year', $currentYear);   // <-- เปลี่ยนเป็น field 'year'
                    }
                ], 'point')
                ->orderBy('student_number', 'asc')
                ->get();
            // ไม่ต้องใช้ map อีกแล้ว เพราะ withSum จะสร้าง scores_sum_point มาให้เลย

            $topStudentAwarded = Certificate::where('class_room', $classRoom)
                ->where('month', $displayMonthForTopStudents)
                ->where('year', $displayYearForTopStudents)
                ->exists();

            if (!$topStudentAwarded) {
                // ** ส่วนที่ถูกปรับปรุง: ใช้ field 'month' และ 'year' เพื่อค้นหาคะแนนสูงสุด **
                // 1. ค้นหาคะแนนสูงสุดของเดือนที่แล้วในคำสั่งเดียว
                $maxScoreResult = Student::where('class_room', $classRoom)
                    ->withSum([
                        'scores' => function ($query) use ($displayMonthForTopStudents, $displayYearForTopStudents) {
                            $query->where('month', $displayMonthForTopStudents)
                                ->where('year', $displayYearForTopStudents);
                        }
                    ], 'point')
                    ->orderByDesc('scores_sum_point')
                    ->first();

                $maxScore = $maxScoreResult ? $maxScoreResult->scores_sum_point : 0;

                // 2. ถ้ามีคะแนนสูงสุด (และ > 0) ให้ดึงนักเรียนทั้งหมดที่ได้คะแนนเท่ากับคะแนนสูงสุดนั้น
                if ($maxScore > 0) {
                    $topStudentsInClass = Student::where('class_room', $classRoom)
                        ->withSum([
                            'scores' => function ($query) use ($displayMonthForTopStudents, $displayYearForTopStudents) {
                                // ** ใช้ field 'month' และ 'year' เหมือนกับการค้นหาคะแนนสูงสุดด้านบน **
                                $query->where('month', $displayMonthForTopStudents)
                                    ->where('year', $displayYearForTopStudents);
                            }
                        ], 'point')
                        ->having('scores_sum_point', '=', $maxScore)
                        ->orderBy('student_number', 'asc')
                        ->get();
                } else {
                    $topStudentsInClass = collect();
                }

                // 3. ตรวจสอบว่ามีนักเรียนที่คะแนนสูงสุดซ้ำกันมากกว่า 1 คนหรือไม่
                if ($topStudentsInClass->count() <= 1) {
                    $topStudentsInClass = collect();
                } else {
                    foreach ($topStudentsInClass as $student) {
                        $certificateCount = Certificate::where('student_name', $student->student_name)
                            ->where('class_room', $student->class_room)
                            ->count();
                        $student->certificate_count = $certificateCount;
                    }
                }
            }
        }

        return response()
            ->view('score-entry', [
                'students' => $students,
                'classRoom' => $classRoom,
                'teachers' => $teachers,
                'topStudentsInClass' => $topStudentsInClass,
                'topStudentAwarded' => $topStudentAwarded,
                'currentMonthYear' => $currentMonthYear,
                'previousMonthYearForDisplay' => $previousMonthYearForDisplay,
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

    }



    /**
     * เมธอดสำหรับการค้นหานักเรียน (ครูสามารถค้นหาห้องอื่นได้)
     */
    public function search(Request $request)
    {
        $request->validate([
            'class_room' => 'nullable|string',
            'keyword' => 'nullable|string',
        ]);

        $teacherClassRoom = session('teacher_class_room');

        $topStudentsInClass = collect();
        $topStudentAwarded = false;

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentMonthYear = Carbon::now()->translatedFormat('F Y');

        $previousMonthCarbon = Carbon::now()->subMonth();
        $displayMonthForTopStudents = $previousMonthCarbon->month;
        $displayYearForTopStudents = $previousMonthCarbon->year;
        $previousMonthYearForDisplay = $previousMonthCarbon->translatedFormat('F Y');

        // Check if the logged-in teacher has a classroom assigned.
        if (!$teacherClassRoom) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลห้องเรียนของครูผู้ใช้งาน');
        }

        $query = Student::query();

        $selectedClassRoom = $request->input('class_room');
        $keyword = $request->input('keyword');

        // --- Search Logic ---
        // If a keyword is provided, search all students by name or student code.
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('student_name', 'like', "%{$keyword}%")
                    ->orWhere('student_code', 'like', "%{$keyword}%");
            });
            // If a class room is also selected, filter the search results by that class.
            if ($request->filled('class_room')) {
                $query->where('class_room', $selectedClassRoom);
            }
        }
        // If no keyword is provided, but a class room is selected, display students from that specific class.
        else if ($request->filled('class_room')) {
            $query->where('class_room', $selectedClassRoom);
        }
        // If neither a keyword nor a class room is specified, default to showing the teacher's own classroom.
        else {
            $query->where('class_room', $teacherClassRoom);
            $selectedClassRoom = $teacherClassRoom;
        }

        // Retrieve students and their scores for the current month.
        $students = $query->withSum([
            'scores' => function ($q) use ($currentMonth, $currentYear) {
                $q->where('month', $currentMonth)
                    ->where('year', $currentYear);
            }
        ], 'point')
            ->orderBy('student_number', 'asc')
            ->get();

        // ** Start of modified section **
        // The top students section will only be calculated and displayed if the teacher
        // is viewing their own assigned classroom.
        if ($selectedClassRoom === $teacherClassRoom) {
            $classRoomForTopStudents = $selectedClassRoom;

            $topStudentAwarded = Certificate::where('class_room', $classRoomForTopStudents)
                ->where('month', $displayMonthForTopStudents)
                ->where('year', $displayYearForTopStudents)
                ->exists();

            if (!$topStudentAwarded) {
                $studentsForTopCalculation = Student::where('class_room', $classRoomForTopStudents)
                    ->withSum([
                        'scores' => function ($query) use ($displayMonthForTopStudents, $displayYearForTopStudents) {
                            $query->where('month', $displayMonthForTopStudents)
                                ->where('year', $displayYearForTopStudents);
                        }
                    ], 'point')
                    ->get()
                    ->sortByDesc('scores_sum_point');

                if ($studentsForTopCalculation->isNotEmpty()) {
                    $maxScore = $studentsForTopCalculation->first()->scores_sum_point;
                    $topStudentsInClass = $studentsForTopCalculation->filter(function ($student) use ($maxScore) {
                        return $student->scores_sum_point === $maxScore && $maxScore > 0;
                    });
                    if ($topStudentsInClass->count() <= 1) {
                        $topStudentsInClass = collect();
                    } else {
                        $topStudentsInClass = $topStudentsInClass->sortBy('student_number');
                    }
                } else {
                    $topStudentsInClass = collect();
                }
            }
        }
        // ** End of modified section **

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
        $teachers = Teacher::all()->sortBy(function ($t) use ($orderedRooms) {
            $index = array_search($t->class_room, $orderedRooms);
            return $index !== false ? $index : count($orderedRooms);
        });

        return view('score-entry', [
            'students' => $students,
            'classRoom' => $selectedClassRoom,
            'teachers' => $teachers,
            'allClassRooms' => $orderedRooms,
            'topStudentsInClass' => $topStudentsInClass,
            'topStudentAwarded' => $topStudentAwarded,
            'currentMonthYear' => $currentMonthYear,
            'previousMonthYearForDisplay' => $previousMonthYearForDisplay,
        ]);
    }

    /**
     * เมธอดสำหรับบันทึกคะแนนนักเรียนทั่วไป
     */
    public function save(Request $request)
    {
        $teacherId = session('teacher_id');
        $reason = $request->input('reason');
        $now = now();

        if (!$teacherId || !$reason) {
            return back()->withErrors('กรุณาล็อกอินและกรอกเหตุผลก่อนบันทึกคะแนน');
        }

        if ($request->filled('student_id')) {
            // $student = Student::where('id', $request->student_id)->where('class_room', session('teacher_class_room'))->first();
            $student = Student::find($request->student_id);
            if (!$student) {
                return redirect()->back()->with('error', 'ไม่พบนักเรียนที่คุณต้องการเพิ่มคะแนนในห้องของคุณ');
            }

            Score::create([
                'student_id' => $request->student_id,
                'teacher_id' => $teacherId,
                'reason' => $reason,
                'point' => 1,
                'month' => $now->format('n'),
                'year' => $now->format('Y'),
            ]);
        }

        if (is_array($request->selected_students)) {
            $selectedStudentsIds = $request->input('selected_students');

            if (empty($selectedStudentsIds)) {
                return redirect()->back()->with('error', 'กรุณาเลือกนักเรียนอย่างน้อย 1 คนก่อนเพิ่มคะแนน');
            }

            $studentsToUpdate = Student::whereIn('id', $selectedStudentsIds)
                ->where('class_room', session('teacher_class_room'))
                ->get();

            if ($studentsToUpdate->isEmpty()) {
                return redirect()->back()->with('error', 'ไม่พบนักเรียนที่คุณเลือกในห้องของคุณ');
            }

            foreach ($studentsToUpdate as $student) {
                Score::create([
                    'student_id' => $student->id,
                    'teacher_id' => $teacherId,
                    'reason' => $reason,
                    'point' => 1,
                    'month' => $now->format('n'),
                    'year' => $now->format('Y'),
                ]);
            }
        }

        if (str_contains(url()->previous(), '/student-history/')) {
            return redirect(url()->previous())->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
        } else {
            $currentKeyword = $request->input('keyword');
            $currentClassRoom = $request->input('class_room');

            if (!empty($currentKeyword) || !empty($currentClassRoom)) {
                return redirect()->route('score-entry.search', [
                    'keyword' => $currentKeyword,
                    'class_room' => $currentClassRoom
                ])->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
            } else {
                return redirect()->route('score-entry.form')->with('success', 'บันทึกคะแนนเรียบร้อยแล้ว');
            }
        }
    }



    /**
     * เมธอดสำหรับเพิ่มคะแนนให้นักเรียนที่ถูกเลือกเป็นนักเรียนดีเด่น
     */
    public function selectTopStudent(Request $request)
    {
        $request->validate([
            'selected_top_student_id' => 'required|exists:students,id',
            'class_room' => 'required|string',
        ]);

        $selectedStudentId = $request->input('selected_top_student_id');
        $classRoom = $request->input('class_room');
        $teacherId = session('teacher_id');

        $previousMonthCarbon = Carbon::now()->subMonth();
        $monthToAward = $previousMonthCarbon->month;
        $yearToAward = $previousMonthCarbon->year;

        $selectedStudent = Student::find($selectedStudentId);

        if (!$selectedStudent) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลนักเรียนที่เลือก');
        }

        if ($selectedStudent->class_room !== session('teacher_class_room')) {
            return redirect()->back()->with('error', 'นักเรียนที่เลือกไม่ได้อยู่ในห้องเรียนของคุณ');
        }

        $topStudentAwarded = Certificate::where('class_room', $classRoom)
            ->where('month', $monthToAward)
            ->where('year', $yearToAward)
            ->exists();

        if ($topStudentAwarded) {
            return redirect()->back()->with('error', 'ห้องเรียนนี้ได้มีการมอบรางวัลนักเรียนดีเด่นสำหรับเดือนที่แล้วไปแล้ว');
        }

        Score::create([
            'student_id' => $selectedStudentId,
            'teacher_id' => $teacherId,
            'reason' => 'ประพฤติดีเสมอต้นเสมอปลาย',
            'point' => 1,
            'month' => $monthToAward,
            'year' => $yearToAward,
        ]);

        return redirect()->back()->with('success', 'บันทึกการเลือกนักเรียนดีเด่นสำเร็จแล้ว และเพิ่มคะแนน 1 คะแนนให้นักเรียนแล้ว');
    }
}