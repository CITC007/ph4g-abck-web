<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>ประวัติการได้รับคะแนนของ {{ $student->student_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- ตรวจสอบให้แน่ใจว่า Vite ถูกตั้งค่าอย่างถูกต้องเพื่อรวม app.css และ app.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
    {{-- เพิ่ม Alpine.js CDN (ถ้าไม่ได้รวมผ่าน app.js ของ Vite) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        {
                {
                -- คัดลอก CSS สำหรับ x-cloak มาจาก score-entry --
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 bg-[url('/images/bg-hearts-light.png')] bg-cover bg-fixed bg-no-repeat p-4 sm:p-6"
    x-data="{
        show: false, // สถานะการแสดง Modal
        studentId: {{ $student->id }}, // กำหนด ID นักเรียนเริ่มต้น (มาจาก Controller)
        studentName: '{{ $student->student_name }}', // กำหนดชื่อนักเรียนเริ่มต้น (มาจาก Controller)
        isBulk: false, // ในหน้านี้เราจะไม่ใช้โหมด bulk เนื่องจากเป็นการเพิ่มคะแนนรายบุคคล
        reasonText: '', // *** เพิ่มตัวแปรนี้สำหรับผูกกับ textarea เหตุผล suggestion ***

        // ฟังก์ชันสำหรับจัดการการ submit ฟอร์มใน Modal
        prepareBulkAndSubmit(e) {
            // ในหน้านี้ เราจะไม่ทำส่วนของ Bulk เนื่องจากเป็นการเพิ่มคะแนนรายบุคคล
            // แต่ยังคงเรียกใช้ e.target.submit() เหมือนเดิม
            e.target.submit();
        }
    }" x-init="
        // Watch for changes in 'show' to reset form/checkboxes when modal closes
        $watch('show', value => {
            if (!value) { // ถ้า Modal ปิดลง (value เป็น false)
                // ถ้ามี input field ใน modal ที่ต้องการรีเซ็ตเมื่อปิด สามารถใส่ logic ตรงนี้ได้
                this.reasonText = ''; // *** รีเซ็ตเหตุผลเมื่อ Modal ปิด ***
            } else { // ถ้า Modal เปิดขึ้น (value เป็น true)
                this.reasonText = ''; // *** รีเซ็ตเหตุผลเมื่อ Modal เปิด ***
            }
        });
    ">

    <div class="max-w-5xl mx-auto">

        <a href="{{ route('score-entry') }}"
            class="mb-6 px-3 py-1.5 bg-purple-100 text-purple-800 rounded hover:bg-purple-200 transition text-xs sm:text-sm">
            ← กลับ
        </a>
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800 text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif
        <div
            class="mb-6 p-4 bg-green-50 rounded border border-green-300 shadow-sm max-w-full sm:max-w-4xl mx-auto text-sm sm:text-base">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold mb-2">
                        <span class="text-black font-normal">ประวัติการได้รับคะแนนของ :</span>
                        <span class="text-blue-600 font-semibold">{{ $student->student_name }}</span>
                    </h2>
                    <div class="space-y-1">
                        <div>ชั้นเรียน: <span class="font-semibold text-blue-600">{{ $student->class_room }}</span>
                        </div>
                        <div>คะแนนรวมทั้งหมด: <span class="font-semibold text-green-600">{{ $totalPoints }}</span></div>
                        <div>คะแนนรวมของเดือน
                            <span class="font-semibold text-pink-600">
                                {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->locale('th')->isoFormat('MMMM') }}
                                {{ $currentYear + 543 }}:
                            </span>
                            <span class="font-semibold text-green-600">{{ $monthPoints }}</span>
                        </div>
                    </div>
                </div>

                <div class="self-end">
                    {{-- ปุ่มเพิ่มคะแนน: เรียก Modal โดยตรง --}}
                    <button type="button"
                        @click="show = true; studentId = {{ $student->id }}; studentName = '{{ $student->student_name }}'; isBulk = false"
                        class="relative text-pink-500 hover:text-pink-700 p-2 w-20 h-20" title="เพิ่มคะแนน">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="heartbeat w-full h-full">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312
                            2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0
                            7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>

                        <span
                            class="heartbeat absolute inset-0 flex items-center justify-center text-white font-bold text-sm pointer-events-none">
                            {{ $monthPoints }}
                        </span>
                    </button>

                </div>
            </div>

        </div>

        <div class="backdrop-blur-sm bg-white/80 p-4 sm:p-6 rounded-xl shadow-md max-w-full sm:max-w-4xl mx-auto">
            {{-- ลบ div ที่มี overflow-x-auto ออกไปแล้ว --}}
            <table class="w-full text-center text-xs sm:text-sm rounded-lg border border-gray-300">
                <thead class="bg-purple-100 text-purple-800">
                    <tr>
                        {{-- ปรับคลาส th: ลบ min-w ออกเพื่อการปรับขนาดที่ยืดหยุ่นกว่า --}}
                        <th class="p-2 sm:p-3 border border-gray-300 whitespace-nowrap text-xxs sm:text-xs">
                            วันที่ได้รับ
                        </th>
                        {{-- ปรับคลาส th: ใช้ w-full ร่วมกับ break-words
                        เพื่อให้คอลัมน์เหตุผลปรับความกว้างอัตโนมัติและข้อความขึ้นบรรทัดใหม่ --}}
                        <th class="p-2 sm:p-3 border border-gray-300 text-left w-full break-words">
                            รายการความดี
                        </th>
                        {{-- ปรับคลาส th: ลบ min-w ออก --}}
                        <th class="p-2 sm:p-3 border border-gray-300 whitespace-nowrap text-xxs sm:text-xs">
                            ครูผู้ให้คะแนน
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scores as $score)
                        <tr class="hover:bg-purple-50 transition-colors text-xs sm:text-base">
                            <td class="p-2 sm:p-3 border border-gray-300 whitespace-nowrap text-xxs sm:text-sm">
                                {{ $score->created_at->format('d/m/Y') }}
                            </td>
                            <td class="p-2 sm:p-3 border border-gray-300 text-left">
                                <div x-data="{
                                fullText: `{{ str_replace('`', '\`', $score->reason) }}`,
                                showFull: false,
                                maxLength: 70, // ปรับลดค่า maxLength ลงเพื่อให้พอดีกับจอเล็กๆ มากขึ้น
                                get truncatedText() {
                                    return this.fullText.length > this.maxLength ?
                                           this.fullText.substring(0, this.maxLength) + '...' :
                                           this.fullText;
                                }
                            }">
                                    {{-- เพิ่ม class="break-words" ตรงนี้อีกครั้งเพื่อให้แน่ใจว่าข้อความยาวๆ
                                    จะขึ้นบรรทัดใหม่ --}}
                                    <span x-text="showFull ? fullText : truncatedText" class="block break-words"></span>
                                    <template x-if="fullText.length > maxLength">
                                        <button @click="showFull = !showFull"
                                            class="text-blue-600 hover:underline ml-1 text-xxs sm:text-sm">
                                            <span x-text="showFull ? 'ย่อ' : 'อ่านต่อ'"></span>
                                        </button>
                                    </template>
                                </div>
                            </td>
                            <td class="p-2 sm:p-3 border border-gray-300 whitespace-nowrap text-xxs sm:text-sm">
                                {{ optional($score->teacher)->teacher_name ?? 'ไม่ทราบ' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500 text-sm">ไม่มีข้อมูลคะแนน</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 max-w-full sm:max-w-4xl mx-auto text-center">
            {{ $scores->links() }}
        </div>

    </div>


    <!-- Modal เพิ่มคะแนน -->
    <div x-show="show" x-cloak class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl overflow-auto max-h-[90vh]">
            <h2 class="text-xl font-bold mb-4">
                เพิ่มคะแนน: <span x-text="isBulk ? 'รายการนักเรียนที่เลือก' : studentName"></span>
            </h2>
            <form id="bulk-score-form" method="POST" action="{{ route('score-entry.save') }}"
                @submit.prevent="prepareBulkAndSubmit($event)">
                @csrf
                <template x-if="!isBulk">
                    <input type="hidden" name="student_id" :value="studentId">
                </template>

                {{-- **ส่วนนี้ถูกลบออกไป**
                <div class="mb-4">
                    <label for="points" class="block font-medium mb-1">จำนวนคะแนน:</label>
                    <input type="number" name="points" id="points" required min="1" value="1"
                        class="w-full border p-2 rounded text-sm sm:text-base">
                </div>
                --}}

                <div class="mb-4">
                    <label class="block font-medium mb-1">เหตุผลที่ให้คะแนน:</label>
                    <textarea name="reason" x-model="reasonText" required maxlength="255" rows="3"
                        class="w-full border p-2 rounded text-sm sm:text-base"></textarea>

                    <div class="mt-2 flex flex-wrap gap-2">
                        <button type="button" @click="reasonText = 'มีความซื่อสัตย์'"
                            class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs hover:bg-blue-200 transition">
                            มีความซื่อสัตย์
                        </button>
                        <button type="button" @click="reasonText = 'ช่วยเหลือเพื่อน'"
                            class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs hover:bg-green-200 transition">
                            ช่วยเหลือเพื่อน
                        </button>
                        <button type="button" @click="reasonText = 'ทำความสะอาดห้องเรียน'"
                            class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs hover:bg-yellow-200 transition">
                            ทำความสะอาดห้องเรียน
                        </button>
                        <button type="button" @click="reasonText = 'ส่งงานตรงเวลา'"
                            class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs hover:bg-purple-200 transition">
                            ส่งงานตรงเวลา
                        </button>
                        <button type="button" @click="reasonText = 'มีน้ำใจ'"
                            class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs hover:bg-red-200 transition">
                            มีน้ำใจ
                        </button>
                        <button type="button" @click="reasonText = 'ตั้งใจเรียน'"
                            class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs hover:bg-indigo-200 transition">
                            ตั้งใจเรียน
                        </button>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2">
                    <button type="button" @click="show = false"
                        class="px-4 py-2 bg-gray-300 rounded w-full sm:w-auto text-sm sm:text-base">
                        ปิด
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full sm:w-auto text-sm sm:text-base">
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>


</body>

</html>