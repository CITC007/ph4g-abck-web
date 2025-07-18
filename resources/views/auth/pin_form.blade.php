<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter PIN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/images/heart.png" sizes="32x32" type="image/png" />
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center text-purple-800">กรอก PIN เพื่อเข้าหน้าการสร้างประกาศนียบัตร</h2>

        {{-- PHP block to determine display logic --}}
        @php
            $showCountdown = isset($secondsRemaining) && $secondsRemaining > 0;
            $hasValidationErrors = $errors->any();
            $pinErrorMessage = $errors->first('pin');
        @endphp

        {{-- Display countdown message if currently locked out --}}
        @if ($showCountdown)
            <div id="countdown-message"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-center font-semibold"
                role="alert">
                <strong class="font-bold">ข้อผิดพลาด!</strong> <span class="block sm:inline">คุณพยายามกรอก PIN
                    ผิดหลายครั้งเกินไป โปรดรออีก <span id="countdown-timer">{{ $secondsRemaining }}</span> วินาที
                    ก่อนลองใหม่</span>
            </div>
            {{-- Display general validation errors if not in lockout state --}}
        @elseif ($hasValidationErrors)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">ข้อผิดพลาด!</strong>
                <span class="block sm:inline">{{ $pinErrorMessage }}</span>
            </div>
        @endif

        <form id="pin-form" action="{{ route('certificates.pin.process') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="pin" class="block text-gray-700 text-sm font-bold mb-2">PIN:</label>
                <input type="password" id="pin" name="pin"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="••••••" maxlength="6" required @if ($showCountdown) disabled @endif> {{-- Disable if
                countdown is active --}}
            </div>
            <div class="flex flex-col sm:flex-row-reverse items-center justify-between">
                {{-- ปุ่ม Submit PIN (ย้ายมาอยู่ก่อนปุ่มกลับหน้าหลักในโครงสร้าง HTML) --}}
                <button type="submit" id="submit-pin-button"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex-grow sm:ml-2 w-full sm:w-auto mb-2 sm:mb-0"
                    {{-- เพิ่ม sm:ml-2, w-full, sm:w-auto, mb-2, sm:mb-0 --}} @if ($showCountdown) disabled @endif>
                    เข้าสู่ระบบ
                </button>

                {{-- ปุ่มกลับหน้าหลัก (ย้ายมาอยู่หลังปุ่ม Submit PIN ในโครงสร้าง HTML) --}}
                <a href="{{ url('/') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex-grow text-center w-full sm:w-auto mb-2 sm:mb-0">
                    {{-- เพิ่ม w-full, sm:w-auto, mb-2, sm:mb-0 --}}
                    กลับหน้าหลัก
                </a>
            </div>
        </form>
    </div>

    {{-- JavaScript for countdown functionality --}}
    @if ($showCountdown)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const countdownElement = document.getElementById('countdown-message');

                // Get initial timeLeft from the rendered HTML content
                let timeLeft = parseInt(document.getElementById('countdown-timer').textContent);

                // If for some reason timeLeft is not a number or is already 0/negative
                // (e.g., page reload just as countdown ends or server time differs)
                if (isNaN(timeLeft) || timeLeft <= 0) {
                    // Immediately unlock input/button and hide countdown
                    document.getElementById('pin').disabled = false;
                    document.getElementById('submit-pin-button').disabled = false;
                    countdownElement.style.display = 'none';
                    return; // Stop the script as no countdown is needed
                }

                const pinInput = document.getElementById('pin');
                const submitButton = document.getElementById('submit-pin-button');
                const countdownTimer = document.getElementById('countdown-timer');

                // Initial state setup: disable input/button if countdown is active
                pinInput.disabled = true;
                submitButton.disabled = true;

                function updateCountdown() {
                    if (timeLeft > 0) {
                        countdownTimer.textContent = timeLeft;
                        timeLeft--;
                    } else {
                        // When countdown reaches 0, unlock elements and hide countdown
                        countdownTimer.textContent = '0';
                        pinInput.disabled = false; // Unlock PIN input
                        submitButton.disabled = false; // Unlock Submit button
                        clearInterval(countdownInterval); // Stop the countdown timer
                        countdownElement.style.display = 'none'; // Hide the countdown message
                    }
                }

                const countdownInterval = setInterval(updateCountdown, 1000);
                updateCountdown(); // Call once immediately to set initial display and state
            });
        </script>
    @endif
</body>

</html>