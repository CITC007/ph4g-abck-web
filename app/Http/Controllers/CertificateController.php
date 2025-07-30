<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Score;
use App\Models\Certificate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Throwable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CertificateController extends Controller
{
    private $thaiMonths = [
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
    private $correctPin;
    private $maxAttempts = 5;
    private $decayMinutes = 1; // ล็อกเอาท์นาน 1 นาที

    public function __construct()
    {
        // ดึงค่าจาก .env โดยตรง
        $this->correctPin = env('CERTIFICATE_PIN', 'default_pin_if_env_not_set');
    }
    public function showPinForm()
    {
        if (Session::has('authenticated_by_pin') && Session::get('authenticated_by_pin') === true) {
            return redirect()->route('certificates.index');
        }

        $ipAddress = request()->ip(); // ใช้ request()->ip() ใน showPinForm
        $cacheKey = 'pin_login_attempts:' . $ipAddress;
        $lockoutTimestampKey = 'pin_lockout_timestamp:' . $ipAddress;

        $secondsRemaining = 0; // ค่าเริ่มต้น

        // ตรวจสอบว่าถูกล็อกเอาท์อยู่หรือไม่
        if (Cache::has($cacheKey) && Cache::get($cacheKey) >= $this->maxAttempts) {
            $lockoutTimestamp = Cache::get($lockoutTimestampKey);
            if ($lockoutTimestamp) {
                // คำนวณเวลาที่เหลือ
                $endTime = $lockoutTimestamp + ($this->decayMinutes * 60);
                $secondsRemaining = max(0, $endTime - time());

                // ถ้าเวลาหมดแล้ว ให้เคลียร์ cache เพื่อให้กรอกใหม่ได้เลย
                if ($secondsRemaining <= 0) {
                    Cache::forget($cacheKey);
                    Cache::forget($lockoutTimestampKey);
                    // Reset secondsRemaining เพื่อไม่ให้แสดง countdown
                    $secondsRemaining = 0;
                }
            } else {
                // กรณีเกิดการล็อกเอาท์แต่ไม่มี timestamp (อาจจะจากโค้ดเก่า)
                // ถือว่าล็อกเอาท์ตาม decayMinutes ไปก่อน แล้วจะสร้าง timestamp ใหม่เมื่อมีการพยายามอีกครั้ง
                $secondsRemaining = $this->decayMinutes * 60;
            }
        }
        // แสดง View ฟอร์มกรอก PIN
        // dd($secondsRemaining);
        return view('auth.pin_form', compact('secondsRemaining'));
    }

    public function processPin(Request $request)
    {
        $ipAddress = $request->ip();
        $cacheKey = 'pin_login_attempts:' . $ipAddress;
        $lockoutTimestampKey = 'pin_lockout_timestamp:' . $ipAddress;

        // ตรวจสอบว่า IP นี้ถูกล็อกเอาท์อยู่หรือไม่
        if (Cache::has($cacheKey) && Cache::get($cacheKey) >= $this->maxAttempts) {
            $lockoutTimestamp = Cache::get($lockoutTimestampKey);
            if ($lockoutTimestamp) {
                $endTime = $lockoutTimestamp + ($this->decayMinutes * 60);
                $secondsRemaining = max(0, $endTime - time());

                // ถ้าเวลาล็อกเอาท์ยังไม่หมด
                if ($secondsRemaining > 0) {
                    return redirect()->route('certificates.pin.show')
                        ->withErrors(['pin' => 'คุณพยายามกรอก PIN ผิดหลายครั้งเกินไป โปรดรออีก ' . $secondsRemaining . ' วินาที'])
                        ->withInput($request->only('pin'));
                } else {
                    // เวลาหมดแล้ว เคลียร์ cache เพื่อให้ลองใหม่ได้
                    Cache::forget($cacheKey);
                    Cache::forget($lockoutTimestampKey);
                }
            } else {
                // กรณีไม่มี lockoutTimestamp (อาจจะมาจากโค้ดเก่า) ถือว่าถูกล็อกเอาท์
                return redirect()->route('certificates.pin.show')
                    ->withErrors(['pin' => 'คุณพยายามกรอก PIN ผิดหลายครั้งเกินไป โปรดรออีก ' . $this->decayMinutes . ' นาที'])
                    ->withInput($request->only('pin'));
            }
        }

        $request->validate([
            'pin' => 'required|string|size:6',
        ], [
            'pin.required' => 'กรุณากรอกรหัส PIN',
            'pin.size' => 'รหัส PIN ต้องมี 6 หลัก',
        ]);

        if ($request->input('pin') === $this->correctPin) {
            Session::put('authenticated_by_pin', true);
            Cache::forget($cacheKey); // ลบจำนวนครั้งที่พยายามผิดพลาด
            Cache::forget($lockoutTimestampKey); // ลบ timestamp การล็อกเอาท์
            return redirect()->route('certificates.index');
        } else {
            $attempts = Cache::get($cacheKey, 0) + 1;
            Cache::put($cacheKey, $attempts, $this->decayMinutes * 60);

            // หากถึงจำนวนครั้งที่ทำให้ถูกล็อกเอาท์ ให้บันทึก timestamp
            if ($attempts >= $this->maxAttempts) {
                // บันทึกเวลาที่เริ่มล็อกเอาท์
                Cache::put($lockoutTimestampKey, time(), $this->decayMinutes * 60);
                return redirect()->route('certificates.pin.show')
                    ->withErrors(['pin' => 'คุณพยายามกรอก PIN ผิดหลายครั้งเกินไป โปรดรออีก ' . ($this->decayMinutes * 60) . ' วินาที']) // แสดงเวลาล็อกเอาท์ทันที
                    ->withInput($request->only('pin'));
            }

            return redirect()->route('certificates.pin.show')->withErrors(['pin' => 'รหัส PIN ไม่ถูกต้อง']);
        }
    }
    public function logout(Request $request)
    {
        // ลบค่า Session ที่ใช้ในการยืนยัน PIN ออก
        Session::forget('authenticated_by_pin');

        // Redirect ผู้ใช้กลับไปที่หน้ากรอก PIN หรือหน้าแรกของเว็บไซต์
        // แนะนำให้ Redirect ไปที่หน้ากรอก PIN เพราะหน้า certificates จะถูก redirect ไปที่นั่นอยู่แล้ว
        return redirect('/')->with('status', 'คุณได้ออกจากระบบ PIN เรียบร้อยแล้ว');
        // return redirect()->route('certificates.pin.show')->with('status', 'คุณได้ออกจากระบบ PIN เรียบร้อยแล้ว');
    }
    public function index(Request $request)
    {
        // *** เริ่มต้นด้วยการตรวจสอบ PIN ***
        if (!Session::has('authenticated_by_pin') || Session::get('authenticated_by_pin') !== true) {
            // ถ้ายังไม่ได้ยืนยัน PIN ให้ redirect ไปหน้ากรอก PIN
            return redirect()->route('certificates.pin.show');
        }
        // Fetch all unique class rooms for the filter dropdown
        $allClassRooms = Student::select('class_room')
            ->distinct()
            ->orderBy('class_room')
            ->pluck('class_room');

        // กำหนดปีพุทธศักราชปัจจุบันและเดือนปัจจุบัน
        $currentBuddhistYear = Carbon::now()->year + 543;
        $currentMonth = Carbon::now()->month;

        // ดึงค่าที่ผู้ใช้เลือกจาก Request หรือกำหนดค่าเริ่มต้น
        $selectedClassRoom = $request->input('class_room');
        $selectedMonth = $request->input('month');
        $selectedAcademicYear = $request->input('academic_year') ?? $currentBuddhistYear;

        // Flags สำหรับการแสดงผลสถานะรวม
        $hasDataToCreate = false;
        $hasDataToDownload = false;
        $certificatesIssued = 0;

        // Initialize results array ที่จะส่งไปยัง View
        $resultsByClassRoom = [];

        // 1. ตรวจสอบว่าผู้ใช้ได้เลือกเดือนและปีแล้วหรือไม่
        if (!$selectedMonth || !$selectedAcademicYear) {
            foreach ($allClassRooms as $room) {
                // กรณีที่ยังไม่ได้เลือกเดือนและปี ให้แสดงข้อความแจ้งเตือน
                $resultsByClassRoom[$room] = [
                    (object) [
                        'type' => 'no_data_selected',
                        'class_room' => $room,
                        'student_name_display' => 'โปรดเลือกเดือนและปี',
                        'score_display' => '-',
                        'certificate_display' => '-',
                        'is_red_text' => false,
                        'is_grey_text' => true, // ใช้สีเทาสำหรับข้อความแจ้งเตือน
                        'is_green_text' => false,
                        'is_orange_row' => false,
                        'is_orange_text' => false,
                        'certificate_id' => null,
                        'can_create' => false,
                        'can_download' => false,
                        'student_id_for_action' => null,
                        'month' => null,
                        'year' => null,
                    ]
                ];
            }
            // ส่งข้อมูลกลับไปยัง View ในสถานะเริ่มต้น
            return view('certificates.index', compact(
                'allClassRooms',
                'resultsByClassRoom',
                'currentBuddhistYear',
                'selectedClassRoom',
                'selectedMonth',
                'selectedAcademicYear',
                'hasDataToCreate',
                'hasDataToDownload',
                'certificatesIssued'
            ));
        }

        // --- Date Validation Checks: ตรวจสอบความถูกต้องของเดือนและปีที่เลือก ---
        $redirectParams = [
            'class_room' => $selectedClassRoom,
            // หากเกิด error จะ redirect กลับไปที่เดือนก่อนหน้าของปีปัจจุบัน (ถ้ามี)
            'month' => $currentMonth - 1 > 0 ? $currentMonth - 1 : null,
            'academic_year' => $currentBuddhistYear,
        ];

        if ($selectedAcademicYear > $currentBuddhistYear) {
            session()->flash('error', 'ไม่สามารถออกใบประกาศล่วงหน้าได้ กรุณาเลือกเดือนและปีที่ผ่านมาแล้ว');
            return redirect()->route('certificates.index', $redirectParams);
        }

        if ($selectedAcademicYear == $currentBuddhistYear && $selectedMonth > $currentMonth) {
            session()->flash('error', 'ไม่สามารถออกใบประกาศสำหรับเดือนในอนาคตได้ กรุณาเลือกเดือนที่ผ่านมาแล้ว');
            return redirect()->route('certificates.index', $redirectParams);
        }

        if ($selectedAcademicYear == $currentBuddhistYear && $selectedMonth == $currentMonth) {
            session()->flash('error', 'ไม่สามารถออกใบประกาศสำหรับเดือนปัจจุบันที่ยังไม่สิ้นสุดได้ โปรดเลือกเดือนที่ผ่านมาแล้ว');
            return redirect()->route('certificates.index', $redirectParams);
        }
        // --- End Date Validation Checks ---

        // แปลงปีพุทธศักราชเป็นปีคริสต์ศักราชสำหรับการ Query ฐานข้อมูล
        $selectedGregorianYear = $selectedAcademicYear - 543;

        // วนลูปผ่านห้องเรียนทั้งหมดเพื่อเตรียมข้อมูลสำหรับ View
        foreach ($allClassRooms as $room) {
            // ถ้ามีการเลือกห้องเรียนเฉพาะ และห้องปัจจุบันไม่ใช่ห้องที่เลือก ให้แสดง placeholder
            if ($selectedClassRoom && $selectedClassRoom !== $room) {
                $resultsByClassRoom[$room] = [
                    (object) [
                        'type' => 'not_selected_room',
                        'class_room' => $room,
                        'student_name_display' => 'ไม่แสดงผลในโหมดฟิลเตอร์',
                        'score_display' => '-',
                        'certificate_display' => '-',
                        'is_red_text' => false,
                        'is_grey_text' => true, // ใช้สีเทาสำหรับข้อความแจ้งเตือน
                        'is_green_text' => false,
                        'is_orange_row' => false,
                        'is_orange_text' => false,
                        'certificate_id' => null,
                        'can_create' => false,
                        'can_download' => false,
                        'student_id_for_action' => null,
                        'month' => null,
                        'year' => null,
                    ]
                ];
                continue; // ข้ามไปห้องถัดไป
            }

            // ดึงข้อมูลนักเรียนที่ได้คะแนนสูงสุดสำหรับห้องเรียน เดือน และปีที่เลือก
            $topStudentsInRoom = DB::table('scores')
                ->select(
                    'students.id as student_id',
                    'students.student_name',
                    'students.class_room',
                    DB::raw('SUM(scores.point) as total_points') // รวมคะแนน
                )
                ->join('students', 'scores.student_id', '=', 'students.id')
                ->where('scores.month', $selectedMonth)
                ->where('scores.year', $selectedGregorianYear)
                ->where('students.class_room', $room)
                ->groupBy('students.id', 'students.student_name', 'students.class_room')
                ->orderByDesc('total_points') // เรียงลำดับจากคะแนนสูงสุด
                ->get();

            if ($topStudentsInRoom->isEmpty()) {
                // กรณี: ไม่พบข้อมูลนักเรียนสำหรับห้องนี้ในเดือน/ปีที่เลือก
                $resultsByClassRoom[$room] = [
                    (object) [
                        'type' => 'no_top_student',
                        'class_room' => $room,
                        'student_name_display' => 'ไม่พบข้อมูลนักเรียน',
                        'score_display' => '-',
                        'certificate_display' => '-',
                        'is_red_text' => false,
                        'is_grey_text' => true, // ใช้สีเทาสำหรับข้อความแจ้งเตือน
                        'is_green_text' => false,
                        'is_orange_row' => false,
                        'is_orange_text' => false,
                        'certificate_id' => null,
                        'can_create' => false,
                        'can_download' => false,
                        'student_id_for_action' => null,
                        'month' => $selectedMonth,
                        'year' => $selectedGregorianYear,
                    ]
                ];
            } else {
                $maxPoints = $topStudentsInRoom->max('total_points'); // คะแนนสูงสุดในห้องนั้น
                $topStudents = $topStudentsInRoom->where('total_points', $maxPoints); // นักเรียนทุกคนที่ได้คะแนนสูงสุดเท่ากัน

                $studentsDataForRoom = []; // Array สำหรับเก็บข้อมูลนักเรียนในห้องนี้
                $isMultipleTopStudents = ($topStudents->count() > 1); // Flag: มีนักเรียนคะแนนสูงสุดซ้ำกันหรือไม่

                // วนลูปผ่านนักเรียนทุกคนที่ได้คะแนนสูงสุด (รวมถึงกรณีมีคนเดียว)
                foreach ($topStudents as $student) {
                    // ตรวจสอบว่ามีการออกใบประกาศสำหรับนักเรียนคนนี้แล้วหรือยัง
                    $existingCert = Certificate::where('student_id', $student->student_id)
                        ->where('month', $selectedMonth)
                        ->where('year', $selectedGregorianYear)
                        ->first();

                    // กำหนดค่าเริ่มต้นของ Flags สี
                    $isRedText = false;
                    $isGreenText = false;
                    $isOrangeText = false;
                    $isOrangeRow = false; // สำหรับสีพื้นหลังแถว

                    // *** ลำดับการกำหนดสีตามลำดับความสำคัญ ***
                    // ลำดับ 1: สีเขียว (ถ้าออกใบประกาศแล้ว)
                    if ($existingCert) {
                        $isGreenText = true;
                    }
                    // ลำดับ 2: สีส้ม (ถ้ามีคะแนนสูงสุดซ้ำกัน และยังไม่ออกใบประกาศ)
                    else if ($isMultipleTopStudents && !$existingCert) {
                        $isOrangeText = true;
                        $isOrangeRow = true;
                    }
                    // ลำดับ 3: สีแดง (ถ้ายังไม่ออกใบประกาศ และไม่ใช่กรณีคะแนนซ้ำ)
                    else {
                        $isRedText = true;
                    }

                    // เพิ่มข้อมูลนักเรียนคนนี้เข้าใน array สำหรับห้องนี้
                    $studentsDataForRoom[] = (object) [
                        'type' => 'top_student', // ประเภทข้อมูล
                        'class_room' => $room,
                        'student_name_display' => $student->student_name,
                        'score_display' => $student->total_points,
                        'certificate_display' => $existingCert ? $existingCert->certificate_number : 'ยังไม่ได้ออกใบประกาศ',
                        'certificate_id' => $existingCert ? $existingCert->id : null,
                        'is_issued' => (bool) $existingCert,
                        'is_red_text' => $isRedText,
                        'is_green_text' => $isGreenText,
                        'is_grey_text' => false, // ไม่ใช่ placeholder
                        'is_orange_row' => $isOrangeRow,   // ส่งค่าไป Blade
                        'is_orange_text' => $isOrangeText,  // ส่งค่าไป Blade
                        'can_create' => !$existingCert,      // สามารถสร้างใบประกาศได้หรือไม่
                        'can_download' => (bool) $existingCert, // สามารถดาวน์โหลดใบประกาศได้หรือไม่
                        'student_id_for_action' => $student->student_id,
                        'month' => $selectedMonth,
                        'year' => $selectedGregorianYear,
                    ];

                    // อัปเดต flag รวมสำหรับปุ่ม Create/Download
                    if ($existingCert) {
                        $hasDataToDownload = true;
                        $certificatesIssued++;
                    } else {
                        $hasDataToCreate = true;
                    }
                }
                // กำหนด array ของ Object นักเรียนเข้าใน $resultsByClassRoom สำหรับห้องปัจจุบัน
                $resultsByClassRoom[$room] = $studentsDataForRoom;
            }
        }

        // เรียงลำดับผลลัพธ์ตามชื่อห้องเรียน (Natural Sort เพื่อให้ ป.1, ป.2, ป.10 เรียงถูก)
        uksort($resultsByClassRoom, function ($a, $b) {
            return strnatcmp($a, $b);
        });

        // ส่งข้อมูลทั้งหมดไปยัง View
        return view('certificates.index', compact(
            'allClassRooms',
            'resultsByClassRoom',
            'currentBuddhistYear',
            'selectedClassRoom',
            'selectedMonth',
            'selectedAcademicYear',
            'hasDataToCreate',
            'hasDataToDownload',
            'certificatesIssued'
        ));
    }
    public function saveAndGenerateCertificates(Request $request)
    {
        $request->validate([
            'selected_students' => 'required|string',
        ]);

        $studentsToProcess = json_decode($request->input('selected_students'), true);

        if (!is_array($studentsToProcess) || empty($studentsToProcess)) {
            return back()->with('error', 'ไม่พบข้อมูลนักเรียนที่เลือกสำหรับการสร้างใบประกาศ');
        }

        $generatedCertificateNumbers = []; // For newly created certificates
        $skippedStudents = []; // For students whose certs already exist

        DB::beginTransaction();
        try {
            foreach ($studentsToProcess as $studentData) {
                if (!isset($studentData['id']) || !isset($studentData['score']) || !isset($studentData['month']) || !isset($studentData['year'])) {
                    \Log::warning("ข้อมูลนักเรียนไม่สมบูรณ์สำหรับการสร้างใบประกาศ: " . json_encode($studentData));
                    continue; // Skip incomplete data
                }

                $studentId = $studentData['id'];
                $totalScore = $studentData['score'];
                $month = $studentData['month'];
                $year = $studentData['year']; // Year received is Gregorian

                $student = Student::find($studentId);
                if (!$student) {
                    \Log::warning("ไม่พบนักเรียน ID: {$studentId} สำหรับสร้างใบประกาศ (อาจถูกลบไปแล้ว?)");
                    continue; // Skip if student not found
                }

                // IMPORTANT: Check if certificate already exists before creating
                $existingCert = Certificate::where('student_id', $studentId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($existingCert) {
                    // If certificate already exists, add to skipped list and continue
                    $skippedStudents[] = [
                        'name' => $student->student_name,
                        'cert_number' => $existingCert->certificate_number
                    ];
                    continue; // Skip creating a duplicate
                }

                // Generate new certificate number
                $lastCert = Certificate::orderBy('id', 'desc')->first();
                $lastRunningNumber = 0;
                if ($lastCert && preg_match('/^(\d+)\/\d{4}$/', $lastCert->certificate_number, $matches)) {
                    $lastRunningNumber = (int) $matches[1];
                }
                $newRunningNumber = sprintf('%03d', $lastRunningNumber + 1);
                $certificateNumber = $newRunningNumber . '/' . (Carbon::now()->year + 543);

                // Create the new certificate
                $certificate = Certificate::create([
                    'student_id' => $student->id,
                    'student_name' => $student->student_name,
                    'class_room' => $student->class_room,
                    'total_score' => $totalScore,
                    'month' => $month,
                    'year' => $year,
                    'certificate_number' => $certificateNumber,
                    'issued_at' => Carbon::now(),
                ]);
                $generatedCertificateNumbers[$student->id] = $certificateNumber;
            }
            DB::commit();

            $message = 'สร้างใบประกาศเรียบร้อยแล้ว!';
            if (!empty($skippedStudents)) {
                $message .= ' (มี ' . count($skippedStudents) . ' ใบประกาศที่ไม่ได้สร้างใหม่ เนื่องจากมีอยู่แล้ว)';
            }

            return back()->with('success', $message)
                ->with('generated_cert_numbers', $generatedCertificateNumbers)
                ->with('skipped_students', $skippedStudents); // Pass skipped students for display
        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error("เกิดข้อผิดพลาดในการบันทึกใบประกาศ: " . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการสร้างใบประกาศ: ' . $e->getMessage());
        }
    }

    public function downloadSelectedCertificates(Request $request)
    {
        $request->validate([
            'selected_certificates_for_download' => 'required|json',
            'issue_day' => 'required|numeric|min:1|max:31',
            'issue_month' => 'required|numeric|min:1|max:12',
            'issue_year' => 'required|numeric|digits:4',
            'cert_month' => 'required|numeric|min:1|max:12',
            'cert_semester' => 'required|in:1,2',
            'cert_academic_year' => 'required|numeric|digits:4',
        ]);

        $selectedCertificateIds = json_decode($request->input('selected_certificates_for_download'), true);

        $certificatesToDownload = Certificate::with('student')
            ->whereIn('id', $selectedCertificateIds)
            ->get();

        if ($certificatesToDownload->isEmpty()) {
            return back()->with('error', 'ไม่พบใบประกาศที่เลือกสำหรับดาวน์โหลด');
        }

        $tempFolder = storage_path('app/temp_certificates/');
        if (!File::exists($tempFolder)) {
            File::makeDirectory($tempFolder, 0777, true);
        }

        try {
            $imageManager = new ImageManager(new Driver());
            $fontPath = public_path('fonts/PSL_Butterfly.ttf');
            if (!File::exists($fontPath)) {
                throw new \Exception("ไม่พบไฟล์ฟอนต์ที่: " . $fontPath . ". โปรดตรวจสอบให้แน่ใจว่า PSL_Butterfly.ttf อยู่ใน public/fonts/");
            }
            $fontColor = '#1941E8';
            $fontColor2 = '#ff3faf';

            $issueDay = sprintf('%d', $request->input('issue_day'));
            $issueMonthNumeric = $request->input('issue_month');
            $issueYearBuddhist = $request->input('issue_year');
            $certMonthNumeric = $request->input('cert_month');
            $certSemester = $request->input('cert_semester');
            $certAcademicYear = $request->input('cert_academic_year');

            $issueMonthThai = $this->thaiMonths[$issueMonthNumeric] ?? '';
            $certMonthThai = $this->thaiMonths[$certMonthNumeric] ?? '';

            foreach ($certificatesToDownload as $certificate) {
                $studentName = $certificate->student_name;
                $studentId = $certificate->student_id;
                $certificateNumber = $certificate->certificate_number;
                $classRoom = $certificate->class_room;

                // Load your certificate template image
                $image = $imageManager->read(public_path('cert_templates/empty-bg.png'))->resize(3508, 2480);

                // Add text elements to the image
                $image->text('เลขที่ ' . $certificateNumber, 2900, 250, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(75);
                    $font->color($fontColor);
                    $font->align('right');
                    $font->valign('top');
                });
                $image->text('โรงเรียนอนุบาลเชียงคำ(วัดพระธาตุสบแวน)', 1754, 550, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(180);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text('ขอมอบเกียรติบัตรฉบับนี้เพื่อแสดงว่า', 1754, 730, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text($studentName, 1754, 920, function ($font) use ($fontPath, $fontColor2) {
                    $font->file($fontPath);
                    $font->size(180);
                    $font->color($fontColor2);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text('เป็นผู้มีจิตสาธารณะและเป็นแบบอย่างที่ดีให้กับผู้อื่น', 1754, 1110, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text('โครงการโรงเรียนยุวสุจริต (กิจกรรมหัวใจสีชมพูเชิดชูความดี)', 1754, 1225, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $academicInfoText = "ประจำเดือน {$certMonthThai} ภาคเรียนที่ {$certSemester} ปีการศึกษา {$certAcademicYear}";
                $image->text($academicInfoText, 1754, 1395, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $issueDateText = "ให้ไว้ ณ วันที่ {$issueDay} เดือน{$issueMonthThai} พุทธศักราช {$issueYearBuddhist}";
                $image->text($issueDateText, 1754, 1510, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text('ขออำนวยพรให้ประสพความสุข ความเจริญก้าวหน้าตลอดไป', 1754, 1625, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text('(นายอิสรีย์ ทองภูมิพันธ์)', 1754, 1950, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });
                $image->text('ผู้อำนวยการโรงเรียนอนุบาลเชียงคำ(วัดพระธาตุสบแวน)', 1754, 2080, function ($font) use ($fontPath, $fontColor) {
                    $font->file($fontPath);
                    $font->size(120);
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });

                // Generate and place QR Code
                // $qrContent = route('students.history', ['student' => $studentId]);
                $qrCodeUrl = URL::signedRoute('certificates.score_history', ['certificate' => $certificate->id]);
                $qr = QrCode::format('png')->size(280)->generate($qrCodeUrl);
                $qrPath = $tempFolder . "qr_{$certificate->id}.png";
                file_put_contents($qrPath, $qr);
                $qrImage = $imageManager->read($qrPath);
                $image->place($qrImage, 'bottom-right', 350, 600); // Adjust position as needed

                // Save the generated certificate image
                $fileNameClassRoom = str_replace('/', '_', $classRoom);
                // $filename = preg_replace('/[^A-Za-z0-9ก-๙]/u', '_', $studentName) . '_' . $certMonthThai . $issueYearBuddhist . '_certificate.png';
                $filename = $fileNameClassRoom . '_' . preg_replace('/[^A-Za-z0-9ก-๙]/u', '_', $studentName) . '_' . $certMonthThai . $issueYearBuddhist . '_certificate.png';
                $image->save($tempFolder . $filename);

                // Delete the temporary QR code file
                if (File::exists($qrPath)) {
                    File::delete($qrPath);
                }
            }

            // Create a zip archive of all generated certificates
            $zipName = 'certificates_' . Carbon::now()->format('YmdHis') . '.zip';
            $zipPath = storage_path("app/public/{$zipName}");
            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('ไม่สามารถสร้างไฟล์ ZIP ได้');
            }

            foreach (File::files($tempFolder) as $file) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
            }
            $zip->close();

            // Clean up temporary certificate images
            File::deleteDirectory($tempFolder);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (Throwable $e) {
            \Log::error("เกิดข้อผิดพลาดในการสร้างใบรับรอง: " . $e->getMessage());
            // Ensure temporary directory is cleaned up even on error
            if (File::exists($tempFolder)) {
                File::deleteDirectory($tempFolder);
            }
            return back()->with('error', 'เกิดข้อผิดพลาดในการสร้างใบรับรอง: ' . $e->getMessage());
        } finally {
            // Final cleanup to ensure no leftover files
            if (File::exists($tempFolder)) {
                File::deleteDirectory($tempFolder);
            }
        }
    }
}