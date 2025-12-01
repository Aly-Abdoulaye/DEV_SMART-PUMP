<?php

namespace App\Exports;

use App\Models\Fuel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FuelsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Données à exporter
     */
    public function collection()
    {
        return Fuel::with(['tanks', 'priceHistories'])
            ->where('company_id', $this->companyId)
            ->get();
    }

    /**
     * En-têtes des colonnes
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Code',
            'Prix (FCFA/L)',
            'Description',
            'Statut',
            'Nombre de cuves',
            'Stock total (L)',
            'Capacité totale (L)',
            'Taux de remplissage (%)',
            'Dernière modification',
        ];
    }

    /**
     * Mapping des données
     */
    public function map($fuel): array
    {
        $totalStock = $fuel->tanks->sum('current_volume');
        $totalCapacity = $fuel->tanks->sum('capacity');
        $utilizationRate = $totalCapacity > 0 ? ($totalStock / $totalCapacity) * 100 : 0;

        return [
            $fuel->id,
            $fuel->name,
            $fuel->code,
            $fuel->price,
            $fuel->description ?? 'N/A',
            $fuel->is_active ? 'Actif' : 'Inactif',
            $fuel->tanks->count(),
            $totalStock,
            $totalCapacity,
            round($utilizationRate, 2),
            $fuel->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Styles du fichier Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tête
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD3D3D3'],
                ],
            ],
            // Style alterné pour les lignes
            'A2:Z1000' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF0F0F0'],
                ],
            ],
        ];
    }

    /**
     * Titre de l'onglet
     */
    public function title(): string
    {
        return 'Liste des Carburants';
    }
}