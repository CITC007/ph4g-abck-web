<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TopScoresExport;
use App\Exports\ClassScoresExport;
use PDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    // หน้าแสดงคะแนนสูงสุดแต่ละเดือน (หน้า 3)
    public function topScores(Request $request)
    {
        $month = $request->input('month', now()->format('n'));
        $year = $request->input('year', now()->format('Y'));

        // ดึงคะแนนรวมของนักเรียนแต่ละห้องในเดือนและปีที่เลือก
        $scores = DB::table('scores')
            ->select('students.class_room', 'students.student_name', DB::raw('SUM(scores.point) as total_points'))
            ->join('students', 'scores.student_id', '=', 'students.id')
            ->where('scores.month', $month)
            ->where('scores.year', $year)
            ->groupBy('students.class_room', 'students.student_name')
            ->orderBy('students.class_room')
            ->orderByDesc('total_points')
            ->get();

        // ดึงรายชื่อห้องทั้งหมดจากตาราง students เพื่อให้แสดงทุกห้อง
        $allClassRooms = DB::table('students')
            ->select('class_room')
            ->distinct()
            ->orderBy('class_room')
            ->pluck('class_room');

        // จัดกลุ่มคะแนนตามห้อง
        $grouped = $scores->groupBy('class_room');

        $topScores = [];

        foreach ($allClassRooms as $classRoom) {
            if (isset($grouped[$classRoom])) {
                // ห้องนี้มีคะแนนนักเรียน
                $studentsInRoom = $grouped[$classRoom];

                // หาคะแนนสูงสุดของห้องนี้
                $maxPoints = $studentsInRoom->max('total_points');

                // นักเรียนที่ได้คะแนนสูงสุด (อาจหลายคน)
                $topStudents = $studentsInRoom->where('total_points', $maxPoints);

                // เก็บข้อมูลสำหรับ view
                $topScores[$classRoom] = [
                    'students' => $topStudents,
                    'total_points' => $maxPoints,
                ];
            } else {
                // ห้องนี้ไม่มีคะแนนเลย
                $topScores[$classRoom] = null;
            }
        }

        return view('report.top_scores', [
            'topScores' => $topScores,
            'month' => $month,
            'year' => $year,
        ]);
    }



    // รายงานคะแนนรายชั้น (หน้า 4)
    public function classScores(Request $request)
    {
        $classRoom = $request->input('class_room');
        $month = $request->input('month', now()->format('n'));
        $year = $request->input('year', now()->format('Y'));

        $students = [];

        if ($classRoom) {
            $students = Student::where('class_room', $classRoom)
                ->withSum([
                    'scores' => function ($q) use ($month, $year) {
                        $q->where('month', $month)->where('year', $year);
                    }
                ], 'point')
                ->get()
                ->map(function ($student) {
                    $student->total_points = $student->scores_sum_point ?? 0;
                    return $student;
                });
        }

        return view('report.class_scores', [
            'classScores' => $students,
            'classRoom' => $classRoom,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function exportClassScores(Request $request)
    {
        $class_room = $request->get('class_room');
        $month = $request->get('month');
        $year = $request->get('year');
        $format = $request->get('format', 'xlsx');

        // ดึงข้อมูลนักเรียนและคะแนนรวมตามเงื่อนไข แม้คะแนนจะเป็น 0
        $classScores = DB::table('students')
            ->select(
                'students.student_number',
                'students.student_code',
                'students.student_name',
                'students.class_room',
                DB::raw('COALESCE(SUM(scores.point), 0) as scores_sum_point')
            )
            ->leftJoin('scores', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'scores.student_id')
                    ->whereMonth('scores.created_at', $month)
                    ->whereYear('scores.created_at', $year);
            })
            ->where('students.class_room', $class_room)
            ->groupBy('students.id', 'students.student_number', 'students.student_code', 'students.student_name', 'students.class_room')
            ->orderBy('students.student_number', 'asc')
            ->get();

        $safeClassRoom = str_replace(['/', '\\'], '_', $class_room);
        $filename = 'class_scores_' . $safeClassRoom . '_' . $month . '_' . $year;

        if ($format === 'pdf') {
            $filename .= '.pdf';
            $pdf = PDF::loadView('exports.class_scores', [
                'classScores' => $classScores,
                'class_room' => $class_room,
                'month' => $month,
                'year' => $year,
            ]);
            return $pdf->download($filename);
        }

        $filename .= '.xlsx';
        return Excel::download(new ClassScoresExport($classScores, $class_room, $month, $year), $filename);
    }




    public function exportTopScores(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');
        $format = $request->get('format', 'xlsx');

        // ดึงคะแนนรวมของนักเรียนแต่ละห้องในเดือนและปีที่เลือก
        $scores = DB::table('scores')
            ->select('students.class_room', 'students.student_name', DB::raw('SUM(scores.point) as total_points'))
            ->join('students', 'scores.student_id', '=', 'students.id')
            ->whereMonth('scores.created_at', $month)
            ->whereYear('scores.created_at', $year)
            ->groupBy('students.class_room', 'students.student_name')
            ->get();

        // ดึงห้องเรียนทั้งหมด
        $allClassRooms = DB::table('students')
            ->select('class_room')
            ->distinct()
            ->orderBy('class_room')
            ->pluck('class_room');

        // จัดกลุ่มตามห้อง
        $grouped = $scores->groupBy('class_room');

        // เตรียมข้อมูล topScores แบบ array
        $topScores = [];

        foreach ($allClassRooms as $classRoom) {
            if (isset($grouped[$classRoom])) {
                $studentsInRoom = $grouped[$classRoom];
                $maxPoints = $studentsInRoom->max('total_points');
                $topStudents = $studentsInRoom->where('total_points', $maxPoints);

                $topScores[$classRoom] = [
                    'students' => $topStudents,
                    'total_points' => $maxPoints,
                ];
            } else {
                $topScores[$classRoom] = null;
            }
        }

        if ($format === 'pdf') {
            $pdf = PDF::loadView('exports.top_scores', [
                'topScores' => $topScores,
                'month' => $month,
                'year' => $year
            ]);
            return $pdf->download('top_scores_' . $month . '_' . $year . '.pdf');
        }

        return Excel::download(new TopScoresExport($topScores, $month, $year), 'top_scores_' . $month . '_' . $year . '.xlsx');
    }




}
