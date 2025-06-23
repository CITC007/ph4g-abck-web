<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class TeacherAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'class_room' => 'required|string|max:20',
        ]);

        $inputName = $request->teacher_name;
        $inputClass = $request->class_room;

        // ดึงครูที่อยู่ใน class_room นั้นๆ
        $teachers = Teacher::where('class_room', $inputClass)->get();

        $matchedTeacher = null;
        foreach ($teachers as $teacher) {
            similar_text(strtolower($teacher->teacher_name), strtolower($inputName), $percent);
            if ($percent > 10) {
                $matchedTeacher = $teacher;
                break;
            }
        }

        if ($matchedTeacher) {
            // ใช้ครูที่เจอ
            $teacher = $matchedTeacher;
        } else {
            // เพิ่มครูใหม่
            $teacher = Teacher::create([
                'teacher_name' => $inputName,
                'class_room' => $inputClass,
            ]);
        }

        // ตั้ง session
        session([
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->teacher_name,
            'teacher_class_room' => $teacher->class_room,
        ]);

        return redirect()->route('score-entry.form');
    }

    public function logout()
    {
        session()->forget(['teacher_id', 'teacher_name', 'teacher_class_room']);
        return redirect()->route('score-entry.form');
    }
}
