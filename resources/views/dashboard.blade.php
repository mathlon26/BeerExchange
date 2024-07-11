<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center amaranth-bold font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome to your dashboard!') }}
        </h2>
    </x-slot>

    @if (! Auth::user()->marketOpen())
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="p-6 flex items-center justify-center">
                    <div class="bg-white rounded-lg shadow-lg p-6 flex">
                        <form method="POST" action="{{ route('market.create') }}" class="w-full">
                            @csrf
                            
                            <div class="db-event-column db-event-column1">
                                <h1 class="db-event-title">Start Your Event Here!</h1>
                            </div>
                            <div class="db-event-column db-event-column2">
                                <label class="db-event-label" for="event_name">Choose your event name</label>
                                <input class="db-event-input" type="text" id="event_name" name="name" required value="{{ old('event_name') }}" placeholder="Event Name">
                            </div>
                            <div class="db-event-column db-event-column3">
                                <label class="db-event-label" for="currency">Currency Unit</label>
                                <select class="db-event-select" id="currency" name="unit" required>
                                    <option value="$">USD</option>
                                    <option value="€">EUR</option>
                                    <option value="£">GBP</option>
                                </select>
                            </div>
                            <div class="db-event-column db-event-column4">
                                <button class="db-event-button" type="submit">Start Now</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @else
    @php
        $user = Auth::user();
        $market = $user->markets()->first();
        $drinks = $market->drinks;
    @endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($drinks->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Header section -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-center font-semibold text-xl text-gray-800 leading-tight">
                            Event Dashboard for {{ $market->name }}
                        </h2>
                        <button class="btn-custom-blue px-4 py-2 text-white rounded ">End Event</button>
                    </div>

                    
                        
                    <!-- Summary Info -->
                    <div class="flex flex-wrap gap-6 mb-6">
                        <div class="summary-box p-4 rounded-lg shadow flex-1">
                            <h2 class="text-lg font-bold mb-2">Drinks Sold</h2>
                            @php

                                $totalSold = 0;
                                foreach ($drinks as $drink) {
                                    $totalSold += $drink->amount_sold;
                                }
                                
                            @endphp
                            
                            <p class="text-3xl font-bold text-custom-blue">#{{$totalSold}}</p>
                        </div>
                        <div class="summary-box p-4 rounded-lg shadow flex-1">
                            <h2 class="text-lg font-bold mb-2">Active Drinks</h2>
                            <p class="text-3xl font-bold text-custom-blue">{{$drinks->count()}}</p>
                        </div>
                        <div class="summary-box p-4 rounded-lg shadow flex-1">
                            <h2 class="text-lg font-bold mb-2">Profit and Loss</h2>
                            @if ($market->profit < 0)
                            <p class="text-3xl font-bold text-custom-red">-${{$market->profit}}</p>
                            @else
                            <p class="text-3xl font-bold text-custom-green">${{$market->profit}}</p>
                            @endif
                        </div>
                    </div>
    
                    <!-- Graph Section -->
                    <div>
                        <div class="summary-box p-4 rounded-lg shadow mb-6">
                            <!-- Select box for drinks -->
                            <div class="flex gap-4&² items-center text-left mb-4">
                                <select id="drinkSelect" class="py-2 border border-gray-300 rounded-lg">
                                    @foreach ($drinks as $drink)
                                        <option value="{{ $drink->id }}" data-drink-id="{{ $drink->id }}">{{ $drink->name }}</option>
                                    @endforeach
                                </select>
                                <button class="px-4 py-2 bg-custom-green text-white rounded hover:bg-custom-green:hover">Pump</button>
                                <button class="px-4 py-2 bg-custom-red text-white rounded hover:bg-custom-red:hover">Dump</button>
                            </div>

                            <!-- Placeholder for graph or chart -->
                            <div id="chartDashboard" class="bg-white p-4 border border-gray-300 rounded-lg">
                                <div id="chartDashboardContainer"></div>
                                </div>
                            </div>
                            
                        </div>
                    


                    @php
                        $mostPopularDrink = $drinks->first();
                        $leastPopularDrink = $drinks->first();

                        foreach ($drinks as $drink) {
                            if ($drink->amount_sold > $mostPopularDrink->amount_sold) {
                                $mostPopularDrink = $drink;
                            }
                        }

                        foreach ($drinks as $drink) {
                            if ($drink->amount_sold < $leastPopularDrink->amount_sold) {
                                $leastPopularDrink = $drink;
                            }
                        }
                    @endphp

                    <div class="flex flex-wrap gap-6 mb-6">
                        <div class="summary-box p-4 rounded-lg shadow flex-1">
                            <h2 class="text-lg font-bold mb-2">Most Popular Drink</h2>
                            <p class="text-3xl font-bold text-custom-blue">{{ $mostPopularDrink->name }}</p>
                            <p class="text-3xl font-bold text-custom-blue">{{ $mostPopularDrink->amount_sold }} drinks sold</p>
                        </div>
                        <div class="summary-box p-4 rounded-lg shadow flex-1">
                            <h2 class="text-lg font-bold mb-2">Least Popular Drink</h2>
                            <p class="text-3xl font-bold text-custom-blue">{{ $leastPopularDrink->name }}</p>
                            <p class="text-3xl font-bold text-custom-blue">{{ $leastPopularDrink->amount_sold }} drinks sold</p>
                        </div>
                        <div class="summary-box p-4 rounded-lg shadow flex-1">
                            <h2 class="text-lg font-bold mb-2">Thanks for using</h2>
                            <h2 class="text-lg font-bold mb-2">BeerExchange.com!</h2>
                        </div>
                    </div>
    
                    
    
                    <!-- Buttons for Each Drink (assuming dynamic data) -->
                    <div>
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($drinks as $drink)
                        @php
                            $i += 1;
                        @endphp
                        <div class="summary-box flex justify-between items-center rounded-lg p-4 mb-2">
                            
                            <div class="flex items-center">
                                <span class="m-2 text-lg font-bold">#{{ $i }}</span>
                                <a href="#" class="cashier-drink-logo bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                                    <img class="drink-cashier-img" src="{{ asset($drink->logo) }}" alt="Logo">
                                </a>
                                <span class="m-2 text-lg font-bold">{{ $drink->name }}</span>
                            </div>
                            <span class="m-2 text-lg font-bold">{{ $drink->amount_sold }} drinks sold!</span>

                            <div>
                                <button class="px-4 py-2 btn-custom-blue text-white rounded">Edit</button>
                                <button class="px-4 py-2 bg-custom-green text-white rounded hover:bg-custom-green:hover">Pump</button>
                                <button class="px-4 py-2 bg-custom-red text-white rounded hover:bg-custom-red:hover">Dump</button>
                            </div>
                            
                        </div>
                        @endforeach
                        <!-- Placeholder for dynamic drinks -->
                        <!-- Replace with actual data and logic to display buttons for each drink -->
                    </div>
                    
    
    
                </div>
            </div>
            @else
                    <div class="py-12">
                        
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <!-- Header section -->
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-center font-semibold text-xl text-gray-800 leading-tight">
                                        Event Dashboard for {{ $market->name }}
                                    </h2>
                                    <button class="btn-custom-blue px-4 py-2 text-white rounded ">End Event</button>
                                </div>
                                <h2 class="text-center text-lg font-bold text-gray-800 leading-tight">First create drinks to see their performance in the <a href="{{ route('drinks') }}" class="drinks-dashboard-link">drinks</a> tab.</h2>
                            </div>
                        </div>
                    </div>
                    @endif
        </div>
    </div>
    
    @endif
    <script>
        async function fetchPriceHistory(drinkId) {
            try {
                const response = await fetch(`/api/drink/${drinkId}/price-history`);
                let data = await response.json();
                data = JSON.parse(data.price_history);

                for (var i = 0; i < data.times.length; i++) {
                    data.times[i] = data.times[i].split('T')[1].split('.')[0];
                }

                while (data.prices.length > 10) {
                    data.prices.shift();
                    data.times.shift();
                }
                return data;
            } catch (error) {
                console.error('Error fetching data:', error);
                return { times: [], prices: [] };
            }
        }

        function getSelectedOptionText(selectElement) {
            const selectedOption = selectElement.selectedOptions[0];
            return selectedOption ? selectedOption.text : '';
        }

        async function initializeChart() {
            const selectElement = document.getElementById('drinkSelect');
            const drinkName = selectElement.value;
            const drinkId = document.getElementById('drinkSelect').value;
            const data = await fetchPriceHistory(drinkId);

            const myChart = Highcharts.chart('chartDashboardContainer', {
                chart: {
                    animation: false,
                    type: 'line'
                },
                title: {
                    text: getSelectedOptionText(selectElement)
                },
                yAxis: {
                    min: parseFloat(data.borders.min),
                    max: parseFloat(data.borders.max)
                },
                xAxis: [{
                    title: {
                        text: "Time"
                    },
                    categories: data.times
                }],
                series: [{
                    name: 'Price',
                    data: data.prices
                }],
                accessibility: {
                    enabled: false
                },

                time: {
                    useUTC: false
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },

                exporting: {
                    enabled: false
                },
                // set the y axis range from data.borders.min to data.borders.max
            });


            setInterval(async function() {
                const drinkId = selectElement.value;
                const newData = await fetchPriceHistory(drinkId);

                myChart.series[0].setData(newData.prices);

                myChart.xAxis[0].setCategories(newData.times);

                myChart.setTitle({ text: getSelectedOptionText(selectElement) });
                myChart.yAxis[0].setExtremes(parseFloat(newData.borders.min), parseFloat(newData.borders.max));

            }, 1000);
        }
        document.getElementById('drinkSelect').addEventListener('change', initializeChart);
        initializeChart();
    </script>
        
</x-app-layout>
