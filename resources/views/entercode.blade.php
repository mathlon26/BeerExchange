<x-guest-layout>
    <!-- Session Status -->
    <div class="flex justify-center align-midle text-center">
        <form class="" method="POST" action="{{ route('codesubmit') }}">
            @csrf
            <div class="otp-form">
                <h1 class="mt-4 opt-title font-bold">Enter the code</h1>
                <p class="py-4 text-3xl font-bold">to view all charts and drinks!</p>
                <div class="py-4 opt-container flex flex-row justify-center items-center">
                    <div class="otp-part otp-lpart">BE</div>
                    <div class="otp-dash">-</div>
                    <input name="i1" type="tel" id="otp-input-1" class="otp-input" autocomplete="on" pattern="\d" maxlength="1" oninput="moveToNext(this, 'otp-input-2')" onkeydown="moveToPrevious(event, 'otp-input-1', null)" onpaste="handlePaste(event)">
                    <input name="i2" type="tel" id="otp-input-2" class="otp-input" autocomplete="on" pattern="\d" maxlength="1" oninput="moveToNext(this, 'otp-input-3')" onkeydown="moveToPrevious(event, 'otp-input-2', 'otp-input-1')" disabled>
                    <input name="i3" type="tel" id="otp-input-3" class="otp-input" autocomplete="on" pattern="\d" maxlength="1" oninput="moveToNext(this, 'otp-input-4')" onkeydown="moveToPrevious(event, 'otp-input-3', 'otp-input-2')" disabled>
                    <div class="otp-dash">-</div>
                    <input name="i4" type="tel" id="otp-input-4" class="otp-input" autocomplete="on" pattern="\d" maxlength="1" oninput="moveToNext(this, 'otp-input-5')" onkeydown="moveToPrevious(event, 'otp-input-4', 'otp-input-3')" disabled>
                    <input name="i5" type="tel" id="otp-input-5" class="otp-input" autocomplete="on" pattern="\d" maxlength="1" oninput="moveToNext(this, 'otp-input-6')" onkeydown="moveToPrevious(event, 'otp-input-5', 'otp-input-4')" disabled>
                    <input name="i6" type="tel" id="otp-input-6" class="otp-input" autocomplete="on" pattern="\d" maxlength="1" onkeydown="moveToPrevious(event, 'otp-input-6', 'otp-input-5')" disabled>
                </div>
                @if(session('error'))
                    <div class="text-custom-red">
                        {{ session('error') }}
                    </div>
                @endif
                
                <button type="submit" id="otp-submit-btn" class="copy-code-btn">Go!</button>
            </div>
        </form>
    </div>

    <script>
        function moveToNext(current, nextFieldId) {
            if (current.value.length === current.maxLength) {
                document.getElementById(nextFieldId).disabled = false;
                document.getElementById(nextFieldId).focus();
            }
        }

        function moveToPrevious(event, currentFieldId, previousFieldId) {
            if (event.key === 'Backspace' && event.target.value === '') {
                if (previousFieldId) {
                    const previousField = document.getElementById(previousFieldId);
                    previousField.disabled = false;
                    previousField.focus();
                }
                document.getElementById(currentFieldId).value = '';
            }
        }

        function handlePaste(event) {
            event.preventDefault();
            var pasteData = event.clipboardData.getData('text').trim();
            console.log(pasteData);
            pasteData = pasteData.toUpperCase();
            if (pasteData.slice(0,3) == 'BE-')
            {
                pasteData = pasteData.slice(3);
            }

            pasteData = pasteData.substr(0, 6);

            const inputs = document.querySelectorAll('.otp-input');
            for (let i = 0; i < inputs.length; i++) {
                if (i < pasteData.length) {
                    inputs[i].value = pasteData[i];
                    inputs[i].disabled = false;
                } else {
                    inputs[i].value = '';
                    inputs[i].disabled = true;
                }
            }
            inputs[pasteData.length - 1].focus();
        }

    </script>
</x-guest-layout>
