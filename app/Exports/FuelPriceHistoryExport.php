<?php

namespace App\Exports;

use App\Models\Fuel;
use App\Models\FuelPriceHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FuelPriceHistoryExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $fuel;

    public function __construct(Fuel $fuel)
    {
        $this->fuel = $fuel;
    }

    public function collection()
    {
        return FuelPriceHistory::where('fuel_id', $this->fuel->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date et Heure',
            'Ancien Prix (FCFA)',
            'Nouveau Prix (FCFA)',
            'Variation (FCFA)',
            'Variation (%)',
            'Raison du changement',
            'Modifié par'
        ];
    }

    public function map($history): array
    {
        $changeAmount = $history->new_price - $history->old_price;
        $changePercentage = $history->old_price > 0 ? 
            (($history->new_price - $history->old_price) / $history->old_price) * 100 : 0;

        return [
            $history->created_at->format('d/m/Y H:i'),
            number_format($history->old_price, 0, ',', ' '),
            number_format($history->new_price, 0, ',', ' '),
            number_format($changeAmount, 0, ',', ' '),
            number_format($changePercentage, 2, ',', ' ') . '%',
            $history->change_reason,
            $history->changedByUser->name ?? 'Système'
        ];
    }

    public function title(): string
    {
        return 'Historique Prix';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tête
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE6E6FA']
                ]
            ],
        ];
    }
}