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

        // $currentMonth = Carbon::now()->month;
        // $currentYear = Carbon::now()->year;
        // $currentMonthYear = Carbon::now()->translatedFormat('F Y');

        // $previousMonthCarbon = Carbon::now()->subMonth();
        // $displayMonthForTopStudents = $previousMonthCarbon->month;
        // $displayYearForTopStudents = $previousMonthCarbon->year;
        // $previousMonthYearForDisplay = $previousMonthCarbon->translatedFormat('F Y');
        // dd($previousMonthYearForDisplay);
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
                $studentsForTopCalculation = Student::where('class_room', $classRoom)
                    ->withSum([
                        'scores' => function ($query) use ($displayMonthForTopStudents, $displayYearForTopStudents) {
                            $query->whereMonth('created_at', $displayMonthForTopStudents)
                                ->whereYear('created_at', $displayYearForTopStudents);
                        }
                    ], 'point') // รวมเฉพาะคะแนน (point) ของเดือนที่แล้ว
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

        return view('score-entry', [
            'students' => $students,
            'classRoom' => $classRoom,
            'teachers' => $teachers,
            'topStudentsInClass' => $topStudentsInClass,
            'topStudentAwarded' => $topStudentAwarded,
            'currentMonthYear' => $currentMonthYear,
            'previousMonthYearForDisplay' => $previousMonthYearForDisplay,
        ]);
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

        if (!$teacherClassRoom) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลห้องเรียนของครูผู้ใช้งาน');
        }

        $query = Student::query();

        $selectedClassRoom = $request->input('class_room', $teacherClassRoom);
        $query->where('class_room', $selectedClassRoom);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('student_name', 'like', "%{$keyword}%")
                    ->orWhere('student_code', 'like', "%{$keyword}%");
            });
        }

        // *** แก้ไข: ใช้ withSum เพื่อรวมคะแนนของเดือนปัจจุบันโดยตรงในการค้นหา ***
        $students = $query->withSum([
            'scores' => function ($q) use ($currentMonth, $currentYear) {
                $q->where('month', $currentMonth) // <-- เปลี่ยนเป็น field 'month'
                    ->where('year', $currentYear);   // <-- เปลี่ยนเป็น field 'year'
            }
        ], 'point')
            ->orderBy('student_number', 'asc')
            ->get();

        // ไม่ต้องใช้ transform อีกแล้ว เพราะ withSum จะสร้าง scores_sum_point มาให้เลย

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

        if ($selectedClassRoom === $teacherClassRoom) {
            $topStudentAwarded = Certificate::where('class_room', $teacherClassRoom)
                ->where('month', $displayMonthForTopStudents)
                ->where('year', $displayYearForTopStudents)
                ->exists();

            if (!$topStudentAwarded) {
                $studentsForTopCalculation = Student::where('class_room', $teacherClassRoom)
                    ->withSum([
                        'scores' => function ($query) use ($displayMonthForTopStudents, $displayYearForTopStudents) {
                            $query->whereMonth('created_at', $displayMonthForTopStudents)
                                ->whereYear('created_at', $displayYearForTopStudents);
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

        return view('score-entry', [
            'students' => $students,
            'classRoom' => $selectedClassRoom,
            'teachers' => $teachers,
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
            $student = Student::where('id', $request->student_id)->where('class_room', session('teacher_class_room'))->first();
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