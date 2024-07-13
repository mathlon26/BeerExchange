<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center amaranth-bold font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cashier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()->marketOpen())
                @if (count($drinks) > 0)

                <form method="POST" action="{{ route('checkout') }}">
                    @csrf
                    <div class="drinks-container">
                        @foreach ($drinks as $drink)
                            <div class="drink-cashier-container" data-drink-id="{{ $drink->id }}">
                                <a href="#" class="cashier-drink-logo bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                                    <img class="drink-cashier-img" src="{{ asset($drink->logo) }}" alt="Logo">
                                </a>
                                <div class="drink-info">
                                    <a href="#">{{ $drink->name }}</a>
                                    <h2 id="market-price-{{ $drink->id }}">{{$drink->market->unit . number_format($drink->market_price, 2) }}</h2>
                                </div>
                                <div class="quantity-control">
                                    <button type="button" onclick="changeQuantity({{ $drink->id }}, -1)">-</button>
                                    <input type="text" name="quantity[{{ $drink->id }}]" id="quantity-{{ $drink->id }}" value="0" min="0">
                                    <button type="button" onclick="changeQuantity({{ $drink->id }}, 1)">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-center align-middle text-center">
                        <button type="submit" class="checkout-button">Checkout</button>
                    </div>
                </form>
                @else
                <div class="drinks-container">
                    <div class="drink-cashier-container" data-drink-id="-1">
                        <a href="{{ route('drinks.create') }}" class="cashier-drink-logo bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                            <img class="drink-cashier-img" src="{{ asset('images/BeerLogo.webp') }}" alt="Logo">
                        </a>
                        <div class="drink-info">
                            <a href="#">Create your first drink!</a>
                            <h2 id="market-price--1">$...</h2>
                        </div>
                        <div class="quantity-control">
                            <a href="{{ route('drinks.create') }}" onclick="">Create Drink</a>
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="text-center text-gray-500">
                    Market is closed. Please open a new market session in your <a href="{{ route('dashboard') }}" class="drinks-dashboard-link">dashboard</a> before selling your drinks.
                </div>
            @endif
        </div>
    </div>

    <script>
        function changeQuantity(drinkId, amount) {
            const quantityInput = document.getElementById(`quantity-${drinkId}`);
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity += amount;
            if (currentQuantity < 0) {
                currentQuantity = 0;
            }
            quantityInput.value = currentQuantity;
        }

        document.addEventListener('DOMContentLoaded', function() {
            function updateMarketPrices() {
                var containers = document.querySelectorAll('.drink-cashier-container');
                containers.forEach(function(container) {
                    var drinkId = container.getAttribute('data-drink-id');
                    fetch('/api/drink/' + drinkId + '/price')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.error);
                            }
                            return response.json();
                        })
                        .then(data => {
                            var priceElement = container.querySelector(`#market-price-${drinkId}`);
                            if (priceElement) {
                                priceElement.textContent = data.market_price;
                            }
                        })
                        .catch(error => {
                            console.error('Failed to fetch market price:', error);
                        });
                });
            }

            // Update market prices every 10 seconds
            setInterval(updateMarketPrices, 1000);

            // Initial call to update prices when page loads
            updateMarketPrices();
        });
    </script>
</x-app-layout>
