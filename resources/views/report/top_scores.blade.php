<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>รายงานคะแนนสูงสุดแต่ละเดือน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
</head>

<body class="min-h-screen bg-gray-100 bg-fixed bg-no-repeat bg-center p-4">
    <div class="fixed  bottom-0 left-0 w-full h-full pointer-events-none" aria-hidden="true">
        <img src="/images/bg-hearts-light.png" alt="bg hearts" class="w-full h-full object-cover  rounded-xl
        p-auto sm:p-6 rounded-xl shadow-md max-w-5xl mx-auto
        " />
    </div>

    <div class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md max-w-5xl mx-auto">

        <a href="{{ route('dashboard') }}"
            class="inline-block mb-4 px-3 py-1.5 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 transition text-xs sm:text-sm">
            ← กลับหน้าแรก
        </a>

        <h1 class="text-lg sm:text-2xl font-bold mb-6 text-purple-700 text-center leading-relaxed">
            รายงานคะแนนสูงสุดแต่ละเดือน
        </h1>

        <form method="GET" action="{{ route('report.top_scores') }}"
            class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 mb-6 bg-white/90 p-4 rounded shadow-sm">

            <label for="month" class="flex flex-col gap-1 w-full sm:w-auto">
                <span class="font-medium text-gray-700">เดือน:</span>
                <select name="month" id="month" class="p-2 border rounded text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('th')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </label>

            <label for="year" class="flex flex-col gap-1 w-full sm:w-auto">
                <span class="font-medium text-gray-700">ปี:</span>
                <select name="year" id="year" class="p-2 border rounded text-sm">
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                        <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y + 543 }}
                        </option>
                    @endfor
                </select>
            </label>

            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto text-sm sm:text-base transition">
                    ค้นหา
                </button>

                <a href="{{ route('report.top_scores_export', [
    'month' => request('month', date('n')),
    'year' => request('year', date('Y')),
    'format' => 'xlsx'
]) }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 w-full sm:w-auto text-sm sm:text-base text-center transition">
                    ดาวน์โหลด Excel
                </a>

                <a href="{{ route('report.top_scores_export', [
    'month' => request('month', date('n')),
    'year' => request('year', date('Y')),
    'format' => 'pdf'
]) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 w-full sm:w-auto text-sm sm:text-base text-center transition">
                    ดาวน์โหลด PDF
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            @if(isset($topScores) && count($topScores) > 0)
                <table
                    class="w-full border border-gray-300 text-center shadow-sm rounded-xl overflow-hidden bg-white text-sm sm:text-base">
                    <thead class="bg-purple-100 text-purple-800">
                        <tr>
                            <th class="p-3 border w-1/4">ชั้น</th>
                            <th class="p-3 border">นักเรียน</th>
                            <th class="p-3 border w-1/4">คะแนน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topScores as $score)
                            <tr class="hover:bg-purple-50 transition-colors">
                                <td class="p-3 border break-words">{{ $score->class_room }}</td>
                                <td class="p-3 border break-words">{{ $score->student_name }}</td>
                                <td class="p-3 border font-semibold text-green-600">{{ $score->total_points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 mt-4 text-center text-sm">ไม่มีข้อมูลคะแนนสูงสุดในเดือนนี้</p>
            @endif
        </div>

    </div>

</body>

</html>