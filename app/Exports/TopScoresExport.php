<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TopScoresExport implements FromView
{
    protected $topScores;
    protected $month;
    protected $year;

    public function __construct($topScores, $month, $year)
    {
        $this->topScores = $topScores;
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        return view('exports.top_scores_excel', [
            'topScores' => $this->topScores,
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }
}
