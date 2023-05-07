<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\LogBalance;
use App\Models\Order;
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
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientsBalanceExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithEvents, WithMapping, WithColumnFormatting
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
        $seller_id = $this->hasPrivilege("ALLORDS") ? $this->seller_id : Auth::id();

        $clients = Client::select("id","code", "user_id", "name")
                            ->when($seller_id, function ($query) use($seller_id){
                                $query->where("user_id", $seller_id);
                            })
                            ->where("status", 1);

        $ALLCLNT = $this->hasPrivilege("ALLCLNT");
        if (!$ALLCLNT) $clients->whereIn("user_id", [0, Auth::id()]);

        $clients = $clients->get();

        $clients_id = $clients->pluck("id");

        #  Query for last transactions at start_date
        $last_start_ids = DB::table("transactions_reference as tr")
                        ->select(DB::raw("max(tr.transaction_id) as max_transaction"))
                        ->where("tr.created_at", "<=", $this->start_date ." 00:00:00")
                        ->whereIn("subject_id", $clients_id)
                        ->where("subject_type", "=", "".DB::raw(Client::class)."")
                        ->groupBy("subject_id")
                        ->get()
                        ->pluck("max_transaction"); // null

        #  Query for last transactions at end_date
        $last_end_ids = DB::table("transactions_reference as tr")
                        ->select(DB::raw("max(transaction_id) as max_transaction"))
                        ->where("tr.created_at", "<=", $this->end_date ." 00:00:00")
                        ->where("subject_type", "=", "".DB::raw(Client::class)."")
                        ->whereIn("subject_id", $clients_id)
                        ->groupBy("subject_id")
                        ->get()
                        ->pluck("max_transaction");
                                                        
        #   Transactions
        $transactions = DB::table("transactions_reference as tr")
                        ->select(DB::raw("tr.subject_id as client_id"), 
                                DB::raw("sum(IF(t.concept_id = ".Order::$concept_charge.", t.amount, 0 )) as charges_amount"),
                                DB::raw("sum(IF(t.concept_id = ".Order::$concept_payment.", t.amount, 0 )) as payments_amount"),
                                DB::raw("sum(IF(t.concept_id = ".Order::$concept_refund.", t.amount, 0 )) as refund_amount"),
                                DB::raw("sum(IF(t.concept_id = ".Order::$concept_canceled.", t.amount, 0 )) as canceled_amount")
                        )->join("transactions as t", "t.id", "=", "tr.transaction_id")
                        ->whereBetween("tr.created_at", [$this->start_date . " 00:00:00", $this->end_date . " 23:59:59"])
                        ->whereIn("subject_id", $clients_id)
                        ->groupBy("client_id")
                        ->get()
                        ->toArray();

        #   Initial balances
        $initial_balances = LogBalance::where("entity", "".DB::raw(Client::class)."")
                                       ->whereIn("transaction_id", $last_start_ids)
                                       ->get()
                                       ->toArray();
        #   Final balances
        $final_balances   = LogBalance::where("entity", "".DB::raw(Client::class)."")
                                        ->whereIn("transaction_id", $last_end_ids)
                                        ->get()
                                        ->toArray();   

        return $clients->map(function($client) use ($initial_balances, $final_balances, $transactions ){
                $idxInitBalance  = array_search($client->id, array_column( $initial_balances, "entity_id"));
                $idxFinalBalance = array_search($client->id, array_column( $final_balances, "entity_id"));
                $idxTrans        = array_search($client->id, array_column( $transactions, "client_id"));

                $client->initBalance  = is_numeric( $idxInitBalance ) ? $initial_balances[$idxInitBalance]["balance"] : 0;
                $client->finalBalance = is_numeric( $idxFinalBalance ) ? $final_balances[$idxFinalBalance]["balance"] : 0;
                $client->totCharges   = is_numeric( $idxTrans ) ? $transactions[$idxTrans]->charges_amount : 0;
                $client->totPayments  = is_numeric( $idxTrans ) ? $transactions[$idxTrans]->payments_amount : 0;
                $client->totRefunds   = is_numeric( $idxTrans ) ? $transactions[$idxTrans]->refund_amount : 0;
                $client->totCanceled  = is_numeric( $idxTrans ) ? $transactions[$idxTrans]->canceled_amount : 0;
                return $client;
        });
    }

    public function map($row): array
    {       
        return [
            $row->code,
            $row->name,
            $row->initBalance,
            $row->finalBalance,
            $row->totCharges,
            $row->totPayments,
            $row->totRefunds,
            $row->totCanceled
        ];        
    }

    public function headings(): array
    {
        return [
            [
                "Reporte Balance de Clientes ". $this->start_date . " - " . $this->end_date
            ],
            [
                'Clave',
                "Nombre",
                'Saldo inicial',
                'Saldo final',
                'Cargos',
                'Abonos',
                'Reembolsos',
                'Cancelados'
            ]
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->mergeCells('A1:H1');
        $event->sheet->getStyle('A1:H2')->getFont()->setBold(true);
        
        $row = $event->sheet->getHighestRow();
        $newrow = $event->sheet->getHighestRow() + 1;
        
        $event->sheet->getDelegate()->setCellValue("B{$newrow}","Totales");
        $event->sheet->getStyle("B{$newrow}")->getFont()->setBold(true);

        $event->sheet->getDelegate()->getStyle("C{$newrow}:H{$newrow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
        $event->sheet->getDelegate()->setCellValue("C{$newrow}","=SUM(C3:C{$row})");
        $event->sheet->getDelegate()->setCellValue("C{$newrow}","=SUM(C3:C{$row})");
        $event->sheet->getDelegate()->setCellValue("D{$newrow}","=SUM(D3:D{$row})");
        $event->sheet->getDelegate()->setCellValue("E{$newrow}","=SUM(E3:E{$row})");
        $event->sheet->getDelegate()->setCellValue("F{$newrow}","=SUM(F3:F{$row})");
        $event->sheet->getDelegate()->setCellValue("G{$newrow}","=SUM(G3:G{$row})");
        $event->sheet->getDelegate()->setCellValue("H{$newrow}","=SUM(H3:H{$row})");
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_USD,
            'D' => NumberFormat::FORMAT_CURRENCY_USD,
            'E' => NumberFormat::FORMAT_CURRENCY_USD,
            'F' => NumberFormat::FORMAT_CURRENCY_USD,
            'G' => NumberFormat::FORMAT_CURRENCY_USD,
            'H' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }
}
