<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center amaranth-bold font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Drinks Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()->marketOpen())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Your Drinks</h3>
                    
                    
                    <div class="container mx-auto">
                        @if (count($drinks) > 0)
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="py-2 px-4">Logo</th>
                                    <th class="py-2 px-4">Drink Name</th>
                                    <th class="py-2 px-4">Market Price</th>
                                    <th class="py-2 px-4">Cost Price</th>
                                    <th class="py-2 px-4">Actions</th>
                                    <th class="py-2 px-4"></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($drinks as $drink)
                                    <tr class="border-t">
                                        <td class="py-2 px-4">
                                            @if (File::exists(public_path($drink->logo)))
                                            <img src="{{ asset($drink->logo) }}" alt="Logo" class="drink-cashier-img">
                                            @else
                                            <img src="{{ asset("images/BeerLogo.webp") }}" alt="Logo" class="drink-cashier-img">
                                            @endif
                                            
                                        </td>
                                        <td class="py-2 px-4">{{ $drink->name }}</td>
                                        <td class="py-2 px-4">${{ $drink->market_price }}</td>
                                        <td class="py-2 px-4">${{ $drink->cost_price }}</td>
                                        <td class="py-2 px-4 actions">
                                            <a href="{{ route('drinks.edit', $drink) }}" class="edit-btn">Edit</a>
                                            @if ($drink->allow_manualcrash)
                                            <a href="{{ route('drinks.edit', $drink) }}" class="pump-btn">Pump</a>
                                            <a href="{{ route('drinks.edit', $drink) }}" class="dump-btn">Dump</a>
                                            @else
                                            Edit for more actions
                                            @endif
                                        </td>
                                        <td class="py-2 px-4">
                                            <button class="view-chart-btn">View Chart</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        <a href="{{ route('drinks.create') }}" class="add-drink-btn">
                            <i class="ri-add-circle-fill" &#xEA10;></i>
                            Add Drink
                        </a>
                    </div>
                    


                </div>
            </div>
            @else

                    <div class="text-center text-gray-500">
                        Market is closed. Please open a new market session in your <a href="{{ route('dashboard') }}" class="drinks-dashboard-link">dashboard</a> to create or manage drinks.
                    </div>

                    @endif
        </div>
    </div>
</x-app-layout>

