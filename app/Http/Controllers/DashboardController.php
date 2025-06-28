<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        $grades = [
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

        $topScores = collect();

        foreach ($grades as $class_room) {
            $students = Student::where('class_room', $class_room)
                ->leftJoin('scores', function ($join) use ($currentMonth, $currentYear) {
                    $join->on('students.id', '=', 'scores.student_id')
                        ->whereMonth('scores.created_at', $currentMonth)
                        ->whereYear('scores.created_at', $currentYear);
                })
                ->select('students.student_name', 'students.class_room', DB::raw('COALESCE(SUM(scores.point), 0) as total_points'))
                ->groupBy('students.id', 'students.student_name', 'students.class_room')
                ->orderByDesc('total_points')
                ->orderBy('students.student_name')
                ->get();

            if ($students->isEmpty() || $students->first()->total_points == 0) {
                // ไม่มีคะแนน
                $topScores->push([
                    'class_room' => $class_room,
                    'student_name' => 'ยังไม่มีคะแนนสูงสุด',
                    'total_points' => 0,
                ]);
            } else {
                $maxScore = $students->first()->total_points;

                // นับนักเรียนที่มีคะแนนสูงสุดเท่ากัน
                $topSame = $students->filter(fn($s) => $s->total_points == $maxScore);

                $suffix = $topSame->count() > 1
                    ? ' (มีนักเรียนที่ได้คะแนนสูงสุดเท่ากันอีก ' . ($topSame->count() - 1) . ' คน)'
                    : '';

                $topScores->push([
                    'class_room' => $class_room,
                    'student_name' => $topSame->first()->student_name . $suffix,
                    'total_points' => $maxScore,
                ]);
            }
        }

        return view('dashboard', [
            'topScores' => $topScores,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }
}
