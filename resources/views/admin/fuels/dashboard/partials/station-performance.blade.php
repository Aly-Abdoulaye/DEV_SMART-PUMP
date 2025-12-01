{{-- resources/views/admin/fuels/dashboard/partials/station-performance.blade.php --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar"></i> Performance des Stations
        </h6>
    </div>
    <div class="card-body">
        <div class="chart-bar">
            <canvas id="stationPerformanceChart" width="400" height="200"></canvas>
        </div>
        
        @if(isset($stationPerformance) && count($stationPerformance) > 0)
            <div class="mt-3">
                <div class="list-group list-group-flush">
                    @foreach($stationPerformance as $performance)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-gas-pump text-primary"></i>
                                {{ $performance['station_name'] }}
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ $performance['total_sales'] }} L</div>
                                <small class="text-muted">{{ $performance['revenue'] }} FCFA</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-muted text-center py-3">Aucune donnée de performance disponible</p>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données simulées pour le graphique de performance
    const stationData = @json($stationPerformance ?? []);
    
    if (stationData.length > 0) {
        const ctx = document.getElementById('stationPerformanceChart').getContext('2d');
        const labels = stationData.map(item => item.station_name);
        const salesData = stationData.map(item => item.total_sales);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventes (L)',
                    data: salesData,
                    backgroundColor: [
                        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>