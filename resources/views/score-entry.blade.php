<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เพิ่มคะแนนนักเรียน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
@if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded text-red-800">
        {{ session('error') }}
    </div>
@endif

<body  class="p-4 bg-gray-50"
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
                    return; // หยุดไม่ให้ submit
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
        // เพิ่ม event listener สำหรับ select-all checkbox
        $watch('show', value => {
            if (!value) {
                // ถ้า modal ปิดแล้ว ยกเลิกเลือกทั้งหมด
                document.getElementById('select-all').checked = false;
                document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            }
        });
    "
>

<!-- ปุ่มกลับหน้าแรก -->
<a href="{{ route('dashboard') }}" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
    ← กลับหน้าแรก
</a>

<h1 class="text-2xl font-bold mb-4">เพิ่มคะแนนให้นักเรียน</h1>

<!-- Flash Message -->
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800">
        {{ session('success') }}
    </div>
@endif

<!-- แสดงชื่อครู -->
@if(session()->has('teacher_name'))
    <div class="mb-4 p-3 bg-green-50 rounded border border-green-300">
        <p class="text-lg text-gray-700">
            ครู: <strong>{{ session('teacher_name') }}</strong> ({{ session('teacher_class_room') }})
        </p>
        <form action="{{ route('teacher.auth.logout') }}" method="POST" class="inline-block mt-2">
            @csrf
            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">ออกจากระบบ</button>
        </form>
    </div>
@endif

<!-- ฟอร์มค้นหา -->
@if(session()->has('teacher_name'))
<form action="{{ route('score-entry.search') }}" method="POST" class="flex flex-wrap items-center gap-2 mb-4">
    @csrf
    <input type="text" name="keyword" placeholder="ค้นหาชื่อนักเรียนหรือรหัส" class="p-2 border rounded w-64" />
    <select name="class_room" class="p-2 border rounded">
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
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">ค้นหา</button>
</form>
@endif

<!-- ตารางนักเรียน -->
@if(session()->has('teacher_name'))
    @if(count($students) > 0)
        <form id="bulk-score-form" method="POST" action="{{ route('score-entry.save') }}" @submit.prevent="prepareBulkAndSubmit">
            @csrf
            <table class="w-full text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">
                            <input type="checkbox" id="select-all" 
                                @click="
                                    const checked = $event.target.checked;
                                    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);
                                "
                            >
                            เลือกทั้งหมด
                        </th>
                        <th class="p-2 border">รหัส</th>
                        <th class="p-2 border">ชื่อ</th>
                        <th class="p-2 border">ห้อง</th>
                        <th class="p-2 border">คะแนนสะสม</th>
                        <th class="p-2 border">ประวัติ</th>
                        <th class="p-2 border">เพิ่มคะแนน</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td class="p-2 border">
                                <input type="checkbox" name="selected_students[]" value="{{ $student->id }}" class="student-checkbox">
                            </td>
                            <td class="p-2 border">{{ $student->student_code }}</td>
                            <td class="p-2 border">{{ $student->student_name }}</td>
                            <td class="p-2 border">{{ $student->class_room }}</td>
                            <td class="p-2 border">{{ $student->scores_sum_point ?? 0 }}</td>
                            <td class="p-2 border">
                                <a href="{{ url('/student-history/' . $student->id) }}" class="text-blue-600 hover:underline">ดูประวัติ</a>
                            </td>
                            <td class="p-2 border">
                                <button type="button"
                                    @click="show = true; studentId = {{ $student->id }}; studentName = '{{ $student->student_name }}'; isBulk = false"
                                    class="text-green-600 hover:underline">เพิ่มคะแนน</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- ปุ่มเพิ่มคะแนนกลุ่ม -->
            <button type="button"
                @click="isBulk = true; studentId = null; studentName = ''; show = true;"
                class="mt-4 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                เพิ่มคะแนนให้รายการที่เลือก
            </button>
        </form>
    @else
        <p class="text-gray-500">ไม่พบนักเรียนในชั้นเรียนนี้ หรือยังไม่มีข้อมูล</p>
    @endif
@endif

<!-- Modal เพิ่มคะแนน -->
<div x-show="show" x-cloak
     class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl">
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
            <textarea name="reason" required maxlength="255" rows="3" class="w-full border p-2 rounded"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" @click="show = false" class="px-4 py-2 bg-gray-300 rounded">ปิด</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">บันทึก</button>
        </div>
    </form>
    </div>
</div>

<!-- Popup Login -->
<!-- Popup Login -->
<div x-show="showLogin" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4">เข้าสู่ระบบครู</h2>

        <form action="{{ route('teacher.auth.login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-1">ชื่อครู</label>
                <input type="text" name="teacher_name" required class="w-full p-2 border rounded"
                       value="{{ old('teacher_name') }}" />
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">ครูประจำชั้นเรียน</label>
                <select name="class_room" required class="w-full p-2 border rounded">
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
                <div class="mb-2 text-red-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-300 rounded inline-block text-center">ยกเลิก</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    เข้าใช้งาน
                </button>
            </div>
        </form>
    </div>
</div>


</body>
</html>
