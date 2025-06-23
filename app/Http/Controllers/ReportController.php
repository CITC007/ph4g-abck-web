<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

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

        // รวมข้อมูลให้ได้คะแนนสูงสุดแต่ละห้อง
        $topScores = $scores->groupBy('class_room')->map(function ($group) {
            return $group->sortByDesc('total_points')->first();
        });

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
                ->get();
        }

        return view('report.class_scores', [
            'students' => $students,
            'classRoom' => $classRoom,
            'month' => $month,
            'year' => $year,
        ]);
    }

    // ฟังก์ชันดาวน์โหลดรายงานคะแนนสูงสุด (.xlsx / .pdf)
    public function downloadTopScores(Request $request)
    {
        // TODO: พัฒนาเพิ่มเติม
        return response('ฟังก์ชันดาวน์โหลดรายงานคะแนนสูงสุดยังไม่พัฒนา');
    }

    // ฟังก์ชันดาวน์โหลดรายงานคะแนนรายชั้น (.xlsx / .pdf)
    public function downloadClassScores(Request $request)
    {
        // TODO: พัฒนาเพิ่มเติม
        return response('ฟังก์ชันดาวน์โหลดรายงานคะแนนรายชั้นยังไม่พัฒนา');
    }

    public function exportTopScores(Request $request)
    {
        // โค้ดสร้างไฟล์ Excel หรือ PDF หรือแสดงข้อความ
        return response('Export top scores function not implemented yet');
    }

}
