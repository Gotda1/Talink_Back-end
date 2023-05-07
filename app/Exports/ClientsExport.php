<?php

namespace App\Exports;

use App\Models\Client;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientsExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithEvents, WithMapping, WithColumnFormatting
{
    use HelperTrait, Exportable, RegistersEventListeners;

    public function __construct(){
        
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        try {            
            $clients = Client::select(
                                 DB::raw("clients.*"),
                                 DB::raw("at.code AS acquirer_type"),
                                 DB::raw("u.name AS seller"),
                                )
                                ->leftJoin("users as u", "user_id", "=", "u.id")
                                ->leftJoin("acquirers_type as at", "acquirer_type_code", "=", "at.id")
                                ->when(!$this->hasPrivilege("ALLCLNT"), function($query){
                                    $query->whereIn("user_id", [0, Auth::id()]);
                                })
                                ->get();   

            return $clients;
        } catch (\Throwable $e) {
            report($e);
            return collect();
        }
    }

    public function map($row): array
    {       
        return [
            $row->code,
            $row->name,
            $row->rfc,
            $row->description,
            $row->email,
            $row->phone,
            $row->location,
            $row->address,
            $row->seller ?: "-",
            $row->balance,
            $row->status == 0 ? "Inactivo" : "Activo",
        ];        
    }

    public function headings(): array
    {
        return [
            [
                "Catálogo de Clientes"
            ],
            [
                'Clave',
                "Nombre",
                'R.F.C.',
                'Descripción',
                'Email',
                'Teléfono',
                'Localidad',
                'Dirección',
                "Reclutador",
                "Balance",
                "Estatus"
            ]
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getStyle('A1:K2')->getFont()->setBold(true);

        $event->sheet->getDelegate()->mergeCells('A1:K1');

        $row = $event->sheet->getHighestRow();
        $newrow = $event->sheet->getHighestRow() + 1;
        
        $event->sheet->getDelegate()->setCellValue("I{$newrow}","Totales");
        $event->sheet->getStyle("I{$newrow}")->getFont()->setBold(true);

        $event->sheet->getDelegate()->getStyle("J{$newrow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
        $event->sheet->getDelegate()->setCellValue("J{$newrow}","=SUM(H3:H{$row})");


    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_CURRENCY_USD
        ];
    }
}
