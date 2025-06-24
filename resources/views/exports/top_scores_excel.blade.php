@php
    use Carbon\Carbon;
    $monthName = Carbon::createFromDate($year, $month, 1)->locale('th')->isoFormat('MMMM');
    $yearBuddhist = $year + 543;
@endphp

<h2 style="font-size:18pt; font-weight:bold; margin-bottom: 20px; font-family: 'Sarabun', sans-serif;">
    รายงานผลคะแนนของนักเรียนที่ได้รับคะแนนสูงสุด ประจำเดือน {{ $monthName }} พ.ศ. {{ $yearBuddhist }}
</h2>

<table>
    <thead>
        <tr>
            <th>ชั้นเรียน</th>
            <th>ชื่อนักเรียน</th>
            <th>คะแนนรวม</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topScores as $item)
            <tr>
                <td>{{ $item->class_room }}</td>
                <td>{{ $item->student_name }}</td>
                <td>{{ $item->total_points }}</td>
            </tr>
        @endforeach
    </tbody>
</table>