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
        $currentYear = date('Y'); // ใช้ปี ค.ศ.

        // ระดับชั้นทั้งหมด
        $grades = [
            'อนุบาลห้อง1',
            'อนุบาลห้อง2',
            'อนุบาลห้อง3',
            'อนุบาลห้อง4',
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
            $studentScore = Score::select('student_id', DB::raw('SUM(point) as total_points'))
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->whereHas('student', function ($q) use ($class_room) {
                    $q->where('class_room', $class_room);
                })
                ->groupBy('student_id')
                ->orderByDesc('total_points')
                ->first();

            if ($studentScore && $studentScore->total_points > 0) {
                $student = Student::find($studentScore->student_id);
                $topScores->push([
                    'class_room' => $class_room,
                    'student_name' => $student->student_name,
                    'total_points' => $studentScore->total_points,
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
