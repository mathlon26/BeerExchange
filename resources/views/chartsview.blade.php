<x-charts>
    <div class="bg-slate min-h-screen flex flex-col sm:justify-center items-center">
        
        <div class="px-2 w-full overflow-hidden sm:rounded-lg">
            
            <div class="w-full py-4 bg-white shadow-md overflow-hidden rounded-lg">
                <div>
                    <a href="/">
                        <x-application-logo class="p-2" />
                    </a>
                </div>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Graph Section -->
                    <div>
                        <h1 class="text-3xl text-center font-bold mb-6">{{$market->name}}</h1>
                        <div id="graphSection" class="summary-box p-4 rounded-lg shadow mb-6 w-full">
                            <!-- Select box for drinks -->
                            <div class="flex items-center text-left mb-4 justify-between">
                                <select id="drinkSelect" class="py-2 border border-gray-300 rounded-lg">
                                    @foreach ($drinks as $drink)
                                        <option value="{{ $drink->id }}" data-drink-id="{{ $drink->id }}">{{ $drink->name }}</option>
                                    @endforeach
                                </select>
                                <div>
                                    <a id="fullScreenButton" class="text-white mr-2 p-2 btn-custom-blue rounded disabled:opacity-50" href="#">Full Screen</a>
                                    <a id="allChartModeLink" class="text-white mr-2 p-2 btn-custom-blue rounded disabled:opacity-50" href="#">All Charts</a>
                                </div>
                            </div>
                            <!-- Placeholder for graph or chart -->
                            <div id="chartDashboard" class="bg-white p-4 border border-gray-300 rounded-lg">
                                <div id="chartDashboardContainer">
                                    <p id="loadingText" class="text-gray-500 text-sm text-center">Loading chart...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

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
            const drinkId = selectElement.value;
            const data = await fetchPriceHistory(drinkId);
            const market = @json($market);
            const unit = market.unit;
            const screenWidth = window.innerWidth;

            const myChart = Highcharts.chart('chartDashboardContainer', {
                chart: {
                    animation: false,
                    type: 'line',
                    events: {
                        load: function () {
                            const chart = this;

                            const latestPrice = data.prices[data.prices.length - 1];
                            const screenWidth = window.innerWidth;
                            const t = screenWidth < 956 ? ' ' : unit + latestPrice.toFixed(2);
                            chart.currentPriceLabel = chart.renderer.text(t, chart.plotWidth - 20, 50)
                                .attr({
                                    zIndex: 5
                                })
                                .css({
                                    color: 'black',
                                    fontWeight: 'bold',
                                    fontSize: '36px',
                                    textAlign: 'right'
                                })
                                .add();
                        }
                    }
                },
                title: {
                    text: getSelectedOptionText(selectElement),
                    style: {
                        fontSize: screenWidth < 956 ? '24px' : '50px'
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
                }
            });
            

            async function updateCurrentPriceLabel(newData, myChart) {
                const screenWidth = window.innerWidth;
                const market = @json($market);
                const unit = market.unit;

                const latestPrice = newData.prices[data.prices.length - 1];

                console.log(latestPrice);

                let titleFontSize = screenWidth < 956 ? '24px' : '50px';

                myChart.setTitle({
                    text: getSelectedOptionText(selectElement),
                    style: {
                        fontSize: titleFontSize
                    }
                });

                if (myChart.currentPriceLabel) {
                    myChart.currentPriceLabel.attr({
                        text: screenWidth < 956 ? ' ' : unit + latestPrice.toFixed(2),
                        x: myChart.plotWidth - 20,
                        y: 50
                    });
                } else {
                    myChart.currentPriceLabel = myChart.renderer.text(unit + latestPrice.toFixed(2), myChart.plotWidth - 20, 50)
                        .attr({
                            zIndex: 5
                        })
                        .css({
                            color: 'black',
                            fontWeight: 'bold',
                            fontSize: '36px',
                            textAlign: 'right'
                        })
                        .add();
                }
            }

        

            setInterval(async function () {
                const newData = await fetchPriceHistory(selectElement.value);

                myChart.series[0].setData(newData.prices);
                myChart.xAxis[0].setCategories(newData.times);
                myChart.yAxis[0].setExtremes(parseFloat(newData.borders.min), parseFloat(newData.borders.max));
                updateCurrentPriceLabel(newData, myChart);
            }, 1000);
        }

        document.getElementById('drinkSelect').addEventListener('change', initializeChart);
        const currentUrl = window.location.href;
        const allChartModeUrl = currentUrl + '/all';
        const allChartModeLink = document.getElementById('allChartModeLink');
        allChartModeLink.href = allChartModeUrl;

        
        const fullScreenButton = document.getElementById('fullScreenButton');
        fullScreenButton.addEventListener('click', function (event) {
            event.preventDefault();

            const element = document.getElementById('graphSection');
            const chartDashboard = document.getElementById('chartDashboard');
            const chartDashboardContainer = document.getElementById('chartDashboardContainer');
            const loadingText = document.getElementById('loadingText');

            if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
                fullScreenButton.textContent = 'Exit Full Screen';
                chartDashboard.style.height = '100vh';
                chartDashboardContainer.style.height = 'calc(100vh - 8rem)';
                loadingText.style.display = 'none';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                chartDashboard.style.height = '620px';
                chartDashboardContainer.style.height = '600px';
                fullScreenButton.textContent = 'Full Screen';
            }
        });

        document.addEventListener('fullscreenchange', exitFullscreenHandler);
        document.addEventListener('webkitfullscreenchange', exitFullscreenHandler);
        document.addEventListener('MSFullscreenChange', exitFullscreenHandler);

        function exitFullscreenHandler() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                fullScreenButton.textContent = 'Full Screen';
                chartDashboard.style.height = '620px';
                chartDashboardContainer.style.height = '600px';
            }
        }

        initializeChart();
    </script>
</x-charts>
