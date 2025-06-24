<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เลือกชื่อครู</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>

<body class="p-4 bg-gray-50">

    <h1 class="text-xl font-bold mb-4">พบว่ามีครูในชั้นเรียน {{ $inputClass }} แล้ว</h1>
    <p class="mb-4">กรุณาเลือกชื่อของคุณจากรายการด้านล่าง (กดปุ่มชื่อเพื่อเข้าใช้งาน):</p>

    <div class="space-y-3">
        @foreach($existingTeachers as $teacher)
            <form method="POST" action="{{ route('teacher.auth.login') }}">
                @csrf
                <input type="hidden" name="selected_teacher_id" value="{{ $teacher->id }}">
                <input type="hidden" name="class_room" value="{{ $inputClass }}">
                <input type="hidden" name="teacher_name" value="{{ $teacher->teacher_name }}">
                <button type="submit" class=" px-4 py-2 bg-blue-600 text-white rounded w-3xs hover:bg-blue-700">
                    {{ $teacher->teacher_name }}
                </button>
            </form>
        @endforeach
    </div>

    <hr class="my-6">

    <p>ถ้าไม่ใช่ชื่อของคุณ กรุณากดปุ่มด้านล่างเพื่อลงชื่อด้วยชื่อใหม่:</p>

    <form method="POST" action="{{ route('teacher.auth.login') }}">
        @csrf
        <input type="hidden" name="class_room" value="{{ $inputClass }}">
        <input type="hidden" name="teacher_name" value="{{ $inputName }}">
        <input type="hidden" name="confirm_use_new" value="1">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mt-2">
            ใช้ชื่อใหม่: "{{ $inputName }}"
        </button>
    </form>

</body>

</html>