<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ClassScoresExport implements FromView
{
    protected $classScores;
    protected $class_room;
    protected $month;
    protected $year;

    public function __construct($classScores, $class_room, $month, $year)
    {
        $this->classScores = $classScores;
        $this->class_room = $class_room;
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        return view('exports.class_scores_excel', [
            'classScores' => $this->classScores,
            'class_room' => $this->class_room,
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }
}
