<?php

namespace App\Http\Controllers;

use App\Common\Catalogs;
use App\Models\Client;
use App\Models\Goal;
use App\Models\Order;
use App\Models\OrderBody;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware("jwt");
    }

    /**
     * Get sellers
     *
     * @return Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function sellers(){
        try {
            $sellers = Catalogs::getSellers();

            return $this->successResponse([
                "sellers" => $sellers,
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Records counters
     *
     * @return Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function counters(){
        try {
            $clients    = Catalogs::getClients($this->hasPrivilege("ALLCLNT") ? Auth::id() : null);
            $prospects  = Catalogs::getProspects($this->hasPrivilege("ALLPRSP") ? Auth::id() : null);
            $products   = Product::where("status",1)->count();
            $quotations = Quotation::where("status",0)
                                    ->when(!$this->hasPrivilege("ALLQUOT"), function ($query) {
                                        $query->whereIn("seller_id", [0, Auth::id()]);
                                    })->count();
            $orders     = Order::where("status",0)
                                    ->when(!$this->hasPrivilege("ALLORDR"), function ($query) {
                                        $query->whereIn("seller_id", [0, Auth::id()]);
                                    })->count();

            return $this->successResponse([
                "clients"    => $clients->count(),
                "prospects"  => $prospects->count(),
                "products"   => $products,
                "quotations" => $quotations,
                "orders"     => $orders,
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     *
     * Month performance report
     * @param Request $request
     * @return Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function monthPerformance( Request $request ){
        try {
            $year      = $request->year;
            $month     = $request->month;
            $seller_id = $this->hasPrivilege("ALLORDS") ? $request->seller_id : Auth::id();

            #   Previous finances
            $prev_orders = Order::selectRaw("SUM(total - payed) AS prev_pending")
                            ->whereRaw("created_at < ?", [$year."-".$month."-"."01 00:00:00"])
                            ->when($seller_id, function ($query) use($seller_id){
                                $query->where("seller_id", $seller_id);
                            })
                            ->whereIn("status", [0,1])
                            ->whereRaw("payed < total")
                            ->get()
                            ->first();
            #   Current finances
            $orders = Order::select("id")
                            ->whereRaw("YEAR(created_at) = ? AND MONTH(created_at) = ?", [$year, $month])
                            ->when($seller_id, function ($query) use($seller_id){
                                $query->where("seller_id", $seller_id);
                            })
                           ->whereIn("status", [0,1])
                           ->get()
                           ->pluck("id");

            #   Performance
            $performance = Transaction::selectRaw("COALESCE(SUM(IF(c.`type` = 0, transactions.amount, 0)), 0) as billed")
                                    ->selectRaw("COALESCE(SUM(IF(c.`type`= 1, transactions.amount, 0)), 0) as collected")
                                    ->selectRaw("0 AS pending, 0 AS pcg_collected, 0 AS pcg_pending")
                                    ->join("transactions_reference as tr", "tr.transaction_id", "transactions.id")
                                    ->join("concepts AS c", "transactions.concept_id", "c.id")
                                    ->whereIn("tr.reference_id", $orders)
                                    ->where("reference_type", "".DB::raw(Order::class)."")
                                    ->get()
                                    ->first();

            $performance->prev_pending = $prev_orders->prev_pending ?: 0;

            if($performance->billed > 0){
                $multiple                   = 100 / $performance->billed;
                $performance->pending       = $performance->billed - $performance->collected;
                $performance->pcg_collected = round($performance->collected * $multiple, 2);
                $performance->pcg_pending   = 100 - $performance->pcg_collected;
            }

            $current_goal = Goal::where("year_numb", $request->year)
                                ->where("month_numb", $request->month)
                                ->first();

            $previous_goal = Goal::where("year_numb", $request->year- 1)
                                ->where("month_numb", $request->month)
                                ->first();

            return $this->successResponse([
                "performance"  => $performance,
                "goals"        => [
                    "current" => [
                        "year"   => (int)$request->year,
                        "month"  => (int)$request->month,
                        "amount" => $current_goal ? $current_goal->amount : 0,
                    ],
                    "previous" => [
                        "year"   => (int)$request->year -1,
                        "month"  => (int)$request->month,
                        "amount" => $previous_goal ? $previous_goal->amount : 0,
                    ]
                ]
            ]);

        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Anual performance report
     *
     * @return Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function yearPerformance( Request $request ){
        try {
            $year      = $request->year;
            $seller_id = $this->hasPrivilege("ALLORDS") ? $request->seller_id : Auth::id();

            #   Previous finances
            $prev_orders = Order::selectRaw("SUM(total - payed) AS prev_pending")
                                ->whereRaw("created_at < ?", [$year."-01-01 00:00:00"])
                                ->when($seller_id, function ($query) use($seller_id){
                                    $query->where("seller_id", $seller_id);
                                })
                                ->whereIn("status", [0,1])
                                ->whereRaw("payed < total")
                                ->get()
                                ->first();

            #   Current finances
            $orders = Order::select("id")
                            ->whereRaw("YEAR(created_at) = ?", [$year])
                            ->when($seller_id, function ($query) use($seller_id){
                                $query->where("seller_id", $seller_id);
                            })
                            ->whereIn("status", [0,1])
                            ->get()
                            ->pluck("id");

            $performance = Transaction::selectRaw("MONTH(transactions.created_at) AS vmonth")
                                      ->selectRaw("COALESCE(SUM(IF(c.`type` = 0, transactions.amount, 0)), 0) as billed")
                                      ->selectRaw("COALESCE(SUM(IF(c.`type`= 1, transactions.amount, 0)), 0) as collected")
                                      ->join("transactions_reference as tr", "tr.transaction_id", "=", "transactions.id")
                                      ->join("concepts AS c", "transactions.concept_id", "=", "c.id")
                                      ->whereIn("tr.reference_id", $orders)
                                      ->where("reference_type", "".DB::raw(Order::class)."")
                                      ->groupByRaw("MONTH(transactions.created_at)")
                                      ->get();



            $year_performance = [];
            for ($i=1; $i <= 12; $i++) {
                $year_performance[] = $performance->filter(function($m) use($i) {
                                            return $m->vmonth == $i;
                                        })->first() ?:  (object)array(
                                            "vmonth" => $i,
                                            "billed" => 0,
                                            "collected" => 0,
                                        );
            }



            return $this->successResponse([
                "performance"  => $year_performance,
                "prev_pending" => $prev_orders->prev_pending ?: 0
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    public function clientsPerformance(Request $request){
        try {
            $year      = $request->year;
            $month     = $request->month;
            $seller_id = $this->hasPrivilege("ALLORDS") ? $request->seller_id : Auth::id();

            #   Previous finances
            $prev_orders = Order::selectRaw("SUM(total - payed) AS prev_pending, client_id")
                            ->with("client:id,code,name")
                            ->whereRaw("created_at < ?", [$year."-".$month."-"."01 00:00:00"])
                            ->when($seller_id, function ($query) use($seller_id){
                                $query->where("seller_id", $seller_id);
                            })
                            ->whereIn("status", [0,1])
                            ->whereRaw("payed < total")
                            ->groupBy("client_id")
                            ->get();

            #   Current finances
            $orders = Order::select("id")
                            ->whereRaw("YEAR(created_at) = ? AND MONTH(created_at) = ?", [$year, $month])
                            ->when($seller_id, function ($query) use($seller_id){
                                $query->where("seller_id", $seller_id);
                            })
                            ->whereIn("status", [0,1])
                            ->get()
                            ->pluck("id");

            #   Performance
            $performance = Transaction::selectRaw("COALESCE(SUM(IF(c.`type` = 0, transactions.amount, 0)), 0) as billed")
                                    ->selectRaw("COALESCE(SUM(IF(c.`type`= 1, transactions.amount, 0)), 0) as collected")
                                    ->selectRaw("tr.subject_id, clt.code, clt.name")
                                    ->join("transactions_reference as tr", "tr.transaction_id", "=", "transactions.id")
                                    ->join("concepts AS c", "transactions.concept_id", "=","c.id")
                                    ->join("clients AS clt", "tr.subject_id", "=", "clt.id")
                                    ->whereIn("tr.reference_id", $orders)
                                    ->where("reference_type", "".DB::raw(Order::class)."")
                                    ->where("subject_type", "".DB::raw(Client::class)."")
                                    ->groupBy("tr.subject_id")
                                    ->get();

            return $this->successResponse([
                "performance" => $performance,
                "prev_orders" => $prev_orders,
                "orders"      => $orders
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Services required by client
     *
     * @return void
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function clientsProducts(Request $request){
        $year      = $request->year;
        $month     = $request->month;
        $seller_id = $this->hasPrivilege("ALLORDS") ? $request->seller_id : Auth::id();

        $products = Product::select("id", "code", "name")
                            ->where("status", 1)
                            ->orderBy("name")
                            ->get();

        foreach ($products as $product) {
            $order_item = OrderBody::select("clients.code", "clients.name")
                                    ->selectRaw("SUM(quantity * IF(taxes > 0, unit_price * (total / subtotal ), unit_price)) AS billed")
                                    ->join("orders", "order_id", "orders.id")
                                    ->join("clients", "client_id", "clients.id")
                                    ->where("product_id", $product->id)
                                    ->whereRaw("YEAR(orders.created_at) = $year")
                                    ->whereRaw("MONTH(orders.created_at) = $month")
                                    ->when($seller_id, function ($query) use($seller_id){
                                        $query->where("seller_id", $seller_id);
                                    })
                                    ->groupBy("client_id")
                                    ->get();

            $product->clients = $order_item;

            return $this->successResponse([
                "products" => $products
            ]);
        }
    }
}
