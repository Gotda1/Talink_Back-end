<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\TransactionReference;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\DB;
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

class ClientTransactionsExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithEvents, WithMapping, WithColumnFormatting
{
    use HelperTrait, Exportable, RegistersEventListeners;

    var $start_date;
    var $end_date;
    var $seller_id;

    public function __construct($start_date, $end_date, $seller_id){
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
        $this->seller_id  = $seller_id;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $seller_id = $this->seller_id;

        $transactions = TransactionReference::select(
            "t.created_at",
            "transactions_reference.subject_id",
            "transactions_reference.invoice",
            "c.name as concept",
            "t.observations",
            "t.amount", 
            DB::raw("c.name as client"), 
            "c.type",
            DB::raw("u.name as user_create"), 
        )
        ->join(DB::raw("transactions AS t"), "t.id", "=", "transactions_reference.transaction_id")
        ->leftJoin( DB::raw("clients clt"), "clt.id", "=", "transactions_reference.subject_id")
        ->join(DB::raw("concepts AS c"), "t.concept_id", "c.id")
        ->leftJoin(DB::raw("users AS u"), "t.created_by", "u.id")
        ->when($seller_id, function ($query) use($seller_id){
            $query->where("clt.user_id", $this->seller_id);
        })
        ->whereBetween("t.created_at", [$this->start_date, $this->end_date])
        ->where("transactions_reference.subject_type",  '=', "".DB::raw(Client::class)."")
        ->get();     
        
        
        return $transactions;
    }

    public function map($row): array
    {       
        return [
            $row->created_at,
            $row->invoice,
            $row->client ?: "-",
            $row->amount,
            $row->concept,
            $row->observations,
            $row->user_create,
        ];        
    }

    public function headings(): array
    {
        return [
            [
                "Transacciones Clientes ". $this->start_date . " - " . $this->end_date
            ],
            [
                'Fecha',
                "Folio",
                'Cliente/Prospectp',
                'Cantidad',
                'Concepto',
                'Observaciones',
                'Creado por'
            ]
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->mergeCells('A1:G1');
        $event->sheet->getStyle('A1:G2')->getFont()->setBold(true);

        $row = $event->sheet->getHighestRow();
        $newrow = $event->sheet->getHighestRow() + 1;
        
        $event->sheet->getDelegate()->setCellValue("C{$newrow}","Totales");
        $event->sheet->getStyle("C{$newrow}")->getFont()->setBold(true);
        $event->sheet->getDelegate()->setCellValue("D{$newrow}","=SUM(D3:D{$row})");
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_USD
        ];
    }
}
