<x-charts>
    <div class="bg-white px-6 py-6">
        <!-- Parent container for the grid -->
        <div id="chartContainer" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        </div>
        @if (count($drinks) > 6)
        <div class="flex justify-between text-white">
                <a id="singleChartModeLink" class="mr-2 mt-4 p-4 btn-custom-blue rounded disabled:opacity-50" href="">Back to single chart mode</a>
                <div>
                    <button id="prevButton" onclick="prevButton()" class="mr-2 mt-4 p-4 btn-custom-blue rounded disabled:opacity-50" disabled>Previous</button>
                    <button id="nextButton" onclick="nextButton()" class="mt-4 p-4 btn-custom-blue rounded">Next</button>
                </div>
        </div>
        @else
        <div class="flex justify-between text-white">
            <a id="singleChartModeLink" class="mr-2 mt-4 p-4 btn-custom-blue rounded disabled:opacity-50" href="">Back to single chart mode</a>
        </div>
        @endif
    </div>
    
    <script>
        
        
        

        const drinks = @json($drinks);
        const chartContainer = document.getElementById('chartContainer');
        const cache = {}; // Cache for storing fetched data
        let currentPage = 0;
        const itemsPerPage = 6;

        async function fetchPriceHistory(drinkId, useCache = true) {
            if (useCache && cache[drinkId]) {
                return cache[drinkId]; // Return cached data if available
            }
            try {
                const response = await fetch(`/api/drink/${drinkId}/price-history`);
                let data = await response.json();
                data = JSON.parse(data.price_history);

                data.times = data.times.map(time => time.split('T')[1].split('.')[0]);
                while (data.prices.length > 10) {
                    data.prices.shift();
                    data.times.shift();
                }
                if (useCache) {
                    cache[drinkId] = data; // Cache the fetched data
                }
                return data;
            } catch (error) {
                console.error('Error fetching data:', error);
                return { times: [], prices: [] };
            }
        }

        async function renderCharts(page) {
            chartContainer.innerHTML = '';
            const startIndex = page * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const drinksToRender = drinks.slice(startIndex, endIndex);

            if (drinks.length > 6) {
                document.getElementById('prevButton').disabled = startIndex === 0;
                document.getElementById('nextButton').disabled = endIndex >= drinks.length;
            }
            

            for (const drink of drinksToRender) {
                const chartDiv = document.createElement('div');
                chartDiv.id = 'allChartContainer';
                chartDiv.innerHTML = `
                    <div class="summary-box p-4 rounded-lg shadow w-full">
                        <!-- Placeholder for graph or chart -->
                        <div id="chartDashboard" class="bg-white p-4 border border-gray-300 rounded-lg">
                            <div id="allChartDashboardContainer">
                                <div id="chart${drink.id}" class="text-center">
                                    <p class="text-gray-500 text-sm">Loading chart...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                chartContainer.appendChild(chartDiv);
            }

            for (const drink of drinksToRender) {
                const data = await fetchPriceHistory(drink.id);
                const market = @json($market);
                const unit = market.unit;
                const chart = Highcharts.chart(`chart${drink.id}`, {
                    chart: {
                        animation: false,
                        type: 'line',
                        events: {
                            load: function () {
                                const chart = this;

                                // Render the current price dynamically
                                const latestPrice = data.prices[data.prices.length - 1];
                                chart.currentPriceLabel = chart.renderer.text(unit + latestPrice.toFixed(2), chart.plotWidth - 10, 30)
                                    .attr({
                                        zIndex: 5
                                    })
                                    .css({
                                        color: 'black',
                                        fontWeight: 'bold',
                                        fontSize: '30px',
                                        textAlign: 'right'
                                    })
                                    .add();
                            }
                        }
                    },
                    title: {
                        text: drink.name,
                        style: {
                            fontSize: '36px'
                        }
                    },
                    yAxis: {
                        title: {
                            text: "Price"
                        },
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
                });

                function updateCurrentPriceLabel(newData, chart) {
                    const market = @json($market);
                    const unit = market.unit;
                    const latestPrice = newData.prices[data.prices.length - 1];

                    // Update or create the current price label
                    if (chart.currentPriceLabel) {
                        chart.currentPriceLabel.attr({ text: unit + latestPrice.toFixed(2) });
                    } else {
                        chart.currentPriceLabel = chart.renderer.text(unit + latestPrice.toFixed(2), chart.plotWidth - 10, 30)
                            .attr({
                                zIndex: 5
                            })
                            .css({
                                color: 'black',
                                fontWeight: 'bold',
                                fontSize: '30px',
                                textAlign: 'right'
                            })
                    }
                }

                setInterval(async function() {
                    const newData = await fetchPriceHistory(drink.id, false);
                    chart.series[0].setData(newData.prices);
                    chart.xAxis[0].setCategories(newData.times);
                    chart.setTitle({ text: drink.name });
                    chart.yAxis[0].setExtremes(parseFloat(newData.borders.min), parseFloat(newData.borders.max));
                    updateCurrentPriceLabel(newData, chart);
                }, 1000);
            }
        }

        

        async function prevButton() {
            if (currentPage > 0) {
                currentPage--;
                await renderCharts(currentPage);
            }
        }

        async function nextButton() {
            if ((currentPage + 1) * itemsPerPage < drinks.length) {
                currentPage++;
                await renderCharts(currentPage);
            }
        }

        const currentUrl = window.location.href;
        
        const singleChartModeUrl = currentUrl.replace(/\/all$/, '');
        
        const singleChartModeLink = document.getElementById('singleChartModeLink');
        singleChartModeLink.href = singleChartModeUrl;

        renderCharts(currentPage);
    </script>
</x-charts>


