<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class TeacherAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $teacher = Teacher::find($request->teacher_id);

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
        return redirect()->route('dashboard');
    }
}
