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

        // ถ้ามีการส่ง selected_teacher_id แสดงว่าผู้ใช้เลือกชื่อจากรายชื่อครูที่มีอยู่
        if ($request->has('selected_teacher_id')) {
            $teacher = Teacher::find($request->selected_teacher_id);
            if ($teacher) {
                session([
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->teacher_name,
                    'teacher_class_room' => $teacher->class_room,
                ]);
                return redirect()->route('score-entry.form');
            } else {
                return redirect()->back()->withErrors(['selected_teacher_id' => 'ครูที่เลือกไม่ถูกต้อง']);
            }
        }

        // กรณีผู้ใช้ลงชื่อใหม่ หรือยังไม่ได้เลือกชื่อในฐานข้อมูล
        $teachers = Teacher::where('class_room', $inputClass)->get();
        $matchedTeacher = null;
        $matchedPercent = 0;

        foreach ($teachers as $teacher) {
            similar_text(strtolower($teacher->teacher_name), strtolower($inputName), $percent);
            if ($percent > $matchedPercent) {
                $matchedPercent = $percent;
                $matchedTeacher = $teacher;
            }
        }

        if ($matchedPercent >= 70) {
            // เข้าระบบด้วยชื่อครูที่ตรงมากที่สุด
            $teacher = $matchedTeacher;

            session([
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->teacher_name,
                'teacher_class_room' => $teacher->class_room,
            ]);

            return redirect()->route('score-entry.form');
        }

        // ไม่เจอชื่อที่เหมือนมากพอ → ให้เลือกชื่อเองหรือลงทะเบียนใหม่
        return view('auth.teacher-select', [
            'inputName' => $inputName,
            'inputClass' => $inputClass,
            'existingTeachers' => $teachers,
        ]);
    }



    public function logout()
    {
        session()->forget(['teacher_id', 'teacher_name', 'teacher_class_room']);
        return redirect()->route('score-entry.form');
    }
}
