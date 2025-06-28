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
        @forelse($topScores as $classRoom => $data)
            <tr>
                <td>{{ $classRoom }}</td>
                <td style="text-align: cernter;">
                    @if($data)
                        @php $count = count($data['students']); @endphp
                        @if($count === 1)
                            {{ collect($data['students'])->first()->student_name }}
                        @else
                            @foreach($data['students'] as $index => $student)
                                {{ $index + 1 }}. {{ $student->student_name }}<br>
                            @endforeach
                        @endif
                    @else
                        <span style="text-align: cernter">-------------------ไม่มีผลคะแนน------------------</span>
                    @endif
                </td>
                <td>
                    {{ $data ? $data['total_points'] : 0 }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan=" 3">ไม่มีข้อมูลคะแนนในเดือนนี้
                </td>
            </tr>
        @endforelse
    </tbody>
</table>