<!DOCTYPE html>
<html>

<head>
    <title>ยืนยันตัวตนครู</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css') <!-- หากใช้ Tailwind -->
</head>

<body class="p-4">
    <h1 class="text-xl font-bold mb-4">เข้าใช้งานด้วยชื่อครู</h1>

    <form method="POST" action="/auth-teacher" class="space-y-4">
        @csrf

        <div>
            <label>ชื่อครู</label>
            <input type="text" name="teacher_name" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label>ชั้นเรียน</label>
            <select name="class_room" class="w-full border p-2 rounded" required>
                @for ($i = 1; $i <= 6; $i++)
                    @for ($j = 1; $j <= 4; $j++)
                        <option value="ป.{{$i}}/{{$j}}">ป.{{$i}}/{{$j}}</option>
                    @endfor
                @endfor
                <option value="อนุบาลห้อง1">อนุบาลห้อง1</option>
                <option value="อนุบาลห้อง2">อนุบาลห้อง2</option>
                <option value="อนุบาลห้อง3">อนุบาลห้อง3</option>
                <option value="อนุบาลห้อง4">อนุบาลห้อง4</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
            เข้าใช้งาน
        </button>
    </form>
</body>

</html>