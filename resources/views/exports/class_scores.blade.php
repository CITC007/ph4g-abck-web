<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>รายงานคะแนนนักเรียนรายชั้น</title>
    <style>
        @font-face {
            font-family: 'Sarabun';
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 14pt;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18pt;
            margin: 0;
            font-weight: bold;
        }

        .header h2 {
            font-size: 18pt;
            margin: 0;
            font-weight: normal;
        }

        .header img {
            width: 140px;
            height: auto;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-family: 'Sarabun', sans-serif;
            font-size: 14pt;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <div class="header">
        {{-- เปิดใช้งานโลโก้ถ้ามี --}}
        <img src="{{ public_path('images/school-logo.png') }}" alt="โลโก้โรงเรียน">
        <h1>โครงการโรงเรียนยุวสุจริต (กิจกรรมหัวใจสีชมพูเชิดชูความดี)</h1>
        <h1>รายงานคะแนนความดีของนักเรียนประจำชั้น {{ $class_room }}</h1>
        <h2>ประจำเดือน
            {{ \Carbon\Carbon::createFromDate($year + 543, $month, 1)->locale('th')->isoFormat('MMMM YYYY') }}
        </h2>
    </div>
    @if(count($classScores) > 0)
        <table>
            <thead>
                <tr>
                    <th>รหัสนักเรียน</th>
                    <th>ชื่อนักเรียน</th>
                    <th>ชั้นเรียน</th>
                    <th>คะแนนสะสม</th>
                </tr>
            </thead>
            <tbody>
                @foreach($classScores as $student)
                    <tr>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->class_room }}</td>
                        <td>{{ $student->scores_sum_point ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; font-size: 14pt; color: #888;">ไม่มีข้อมูลนักเรียนในชั้นเรียนนี้</p>
    @endif


    <!-- แสดง Timestamp ดาวน์โหลด -->
    <div class="timestamp">
        วันที่ดาวน์โหลด: {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY HH:mm') }}
    </div>
</body>

</html>