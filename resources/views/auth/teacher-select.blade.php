<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เลือกชื่อครู</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>

<body class="p-4 bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-md rounded-xl p-6 w-full max-w-md space-y-4">
        <h1 class="text-2xl font-bold text-purple-700">เลือกชื่อครู</h1>
        <p class="text-gray-700">พบว่ามีครูในชั้นเรียน <span
                class="font-semibold text-blue-600">{{ $inputClass }}</span> แล้ว</p>
        <p class="text-sm text-gray-600 mb-4">กรุณาเลือกชื่อของคุณจากรายการด้านล่าง</p>

        <div class="space-y-2">
            @foreach($existingTeachers as $teacher)
                <form method="POST" action="{{ route('teacher.auth.login') }}">
                    @csrf
                    <input type="hidden" name="selected_teacher_id" value="{{ $teacher->id }}">
                    <input type="hidden" name="class_room" value="{{ $inputClass }}">
                    <input type="hidden" name="teacher_name" value="{{ $teacher->teacher_name }}">
                    <button type="submit"
                        class="w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        {{ $teacher->teacher_name }}
                    </button>
                </form>
            @endforeach
        </div>

        <div class="border-t pt-4 mt-4 text-gray-700">
            <p class="text-sm mb-2">ถ้าไม่ใช่ชื่อของคุณ ให้ใช้ชื่อใหม่แทน:</p>
            <form method="POST" action="{{ route('teacher.auth.login') }}">
                @csrf
                <input type="hidden" name="class_room" value="{{ $inputClass }}">
                <input type="hidden" name="teacher_name" value="{{ $inputName }}">
                <input type="hidden" name="confirm_use_new" value="1">
                <button type="submit"
                    class="w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    ใช้ชื่อใหม่: "{{ $inputName }}"
                </button>
            </form>
        </div>
    </div>

</body>

</html>