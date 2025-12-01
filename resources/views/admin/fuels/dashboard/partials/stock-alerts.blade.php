
@if(count($lowStockAlerts) > 0)
    @foreach($lowStockAlerts as $stationName => $alerts)
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex justify-content-between align-items-start">
                <strong class="flex-grow-1">{{ $stationName }}</strong>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            <ul class="mb-0 mt-2 pl-3">
                @foreach($alerts as $alert)
                <li class="small">
                    <span class="badge mr-1" style="background-color: {{ $alert['fuel_color'] }}; color: white;">
                        {{ $alert['fuel_name'] }}
                    </span>
                    : {{ number_format($alert['current_volume'], 0, ',', ' ') }}L / 
                    Seuil: {{ number_format($alert['min_threshold'], 0, ',', ' ') }}L
                </li>
                @endforeach
            </ul>
        </div>
    @endforeach
@else
    <div class="text-center text-success py-3">
        <i class="fas fa-check-circle fa-2x mb-2"></i>
        <p class="mb-0">Aucune alerte de stock</p>
        <small class="text-muted">Toutes les cuves sont approvisionnées</small>
    </div>
@endif