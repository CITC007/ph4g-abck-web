<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>ประวัติการได้รับคะแนนของ {{ $student->student_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>

<body class="p-4 bg-gray-50">

    <button onclick="window.history.back()" class="mb-4 px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">
        ← กลับ
    </button>

    <div class="mb-4 p-3 bg-green-50 rounded border border-green-300">
        <h2 class="text-xl font-bold mb-1">
            <span class="text-black font-normal">ประวัติการได้รับคะแนนของ : </span>
            <span class="text-blue-600 font-semibold">{{ $student->student_name }}</span>
        </h2>
        <div>ชั้นเรียน: <span class="font-semibold text-blue-600">{{ $student->class_room }}</span></div>
        <div>คะแนนรวมทั้งหมด: <span class="font-semibold text-green-600">{{ $totalPoints }}</span></div>
        <div>คะแนนรวมของเดือน <span
                class="font-semibold text-pink-600">{{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->locale('th')->isoFormat('MMMM') }}
                {{ $currentYear + 543 }}:</span> <span class="font-semibold text-green-600">{{ $monthPoints }}</span>
        </div>
    </div>


    <table class="w-full border text-center">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">วันที่ได้รับคะแนน</th>
                <th class="p-2 border">รายการความดี</th>
                <th class="p-2 border">เดือนที่ได้รับคะแนน</th>
                <th class="p-2 border">ครูผู้ให้คะแนน</th>
            </tr>
        </thead>
        <tbody>
            @forelse($scores as $score)
                <tr>
                    <td class="p-2 border">{{ $score->created_at->format('d/m/Y') }}</td>
                    <td class="p-2 border">{{ $score->reason }}</td>
                    <td class="p-2 border">
                        {{ \Carbon\Carbon::create($score->year, $score->month, 1)->locale('th')->isoFormat('MMMM') }}
                        {{ $score->year + 543 }}
                    </td>
                    <td class="p-2 border">
                        {{ optional($score->teacher)->teacher_name ?? 'ไม่ทราบ' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-2 text-center text-gray-500">ไม่มีข้อมูลคะแนน</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $scores->links() }}
    </div>

</body>

</html>