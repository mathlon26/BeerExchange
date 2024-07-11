<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center amaranth-bold font-semibold text-xl text-gray-800 leading-tight">
            {{ __('QR-code') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()->marketOpen())
                @if (isset($qrCodeImage))
                    <div class="flex flex-col text-center justify-center items-center">
                        <p class="py-12">Scan this QR code to go directly to the session.</p>
                        <img src="data:image/png;base64,{{ base64_encode($qrCodeImage) }}" alt="QR Code">
                    </div>
                    <div class="otp-container">
                        <div class="otp-code shadow flex flex-col justify-center items-center">
                            <div class="flex flex-row justify-center items-center">
                                <div class="otp-part otp-lpart">BE</div>
                                <div class="otp-dash">-</div>
                                @php
                                    $i = 0;
                                    foreach (str_split(substr($market_session_id, 3)) as $char) {
                                        $i++;
                                        if ($i != 4) {
                                            echo '<div class="otp-part shadow">' . $char . '</div>';
                                        }
                                        else {
                                            echo '<div class="otp-dash">-</div>';
                                            echo '<div class="otp-part shadow">' . $char . '</div>';
                                        }
                                    }
                                @endphp
                            </div>
                            <a class="copy-code-btn" href="">Copy!</a>
                            <p>Share the code or QR-code with your clients.</p>
                            <p>Clients can enter the code on the homepage!</p>
                        </div>
                    </div>
                    

                @endif
            @else
                <div class="text-center text-gray-500">
                    Market is closed. Please open a new market session in your <a href="{{ route('dashboard') }}" class="drinks-dashboard-link">dashboard</a> to generate a qr-code for your event.
                </div>
            @endif
        </div>
    </div>
    <script>
        const copyBtn = document.querySelector('.copy-code-btn');

        copyBtn.addEventListener('click', function() {
            let otpCode = @json($market_session_id);

            const textarea = document.createElement('textarea');
            textarea.value = otpCode;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            alert('Code copied to clipboard!');
        });
    </script>
</x-app-layout>
