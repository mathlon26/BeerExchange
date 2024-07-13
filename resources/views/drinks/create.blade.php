<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center amaranth-bold font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create drinks') }}
        </h2>
    </x-slot>
    <style>
        
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="p-6 flex flex-col items-center justify-center">
                    
                    <form method="POST" action="{{ route('drinks.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="top-create-drink-container shadow-sm bg-white border-b">
                            <h1 class="text-center amaranth-bold font-semibold text-xl text-gray-800 leading-tight">
                                Create a new drink
                            </h1>
                            <div class="drink-create-container border-gray-200 drinks-container">
                            
                                <div class="drink-create-logo flex flex-col align-middle items-center justify-center shadow-sm summary-box">
                                    <img id="drink-image-preview" class="drink-create-img" src="{{ old('image', asset('images/BeerLogo.webp')) }}" alt="Logo">
                                    <div class="flex flex-col justify-center py-2">
                                        <label id="#for-image-upload" for="image-upload" class="custom-file-upload">Choose Image</label>
                                        <input type="file" id="image-upload" name="image" accept=".png, .jpg, .jpeg, .webp" onchange="previewImage(event)">
                                        @error('image')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                        <p>256x256 recommended</p>
                                        <p>only .png, .jpg, .jpeg, .webp allowed</p>
                                    </div>
                                    <div class="flex flex-col drink-info-create-container summary-box">
                                        <div class="flex flex-row items-center">
                                            <input type="checkbox" name="manual_pump_dump" id="manual_pump_dump" {{ old('manual_pump_dump') ? 'checked' : '' }}>
                                            <label class="" for="manual_pump_dump">Allow manual Pump/Dump</label>
                                        </div>
                                        <div class="flex flex-row items-center">
                                            <input type="checkbox" name="auto_pump_dump" id="auto_pump_dump" {{ old('auto_pump_dump') ? 'checked' : '' }}>
                                            <label class="" for="auto_pump_dump">Allow auto Pump/Dump</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col drink-info-create-container summary-box">
                                    <div class="drink-create-item items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="drink_name">Drink Name</label>
                                            <input type="text" id="drink_name" name="drink_name" maxlength="20" value="{{ old('drink_name') }}">
                                        </div>
                                        @error('drink_name')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="start_price">Start Price</label>
                                            <input type="number" id="start_price" name="start_price" step="0.01" min="0" value="{{ old('start_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        
                                        </div>
                                        @error('start_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="bottom_price">Bottom Price</label>
                                            <input type="number" id="bottom_price" name="bottom_price" step="0.01" min="0" value="{{ old('bottom_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        </div>
                                        @error('bottom_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="upper_price">Upper Price</label>
                                            <input type="number" id="upper_price" name="upper_price" step="0.01" min="0" value="{{ old('upper_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        </div>
                                        @error('upper_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item items-center justify-center align-middle">
                                        <div class="flex flex-row gap-10 items-center justify-between">
                                            <label class="" for="retail_price">Retail Price</label>
                                            <input type="number" id="retail_price" name="retail_price" step="0.01" min="0" value="{{ old('retail_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                            
                                        </div>
                                        @error('retail_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                        </div>
                                    </div>
                                
                            </div>
                            <div class="flex flex-row justify-center items-center align-middle">
                                <button class="drink-create-submitbtn" type="submit">Submit</button>
                            </div>
                        </div>
                        
                    </form>
                    <a class="homelink mt-6" href="{{route('drinks')}}">Go Back</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imagePreview = document.getElementById('drink-image-preview');
                imagePreview.src = e.target.result;
            };
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
    
</x-app-layout>

<!--
<div class="flex flex-col drink-info-create-container summary-box">
                                    <div class="drink-create-item flex flex-col items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="drink_name">Drink Name</label>
                                            <input type="text" id="drink_name" name="drink_name" maxlength="20" value="{{ old('drink_name') }}">
                                        </div>
                                        @error('drink_name')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="drink-create-item flex flex-col items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="start_price">Start Price</label>
                                            <input type="number" id="start_price" name="start_price" step="0.01" min="0" value="{{ old('start_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        </div>
                                        @error('start_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item flex flex-col items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="bottom_price">Bottom Price</label>
                                            <input type="number" id="bottom_price" name="bottom_price" step="0.01" min="0" value="{{ old('bottom_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        </div>
                                        @error('bottom_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item flex flex-col items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="upper_price">Upper Price</label>
                                            <input type="number" id="upper_price" name="upper_price" step="0.01" min="0" value="{{ old('upper_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        </div>
                                        @error('upper_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="drink-create-item flex flex-col items-center justify-center align-middle">
                                        <div class="flex flex-row items-center justify-between">
                                            <label class="" for="retail_price">Retail Price</label>
                                            <input type="number" id="retail_price" name="retail_price" step="0.01" min="0" value="{{ old('retail_price') }}" pattern="^\d+(\.\d{1,2})?$">
                                        </div>
                                        @error('retail_price')
                                            <p class="text-custom-red">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
-->