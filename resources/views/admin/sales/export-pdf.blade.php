{{-- resources/views/admin/sales/export-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Ventes</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .header p {
            color: #7f8c8d;
            margin: 5px 0;
        }
        .company-info {
            text-align: left;
            margin-bottom: 20px;
        }
        .period-info {
            text-align: right;
            margin-bottom: 20px;
        }
        .stats-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #34495e;
            color: white;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>Rapport des Ventes</h1>
        <p>Système SMART PUMP - Administration</p>
        <p>Généré le : {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Informations de la période -->
    <div class="company-info">
        <strong>Entreprise :</strong> {{ Auth::user()->company->name ?? 'N/A' }}<br>
        <strong>Généré par :</strong> {{ Auth::user()->name }}
    </div>

    <div class="period-info">
        <strong>Période :</strong> 
        {{ $stats['debut_periode']->format('d/m/Y') }} 
        au 
        {{ $stats['fin_periode']->format('d/m/Y') }}
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['total_ventes'], 0, ',', ' ') }}</div>
            <div class="stat-label">Ventes</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['total_volume'], 2, ',', ' ') }} L</div>
            <div class="stat-label">Volume Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['total_montant'], 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Chiffre d'Affaires</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $stats['total_ventes'] > 0 ? number_format($stats['total_montant'] / $stats['total_ventes'], 0, ',', ' ') : 0 }} FCFA</div>
            <div class="stat-label">Moyenne/Vente</div>
        </div>
    </div>

    <!-- Tableau des ventes -->
    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Date & Heure</th>
                <th>Station</th>
                <th>Pompe</th>
                <th>Volume</th>
                <th>Prix U.</th>
                <th>Montant</th>
                <th>Mode Paiement</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $index => $sale)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                <td>{{ $sale->station->name ?? 'N/A' }}</td>
                <td class="text-center">Pompe {{ $sale->pump->numero ?? '' }}</td>
                <td class="text-right">{{ number_format($sale->volume, 2, ',', ' ') }} L</td>
                <td class="text-right">{{ number_format($sale->unit_price, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($sale->total_amount, 0, ',', ' ') }}</td>
                <td>
                    @php
                        $paymentLabels = [
                            'cash' => 'Espèces',
                            'card' => 'Carte',
                            'mobile_money' => 'Mobile Money',
                            'customer_account' => 'Compte Client'
                        ];
                    @endphp
                    {{ $paymentLabels[$sale->payment_method] ?? $sale->payment_method }}
                </td>
                <td class="text-center">
                    @if($sale->status == 'completed')
                    <span class="badge badge-completed">Complète</span>
                    @elseif($sale->status == 'cancelled')
                    <span class="badge badge-cancelled">Annulée</span>
                    @else
                    <span class="badge badge-pending">En attente</span>
                    @endif
                </td>
            </tr>
            @endforeach
            
            <!-- Ligne de total -->
            @if($sales->count() > 0)
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAUX :</td>
                <td class="text-right">{{ number_format($sales->sum('volume'), 2, ',', ' ') }} L</td>
                <td></td>
                <td class="text-right">{{ number_format($sales->sum('total_amount'), 0, ',', ' ') }} FCFA</td>
                <td colspan="2"></td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Ventilation par station -->
    @if($stations->count() > 1)
    <div style="margin-top: 40px;">
        <h3 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
            Ventilation par Station
        </h3>
        
        <table style="margin-top: 10px;">
            <thead>
                <tr>
                    <th>Station</th>
                    <th>Nombre de Ventes</th>
                    <th>Volume Total</th>
                    <th>Chiffre d'Affaires</th>
                    <th>% du Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = $stats['total_montant'];
                @endphp
                @foreach($stations as $station)
                @php
                    $stationSales = $sales->where('station_id', $station->id);
                    $stationAmount = $stationSales->sum('total_amount');
                    $percentage = $totalAmount > 0 ? ($stationAmount / $totalAmount) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $station->name }}</td>
                    <td class="text-center">{{ $stationSales->count() }}</td>
                    <td class="text-right">{{ number_format($stationSales->sum('volume'), 2, ',', ' ') }} L</td>
                    <td class="text-right">{{ number_format($stationAmount, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($percentage, 1, ',', ' ') }} %</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Pied de page -->
    <div class="footer">
        Document généré automatiquement par SMART PUMP - 
        Page 1 sur 1 - 
        {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>