<div>


    <div>

        {{-- navbar --}}
        <nav class="bg-white shadow-lg py-4 fixed w-full top-0 z-50">
            <div class="container mx-auto px-4 flex justify-between items-center">
                {{-- Logo/Brand (Optional) --}}
                <a href="#"
                    class="text-2xl font-bold text-blue-700 hover:text-blue-800 transition-colors duration-200">
                    Fake News Detector
                </a>

                {{-- Navigation Links --}}
                <div>
                    <ul class="flex space-x-8">
                        <li>
                            <a href="/"
                                class="text-gray-700 hover:text-blue-600 font-semibold text-lg transition-colors duration-200">
                                Home
                            </a>
                        </li>
                        <li>
                            <a wire:navigate href="{{ route('analytics') }}"
                                class="text-gray-700 hover:text-blue-600 font-semibold text-lg transition-colors duration-200">
                                Analytics
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>






        {{-- Bar Chart for Average Fakeness Scores --}}
        <div class="container mx-auto px-6 py-20">
            <div class="text-center mb-12 animate-fade-in-down">
                <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl mb-12">
                    <div class="bg-white p-6 rounded-2xl shadow-xl">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Average Fakeness Score by Domain
                        </h2>
                        <canvas id="fakenessBarChart"></canvas>
                    </div>

                    {{-- Pie Chart for Article Distribution --}}
                    <div class="bg-white p-6 rounded-2xl shadow-xl">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Article Distribution by Domain
                        </h2>
                        <canvas id="articlePieChart"></canvas>
                    </div>
                </div>

                {{-- <div class="mt-12">
                    <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Domain Statistics</h2>
                    @if (count($domainAnalytics) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($domainAnalytics as $analytics)
                                <div
                                    class="bg-white rounded-2xl shadow-lg p-6 transform transition-transform duration-300 hover:-translate-y-1 hover:shadow-xl flex flex-col items-center justify-center text-center">
                                    <p class="text-xl font-bold text-blue-700 mb-2">{{ $analytics['domain'] }}</p>
                                    <div class="flex items-center justify-center space-x-4">
                                        <div class="text-gray-700 text-lg font-semibold">
                                            Articles: <span
                                                class="text-2xl font-extrabold text-indigo-600">{{ $analytics['article_count'] }}</span>
                                        </div>
                                        <div class="text-gray-700 text-lg font-semibold">
                                            Avg Score: <span
                                                class="text-2xl font-extrabold {{ $analytics['average_score'] >= 70 ? 'text-red-600' : ($analytics['average_score'] >= 30 ? 'text-yellow-600' : 'text-green-600') }}">{{ $analytics['average_score'] }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-600 text-lg">No article data available for analytics yet.</p>
                    @endif
                </div> --}}
            </div>
        </div>
    </div>
    {{-- table --}}

    <div class="container mx-auto px-6 py-20">
        <div class="text-center mb-12 animate-fade-in-down">
            <h1 class="text-5xl font-extrabold text-gray-800 tracking-tight">Analytics Dashboard</h1>
            <p class="text-gray-600 mt-3 text-lg">Insights into domain fakeness scores</p>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl mb-12">
            {{-- <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Average Fakeness by Domain</h2>
            <div class="mb-8">
                <canvas id="fakenessChart"></canvas>
            </div> --}}

            <h3 class="text-2xl font-bold text-gray-800 mb-4">Domain Breakdown</h3>
            @if (count($domainAnalytics) > 0)
                <div class="overflow-x-auto rounded-xl shadow-md">
                    <table class="min-w-full bg-white border-collapse">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="py-3 px-6 text-left text-sm font-semibold rounded-tl-xl">Domain</th>
                                <th class="py-3 px-6 text-left text-sm font-semibold">Average Fakeness Score (%)</th>
                                <th class="py-3 px-6 text-left text-sm font-semibold rounded-tr-xl">Articles Analyzed
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($domainAnalytics as $data)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b border-gray-200">
                                    <td class="py-4 px-6 text-gray-800 font-medium">{{ $data['domain'] }}</td>
                                    <td class="py-4 px-6 text-gray-800">
                                        <span
                                            class="font-bold {{ $data['average_score'] >= 70 ? 'text-red-600' : ($data['average_score'] >= 30 ? 'text-yellow-600' : 'text-green-600') }}">
                                            {{ $data['average_score'] }}%
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-800">{{ $data['article_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-600 mt-8">No articles analyzed yet to display analytics.</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@script
    <script src="https://cdn.jsdelivr.net/npm/chart.js">
        document.addEventListener('livewire:initialized', () => {
            // Ensure Chart.js is loaded and canvas exists
            const ctx = document.getElementById('fakenessChart');

            if (ctx) {
                new Chart(ctx, {
                    type: 'bar', // You can change this to 'pie', 'line', etc.
                    data: {
                        labels: @json($chartLabels), // Data from Livewire component
                        datasets: [{
                            label: 'Average Fakeness Score (%)',
                            data: @json($chartData), // Data from Livewire component
                            backgroundColor: [
                                // Dynamically color bars based on score (example)
                                @foreach ($chartData as $score)
                                    '{{ $score >= 70 ? '#ef4444' : ($score >= 30 ? '#f59e0b' : '#22c55e') }}',
                                @endforeach
                            ],
                            borderColor: [
                                @foreach ($chartData as $score)
                                    '{{ $score >= 70 ? '#dc2626' : ($score >= 30 ? '#d97706' : '#16a34a') }}',
                                @endforeach
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Allow canvas to resize freely
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100, // Fakeness score is 0-100%
                                title: {
                                    display: true,
                                    text: 'Average Fakeness Score (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Domain'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // No need for legend if only one dataset
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        // pie chart




        document.addEventListener('livewire:navigated', () => {
            // Bar Chart (existing)
            const barCtx = document.getElementById('fakenessBarChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Average Fakeness Score',
                            data: @json($chartData),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Score (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Domain'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Pie Chart (new)
            const pieCtx = document.getElementById('articlePieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: @json($pieChartLabels),
                        datasets: [{
                            label: 'Number of Articles',
                            data: @json($pieChartData),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)', // Red
                                'rgba(54, 162, 235, 0.8)', // Blue
                                'rgba(255, 206, 86, 0.8)', // Yellow
                                'rgba(75, 192, 192, 0.8)', // Teal
                                'rgba(153, 102, 255, 0.8)', // Purple
                                'rgba(255, 159, 64, 0.8)', // Orange
                                'rgba(102, 255, 102, 0.8)', // Light Green
                                'rgba(255, 102, 204, 0.8)', // Pink
                                'rgba(102, 204, 255, 0.8)' // Light Blue
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + ' articles';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endscript
