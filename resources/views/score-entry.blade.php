<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เพิ่มคะแนนนักเรียน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <style>
        [x-cloak] { display: none !important; }
        /* เพิ่ม font-size ที่เล็กกว่า text-xs หากยังไม่มีใน tailwind.config.js */
        .text-xxs {
            font-size: 0.65rem; /* ~10.4px */
        }

        /* Responsive Table Adjustments */
        /* สำหรับหน้าจอเล็กกว่า sm breakpoint (640px) */
        @media (max-width: 639px) {
            .responsive-table {
                /* ทำให้ตารางบีบอัดให้พอดีจอ ไม่ให้มี horizontal scroll */
                width: 100%; /* สำคัญมากเพื่อให้ตารางเต็มความกว้างที่มี */
                table-layout: fixed; /* บังคับให้ความกว้างคอลัมน์คงที่ */
            }
            .responsive-table th,
            .responsive-table td {
                padding: 0.5rem; /* ลด padding ลงเล็กน้อยบนมือถือ */
                font-size: 0.75rem; /* text-xs (12px) - ใช้ Tailwind class แทนได้ใน HTML*/
            }
            /* กำหนดความกว้างของแต่ละคอลัมน์บนมือถือ */
            .responsive-table th:nth-child(1), .responsive-table td:nth-child(1) { /* Checkbox */
                width: 8%; /* ลดพื้นที่ให้ Checkbox */
            }
            .responsive-table th:nth-child(2), .responsive-table td:nth-child(2) { /* เพิ่ม */
                width: 15%; /* พื้นที่สำหรับปุ่มหัวใจ */
            }
            .responsive-table th:nth-child(3), .responsive-table td:nth-child(3) { /* ชื่อ */
                width: 47%; /* เพิ่มพื้นที่ให้ชื่อนักเรียน แต่ยังต้องมีการตัดคำ */
            }
            .responsive-table th:nth-child(4), .responsive-table td:nth-child(4) { /* ห้อง */
                width: 15%; /* ห้องเรียน */
            }
            .responsive-table th:nth-child(5), .responsive-table td:nth-child(5) { /* คะแนน */
                width: 15%; /* คะแนน */
            }
            /* ตรวจสอบให้แน่ใจว่ารวมกันแล้วได้ประมาณ 100% (8+15+47+15+15 = 100%) */
        }
    </style>
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

{{-- เพิ่ม padding-bottom ให้ body เพื่อเว้นที่ให้ปุ่ม fixed บนมือถือเท่านั้น --}}
<body class="min-h-screen bg-gray-100 bg-cover bg-fixed bg-no-repeat p-4 pb-20 sm:pb-4"
      x-data="{
        show: false,
        studentId: null,
        studentName: '',
        isBulk: false,
        reasonText: '',
        showLogin: {{ session()->has('teacher_name') ? 'false' : 'true' }},
        // เพิ่มตัวแปรนี้สำหรับควบคุมการแสดงผลส่วนเลือกนักเรียนดีเด่น
        showTopStudentSelection: {{ $topStudentsInClass->isNotEmpty() && !$topStudentAwarded ? 'true' : 'false' }},
        prepareBulkAndSubmit(e) {
            if (this.isBulk) {
                const selected = document.querySelectorAll('input[name=\'selected_students[]\']:checked');
                if (selected.length === 0) {
                    alert('กรุณาเลือกนักเรียนอย่างน้อย 1 คนก่อนเพิ่มคะแนน');
                    return;
                }
                selected.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_students[]';
                    input.value = cb.value;
                    e.target.appendChild(input);
                });
            }
            e.target.submit();
        },
        checkAndShowBulkModal() {
            const selectedStudents = document.querySelectorAll('.student-checkbox:checked');
            if (selectedStudents.length === 0) {
                alert('กรุณาเลือกนักเรียนอย่างน้อย 1 คนก่อนเพิ่มคะแนน');
            } else {
                this.isBulk = true;
                this.studentId = null;
                this.studentName = '';
                this.show = true;
                this.reasonText = '';
            }
        }
      }"
      x-init="
        $watch('show', value => {
            if (!value) {
                document.getElementById('select-all').checked = false;
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
                this.reasonText = '';
            }
        });
      "
>
    {{-- Background Image (Layer 0) --}}
    <div class="fixed bottom-0 left-0 w-full h-full pointer-events-none" aria-hidden="true">
        <img src="/images/bg-hearts-light.png" alt="bg hearts" class="w-full h-full object-cover rounded-xl" />
    </div>

    {{-- Main Content Area (Layer 2 - Table and other content) --}}
    <div class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md max-w-6xl mx-auto relative z-10">

        <a href="{{ route('dashboard') }}"
           class="inline-block mb-4 px-3 py-1.5 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 transition text-xs sm:text-sm">
            ← กลับหน้าแรก
        </a>

        <h1 class="text-lg sm:text-2xl font-bold text-center text-purple-700 mb-6">เพิ่มคะแนนให้นักเรียน</h1>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800 text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded text-red-800 text-sm sm:text-base">
                {{ session('error') }}
            </div>
        @endif

        @if(session()->has('teacher_name'))
            <div class="flex flex-col items-end mb-4 p-3 bg-green-50 rounded border border-green-300 text-sm sm:text-base">
                <p class="text-gray-700 text-right">
                    ครูผู้ใช้งาน: <strong class="text-blue-600">{{ session('teacher_name') }}</strong> ({{ session('teacher_class_room') }})
                </p>
                <form action="{{ route('teacher.auth.logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm sm:text-base">
                        ออกจากระบบ
                    </button>
                </form>
            </div>
        @endif

        @if(session()->has('teacher_name'))
            <form action="{{ route('score-entry.search') }}" method="POST"
                  class="flex flex-col sm:flex-row sm:items-center sm:gap-3 mb-4">
                @csrf
                <input type="text" name="keyword" placeholder="ค้นหาชื่อนักเรียนหรือรหัส"
                       class="p-2 border rounded w-full sm:w-64 mb-2 sm:mb-0 text-sm sm:text-base" />
                <select name="class_room" class="p-2 border rounded w-full sm:w-auto mb-2 sm:mb-0 text-sm sm:text-base">
                    <option value="">-- ทุกชั้นเรียน --</option>
                    @foreach([
                        'ป.1/1','ป.1/2','ป.1/3','ป.1/4',
                        'ป.2/1','ป.2/2','ป.2/3','ป.2/4',
                        'ป.3/1','ป.3/2','ป.3/3','ป.3/4',
                        'ป.4/1','ป.4/2','ป.4/3','ป.4/4',
                        'ป.5/1','ป.5/2','ป.5/3','ป.5/4',
                        'ป.6/1','ป.6/2','ป.6/3','ป.6/4',
                    ] as $room)
                        <option value="{{ $room }}" {{ $classRoom == $room ? 'selected' : '' }}>{{ $room }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded w-full sm:w-auto text-sm sm:text-base">ค้นหา</button>
            </form>
        @endif

        {{--- ส่วนใหม่: การเลือกนักเรียนดีเด่นกรณีคะแนนสูงสุดซ้ำ ---}}
        @if(session()->has('teacher_name'))
            <div x-show="showTopStudentSelection" x-cloak
                 class="mb-6 p-4 border border-blue-300 bg-blue-50 rounded-lg shadow-md">
                <h2 class="text-lg font-bold text-blue-700 mb-3 text-center">
                    เลือกนักเรียนดีเด่นประจำเดือน {{ $previousMonthYearForDisplay }} ({{ session('teacher_class_room') }})
                </h2>
                @if($topStudentAwarded)
                    <p class="text-green-600 font-semibold text-center py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 align-middle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        ห้องเรียนของคุณได้มอบรางวัลนักเรียนดีเด่นสำหรับเดือนนี้เรียบร้อยแล้ว
                    </p>
                @elseif($topStudentsInClass->isNotEmpty())
                    <p class="text-gray-700 mb-4 text-center">
                        พบนักเรียนที่ได้คะแนนความดีสูงสุดเท่ากันจำนวน <strong class="text-blue-600">{{ $topStudentsInClass->count() }}</strong> คน
                        โปรดเลือก <strong class="text-purple-600">1 คน</strong> เพื่อรับรางวัลนักเรียนดีเด่น:
                    </p>
                    <form action="{{ route('score-entry.select-top-student') }}" method="POST">
                        @csrf
                        <input type="hidden" name="class_room" value="{{ session('teacher_class_room') }}">
                        <div class="space-y-3 mb-4">
                            @foreach($topStudentsInClass as $student)
                                <label class="flex items-center p-3 bg-white border border-gray-200 rounded-md shadow-sm cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="selected_top_student_id" value="{{ $student->id }}" required
                                           class="form-radio text-blue-600 h-5 w-5 mr-3">
                                    <span class="text-gray-800 font-medium flex-grow">{{ $student->student_name }}</span>
                                    <span class="ml-auto text-blue-600 font-bold">{{ $student->scores_sum_point }} คะแนน</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            <button type="submit"
                                    class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 text-sm sm:text-base w-full sm:w-auto">
                                ยืนยันการเลือกนักเรียน
                            </button>
                        </div>
                    </form>
                @else
                    {{-- อันนี้จะถูกซ่อนด้วย x-show หาก topStudentsInClass->isNotEmpty() เป็น false หรือ topStudentAwarded เป็น true --}}
                    {{-- แต่ถ้า x-show เป็น false อยู่แล้ว ก็จะไม่แสดงเลย --}}
                    {{-- เพิ่มข้อความที่เหมาะสมหากไม่มีนักเรียนที่เข้าเงื่อนไขให้เลือก --}}
                    @if(!$topStudentAwarded && $classRoom && $students->isNotEmpty())
                        <p class="text-gray-600 text-center py-2">
                            ไม่มีนักเรียนที่มีคะแนนสูงสุดซ้ำกันในห้องของคุณสำหรับเดือนนี้ หรือยังไม่มีนักเรียนที่มีคะแนนสะสมที่สามารถเป็นนักเรียนดีเด่นได้
                        </p>
                    @endif
                @endif
            </div>
        @endif
        {{--- สิ้นสุดส่วนใหม่ ---}}

        @if(session()->has('teacher_name'))
            @if(count($students) > 0)
                {{-- ลบ overflow-x-auto จาก div ที่ครอบตาราง --}}
                <div class="rounded-xl overflow-hidden shadow-sm">
                    {{-- เพิ่ม class responsive-table และ table-fixed --}}
                    <table class="w-full border-collapse border border-gray-300 text-center bg-white responsive-table">
                        <thead class="bg-purple-100 text-purple-800 text-sm sm:text-base">
                            <tr>
                                <th class="p-3 border">
                                    <input type="checkbox" id="select-all"
                                        @click="const checked = $event.target.checked;
                                                    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);"
                                    >
                                </th>
                                <th class="p-3 border">เพิ่ม</th>
                                <th class="p-3 border">ชื่อ</th>
                                <th class="p-3 border">ห้อง</th>
                                <th class="p-3 border">คะแนน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr class="hover:bg-purple-50 transition-colors text-sm sm:text-base">
                                    <td class="p-3 border">
                                        <input type="checkbox" name="selected_students[]" value="{{ $student->id }}" class="student-checkbox">
                                    </td>
                                    <td class="p-3 border">
                                        <button type="button"
                                            @click="show = true; studentId = {{ $student->id }}; studentName = '{{ $student->student_name }}'; isBulk = false"
                                            class="text-pink-500 hover:text-pink-700" title="เพิ่มคะแนน">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312
                                                          2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0
                                                          7.22 9 12 9 12s9-4.78 9-12Z" />
                                            </svg>
                                        </button>
                                    </td>
                                    {{-- ใช้ whitespace-nowrap overflow-hidden text-ellipsis ที่นี่ --}}
                                    <td class="p-3 border whitespace-nowrap overflow-hidden text-ellipsis">
                                        <a href="{{ url('/student-history/' . $student->id) }}" class="text-blue-600 hover:underline">
                                            {{ $student->student_name }}
                                        </a>
                                    </td>
                                    <td class="p-3 border">{{ $student->class_room }}</td>
                                    <td class="p-3 border {{ $student->scores_sum_point > 0 ? 'text-green-600' : 'text-gray-400' }} ">{{ $student->scores_sum_point ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Pagination Links --}}
                    @if(method_exists($students, 'links'))
                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
                {{-- Placeholder สำหรับปุ่มเพิ่มคะแนนแบบกลุ่มบน PC --}}
                {{-- จะแสดงเมื่อหน้าจอใหญ่กว่า sm: (640px) ขึ้นไป --}}
                <div class="hidden sm:block mt-4 max-w-6xl mx-auto">
                    <button type="button" form="bulk-score-form"
                            @click="isBulk = true; studentId = null; studentName = ''; show = true;"
                            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 w-full text-sm sm:text-base">
                        เพิ่มคะแนนในรายการที่เลือก
                    </button>
                </div>
            @else
                <p class="text-gray-500 text-sm sm:text-base">ไม่พบข้อมูลนักเรียน กรุณากดเลือกชั้นเรียนหรือกรอกคำค้นเพื่อแสดงผล</p>
            @endif
        @endif
    </div>

    {{-- ปุ่ม "เพิ่มคะแนนให้รายการที่เลือก" (Fixed Button สำหรับ Mobile) --}}
    {{-- จะแสดงเมื่อหน้าจอเล็กกว่า sm: (640px) เท่านั้น --}}
    @if(session()->has('teacher_name') && count($students) > 0)
        <div class="fixed bottom-0 left-0 w-full p-4 bg-white/90 backdrop-blur-sm z-20 sm:hidden">
            <button type="button" form="bulk-score-form"
                    @click="checkAndShowBulkModal()"
                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 w-full text-sm sm:text-base">
                เพิ่มคะแนนให้รายการที่เลือก
            </button>
        </div>
    @endif


    <div x-show="show" x-cloak
      class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl overflow-auto max-h-[90vh]">
            <h2 class="text-xl font-bold mb-4">
                เพิ่มคะแนน: <span x-text="isBulk ? 'รายการนักเรียนที่เลือก' : studentName"></span>
            </h2>
            <form id="bulk-score-form" method="POST" action="{{ route('score-entry.save') }}"
                  @submit.prevent="prepareBulkAndSubmit($event)">
                @csrf
                <template x-if="!isBulk">
                    <input type="hidden" name="student_id" :value="studentId">
                </template>

                <div class="mb-4">
                    <label class="block font-medium mb-1">เหตุผลที่ให้คะแนน:</label>
                    <textarea name="reason" x-model="reasonText" required maxlength="255" rows="3"
                              class="w-full border p-2 rounded text-sm sm:text-base"></textarea>

                    <div class="mt-2 flex flex-wrap gap-2">
                        <button type="button" @click="reasonText = 'มีความซื่อสัตย์'"
                                class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs hover:bg-blue-200 transition">
                            มีความซื่อสัตย์
                        </button>
                        <button type="button" @click="reasonText = 'มีจิตสาธารณะ'"
                                class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs hover:bg-green-200 transition">
                            มีจิตสาธารณะ
                        </button>
                        <button type="button" @click="reasonText = 'ทำความสะอาดห้องเรียน'"
                                class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs hover:bg-yellow-200 transition">
                            ทำความสะอาดห้องเรียน
                        </button>
                        <button type="button" @click="reasonText = 'ส่งงานตรงเวลา'"
                                class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs hover:bg-purple-200 transition">
                            ส่งงานตรงเวลา
                        </button>
                        <button type="button" @click="reasonText = 'มีความเอื้อเฟื้อเผื่อแผ่'"
                                class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs hover:bg-red-200 transition">
                            มีความเอื้อเฟื้อเผื่อแผ่
                        </button>
                        <button type="button" @click="reasonText = 'ตั้งใจเรียน'"
                                class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs hover:bg-indigo-200 transition">
                            ตั้งใจเรียน
                        </button>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2">
                    <button type="button" @click="show = false"
                            class="px-4 py-2 bg-gray-300 rounded w-full sm:w-auto text-sm sm:text-base">
                        ปิด
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full sm:w-auto text-sm sm:text-base">
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (!session('teacher_id'))
        <div x-show="showLogin" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white/90 backdrop-blur-md p-6 rounded-xl shadow-xl w-full max-w-sm overflow-auto max-h-[90vh]">
                <h2 class="text-xl font-bold text-purple-800 mb-4 text-center">เข้าสู่ระบบโดยเลือกชื่อครู</h2>

                <form action="{{ route('teacher.auth.login') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="block font-medium mb-1 text-gray-700">ชื่อครู</label>
                        <select name="teacher_id" required
                                class="w-full p-2 border rounded text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <option value="" disabled selected>-- เลือกชื่อครู --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->class_room }} ({{ $teacher->teacher_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($errors->any())
                        <div class="text-red-600 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 flex-wrap">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full sm:w-auto text-sm sm:text-base transition">
                            เข้าใช้งาน
                        </button>
                        <a href="{{ route('dashboard') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 w-full sm:w-auto text-sm sm:text-base transition text-center">
                            ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif
</body>
</html>