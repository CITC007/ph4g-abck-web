<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>ประวัติการได้รับคะแนนของ {{ $student->student_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
</head>

<body
    class="min-h-screen bg-gray-100 bg-[url('/images/bg-hearts-light.png')] bg-cover bg-fixed bg-no-repeat p-4 sm:p-6">

    <div class="max-w-5xl mx-auto">

        <button onclick="window.history.back()"
            class="mb-6 px-4 py-2 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 transition text-sm sm:text-base">
            ← กลับ
        </button>

        <div
            class="mb-6 p-4 bg-green-50 rounded border border-green-300 shadow-sm max-w-full sm:max-w-4xl mx-auto text-sm sm:text-base">
            <h2 class="text-xl font-bold mb-2">
                <span class="text-black font-normal">ประวัติการได้รับคะแนนของ :</span>
                <span class="text-blue-600 font-semibold">{{ $student->student_name }}</span>
            </h2>
            <div class="space-y-1">
                <div>ชั้นเรียน: <span class="font-semibold text-blue-600">{{ $student->class_room }}</span></div>
                <div>คะแนนรวมทั้งหมด: <span class="font-semibold text-green-600">{{ $totalPoints }}</span></div>
                <div>คะแนนรวมของเดือน
                    <span class="font-semibold text-pink-600">
                        {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->locale('th')->isoFormat('MMMM') }}
                        {{ $currentYear + 543 }}:
                    </span>
                    <span class="font-semibold text-green-600">{{ $monthPoints }}</span>
                </div>
            </div>
        </div>

        <div
            class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md overflow-x-auto max-w-full sm:max-w-4xl mx-auto">
            <table class="w-full text-center min-w-[400px] text-xs sm:text-sm rounded-lg overflow-hidden">
                <thead class="bg-purple-100 text-purple-800">
                    <tr>
                        <th class="p-3 border border-gray-300 whitespace-nowrap">วันที่ได้รับคะแนน</th>
                        <th class="p-3 border border-gray-300 whitespace-nowrap text-left">รายการความดี</th>
                        <th class="p-3 border border-gray-300 whitespace-nowrap">เดือนที่ได้รับคะแนน</th>
                        <th class="p-3 border border-gray-300 whitespace-nowrap">ครูผู้ให้คะแนน</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scores as $score)
                        <tr class="hover:bg-purple-50 transition-colors text-sm sm:text-base">
                            <td class="p-3 border border-gray-300 whitespace-nowrap">
                                {{ $score->created_at->format('d/m/Y') }}
                            </td>
                            <td class="p-3 border border-gray-300 text-left">{{ $score->reason }}</td>
                            <td class="p-3 border border-gray-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::create($score->year, $score->month, 1)->locale('th')->isoFormat('MMMM') }}
                                {{ $score->year + 543 }}
                            </td>
                            <td class="p-3 border border-gray-300 whitespace-nowrap">
                                {{ optional($score->teacher)->teacher_name ?? 'ไม่ทราบ' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500 text-sm">ไม่มีข้อมูลคะแนน</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 max-w-full sm:max-w-4xl mx-auto text-center">
            {{ $scores->links() }}
        </div>

    </div>

</body>

</html>