<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // สำหรับจัดการวันที่

class ScoreHistoryController extends Controller
{
    /**
     * Display the score history for a specific student based on a signed certificate link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Certificate   $certificate  The Certificate model resolved by route model binding.
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show(Request $request, Certificate $certificate)
    {
        // Middleware 'signed' ได้ตรวจสอบความถูกต้องของ URL แล้ว
        // ตอนนี้เราสามารถใช้ $certificate object ได้อย่างปลอดภัย

        // ดึงข้อมูลนักเรียนจากใบประกาศ
        $student = $certificate->student;

        if (!$student) {
            Log::warning("ScoreHistoryController: Student not found for certificate ID: {$certificate->id}");
            abort(404, 'ไม่พบข้อมูลนักเรียนสำหรับใบประกาศนี้');
        }

        // ดึงเดือนและปีที่ระบุในใบประกาศ (จาก field ในตาราง certificates)
        $certMonth = $certificate->month;
        $certYear = $certificate->year;

        // ดึงประวัติคะแนนสำหรับนักเรียนคนนี้ ในเดือนและปีที่เกี่ยวข้องกับใบประกาศเท่านั้น
        $scores = Score::where('student_id', $student->id)
            ->where('month', $certMonth)
            ->where('year', $certYear)
            ->with('teacher')
            ->orderBy('created_at', 'asc') // หรือตามวันที่บันทึกคะแนน
            ->get();

        // *** เพิ่มบรรทัดนี้เพื่อคำนวณคะแนนรวม ***
        $totalScores = $scores->sum('point');
        // แปลงเดือนเป็นภาษาไทยสำหรับแสดงผล (ถ้าคุณมี array เดือนใน Controller หลัก)
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];
        $certMonthThai = $thaiMonths[$certMonth] ?? '';
        $issueYearBuddhist = $certYear + 543; // แปลงปีเป็น พ.ศ.

        return view('scores.history', compact('student', 'scores', 'totalScores', 'certMonthThai', 'issueYearBuddhist'));
    }
}