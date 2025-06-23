<!-- resources/views/teacher-login.blade.php -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบครู</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h1 class="text-xl font-bold mb-4 text-center">เข้าสู่ระบบครู</h1>

        @if(session('error'))
            <div class="mb-3 p-3 bg-red-100 text-red-700 rounded border border-red-300">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.auth.login') }}">
            @csrf

            <div class="mb-4">
                <label for="teacher_name" class="block mb-1 font-medium">ชื่อครู</label>
                <input type="text" id="teacher_name" name="teacher_name" required class="w-full p-2 border rounded" value="{{ old('teacher_name') }}">
            </div>

            <div class="mb-4">
                <label for="class_room" class="block mb-1 font-medium">ชั้นเรียน</label>
                <select id="class_room" name="class_room" required class="w-full p-2 border rounded">
                    <option value="">-- เลือกชั้นเรียน --</option>
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

            <div class="flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">กลับหน้าแรก</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">เข้าใช้งาน</button>
            </div>
        </form>
    </div>

</body>
</html>
