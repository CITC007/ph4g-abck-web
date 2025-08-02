<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างใบประกาศ</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
    <style>
        /* Custom styles can be kept here if they are not easily covered by Tailwind */
        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
        }

        .table-auto {
            width: 100%;
            border-collapse: collapse;
        }

        .table-auto th,
        .table-auto td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }

        .table-auto th {
            background-color: #f8fafc;
            font-weight: bold;
        }

        /* Custom styles for button groups */
        .button-group {
            display: flex;
            /* Make it a flex container */
            flex-wrap: wrap;
            /* Allow items to wrap to next line if space is limited */
            gap: 1rem;
            margin-top: 1rem;
            padding: 1rem;
            /* Reduced padding for lower height */
            border-radius: 0.5rem;
            align-items: center;
            /* Vertically align items in the center */
            justify-content: space-between;
            /* Distribute space between items (heading and button group) */
        }

        .button-group.create-group {
            background-color: #f3e8ff;
            /* Light purple background */
            border: 1px solid #a78bfa;
            /* Medium purple border */
        }

        .button-group.download-group {
            background-color: #e0ffe0;
            /* Light green background */
            border: 1px solid #4ade80;
            /* Medium green border */
        }

        /* Ensure button sub-groups also have proper spacing */
        .button-group .flex.items-center {
            gap: 1rem;
            /* Apply gap within button sub-groups too */
            flex-wrap: wrap;
            /* Allow buttons to wrap within their sub-group if needed */
        }
    </style>
</head>

<body class="bg-gray-100 p-6">
    <a href="{{ route('dashboard') }}"
        class="inline-block mb-4 px-3 py-1.5 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 transition text-xs sm:text-sm">
        ← กลับหน้าแรก
    </a>

    <div class="container mx-auto">
        <h1 class="text-3xl font-bold text-purple-700 text-center mb-8">
            สร้างใบประกาศเกียรติบัตรให้นักเรียนที่ได้คะแนนสูงสุดทุกห้องเรียน</h1>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">สำเร็จ!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                @if (session('generated_cert_numbers'))
                    <ul class="mt-2 list-disc list-inside">
                        @foreach (session('generated_cert_numbers') as $student_id => $cert_number)
                            <li>รหัสประจำตัวนักเรียน {{ $student_id }}: เลขที่ {{ $cert_number }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        {{-- Error Message (This is where the error messages from the controller will be displayed) --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">ผิดพลาด!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">ข้อผิดพลาดในการป้อนข้อมูล:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search Student Section --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="flex items-center justify-between p-4 mb-4"> {{-- ปรับ div หลัก --}}
                <h2 class="text-xl font-bold">1. ค้นหานักเรียน</h2> {{-- เอา mb-4 ออกจาก h2 --}}

                {{-- ปุ่ม logout --}}
                <form action="{{ route('certificates.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline">
                        Logout
                    </button>
                </form>
            </div>
            <form action="{{ route('certificates.index') }}" method="GET" class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label for="class_room" class="block text-gray-700 font-semibold mb-2">ชั้นเรียน:</label>
                        <select id="class_room" name="class_room" class="w-full border border-gray-300 p-2 rounded">
                            <option value="">-- ทั้งหมด --</option>
                            {{-- เปลี่ยน $classRooms เป็น $allClassRooms ตาม Controller --}}
                            @foreach ($allClassRooms as $room)
                                <option value="{{ $room }}" {{ $selectedClassRoom == $room ? 'selected' : '' }}>
                                    {{ $room }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-gray-700 font-semibold mb-2">เดือน:</label>
                        <select id="month" name="month" class="w-full border border-gray-300 p-2 rounded">
                            <option value="">-- เลือกเดือน --</option>
                            @php
                                $thaiMonths = [
                                    1 => 'มกราคม',
                                    2 => 'กุมภาพันธ์',
                                    3 => 'มีนาคม',
                                    4 => 'เมษายน',
                                    5 => 'พฤษภาคม',
                                    6 => 'มิถุนายน',
                                    7 => 'กรกฎาคม',
                                    8 => 'สิงหาคม',
                                    9 => 'กันยายน',
                                    10 => 'ตุลาคม',
                                    11 => 'พฤศจิกายน',
                                    12 => 'ธันวาคม',
                                ];
                            @endphp
                            @foreach ($thaiMonths as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="academic_year" class="block text-gray-700 font-semibold mb-2">ปีการศึกษา
                            (พ.ศ.):</label>
                        <input type="number" id="academic_year" name="academic_year"
                            class="w-full border border-gray-300 p-2 rounded"
                            placeholder="เช่น {{ $currentBuddhistYear }}"
                            value="{{ old('academic_year', $selectedAcademicYear ?? $currentBuddhistYear) }}">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            ค้นหา
                        </button>

                        <button type="button" onclick="window.location.href = '{{ route('certificates.index') }}';"
                            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                            ล้างค่า
                        </button>
                    </div>
                </div>
            </form>
        </div>



        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold mb-4">2. รายชื่อนักเรียนที่ได้คะแนนสูงสุดของเดือน
                @php
                    // สร้าง array ของชื่อเดือนภาษาไทย โดยให้ index เริ่มจาก 1
                    $thaiMonths = [
                        1 => 'มกราคม',
                        2 => 'กุมภาพันธ์',
                        3 => 'มีนาคม',
                        4 => 'เมษายน',
                        5 => 'พฤษภาคม',
                        6 => 'มิถุนายน',
                        7 => 'กรกฎาคม',
                        8 => 'สิงหาคม',
                        9 => 'กันยายน',
                        10 => 'ตุลาคม',
                        11 => 'พฤศจิกายน',
                        12 => 'ธันวาคม',
                    ];
                    $displayMonthName = '--ที่เลือก--';
                    if (isset($selectedMonth) && !empty($selectedMonth) && is_numeric($selectedMonth) && array_key_exists((int) $selectedMonth, $thaiMonths)) {
                        $displayMonthName = $thaiMonths[(int) $selectedMonth];
                    }
                @endphp
                <span class="text-blue-600"> {{-- ใช้ span และคลาส text-blue-600 เพื่อทำให้ตัวหนังสือเป็นสีฟ้า --}}
                    {{ $displayMonthName }} พ.ศ.{{ $currentBuddhistYear ?? '' }}
                </span>
            </h2>

            <div class="border border-gray-300 rounded p-4 mb-4">
                @if (empty($resultsByClassRoom))
                    <p class="text-gray-600 text-center">โปรดเลือกเดือนและปีการศึกษาเพื่อดูข้อมูลนักเรียน</p>
                @else
                    {{-- แก้ตรงนี้ใหม่ให้กลาง --}}
                    <div class="w-full overflow-x-auto">
                        <div class="flex justify-center">
                            <table
                                class="w-full text-sm text-left text-gray-700 border border-gray-300 shadow-sm border-collapse">
                                <thead class="bg-gray-100 text-gray-900">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-3 text-center">เลือก</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center">ชั้นเรียน</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center">ชื่อนักเรียน</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center">คะแนนรวม</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center">เลขที่ใบประกาศฯ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- วนลูปแรก: วนผ่านแต่ละห้องเรียน --}}
                                    @foreach ($resultsByClassRoom as $room => $studentsInRoom)
                                        {{-- วนลูปที่สอง: วนผ่านนักเรียนแต่ละคนในห้องนั้นๆ --}}
                                        @foreach ($studentsInRoom as $studentData)
                                            <tr
                                                class="{{ $studentData->is_green_text ? 'bg-green-100' : '' }} 
                                                                                                                                                                                                                                                                       {{ !$studentData->is_green_text && $studentData->is_red_text ? 'bg-red-100' : '' }}
                                                                                                                                                                                                                                                                       {{ $studentData->is_orange_row ? 'bg-orange-50' : '' }}">
                                                {{--
                                                เพิ่มสีพื้นหลังส้มตรงนี้ --}}
                                                <td class="border border-gray-300 px-4 py-2 flex justify-center items-center">
                                                    {{-- ใช้ $studentData แทน $roomData --}}
                                                    @if ($studentData->can_create)
                                                        <input type="checkbox" name="selected_students_to_create[]"
                                                            value="{{ $studentData->student_id_for_action }}"
                                                            data-score="{{ $studentData->score_display }}" {{-- เพิ่ม data-score --}}
                                                            data-month="{{ $studentData->month }}" {{-- เพิ่ม data-month --}}
                                                            data-year="{{ $studentData->year }}" {{-- เพิ่ม data-year --}}
                                                            class="form-checkbox h-5 w-5 text-blue-600 create-cert-checkbox">
                                                    @elseif ($studentData->can_download)
                                                        <input type="checkbox" name="selected_certs_to_download[]"
                                                            value="{{ $studentData->certificate_id }}"
                                                            class="form-checkbox h-5 w-5 text-green-600 download-cert-checkbox">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="border border-gray-300 px-4 py-2 text-center">
                                                    {{ $studentData->class_room }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 px-4 py-2 text-center
                                                                                                                                                                                                                                                                    {{ $studentData->is_red_text ? 'text-red-600 font-medium' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_green_text ? 'text-green-600 font-medium' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_grey_text ? 'text-gray-500' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_orange_text ? 'text-orange-700 font-medium' : '' }}">
                                                    {{--
                                                    เพิ่มสีตัวหนังสือส้มตรงนี้ --}}
                                                    {{ $studentData->student_name_display }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 px-4 py-2 text-center
                                                                                                                                                                                                                                                                    {{ $studentData->is_red_text ? 'text-red-600 font-medium' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_green_text ? 'text-green-600 font-medium' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_grey_text ? 'text-gray-500' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_orange_text ? 'text-orange-700 font-medium' : '' }}">
                                                    {{--
                                                    เพิ่มสีตัวหนังสือส้มตรงนี้ --}}
                                                    {{ $studentData->score_display }}
                                                </td>
                                                <td
                                                    class="border border-gray-300 px-4 py-2 text-center
                                                                                                                                                                                                                                                                    {{ $studentData->is_red_text ? 'text-red-600 font-medium' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_green_text ? 'text-green-600 font-medium' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_grey_text ? 'text-gray-500' : '' }}
                                                                                                                                                                                                                                                                    {{ $studentData->is_orange_text ? 'text-orange-700 font-medium' : '' }}">
                                                    {{--
                                                    เพิ่มสีตัวหนังสือส้มตรงนี้ --}}
                                                    {{ $studentData->certificate_display }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                @endif
            </div>



            <hr class="my-6 border-gray-300">

            {{-- Main container for side-by-side button groups --}}
            <div class="flex flex-col md:flex-row gap-6">

                {{-- Action buttons for creating certificates (Conditional visibility based on students found) --}}
                <div class="button-group create-group md:w-1/2" id="create-buttons-container"
                    style="display: {{ $hasDataToCreate ? 'flex' : 'none' }};">
                    <form id="createCertForm" action="{{ route('certificates.save') }}" method="POST"
                        class="flex flex-col gap-4">
                        @csrf

                        {{-- แถวแรก: หัวข้อ, ข้อความ "เลขที่", และช่องกรอก --}}
                        <div class="flex items-center gap-4 mt-4">
                            <h3 class="text-lg font-semibold text-purple-800 whitespace-nowrap">
                                ตัวเลือกสำหรับสร้างใบประกาศ
                            </h3>
                            <label for="certificate_number"
                                class="text-lg font-semibold text-red-800 whitespace-nowrap">
                                เลขที่ใบประกาศฯ <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="certificate_number" id="certificate_number" required
                                class="block w-32 rounded-md border-2 border-red-500 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2"
                                placeholder="กรอกเลขที่">
                        </div>

                        {{-- แถวที่สอง: ปุ่มต่างๆ --}}
                        <div class="flex items-center gap-4 flex-wrap mt-4">
                            <button type="button" id="selectAllStudentsToCreate"
                                class="bg-purple-200 hover:bg-purple-300 text-purple-800 font-semibold py-2 px-4 rounded">
                                เลือกทั้งหมด
                            </button>
                            <button type="button" id="deselectAllStudentsToCreate"
                                class="bg-purple-200 hover:bg-purple-300 text-purple-800 font-semibold py-2 px-4 rounded">
                                ยกเลิกการเลือกทั้งหมด
                            </button>
                            <input type="hidden" name="selected_students" id="selectedStudentsToCreateInput">
                            <button type="submit" id="createCertButton" disabled
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded opacity-50 cursor-not-allowed">
                                สร้างใบประกาศที่เลือก
                            </button>
                        </div>
                    </form>
                </div>
                <!-- {{-- Action buttons for creating certificates (Conditional visibility based on students found) --}}
                <div class="button-group create-group md:w-1/2" id="create-buttons-container"
                    style="display: {{ $hasDataToCreate ? 'flex' : 'none' }};"> {{-- แก้ไขตรงนี้!! --}}
                    <h3 class="text-lg font-semibold text-purple-800">ตัวเลือกสำหรับสร้างใบประกาศ</h3>
                    <div class="flex items-center gap-4"> {{-- Group for buttons and form --}}
                        <button id="selectAllStudentsToCreate"
                            class="bg-purple-200 hover:bg-purple-300 text-purple-800 font-semibold py-2 px-4 rounded">
                            เลือกทั้งหมด
                        </button>
                        <button id="deselectAllStudentsToCreate"
                            class="bg-purple-200 hover:bg-purple-300 text-purple-800 font-semibold py-2 px-4 rounded">
                            ยกเลิกการเลือกทั้งหมด
                        </button>
                        <form id="createCertForm" action="{{ route('certificates.save') }}" method="POST"
                            class="inline-block">
                            @csrf
                            <input type="hidden" name="selected_students" id="selectedStudentsToCreateInput">
                            <button type="submit" id="createCertButton" disabled
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded opacity-50 cursor-not-allowed">
                                สร้างใบประกาศที่เลือก
                            </button>
                        </form>
                    </div>
                </div> -->

                {{-- Action buttons for downloading existing certificates (Conditional visibility) --}}
                <div class="button-group download-group md:w-1/2" id="download-buttons-container"
                    style="display: {{ $hasDataToDownload ? 'flex' : 'none' }};"> {{-- แก้ไขตรงนี้!! --}}
                    <h3 class="text-lg font-semibold text-green-800">ตัวเลือกสำหรับดาวน์โหลดใบประกาศ</h3>
                    <div class="flex items-center gap-4"> {{-- Group for buttons and form --}}
                        <button id="selectAllStudentsToDownload"
                            class="bg-green-200 hover:bg-green-300 text-green-800 font-semibold py-2 px-4 rounded">
                            เลือกทั้งหมด (ดาวน์โหลด)
                        </button>
                        <button id="deselectAllStudentsToDownload"
                            class="bg-green-200 hover:bg-green-300 text-green-800 font-semibold py-2 px-4 rounded">
                            ยกเลิกการเลือกทั้งหมด (ดาวน์โหลด)
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Download Certificate Form Section (Conditional visibility) --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-6" id="download-form-container"
            style="display: {{ $certificatesIssued > 0 || $hasDataToDownload ? 'block' : 'none' }};"> {{-- แก้ไขตรงนี้!!
            --}}
            <h2 class="text-xl font-bold mb-4">3. กำหนดข้อมูลสำหรับใบประกาศ และดาวน์โหลด</h2>
            <form id="downloadForm" action="{{ route('certificates.downloadSelected') }}" method="POST">
                @csrf
                <input type="hidden" name="selected_certificates_for_download" id="selectedCertificatesToDownloadInput">

                {{-- Issue Date --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="issue_day" class="block text-gray-700 font-semibold mb-2">วันที่ออกใบประกาศ</label>
                        <input type="number" id="issue_day" name="issue_day"
                            class="w-full border border-gray-300 p-2 rounded" value="{{ old('issue_day', date('d')) }}"
                            min="1" max="31" required>
                        @error('issue_day') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="issue_month"
                            class="block text-gray-700 font-semibold mb-2">เดือนที่ออกใบประกาศ</label>
                        <select id="issue_month" name="issue_month" class="w-full border border-gray-300 p-2 rounded"
                            required>
                            @php
                                $thaiMonths = [
                                    1 => 'มกราคม',
                                    2 => 'กุมภาพันธ์',
                                    3 => 'มีนาคม',
                                    4 => 'เมษายน',
                                    5 => 'พฤษภาคม',
                                    6 => 'มิถุนายน',
                                    7 => 'กรกฎาคม',
                                    8 => 'สิงหาคม',
                                    9 => 'กันยายน',
                                    10 => 'ตุลาคม',
                                    11 => 'พฤศจิกายน',
                                    12 => 'ธันวาคม',
                                ];
                            @endphp
                            @foreach ($thaiMonths as $num => $name)
                                <option value="{{ $num }}" {{ old('issue_month', date('n')) == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('issue_month') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="issue_year" class="block text-gray-700 font-semibold mb-2">ปี พ.ศ.
                            ที่ออกใบประกาศ</label>
                        <input type="number" id="issue_year" name="issue_year"
                            class="w-full border border-gray-300 p-2 rounded" placeholder="เช่น 2568"
                            value="{{ old('issue_year', $currentBuddhistYear) }}" min="2500" max="3000" required>
                        @error('issue_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Certificate Content Details (Month, Semester, Academic Year) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="cert_month" class="block text-gray-700 font-semibold mb-2">ประจำเดือน
                            (สำหรับใบประกาศ)</label>
                        <select id="cert_month" name="cert_month" class="w-full border border-gray-300 p-2 rounded"
                            required>
                            <option value="">-- เลือกเดือน --</option>
                            @php
                                $thaiMonths = [
                                    1 => 'มกราคม',
                                    2 => 'กุมภาพันธ์',
                                    3 => 'มีนาคม',
                                    4 => 'เมษายน',
                                    5 => 'พฤษภาคม',
                                    6 => 'มิถุนายน',
                                    7 => 'กรกฎาคม',
                                    8 => 'สิงหาคม',
                                    9 => 'กันยายน',
                                    10 => 'ตุลาคม',
                                    11 => 'พฤศจิกายน',
                                    12 => 'ธันวาคม',
                                ];
                            @endphp
                            @foreach ($thaiMonths as $num => $name)
                                <option value="{{ $num }}" {{ old('cert_month', $selectedMonth ?? date('n')) == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cert_month') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cert_semester" class="block text-gray-700 font-semibold mb-2">ภาคเรียนที่
                            (สำหรับใบประกาศ)</label>
                        <select id="cert_semester" name="cert_semester"
                            class="w-full border border-gray-300 p-2 rounded" required>
                            <option value="1" {{ old('cert_semester', 1) == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('cert_semester', 2) == '2' ? 'selected' : '' }}>2</option>
                        </select>
                        @error('cert_semester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cert_academic_year" class="block text-gray-700 font-semibold mb-2">ปีการศึกษา พ.ศ.
                            (สำหรับใบประกาศ)</label>
                        <input type="number" id="cert_academic_year" name="cert_academic_year"
                            class="w-full border border-gray-300 p-2 rounded" placeholder="เช่น 2568"
                            value="{{ old('cert_academic_year', $selectedAcademicYear ?? $currentBuddhistYear) }}"
                            min="2500" max="3000" required>
                        @error('cert_academic_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" id="downloadButton" disabled
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded opacity-50 cursor-not-allowed">
                    ดาวน์โหลดใบประกาศที่เลือก (.zip)
                </button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createCertCheckboxes = document.querySelectorAll('.create-cert-checkbox');
            const selectedStudentsToCreateInput = document.getElementById('selectedStudentsToCreateInput');
            const createCertButton = document.getElementById('createCertButton');
            const selectAllStudentsToCreateBtn = document.getElementById('selectAllStudentsToCreate');
            const deselectAllStudentsToCreateBtn = document.getElementById('deselectAllStudentsToCreate');

            // เพิ่มตัวแปรสำหรับช่องกรอกเลขที่ใบประกาศฯ
            const certificateNumberInput = document.getElementById('certificate_number');

            const downloadCertCheckboxes = document.querySelectorAll('.download-cert-checkbox');
            const selectedCertificatesToDownloadInput = document.getElementById('selectedCertificatesToDownloadInput');
            const downloadButton = document.getElementById('downloadButton');
            const selectAllStudentsToDownloadBtn = document.getElementById('selectAllStudentsToDownload');
            const deselectAllStudentsToDownloadBtn = document.getElementById('deselectAllStudentsToDownload');

            // Containers for conditional display
            const downloadButtonsContainer = document.getElementById('download-buttons-container');
            const downloadFormContainer = document.getElementById('download-form-container');
            const createButtonsContainer = document.getElementById('create-buttons-container');

            // Variables passed from Controller
            // ตรวจสอบว่ามีตัวแปรเหล่านี้หรือไม่ก่อนใช้งานใน JS
            const hasDataToCreate = {{ isset($hasDataToCreate) && $hasDataToCreate ? 'true' : 'false' }};
            const hasDataToDownload = {{ isset($hasDataToDownload) && $hasDataToDownload ? 'true' : 'false' }};
            const certificatesIssuedCount = {{ isset($certificatesIssued) ? $certificatesIssued : 0 }};


            function updateCreateCertButtonState() {
                const selectedStudentsData = [];
                createCertCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedStudentsData.push({
                            id: checkbox.value,
                            score: checkbox.dataset.score,
                            month: checkbox.dataset.month,
                            year: checkbox.dataset.year
                        });
                    }
                });
                selectedStudentsToCreateInput.value = JSON.stringify(selectedStudentsData);

                // ** เพิ่มการตรวจสอบช่องกรอกเลขที่ใบประกาศฯ (ห้ามเป็นค่าว่าง) **
                const isCertNumberFilled = certificateNumberInput.value.trim() !== '';

                if (selectedStudentsData.length > 0 && isCertNumberFilled) {
                    createCertButton.disabled = false;
                    createCertButton.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    createCertButton.disabled = true;
                    createCertButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            function updateDownloadButtonState() {
                const selectedCertificateIds = [];
                downloadCertCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const certId = checkbox.value; // Value is already the certificate ID
                        if (certId) {
                            selectedCertificateIds.push(certId);
                        }
                    }
                });
                selectedCertificatesToDownloadInput.value = JSON.stringify(selectedCertificateIds);

                if (selectedCertificateIds.length > 0) {
                    downloadButton.disabled = false;
                    downloadButton.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    downloadButton.disabled = true;
                    downloadButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            // Event Listeners for Create Certificate actions
            createCertCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCreateCertButtonState);
            });

            // ** เพิ่ม Event Listener สำหรับช่องกรอกเลขที่ใบประกาศฯ **
            if (certificateNumberInput) {
                certificateNumberInput.addEventListener('input', updateCreateCertButtonState);
            }

            if (selectAllStudentsToCreateBtn) {
                selectAllStudentsToCreateBtn.addEventListener('click', function () {
                    createCertCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    updateCreateCertButtonState();
                });
            }

            if (deselectAllStudentsToCreateBtn) {
                deselectAllStudentsToCreateBtn.addEventListener('click', function () {
                    createCertCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    updateCreateCertButtonState();
                });
            }

            // Event Listeners for Download Certificate actions
            downloadCertCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateDownloadButtonState);
            });

            if (selectAllStudentsToDownloadBtn) {
                selectAllStudentsToDownloadBtn.addEventListener('click', function () {
                    downloadCertCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    updateDownloadButtonState();
                });
            }

            if (deselectAllStudentsToDownloadBtn) {
                deselectAllStudentsToDownloadBtn.addEventListener('click', function () {
                    downloadCertCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    updateDownloadButtonState();
                });
            }

            // Initial state update on page load
            updateCreateCertButtonState();
            updateDownloadButtonState();

            // Conditionally show/hide buttons and form containers based on data from controller
            // ใช้ค่า hasDataToCreate และ hasDataToDownload ที่ส่งมาจาก Controller เพื่อควบคุมการแสดงผล
            if (hasDataToCreate) {
                createButtonsContainer.style.display = 'flex';
            } else {
                createButtonsContainer.style.display = 'none';
            }

            if (hasDataToDownload) {
                downloadButtonsContainer.style.display = 'flex';
            } else {
                downloadButtonsContainer.style.display = 'none';
            }

            // ส่วนฟอร์มดาวน์โหลดจะแสดงเมื่อมีใบประกาศถูกออกแล้ว หรือมีข้อมูลที่สามารถดาวน์โหลดได้
            if (certificatesIssuedCount > 0 || hasDataToDownload) {
                downloadFormContainer.style.display = 'block';
            } else {
                downloadFormContainer.style.display = 'none';
            }


            // If there were generated certificate numbers from a previous submission, log them for debugging
            @if (session('generated_cert_numbers'))
                const generatedCertNumbers = {!! json_encode(session('generated_cert_numbers')) !!};
                console.log('Generated Certificate Numbers:', generatedCertNumbers);
            @endif
    });
    </script>


</body>

</html>