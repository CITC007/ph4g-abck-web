<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>รายงานคะแนนนักเรียนรายชั้น</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
</head>

<body class="min-h-screen bg-gray-100 bg-fixed bg-no-repeat bg-center p-4">
    <div class="fixed bottom-0 left-0 w-full h-full pointer-events-none" aria-hidden="true">
        <img src="/images/bg-hearts-light.png" alt="bg hearts" class="w-full h-full object-cover rounded-xl
        p-auto sm:p-6 rounded-xl shadow-md max-w-5xl mx-auto" />
    </div>

    <div class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md max-w-5xl mx-auto">

        <a href="{{ route('dashboard') }}"
            class="inline-block mb-4 px-3 py-1.5 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 transition text-xs sm:text-sm">
            ← กลับหน้าแรก
        </a>

        <h1 class="text-lg sm:text-2xl font-bold mb-6 text-purple-700 text-center leading-relaxed">
            รายงานผลคะแนนประจำชั้นเรียน
        </h1>

        <form method="GET" action="{{ route('report.class_scores') }}"
            class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 mb-6 bg-white/90 p-4 rounded shadow-sm">

            <label for="class_room" class="w-full sm:w-auto flex flex-col gap-1">
                <span class="font-medium text-gray-700">ชั้นเรียน:</span>
                <select name="class_room" id="class_room" class="p-2 border rounded text-sm" required>
                    <option value="" disabled selected>-- เลือกชั้นเรียน --</option>
                    @foreach([
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
            </label>

            <label for="month" class="w-full sm:w-auto flex flex-col gap-1">
                <span class="font-medium text-gray-700">เดือน:</span>
                <select name="month" id="month" class="p-2 border rounded text-sm">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('th')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </label>

            <label for="year" class="w-full sm:w-auto flex flex-col gap-1">
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
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 w-full sm:w-auto text-sm sm:text-base transition">
                    ค้นหา
                </button>

                <a href="{{ route('report.class-scores.download', [
                    'class_room' => request('class_room'),
                    'month' => request('month'),
                    'year' => request('year'),
                    'format' => 'xlsx'
                ]) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full sm:w-auto text-sm sm:text-base text-center transition">
                    ดาวน์โหลด Excel
                </a>

                <a href="{{ route('report.class-scores.download', [
                    'class_room' => request('class_room'),
                    'month' => request('month'),
                    'year' => request('year'),
                    'format' => 'pdf'
                ]) }}"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 w-full sm:w-auto text-sm sm:text-base text-center transition">
                    ดาวน์โหลด PDF
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            @if(isset($classScores) && count($classScores) > 0)
                <table
                    class="w-full border border-gray-300 text-center shadow-sm rounded-xl overflow-hidden bg-white text-sm sm:text-base">
                    <thead class="bg-purple-100 text-purple-800">
                        <tr>
                            <th class="p-3 border w-1/6">เลขที่</th>
                            <th class="p-3 border break-words">ชื่อนักเรียน 
                              @if(isset($classScores) && count($classScores) > 0)
                                (ชั้น {{ $classScores->first()->class_room }})
                            @endif
                            </th>
                            <th class="p-3 border w-1/6 hidden sm:table-cell">ชั้นเรียน</th>
                            <th class="p-3 border w-1/6">คะแนนสะสม</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classScores as $student)
                            <tr class="hover:bg-purple-50 transition-colors">
                                <td class="p-3 border break-words">{{ $student->student_number }}</td>
                                <td class="p-3 border break-words">{{ $student->student_name }}</td>
                                <td class="p-3 border hidden sm:table-cell">{{ $student->class_room }}</td>
                                <td class="p-3 border font-semibold text-green-600">{{ $student->scores_sum_point ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center text-sm mt-4">ไม่มีข้อมูลนักเรียนในชั้นเรียนนี้ หรือยังไม่เลือกชั้นเรียน</p>
            @endif
        </div>

    </div>

</body>

</html>
