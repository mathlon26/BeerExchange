<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Drink') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('drinks.update', $drink) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="form-input w-full" value="{{ old('name', $drink->name) }}">
                        </div>

                        <div class="mb-4">
                            <label for="logo" class="block text-gray-700">Logo</label>
                            <input type="text" name="logo" id="logo" class="form-input w-full" value="{{ old('logo', $drink->logo) }}">
                        </div>

                        <div class="mb-4">
                            <label for="market_price" class="block text-gray-700">Market Price</label>
                            <input type="number" name="market_price" id="market_price" class="form-input w-full" value="{{ old('market_price', $drink->market_price) }}">
                        </div>

                        <!-- Add other fields as necessary -->

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">Update Drink</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
