<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard คะแนนสูงสุด</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>

<body class="p-4 bg-gray-50">

    @php
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
            12 => 'ธันวาคม',
        ];

        $monthName = $thaiMonths[$currentMonth] ?? '';
        $yearBuddhist = $currentYear + 543;
    @endphp

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 sm:gap-3">
            <a href="{{ url('/score-entry') }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-blue-700 transition text-center">
                เพิ่มคะแนนหัวใจให้นักเรียน
            </a>

            <a href="{{ route('report.top_scores') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center">
                รายงานคะแนนสูงสุดแต่ละเดือน
            </a>

            <a href="{{ route('report.class_scores') }}"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-center">
                รายงานคะแนนรายชั้นเรียน
            </a>


        </div>
    </div>


    <div class="div">
        <h1 class="text-xl sm:text-2xl font-bold mb-4 text-center">
            รายการความดีนักเรียนที่ได้คะแนนสูงสุด ประจำเดือน
            <span class="text-blue-500">{{ $monthName }}</span>
            พ.ศ. <span class="text-blue-500">{{ $yearBuddhist }}</span>
        </h1>
    </div>
    <table class="w-full border text-left">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border text-center">ระดับชั้น</th>
                <th class="p-2 border text-center">ชื่อนักเรียน</th>
                <th class="p-2 border text-center">คะแนน</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topScores as $item)
                <tr>
                    <td class="p-2 border text-center">{{ $item['class_room'] }}</td>
                    <td class="p-2 border text-center">{{ $item['student_name'] }}</td>
                    <td class="p-2 border text-center">{{ $item['total_points'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-2 text-center text-gray-500">ยังไม่มีข้อมูลคะแนนในเดือนนี้</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>