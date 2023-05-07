<?php

namespace App\Http\Controllers;

use App\Common\Catalogs;
use App\Exports\OrdersExport;
use App\Http\Requests\SaveNoteRequest;
use App\Http\Requests\SaveOrderPayment;
use App\Http\Requests\SaveOrderRequest;
use App\Models\NoteAttachment;
use App\Models\NoteObservation;
use App\Models\NoteStatus;
use App\Models\Order;
use App\Models\OrderBody;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware("jwt")->except("show");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try
        {
            $metaGlobal = Order::where('status', 0)->sum('total');
            $orders = Order::with([
                "client",
                "seller",
                "order_body",
                "note_observations.note_status"
            ])->where(function ($query) use ($request) {
                $query->whereBetween("created_at",    ["$request->start_date 00:00:00", "$request->end_date 23:59:59"])
                    ->orwhereBetween("contratado_at", ["$request->start_date 00:00:00", "$request->end_date 23:59:59"]);
                })
                ->when(!$this->hasPrivilege("ALLORDS"), function ($query) use ($request)
                {
                    $query->whereIn("seller_id", [0, $request->requester->id]);
                })->when($request->seller_id, function ($query) use ($request)
                {
                    $query->where("seller_id", $request->seller_id);
                })->when(is_numeric($request->status), function ($query) use ($request)
                {
                    $query->where("status", $request->status);
                })->get();

            $goal = 0;
            $accumulated = 0;

            $orders->each(function($o) use(&$goal, &$accumulated)
            {
                if($o->status == 0)
                {
                    $goal += $o->subtotal;
                }
                if($o->status == 1)
                {
                    $accumulated += $o->subtotal;
                }
                $pending = $o.total - $o.payed;
            });

            return $this->successResponse([
                "orders"      => $orders,
                "goal"        => $goal,
                "accumulated" => $accumulated,
                "metaGlobal"  => $metaGlobal
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try
        {
            $products = Product::with("unit")
                ->where("status", 1)
                ->get();

            $clients  = Catalogs::getClients($this->hasPrivilege("ALLCLNT") ? Auth::id() : null);

            $payment_methods = Catalogs::getPaymentMethods();

            return $this->successResponse([
                "products"        => $products,
                "clients"         => $clients,
                "payment_methods" => $payment_methods
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveOrderRequest $request)
    {
        try
        {
            $order = Order::create($request->validated());

            collect($request->order_body)->each(function ($item, $idx) use ($order)
            {
                $order->order_body()->save(new OrderBody($item));
            });
            $order->setAmounts();

            collect($request->attachments)->each(function ($item) use ($order)
            {
                $order->attachments_img()->save(new NoteAttachment($item));
            });

            $order->addChargeTransaction($order->total);

            if ($request->amount > 0)
            {
                $order->addPaymentTransaction($request->amount);
            }

            // envia correo de confirmación de inicio de reclutamiento
            $valores = Order::selectRaw("clients.name as nombre, clients.email,
            lower(GROUP_CONCAT(DISTINCT replace(orders_body.name,'Reclutamiento- Puesto:','') SEPARATOR ', ')) as productos")
            ->join('orders_body','orders_body.order_id','orders.id')
            ->join('clients','clients.id','orders.client_id')
            ->where('order_id',$order->id)
            ->get();

            $correo = $valores[0]->email ;

            $response = Http::get('https://apptalink.com/correos/alta.php',[
                'nombre' => $valores[0]->nombre,
                'vacante' => $valores[0]->productos,
                'correo' => $valores[0]->email
            ]);
            //$salida = $response->body();
            // termina envia correo

            return $this->successResponse([
                "order"   => $order
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ORDPRICES = $this->hasPrivilege("ORDPRICES");

        $order = Order::find($id);
        $pdf = app('dompdf.wrapper');
        $pdf->loadView("documents.order", compact('order', 'ORDPRICES'));

        return $pdf->stream($order->invoice . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try
        {
            $order = Order::with(["order_body.product.unit", "client", "user", "attachments_img"])
                ->find($id);

            return $this->successResponse([
                "order" => $order
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveOrderRequest $request, $id)
{
    try {
        $order = Order::find($id);
        $order->fill( $request->validated());

        $order->order_body()->delete();
        $order->order_body()->saveMany(collect($request->order_body)->map(function($item){
            return new OrderBody($item);
        }));
        $order->setAmounts();

        $attcKeep = collect($request->attachmentsserv);
        $order->attachments_img()->each(function($item) use($attcKeep){
            if(($key = $attcKeep->pluck("id")->search($item->id)) > -1){
                $item->description = $attcKeep[$key]["description"];
                $item->save();
            }else{
                $item->delete();
            }
        });
        $order->attachments_img()->saveMany(collect($request->attachments)->map(function($item){
            return new NoteAttachment($item);
        }));

        $closed = 0;
        $order_body = collect($request->order_body);
        OrderBody::where("order_id", $id)
            ->where("quantity", "quantity_surt")
            ->update([
                "status"        => "1"
            ]);
        $order_body->each(function ($obi) use (&$closed, $id)
        {
            if ($obi["quantity_surt"] == $obi["quantity"])
            {
                $obi["status"] = 1;
                $closed++;
            }

            /*OrderBody::where("order_id", $id)
                ->where("order", $obi["order"])
                ->update([
                    "status"        => $obi["status"]
                ]);*/
        });

        if ($closed == $order_body->count())
        {
            $order->status = 1;
            $order->contratado_at = $order->FreshTimestamp();
            $order->save();
        }
        return $this->successResponse([
            "order" => $order
        ]);
    }
    catch (\Throwable $e) {
        return $this->failedResponse($e);
    }
}


    public function updateCandidates(Request $request, $id)
    {
        try
        {
            $order = Order::find($id);
            $order->candidates = $request->candidates;
            $order->save();

            return $this->successResponse([
                "order"   => $order
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Cancel the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        try
        {
            $order = Order::find($id);

            if ($order->payed > 0)
                $order->addRefundTransaction($order->payed, 1, "UNDF", "OrderCanceled");

            OrderBody::where("order_id", $id)->update(["quantity_surt" => 0]);

            $order->status = -1;
            $order->save();

            $order->addCanceledTransaction($order->total);

            return $this->successResponse([
                "order" => $order
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $order = Order::find($id);
            $order->delete();

            return $this->successResponse([
                "order" => $order
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Get note type observations
     *
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function getObsvTypes()
    {
        try
        {
            $note_status = NoteStatus::where("note_type", Order::class)
                ->get();
            return $this->successResponse([
                "note_status" => $note_status
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Get payment methods
     *
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function getPaymentMethods()
    {
        try
        {
            $payment_methods = Catalogs::getPaymentMethods();
            return $this->successResponse([
                "payment_methods" => $payment_methods
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Get sellers
     *
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function getSellers()
    {
        try
        {
            $sellers = Catalogs::getSellers();
            return $this->successResponse([
                "sellers" => $sellers
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Get note observations
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function getObservations($id)
    {
        try
        {
            $note_observations = NoteObservation::with(["note_status", "user"])
                ->where("note_id", $id)
                ->where("note_type", Order::class)
                ->get();

            return $this->successResponse([
                "note_observations" => $note_observations
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Create new observation
     *
     * @param SaveNoteRequest $request
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function createNoteObservation(SaveNoteRequest $request)
    {
        try
        {
            $observation = NoteObservation::create($request->validated());
            $observation->note_status;
            $observation->user;
            return $this->successResponse([
                "observation" => $observation
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Create order payment
     *
     * @param SaveOrderPayment $request
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function createPayment(SaveOrderPayment $request)
    {
        try
        {
            $order = Order::find($request->reference_id);
            $validate = $order->validatePayment($request->amount);
            if (!$validate[0])
            {
                return $this->failedResponse(null, "El pago supera la cantidad restante: $validate[1]");
            }

            $order->addPaymentTransaction(
                $request->amount,
                $request->account_id,
                $request->payment_method_code,
                $request->observations
            );

            return $this->successResponse([
                "order" => $order
            ]);
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }

    /**
     * Eports orders listing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function exportOrders(Request $request)
    {
        try
        {
            $report = Excel::download(new OrdersExport(
                $request->start_date,
                $request->end_date,
                $request->seller_id,
                $request->status,
            ), "orders.xlsx", \Maatwebsite\Excel\Excel::XLSX);

            return $report;
        }
        catch (\Throwable $e)
        {
            return $this->failedResponse($e);
        }
    }
}
