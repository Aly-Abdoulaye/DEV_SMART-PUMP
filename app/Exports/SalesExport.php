<?php
// app/Exports/SalesExport.php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Station;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class SalesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;
    protected $stationIds;
    
    public function __construct(Request $request, $stationIds)
    {
        $this->request = $request;
        $this->stationIds = $stationIds;
    }
    
    public function query()
    {
        $query = Sale::with(['station', 'pump.fuel', 'employee', 'customer'])
                     ->whereIn('station_id', $this->stationIds);
        
        // Appliquer les mêmes filtres que dans le controller
        $this->applyFilters($query);
        
        return $query->orderBy('sale_date', 'desc');
    }
    
    public function headings(): array
    {
        return [
            'N° Ticket',
            'Date',
            'Heure',
            'Station',
            'Pompe',
            'Carburant',
            'Volume (L)',
            'Prix Unitaire',
            'Montant Total',
            'Mode Paiement',
            'Employé',
            'Client',
            'Indice Début',
            'Indice Fin',
            'Statut'
        ];
    }
    
    public function map($sale): array
    {
        // CORRECTION : Accéder aux relations avec les bons noms
        return [
            $sale->id, // ou un numéro de ticket personnalisé
            $sale->sale_date->format('d/m/Y'),
            $sale->sale_date->format('H:i:s'),
            $sale->station->name ?? '',
            'Pompe ' . ($sale->pump->numero ?? ''),
            $sale->pump->fuel->name ?? '',
            number_format($sale->volume, 2, ',', ''),
            number_format($sale->unit_price, 0, ',', ''),
            number_format($sale->total_amount, 0, ',', ''),
            $this->getPaymentMethodLabel($sale->payment_method),
            $sale->employee->name ?? '',
            $sale->customer->name ?? '',
            number_format($sale->start_index, 3, ',', ''),
            number_format($sale->end_index, 3, ',', ''),
            $this->getStatusLabel($sale->status),
        ];
    }
    
    private function getPaymentMethodLabel($method)
    {
        $labels = [
            'cash' => 'Espèces',
            'card' => 'Carte',
            'mobile_money' => 'Mobile Money',
            'customer_account' => 'Compte Client',
        ];
        
        return $labels[$method] ?? $method;
    }
    
    private function getStatusLabel($status)
    {
        $labels = [
            'completed' => 'Complète',
            'cancelled' => 'Annulée',
            'pending' => 'En attente',
        ];
        
        return $labels[$status] ?? $status;
    }
    
    private function applyFilters($query)
    {
        if ($this->request->filled('station_id')) {
            $query->where('station_id', $this->request->station_id);
        }
        
        if ($this->request->filled('pump_id')) {
            $query->where('pump_id', $this->request->pump_id);
        }
        
        if ($this->request->filled('payment_method')) {
            $query->where('payment_method', $this->request->payment_method);
        }
        
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        
        if ($this->request->filled('date_debut') && $this->request->filled('date_fin')) {
            $query->whereBetween('sale_date', [
                $this->request->date_debut,
                \Carbon\Carbon::parse($this->request->date_fin)->endOfDay()
            ]);
        }
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style de l'en-tête
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ]
            ],
            
            // Style pour les montants
            'H' => ['alignment' => ['horizontal' => 'right']],
            'I' => ['alignment' => ['horizontal' => 'right']],
            
            // Bordures
            'A1:O1' => ['borders' => ['bottom' => ['borderStyle' => 'thin']]],
        ];
    }
    
}