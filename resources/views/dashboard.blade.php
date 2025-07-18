<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>หัวใจสีชมพูเชิดชูความดี</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
</head>

<body class="min-h-screen bg-gray-100 bg-fixed bg-no-repeat bg-center p-4">
    <div class="fixed bottom-0 left-0 w-full h-full pointer-events-none" aria-hidden="true">
        <img src="/images/bg-hearts-light.png" alt="bg hearts"
            class="w-full h-full object-cover rounded-xl p-auto sm:p-6 rounded-xl shadow-md max-w-5xl mx-auto" />
    </div>

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

    <div class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md max-w-5xl mx-auto">

        <!-- ปุ่ม -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <!-- โลโก้โรงเรียน -->
                <div class="hidden sm:block self-start ml-1">
                    <img src="/images/school-logo.png" alt="โลโก้โรงเรียน"
                        class="h-28 w-auto transition-transform duration-300 hover:scale-105 hover:brightness-110 hover:drop-shadow-md" />
                </div>
                <!-- ปุ่มต่างๆ -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mt-4 sm:mt-0">
                    <a href="{{ url('/score-entry') }}"
                        class="px-4 py-2 text-white rounded hover:brightness-110 transition text-center flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap"
                        style="background-color: #FF9898;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="heartbeat w-5 h-5 shrink-0">
                            <path
                                d="m9.653 16.915-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
                        </svg>
                        <span>เพิ่มคะแนนหัวใจให้นักเรียน</span>
                    </a>

                    <a href="{{ route('report.top_scores') }}"
                        class="whitespace-nowrap px-4 py-2 text-white rounded hover:brightness-110 transition text-center text-sm sm:text-base"
                        style="background-color: #9B7EBD;">
                        รายงานคะแนนสูงสุดแต่ละเดือน
                    </a>

                    <a href="{{ route('report.class_scores') }}"
                        class="whitespace-nowrap px-4 py-2 text-white rounded hover:brightness-110 transition text-center text-sm sm:text-base"
                        style="background-color: #7F55B1;">
                        รายงานคะแนนรายชั้นเรียน
                    </a> <a href="{{ route('certificates.index') }}"
                        class="hidden sm:block whitespace-nowrap px-4 py-2 text-white rounded hover:brightness-110 transition text-center text-sm sm:text-base"
                        style="background-color:rgb(99, 42, 168);">
                        ออกใบประกาศ
                    </a>
                </div>
            </div>
        </div>

        <!-- หัวเรื่อง -->
        <h1 class="text-lg sm:text-2xl font-bold text-center mb-6 text-purple-700 leading-relaxed">
            รายการความดีนักเรียนที่ได้คะแนนหัวใจสูงสุด ประจำเดือน
            <span class="text-blue-500">{{ $monthName }}</span>
            พ.ศ. <span class="text-blue-500">{{ $yearBuddhist }}</span>
        </h1>

        <!-- ตาราง -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-center shadow-sm rounded-xl overflow-hidden bg-white">
                <thead class="bg-purple-100 text-purple-800 text-sm sm:text-base">
                    <tr>
                        <th class="p-3 border">ห้องเรียน</th>
                        <th class="p-3 border">ชื่อนักเรียน</th>
                        <th class="p-3 border">
                            <div class="inline-flex items-center justify-center gap-1">
                                <span>คะแนน</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#ec4899"
                                    class="w-5 h-5 shrink-0">
                                    <path
                                        d="m9.653 16.915-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
                                </svg>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topScores as $item)
                        <tr class="hover:bg-purple-50 transition-colors text-sm sm:text-base">
                            <td class="p-3 border">{{ $item['class_room'] }}</td>
                            <td class="p-3 border {{ $item['total_points'] == 0 ? 'text-gray-500 italic' : '' }}">
                                {{ $item['student_name'] }}
                            </td>
                            <td
                                class="p-3 border font-semibold {{ $item['total_points'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $item['total_points'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-gray-500">ยังไม่มีข้อมูลคะแนนในเดือนนี้</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>