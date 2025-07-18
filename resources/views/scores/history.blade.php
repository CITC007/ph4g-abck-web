<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>ประวัติคะแนนหัวใจของ {{ $student->student_name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #fce4ec;
            /* Light Pink */
        }

        .header-bg {
            background-color: #f06292;
            /* Medium Pink */
            color: white;
            padding: 0.5rem;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .main-card {
            border-radius: 0.5rem;
            overflow: hidden;
            /* Ensures rounded corners apply to children */
        }

        .info-card {
            background-color: #e3f2fd;
            /* Light Blue */
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .table-header {
            background-color: #bbdefb;
            /* Lighter Blue */
            color: #1a202c;
            /* Darker text for contrast */
        }

        .button-print {
            background-color: #42a5f5;
            /* Blue */
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .button-print:hover {
            background-color: #2196f3;
            /* Darker Blue on Hover */
        }

        /* --- Media Queries สำหรับ Mobile (รวมถึง iPhone 14 Pro Max) --- */
        @media (max-width: 640px) {

            body {
                padding: 0;
                /* ยังคงลบ padding ของ body ออก เพื่อให้ main-card จัดการ margin แทน */
            }

            .main-card {
                max-width: 100%;
                /* ทำให้ card หลักใช้พื้นที่เต็มหน้าจอมากขึ้น */
                margin-left: 0.5rem;
                /* เพิ่ม margin ซ้าย */
                margin-right: 0.5rem;
                /* เพิ่ม margin ขวา เพื่อให้มีขอบ */
                border-radius: 0.5rem;
                /* คงขอบมนไว้ */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                /* คงเงาไว้ */
            }

            .header-bg {
                padding: 1rem;
                /* ลด padding ของ header */
                /* คง border-top-left-radius และ border-top-right-radius ไว้ตาม main-card */
            }

            .header-bg h1 {
                font-size: 1.25rem;
                /* ลดขนาด font หัวข้อ */
            }

            .p-6 {
                padding: 1rem;
                /* ลด padding ของ content ภายใน card */
            }

            .info-card {
                padding: 0.75rem;
                /* ลด padding ของ info card */
            }

            .info-card p {
                font-size: 1rem;
                /* ปรับขนาด font สำหรับข้อมูลทั่วไป */
            }

            .info-card p span {
                font-size: 1rem;
                /* ให้ span มีขนาดเท่ากัน */
            }

            .info-card p.font-bold {
                font-size: 1.125rem;
                /* สำหรับ "ได้คะแนนหัวใจทั้งสิ้น" ใหญ่กว่าข้อมูลอื่นเล็กน้อย */
            }

            /* ปรับขนาดและ padding ของตารางสำหรับหน้าจอเล็ก */
            .table-header th,
            .text-base.font-light td {
                padding: 0.75rem 0.5rem;
                /* ลด padding */
                font-size: 0.875rem;
                /* ลดขนาด font */
            }

            .whitespace-nowrap {
                white-space: normal;
                /* อนุญาตให้หักบรรทัดได้ ถ้าจำเป็น */
            }

            /* สำคัญ: ลบ margin ติดลบออก เพื่อให้ตารางมีขอบและเห็นกรอบ */
            .overflow-x-auto {
                margin-left: 0;
                margin-right: 0;
                /* ถ้าต้องการให้กรอบตารางชิดขอบของ .p-6 ให้ลบ border-radius ออก หรือคงไว้ตามเดิม*/
                /* border-radius: 0.5rem; */
                /* ตามค่าเดิมของ shadow-md rounded-lg */
            }

            .min-w-full {
                width: 100%;
                /* ให้ตารางใช้ความกว้างเต็มพื้นที่ภายใน overflow-x-auto */
            }

            .button-print {
                padding: 0.6rem 1.2rem;
                /* ลดขนาดปุ่ม */
                font-size: 0.9rem;
                /* ลดขนาด font ปุ่ม */
            }
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #fff;
                /* White background for printing */
            }

            .no-print {
                display: none;
            }

            .main-card {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>

<body class="p-4">
    <div class="max-w-4xl mx-auto bg-white main-card shadow-lg">
        <div class="header-bg">
            <h1 class="text-2xl font-bold text-center flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="heartbeat w-6 h-6 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
                ประวัติคะแนนหัวใจ
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="heartbeat w-6 h-6 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </h1>
        </div>

        <div class="p-6">
            <div class="info-card">
                <p class="text-xl mb-2 font-semibold">ชื่อนักเรียน: <span
                        class="text-blue-700">{{ $student->student_name }}</span></p>
                <p class="text-xl mb-2 font-semibold">ชั้นเรียน: <span
                        class="text-blue-700">{{ $student->class_room }}</span></p>
                <p class="text-xl">เดือนที่ได้รับประกาศนียบัตร: <span class="text-blue-700">{{ $certMonthThai }}
                        {{ $issueYearBuddhist }}</span></p>
                <p class="text-xl font-bold mt-4">ได้คะแนนหัวใจทั้งสิ้น: <span class="text-pink-700">{{ $totalScores }}
                        คะแนน</span></p>

            </div>

            @if ($scores->isEmpty())
                <p class="text-center text-gray-600 py-8 text-lg">ไม่พบประวัติคะแนนสำหรับเดือนนี้</p>
            @else
                <div class="overflow-x-auto shadow-md rounded-lg">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="table-header text-normal leading-normal">
                                <th class="py-3 px-6 text-center">วันที่ได้รับคะแนน</th>
                                <th class="py-3 px-6 text-center">คะแนนที่ได้รับ</th>
                                <th class="py-3 px-6 text-center">ครูผู้ให้คะแนน</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-base font-light">
                            @foreach ($scores as $score)
                                <tr class="border-b border-gray-200 hover:bg-pink-50">
                                    <td class="py-3 px-6 text-center whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($score->created_at)->locale('th')->isoFormat('Do MMMM') }}
                                        {{ \Carbon\Carbon::parse($score->created_at)->year + 543 }}
                                    </td>
                                    <td class="py-3 px-6 text-center font-bold text-blue-600">{{ $score->point }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $score->teacher->teacher_name ?? 'ไม่ระบุ' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="text-center mt-8 no-print">
                <button onclick="window.print()" class="button-print">
                    พิมพ์หน้านี้
                </button>
            </div>
        </div>
    </div>
</body>

</html>