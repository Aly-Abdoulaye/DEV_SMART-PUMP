<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historique des Prix - {{ $company->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .report-title {
            font-size: 16px;
            color: #7f8c8d;
            margin: 5px 0;
        }
        .export-info {
            font-size: 10px;
            color: #95a5a6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .trend-up {
            color: #e74c3c;
            font-weight: bold;
        }
        .trend-down {
            color: #27ae60;
            font-weight: bold;
        }
        .trend-stable {
            color: #7f8c8d;
        }
        .stats {
            margin: 20px 0;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        .stat-item {
            display: inline-block;
            margin-right: 20px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="report-title">Historique des Changements de Prix</div>
        <div class="export-info">
            Exporté le {{ $exportDate }} par {{ $user->name }}
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="stats">
        <div class="stat-item">Total: {{ $stats['total_changes'] }} changements</div>
        <div class="stat-item trend-up">Hausses: {{ $stats['increases'] }}</div>
        <div class="stat-item trend-down">Baisses: {{ $stats['decreases'] }}</div>
        <div class="stat-item trend-stable">Stables: {{ $stats['stable'] }}</div>
    </div>

    {{-- Tableau des changements --}}
    <table>
        <thead>
            <tr>
                <th>Carburant</th>
                <th>Ancien Prix</th>
                <th>Nouveau Prix</th>
                <th>Variation</th>
                <th>Date</th>
                <th>Modifié par</th>
                <th>Raison</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentPriceChanges as $change)
            <tr>
                <td><strong>{{ $change['fuel_name'] }}</strong></td>
                <td>{{ $change['old_price_formatted'] }}</td>
                <td><strong>{{ $change['new_price_formatted'] }}</strong></td>
                <td>
                    <span class="trend-{{ $change['trend'] }}">
                        {{ $change['change_percentage_formatted'] }}
                        ({{ $change['change_amount_formatted'] }})
                    </span>
                </td>
                <td>{{ $change['change_date'] }}</td>
                <td>{{ $change['changed_by'] }}</td>
                <td>{{ $change['reason'] ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Aucun changement de prix récent</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement par le système {{ config('app.name') }}
    </div>
</body>
</html>