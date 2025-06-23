<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>รายงานคะแนนนักเรียนรายชั้น</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>
<body class="p-4 bg-gray-50">

<a href="{{ route('dashboard') }}" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
    ← กลับหน้าแรก
</a>

<h1 class="text-2xl font-bold mb-4">รายงานคะแนนนักเรียนรายชั้น</h1>

<form method="GET" action="{{ route('report.class_scores') }}" class="flex flex-wrap gap-2 mb-4 items-center">
    <label for="class_room">ชั้นเรียน:</label>
    <select name="class_room" id="class_room" class="p-2 border rounded" required>
        <option value="" disabled selected>-- เลือกชั้นเรียน --</option>
        @foreach([
            'อนุบาลห้อง1', 'อนุบาลห้อง2', 'อนุบาลห้อง3', 'อนุบาลห้อง4',
            'ป.1/1','ป.1/2','ป.1/3','ป.1/4',
            'ป.2/1','ป.2/2','ป.2/3','ป.2/4',
            'ป.3/1','ป.3/2','ป.3/3','ป.3/4',
            'ป.4/1','ป.4/2','ป.4/3','ป.4/4',
            'ป.5/1','ป.5/2','ป.5/3','ป.5/4',
            'ป.6/1','ป.6/2','ป.6/3','ป.6/4',
        ] as $room)
            <option value="{{ $room }}" {{ request('class_room') == $room ? 'selected' : '' }}>{{ $room }}</option>
        @endforeach
    </select>

    <label for="month">เดือน:</label>
    <select name="month" id="month" class="p-2 border rounded">
        @for($m=1; $m<=12; $m++)
            <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($m)->locale('th')->isoFormat('MMMM') }}
            </option>
        @endfor
    </select>

    <label for="year">ปี:</label>
    <select name="year" id="year" class="p-2 border rounded">
        @php $currentYear = date('Y'); @endphp
        @for($y = $currentYear; $y >= $currentYear - 5; $y--)
            <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                {{ $y + 543 }}
            </option>
        @endfor
    </select>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">ค้นหา</button>
</form>

@if(isset($classScores) && count($classScores) > 0)
    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">รหัส</th>
                <th class="p-2 border">ชื่อนักเรียน</th>
                <th class="p-2 border">ชั้นเรียน</th>
                <th class="p-2 border">คะแนนสะสม</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classScores as $student)
                <tr>
                    <td class="p-2 border">{{ $student->student_code }}</td>
                    <td class="p-2 border">{{ $student->student_name }}</td>
                    <td class="p-2 border">{{ $student->class_room }}</td>
                    <td class="p-2 border">{{ $student->scores_sum_point ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 flex gap-4">
        <a href="{{ route('report.class-scores.download', ['class_room' => request('class_room'), 'month' => request('month'), 'year' => request('year'), 'format' => 'xlsx']) }}"
           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            ดาวน์โหลด Excel (.xlsx)
        </a>

        <a href="{{ route('report.class-scores.download', ['class_room' => request('class_room'), 'month' => request('month'), 'year' => request('year'), 'format' => 'pdf']) }}"
           class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            ดาวน์โหลด PDF
        </a>
    </div>
@else
    <p class="text-gray-500">ไม่มีข้อมูลนักเรียนในชั้นเรียนนี้ หรือยังไม่เลือกชั้นเรียน</p>
@endif

</body>
</html>
