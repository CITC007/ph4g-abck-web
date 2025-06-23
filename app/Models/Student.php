<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_code',
        'student_name',
        'class_room',
    ];
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

}
