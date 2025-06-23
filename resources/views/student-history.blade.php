<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>ประวัติการได้รับคะแนนของ {{ $student->student_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>

<body class="p-4 bg-gray-50">

    <button onclick="window.history.back()" class="mb-4 px-3 py-1 bg-gray-300 rounded">กลับ</button>

    <h2 class="text-xl font-bold mb-2">ประวัติการได้รับคะแนนของ : {{ $student->student_name }}</h2>
    <div class="mb-2">ชั้นเรียน: {{ $student->class_room }}</div>
    <div class="mb-2 font-semibold">คะแนนรวมทั้งหมด: {{ $totalPoints }}</div>
    <div class="mb-6 font-semibold">
        คะแนนรวมเดือน {{ $currentMonth }}/{{ $currentYear }}: {{ $monthPoints }}
    </div>

    <table class="w-full border text-left">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">วันที่ได้รับคะแนน</th>
                <th class="p-2 border">รายการความดี</th>
                <th class="p-2 border">เดือนที่ได้รับคะแนน</th>
                <th class="p-2 border">ครูผู้ให้คะแนน</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scores as $score)
                <tr>
                    <td class="p-2 border">{{ $score->created_at->format('d/m/Y') }}</td>
                    <td class="p-2 border">{{ $score->reason }}</td>
                    <td class="p-2 border">{{ $score->month }}/{{ $score->year }}</td>
                    <td class="p-2 border">
                        {{ optional($score->teacher)->teacher_name ?? 'ไม่ทราบ' }}
                    </td>
                </tr>
            @endforeach
            @if($scores->isEmpty())
                <tr>
                    <td colspan="4" class="p-2 text-center text-gray-500">ไม่มีข้อมูลคะแนน</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>

</html>