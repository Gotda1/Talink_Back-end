<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Order;
use App\Models\Prospect;
use App\Models\Quotation;
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

class QuotationsExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithEvents, WithMapping, WithColumnFormatting
{
    use HelperTrait, Exportable, RegistersEventListeners;

    var $start_date;
    var $end_date;
    var $seller_id;
    var $status; 

    public function __construct($start_date, $end_date, $seller_id, $status){
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
        $this->seller_id  = $seller_id;
        $this->status     = $status;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        try {
            $ctx = $this;
            DB::enableQueryLog();
            
            $query = Quotation::select(
                DB::raw("
                quotations.id,
                quotations.invoice,
                quotations.created_at,
                a.name,
                quotations.taxes,
                quotations.subtotal,
                quotations.total,
                quotations.status,
                o.invoice AS order_invoice
            "))
            ->leftJoin("orders as o", "o.id", "=", "order_id")
            ->whereBetween("quotations.created_at", [$this->start_date, $this->end_date])
            ->when(!$this->hasPrivilege("ALLORDS"), function ($query) {
                $query->whereIn("seller_id", [0,Auth::id()]);
            })->when($this->seller_id, function ($query) use ($ctx) {
                $query->where("seller_id",$ctx->seller_id);
            })->when($this->status > -1, function ($query)  use ($ctx){
                $query->where("status", $ctx->status);
            });
            
            $quot_prospects = clone $query;
            $quot_clients   = clone $query; 

            $quot_prospects = $quot_prospects->leftJoin("prospects as a", "a.id", "=", "quotations.acquirer_id")
                                         ->where("catalogue", "=", "".DB::raw(Prospect::class)."")
                                         ->get();
            $quot_clients   = $quot_clients->where("catalogue", "=", "".DB::raw(Client::class)."")
                                         ->leftJoin("clients as a", "a.id", "=", "quotations.acquirer_id")
                                         ->get();
            
            $quotations = $quot_prospects->merge($quot_clients);
            return $quotations;
        } catch (\Throwable $e) {
            report($e);
            return collect();
        }
    }

    public function map($row): array
    {
        $status = "Pendiente";
        if($row->status == -1)
            $status = "Cancelada";
        elseif($row->status == 1)
            $status = "Resuelta";
        

        return [
            $row->id,
            $row->invoice,
            $row->created_at,
            $row->name,
            $row->taxes,
            $row->subtotal,
            $row->total,
            $status,
            $row->order_invoice ?: "-" ,
        ];
        
    }

    public function headings(): array
    {
        return [
            [
                "Reporte de Cotizaciones ". $this->start_date . " - " . $this->end_date
            ],
            [
                'ID',
                "Folio",
                'Fecha',
                'Cliente',
                'I.V.A.',
                'Subtotal',
                'Total',
                "Status",
                "Pedido"
            ]
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->mergeCells('A1:I1');
        $event->sheet->getStyle('A1:I2')->getFont()->setBold(true);

        $row = $event->sheet->getHighestRow();
        $newrow = $event->sheet->getHighestRow() + 1;
        $event->sheet->getDelegate()->setCellValue("D{$newrow}","Totales");
        $event->sheet->getStyle("D{$newrow}")->getFont()->setBold(true);

        $event->sheet->getDelegate()->setCellValue("E{$newrow}","=SUM(E3:E{$row})");
        $event->sheet->getDelegate()->setCellValue("F{$newrow}","=SUM(F3:E{$row})");
        $event->sheet->getDelegate()->setCellValue("G{$newrow}","=SUM(G3:E{$row})");
        $event->sheet->getDelegate()->getStyle("E{$newrow}:G{$newrow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD,
            'F' => NumberFormat::FORMAT_CURRENCY_USD,
            'G' => NumberFormat::FORMAT_CURRENCY_USD
        ];
    }
}
