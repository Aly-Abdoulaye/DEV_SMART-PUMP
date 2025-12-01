<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Carburant</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Ventes Aujourd'hui</th>
                <th>Taux Remplissage</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Compter les alertes par carburant
                $alertsByFuel = [];
                foreach ($lowStockAlerts as $alert) {
                    $fuelName = $alert['fuel_type'];
                    if (!isset($alertsByFuel[$fuelName])) {
                        $alertsByFuel[$fuelName] = 0;
                    }
                    $alertsByFuel[$fuelName]++;
                }
            @endphp
            
            @forelse($fuelSummary as $fuel)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="color-indicator mr-2" 
                             style="background-color: {{ $fuel['color'] ?? '#007bff' }}; width: 16px; height: 16px; border-radius: 50%;"></div>
                        <strong>{{ $fuel['name'] ?? 'N/A' }}</strong>
                    </div>
                </td>
                <td class="font-weight-bold text-success">
                    {{ $fuel['current_price_formatted'] ?? '0 FCFA' }}
                </td>
                <td>
                    {{ number_format($fuel['current_volume'] ?? 0, 0, ',', ' ') }} L
                    <small class="text-muted d-block">
                        / {{ number_format($fuel['total_capacity'] ?? 0, 0, ',', ' ') }} L
                    </small>
                </td>
                <td>
                    <div class="text-primary font-weight-bold">
                        {{ number_format($fuel['today_sales_volume'] ?? 0, 0, ',', ' ') }} L
                    </div>
                    <small class="text-success">
                        {{ number_format($fuel['today_sales_revenue'] ?? 0, 0, ',', ' ') }} FCFA
                    </small>
                </td>
                <td>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar 
                            @if(($fuel['utilization_rate'] ?? 0) > 80) bg-success
                            @elseif(($fuel['utilization_rate'] ?? 0) > 50) bg-info
                            @elseif(($fuel['utilization_rate'] ?? 0) > 20) bg-warning
                            @else bg-danger
                            @endif" 
                            role="progressbar" 
                            style="width: {{ $fuel['utilization_rate'] ?? 0 }}%"
                            aria-valuenow="{{ $fuel['utilization_rate'] ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                        </div>
                    </div>
                    <small>{{ number_format($fuel['utilization_rate'] ?? 0, 1) }}%</small>
                </td>
                <td>
                    @if(($alertsByFuel[$fuel['name']] ?? 0) > 0)
                        <span class="badge badge-warning">
                            <i class="fas fa-exclamation-triangle"></i> {{ $alertsByFuel[$fuel['name']] }} alerte(s)
                        </span>
                    @else
                        <span class="badge badge-success">
                            <i class="fas fa-check"></i> OK
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-gas-pump fa-2x mb-2"></i>
                    <p>Aucun carburant configuré</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>