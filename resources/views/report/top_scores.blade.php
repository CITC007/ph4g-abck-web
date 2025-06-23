<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />

    <title>รายงานคะแนนสูงสุดแต่ละเดือน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>

<body class="p-4 bg-gray-50">
    <!-- ปุ่มกลับหน้าแรก -->
    <a href="{{ route('dashboard') }}"
        class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        ← กลับหน้าแรก
    </a>

    <h1 class="text-2xl font-bold mb-4">รายงานคะแนนสูงสุดแต่ละเดือน</h1>

    <form method="GET" action="{{ route('report.top_scores') }}" class="flex flex-wrap gap-2 mb-4 items-center">
        <label for="month">เดือน:</label>
        <select name="month" id="month" class="p-2 border rounded">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->locale('th')->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>

        <label for="year">ปี:</label>
        <select name="year" id="year" class="p-2 border rounded">
            @php
                $currentYear = date('Y');
            @endphp
            @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y + 543 }}</option>
            @endfor
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">ค้นหา</button>
    </form>

    @if(isset($topScores) && count($topScores) > 0)
        <table class="w-full text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">ชั้นเรียน</th>
                    <th class="p-2 border">ชื่อนักเรียน</th>
                    <th class="p-2 border">คะแนนสูงสุด</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topScores as $score)
                    <tr>
                        <td class="p-2 border">{{ $score->class_room }}</td>
                        <td class="p-2 border">{{ $score->student_name }}</td>
                        <td class="p-2 border">{{ $score->total_points }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex gap-4">
            <a href="{{ route('report.top_scores_export', ['month' => request('month', date('n')), 'year' => request('year', date('Y')), 'format' => 'xlsx']) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">ดาวน์โหลด Excel (.xlsx)</a>
            <a href="{{ route('report.top_scores_export', ['month' => request('month', date('n')), 'year' => request('year', date('Y')), 'format' => 'pdf']) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">ดาวน์โหลด PDF</a>
        </div>

    @else
        <p class="text-gray-500">ไม่มีข้อมูลคะแนนสูงสุดในเดือนนี้</p>
    @endif

</body>

</html>