<?php

namespace App\Exports;

use App\Models\Order;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithBackgroundColor;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithEvents, WithMapping, WithColumnFormatting
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
    
            $orders = Order::select(DB::raw("
                orders.id,
                invoice,
                orders.created_at,
                c.name,
                orders.taxes,
                orders.subtotal,
                orders.total,
                orders.payed,
                orders.status,
                u.name as seller,
                ucby.name as user_create,
                SUM(ob.unit_price) as requested, 
                SUM(ob.unit_price * ob.quantity_surt) as done"
            ))
            ->leftJoin("clients as c", "c.id", "=", "client_id")
            ->leftJoin("users as u", "u.id", "=", "seller_id")
            ->leftJoin("users as ucby", "ucby.id", "=", "orders.created_by")
            ->leftJoin("orders_body as ob", "ob.order_id", "=", "orders.id")
            ->groupBy("orders.id")
            ->whereBetween("orders.created_at", [$this->start_date, $this->end_date])
            ->when(!$this->hasPrivilege("ALLORDS"), function ($query) {
                $query->whereIn("seller_id", [0,Auth::id()]);
            })->when($this->seller_id, function ($query) use ($ctx) {
                $query->where("seller_id",$ctx->seller_id);
            })->when($this->status > -1, function ($query)  use ($ctx){
                $query->where("status", $ctx->status);
            })->get();
            
            return $orders;
        } catch (\Throwable $e) {
            report($e);
            return collect();
        }
    }

    public function map($row): array
    {
        $status = "Abierto";
        if($row->status == -1)
            $status = "Cancelado";
        elseif($row->status == 1)
            $status = "Cerrado";
        
        $prcPrivilege = $this->hasPrivilege("ORDPRICES");

        return [
            $row->id,
            $row->invoice,
            $row->created_at,
            $row->name,
            $prcPrivilege ? $row->taxes : "-",
            $prcPrivilege ? $row->subtotal : "-",
            $prcPrivilege ? $row->total : "-",
            $prcPrivilege ? $row->payed : "-",
            $prcPrivilege ? ($row->total - $row->payed) : "-",
            $row->requested,
            $row->done,
            $status,
            $row->seller,
            $row->user_create
        ];
        
    }

    public function headings(): array
    {
        return [
            [
                "Reporte de Pedidos ". $this->start_date . " - " . $this->end_date
            ],
            [
                'ID',
                "Folio",
                'Fecha',
                'Cliente',
                'I.V.A.',
                'Subtotal',
                'Total',
                'Pagado',
                'Por Pagar',
                "Solicitado",
                "Cerrado",
                "Status",
                "Reclutador",
                "Creado por"
            ]
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->mergeCells('A1:N1');
        $event->sheet->getStyle('A1:N2')->getFont()->setBold(true);
        
        $row = $event->sheet->getHighestRow();
        $newrow = $event->sheet->getHighestRow() + 1;
        
        $event->sheet->getDelegate()->setCellValue("D{$newrow}","Totales");
        $event->sheet->getStyle("D{$newrow}")->getFont()->setBold(true);

        $event->sheet->getDelegate()->setCellValue("E{$newrow}","=SUM(E3:E{$row})");
        $event->sheet->getDelegate()->setCellValue("F{$newrow}","=SUM(F3:F{$row})");
        $event->sheet->getDelegate()->setCellValue("G{$newrow}","=SUM(G3:G{$row})");
        $event->sheet->getDelegate()->setCellValue("H{$newrow}","=SUM(H3:H{$row})");
        $event->sheet->getDelegate()->setCellValue("I{$newrow}","=SUM(I3:I{$row})");
        $event->sheet->getDelegate()->setCellValue("J{$newrow}","=SUM(J3:J{$row})");
        $event->sheet->getDelegate()->setCellValue("K{$newrow}","=SUM(K3:K{$row})");
        $event->sheet->getDelegate()->getStyle("E{$newrow}:K{$newrow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD,
            'F' => NumberFormat::FORMAT_CURRENCY_USD,
            'G' => NumberFormat::FORMAT_CURRENCY_USD,
            'H' => NumberFormat::FORMAT_CURRENCY_USD,
            'I' => NumberFormat::FORMAT_CURRENCY_USD,
            'J' => NumberFormat::FORMAT_CURRENCY_USD,
            'K' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }
}
