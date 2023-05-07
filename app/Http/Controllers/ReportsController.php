<?php

namespace App\Http\Controllers;

use App\Exports\ClientsBalanceExport;
use App\Exports\ClientTransactionsExport;
use App\Models\Client;
use App\Models\LogBalance;
use App\Models\Order;
use App\Models\TransactionReference;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    use HelperTrait;

    
    public function reportsClients(Request $request){
        try {
            $report = Excel::download(new ClientsBalanceExport(
                $request->start_date,
                $request->end_date,
                $request->seller_id
            ), "clients_balance.xlsx", \Maatwebsite\Excel\Excel::XLSX);

            return $report;
            
        } catch (\Throwable $e) {
            report($e);
            return $this->failedResponse($e);
        }
    }

    public function clientsTransactions(Request $request){
        try {
            $report = Excel::download(new ClientTransactionsExport(
                $request->start_date,
                $request->end_date,
                $request->seller_id
            ), "clients_transactions.xlsx", \Maatwebsite\Excel\Excel::XLSX);

            return $report;
            
        } catch (\Throwable $e) {
            report($e);
            return $this->failedResponse($e);
        }
    }
}
