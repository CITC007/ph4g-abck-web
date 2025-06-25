<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เพิ่มคะแนนนักเรียน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-gray-100  bg-cover bg-fixed bg-no-repeat p-4"
      x-data="{
        show: false,
        studentId: null,
        studentName: '',
        isBulk: false,
        showLogin: {{ session()->has('teacher_name') ? 'false' : 'true' }},
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
        }
    }"
    x-init="
        $watch('show', value => {
            if (!value) {
                document.getElementById('select-all').checked = false;
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            }
        });
    "
>
<div class="fixed  bottom-0 left-0 w-full h-full pointer-events-none" aria-hidden="true">
        <img src="/images/bg-hearts-light.png" alt="bg hearts" class="w-full h-full object-cover  rounded-xl
        p-4 sm:p-6 rounded-xl shadow-md max-w-8xl mx-auto
        " />
    </div>
    <div class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md max-w-6xl mx-auto">

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
                    'อนุบาลห้อง1', 'อนุบาลห้อง2', 'อนุบาลห้อง3', 'อนุบาลห้อง4',
                    'ป.1/1','ป.1/2','ป.1/3','ป.1/4',
                    'ป.2/1','ป.2/2','ป.2/3','ป.2/4',
                    'ป.3/1','ป.3/2','ป.3/3','ป.3/4',
                    'ป.4/1','ป.4/2','ป.4/3','ป.4/4',
                    'ป.5/1','ป.5/2','ป.5/3','ป.5/4',
                    'ป.6/1','ป.6/2','ป.6/3','ป.6/4',
                ] as $room)
                    <option value="{{ $room }}">{{ $room }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded w-full sm:w-auto text-sm sm:text-base">ค้นหา</button>
        </form>
        @endif

        @if(session()->has('teacher_name'))
            @if(count($students) > 0)
                <form id="bulk-score-form" method="POST" action="{{ route('score-entry.save') }}" 
                      @submit.prevent="prepareBulkAndSubmit" 
                      class="overflow-x-auto">
                    @csrf
                    <table class="w-full table-fixed border border-collapse border-gray-300 text-xs sm:text-sm bg-white rounded shadow-sm">
    <thead class="bg-purple-100 text-purple-800">
        <tr>
            <th class="w-[10%] p-1 border text-center">
                <input type="checkbox" id="select-all"
                    @click="const checked = $event.target.checked;
                            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);"
                >
            </th>
            <th class="w-[15%] p-1 border text-center">เพิ่ม</th>
            <th class="w-[40%] p-1 border text-center">ชื่อ</th>
            <th class="w-[20%] p-1 border text-center">ห้อง</th>
            <th class="w-[15%] p-1 border text-center">คะแนน</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
            <tr class="hover:bg-purple-50">
                <td class="p-1 border text-center">
                    <input type="checkbox" name="selected_students[]" value="{{ $student->id }}" class="student-checkbox">
                </td>
                <td class="p-1 border text-center">
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
                <td class="p-1 border text-center truncate">
                    <a href="{{ url('/student-history/' . $student->id) }}" class="text-blue-600 hover:underline">
                        {{ $student->student_name }}
                    </a>
                </td>
                <td class="p-1 border text-center">{{ $student->class_room }}</td>
                <td class="p-1 border text-center">{{ $student->scores_sum_point ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


                    <button type="button"
                        @click="isBulk = true; studentId = null; studentName = ''; show = true;"
                        class="mt-4 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 w-full sm:w-auto text-sm sm:text-base">
                        เพิ่มคะแนนให้รายการที่เลือก
                    </button>
                </form>
            @else
                <p class="text-gray-500 text-sm sm:text-base">ไม่พบนักเรียนในชั้นเรียนนี้ หรือยังไม่มีข้อมูล</p>
            @endif
        @endif
    </div>

    <!-- Modal เพิ่มคะแนน -->
    <div x-show="show" x-cloak class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl overflow-auto max-h-[90vh]">
        <h2 class="text-xl font-bold mb-4">
            เพิ่มคะแนน: <span x-text="isBulk ? 'รายการนักเรียนที่เลือก' : studentName"></span>
        </h2>
        <form method="POST" action="{{ route('score-entry.save') }}" @submit.prevent="prepareBulkAndSubmit($event)">
            @csrf
            <template x-if="!isBulk">
                <input type="hidden" name="student_id" :value="studentId">
            </template>

            <div class="mb-4">
                <label class="block font-medium mb-1">เหตุผลที่ให้คะแนน:</label>
                <textarea name="reason" required maxlength="255" rows="3"
                          class="w-full border p-2 rounded text-sm sm:text-base"></textarea>
            </div>

            <!-- ปุ่มจัดแนวให้ปิดอยู่ล่างบนมือถือ -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button type="button"
                        @click="show = false"
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


    <!-- Popup Login -->
    <div x-show="showLogin" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white/90 backdrop-blur-md p-6 rounded-xl shadow-xl w-full max-w-sm overflow-auto max-h-[90vh]">
        <h2 class="text-xl font-bold text-purple-800 mb-4 text-center">เข้าสู่ระบบโดยใช้ชื่อครู ?</h2>
        <form action="{{ route('teacher.auth.login') }}" method="POST" class="flex flex-col gap-4">
            @csrf

            <div>
                <label class="block font-medium mb-1 text-gray-700">ชื่อครู</label>
                <input type="text" name="teacher_name" required
                    class="w-full p-2 border rounded text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-purple-400"
                    value="{{ old('teacher_name') }}" />
            </div>

            <div>
                <label class="block font-medium mb-1 text-gray-700">ครูประจำชั้นเรียน</label>
                <select name="class_room" required
                    class="w-full p-2 border rounded text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-purple-400">
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
                        <option value="{{ $room }}" {{ old('class_room') == $room ? 'selected' : '' }}>{{ $room }}</option>
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


</body>
</html>
