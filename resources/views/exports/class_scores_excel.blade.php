@php
    use Carbon\Carbon;
    $monthName = Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM');
    $yearBuddhist = $year + 543;
@endphp

<h2 style="font-weight:bold; font-family:'Sarabun', sans-serif;">
    รายงานคะแนนนักเรียนรายชั้น {{ $class_room }} ประจำเดือน {{ $monthName }} พ.ศ. {{ $yearBuddhist }}
</h2>

<table>
    <thead>
        <tr>
            <th>รหัส</th>
            <th>ชื่อนักเรียน</th>
            <th>ชั้นเรียน</th>
            <th>คะแนนสะสม</th>
        </tr>
    </thead>
    <tbody>
        @foreach($classScores as $student)
            <tr>
                <td>{{ $student->student_code }}</td>
                <td>{{ $student->student_name }}</td>
                <td>{{ $student->class_room }}</td>
                <td>{{ $student->scores_sum_point ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>