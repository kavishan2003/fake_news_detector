

<div>
    <div class="container mx-auto px-4 py-10">
        <div class="text-center mb-12 animate-fade-in-down">
            <h1 class="text-5xl font-extrabold text-gray-800 tracking-tight">Analytics Dashboard</h1>
            <p class="text-gray-600 mt-3 text-lg">Insights into domain fakeness scores</p>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Average Fakeness by Domain</h2>
            <div class="mb-8">
                <canvas id="fakenessChart"></canvas>
            </div>

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

    @script
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
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
        </script>
    @endscript




</div>
