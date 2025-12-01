<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Historique des Prix - {{ $fuel->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }
        .company-info {
            flex: 1;
        }
        .company-logo {
            max-height: 80px;
            max-width: 200px;
        }
        .document-title {
            text-align: center;
            flex: 2;
        }
        .document-title h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 20px;
        }
        .document-title .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        .fuel-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .fuel-info strong {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .variation-positive {
            color: #27ae60;
            font-weight: bold;
        }
        .variation-negative {
            color: #e74c3c;
            font-weight: bold;
        }
        .variation-neutral {
            color: #7f8c8d;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #7f8c8d;
            font-size: 10px;
        }
        .app-logo {
            max-height: 40px;
            max-width: 120px;
        }
        .footer-info {
            text-align: right;
        }
        .summary {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 10px;
            color: #7f8c8d;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- En-tête avec logo de l'entreprise -->
    <div class="header">
        <div class="company-info">
            @if($companyLogo)
                <img src="{{ $companyLogo }}" class="company-logo" alt="Logo entreprise">
            @else
                <div style="font-weight: bold; color: #2c3e50;">
                    {{ $company->name ?? 'Entreprise' }}
                </div>
            @endif
        </div>
        
        <div class="document-title">
            <h1>HISTORIQUE DES PRIX</h1>
            <div class="subtitle">{{ $fuel->name }}</div>
            <div class="subtitle">Période: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>
        </div>
        
        <div style="flex: 1;"></div> <!-- Espace vide pour l'équilibre -->
    </div>

    <div class="fuel-info">
        <strong>Carburant:</strong> {{ $fuel->name }} ({{ $fuel->code }})<br>
        <strong>Prix actuel:</strong> {{ number_format($fuel->current_price ?? $fuel->price, 0, ',', ' ') }} FCFA/L<br>
        <strong>Station:</strong> {{ $fuel->station->name ?? 'Non assigné' }}<br>
        @if($company)
            <strong>Entreprise:</strong> {{ $company->name }}
        @endif
    </div>

    @if($priceHistory->count() > 0)
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $priceHistory->count() }}</div>
                <div class="summary-label">CHANGEMENTS</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">
                    @php
                        $firstChange = $priceHistory->first();
                        $lastChange = $priceHistory->last();
                        $totalChange = $firstChange ? ($lastChange->new_price - $firstChange->old_price) : 0;
                    @endphp
                    {{ number_format($totalChange, 0, ',', ' ') }} FCFA
                </div>
                <div class="summary-label">VARIATION TOTALE</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">
                    @if($firstChange && $firstChange->old_price > 0)
                        {{ number_format(($totalChange / $firstChange->old_price) * 100, 2) }}%
                    @else
                        0%
                    @endif
                </div>
                <div class="summary-label">ÉVOLUTION</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Ancien Prix</th>
                <th>Nouveau Prix</th>
                <th>Variation</th>
                <th>%</th>
                <th>Raison</th>
                <th>Modifié par</th>
            </tr>
        </thead>
        <tbody>
            @foreach($priceHistory as $history)
            @php
                $changeAmount = $history->new_price - $history->old_price;
                $changePercentage = $history->old_price > 0 ? 
                    (($history->new_price - $history->old_price) / $history->old_price) * 100 : 0;
            @endphp
            <tr>
                <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ number_format($history->old_price, 0, ',', ' ') }} FCFA</td>
                <td><strong>{{ number_format($history->new_price, 0, ',', ' ') }} FCFA</strong></td>
                <td class="variation-{{ $changeAmount > 0 ? 'positive' : ($changeAmount < 0 ? 'negative' : 'neutral') }}">
                    {{ $changeAmount > 0 ? '+' : '' }}{{ number_format($changeAmount, 0, ',', ' ') }} FCFA
                </td>
                <td class="variation-{{ $changeAmount > 0 ? 'positive' : ($changeAmount < 0 ? 'negative' : 'neutral') }}">
                    {{ $changeAmount > 0 ? '+' : '' }}{{ number_format($changePercentage, 2, ',', ' ') }}%
                </td>
                <td>{{ $history->change_reason }}</td>
                <td>{{ $history->changedByUser->name ?? 'Système' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <h3>Aucun historique de prix disponible</h3>
        <p>Aucun changement de prix n'a été enregistré pour cette période.</p>
    </div>
    @endif

    <!-- Pied de page avec logo de l'application -->
    <div class="footer">
        <div>
            @if($appLogo)
                <img src="{{ $appLogo }}" class="app-logo" alt="Logo application">
            @endif
        </div>
        
        <div class="footer-info">
            Document généré le {{ now()->format('d/m/Y à H:i') }}<br>
            {{ config('app.name') }} - Système de gestion des carburants
        </div>
    </div>
</body>
</html>