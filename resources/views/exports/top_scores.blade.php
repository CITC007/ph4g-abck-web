<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายงานคะแนนสูงสุด</title>
    <style>
        @page {
            size: A4;
            margin: 0mm 15mm 15mm 15mm;
        }

        @font-face {
            font-family: 'Sarabun';
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Sarabun';
            src: url("{{ storage_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'Sarabun';
            src: url("{{ storage_path('fonts/THSarabunNew-BoldItalic.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 16pt;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 140px;
            height: auto;
            margin-bottom: 10px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            font-family: 'Sarabun', sans-serif;
            font-size: 15pt;
            border: 1px solid #333;
            padding: 1px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-muted {

            font-style: italic;
            color: #888;
        }

        .timestamp {
            margin-top: 10px;
            font-size: 12pt;
            text-align: right;
        }
    </style>

</head>

<body>

    <div class="header">
        {{-- เปิดใช้งานโลโก้ถ้ามี --}}
        <img src="{{ public_path('images/school-logo.png') }}" alt="โลโก้โรงเรียน">
        <h1>โครงการโรงเรียนยุวสุจริต (กิจกรรมหัวใจสีชมพูเชิดชูความดี)</h1>
        <h1>รายงานผลคะแนนความดีของนักเรียนที่ได้รับคะแนนสูงสุด</h1>
        <h2>ประจำเดือน
            {{ \Carbon\Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM') }}
            พ.ศ. {{ $year + 543 }}
        </h2>
    </div>

    @if(count($topScores) > 0)
        <table>
            <thead>
                <tr>
                    <th>ห้องเรียน</th>
                    <th>ชื่อนักเรียน</th>
                    <th>คะแนนรวม</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topScores as $classRoom => $data)
                    <tr>
                        <td>{{ $classRoom }}</td>
                        <td style="text-align: cernter;">
                            @if($data)
                                @php $count = count($data['students']); @endphp
                                @if($count === 1)
                                    {{ collect($data['students'])->first()->student_name }}
                                @else
                                    @foreach($data['students'] as $index => $student)
                                        {{ $index + 1 }}. {{ $student->student_name }}<br>
                                    @endforeach
                                @endif
                            @else
                                <span style="text-align: center; font-size: 14pt; color: #888">-------------------ไม่มีผลคะแนน------------------</span>
                            @endif
                        </td>
                        <td>
                            {{ $data ? $data['total_points'] : 0 }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan=" 3">ไม่มีข้อมูลคะแนนในเดือนนี้
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <p style="text-align: center; font-size: 14pt; color: #888;">ไม่มีข้อมูลคะแนนในเดือนนี้</p>
    @endif


    <!-- แสดง Timestamp ดาวน์โหลด -->
    <div class="timestamp">
        วันที่ดาวน์โหลด: {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY HH:mm') }}
    </div>
</body>

</html>