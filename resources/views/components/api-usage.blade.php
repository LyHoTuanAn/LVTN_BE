<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">API Usage Over Time</h3>
    </div>
    <div class="p-6">
        <canvas id="apiUsageChart" height="300"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('apiUsageChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'API Usage',
                        data: [400, 300, 200, 278, 189, 239, 349],
                        borderColor: '#8884d8',
                        tension: 0.3,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>

