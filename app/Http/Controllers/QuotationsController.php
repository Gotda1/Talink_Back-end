<?php

namespace App\Http\Controllers;

use App\Common\Catalogs;
use App\Exports\QuotationsExport;
use App\Http\Requests\SaveNoteRequest;
use App\Http\Requests\SaveQuotationRequest;
use App\Models\Client;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\NoteAttachment;
use App\Models\Product;
use App\Models\NoteObservation;
use App\Models\NoteStatus;
use App\Models\Order;
use App\Models\OrderBody;
use App\Models\QuotationBody;
use Illuminate\Http\Request;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QuotationsController extends Controller
{
    use HelperTrait;
    public function __construct(){
        $this->middleware("jwt");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        try{
            $quotations = Quotation::with([
                "acquirer",
                "seller",
                "order"
            ])->whereBetween("created_at", [$request->start_date, $request->end_date])
              ->when(!$this->hasPrivilege("ALLQUOT"), function ($query) use($request) {
                $query->whereIn("seller_id", [0,$request->requester->id]);
            })->when($request->seller_id, function ($query) use($request) {
                $query->where("seller_id",$request->seller_id);
            })->when($request->status >= -1, function ($query) use($request) {
                $query->where("status", $request->status);
            })->orderBy('status')->get();

            return $this->successResponse([
                "quotations" => $quotations,
            ]);
        } catch (\Throwable $e) {
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
        try {
            $products = Product::with("unit")
                                ->where("status", 1)
                                ->get();
            return $this->successResponse([
                "products" => $products,
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveQuotationRequest $request)
    {
        try {
            $quotation = Quotation::create($request->validated());
            collect($request->quot_body)->each(function ($item, $idx) use($quotation)
            {
                $quotation->quot_body()->save(new QuotationBody($item));
            });
            $quotation->setAmounts();

            collect($request->attachments)->each(function ($item) use($quotation)
            {
                $quotation->attachments_img()->save(new NoteAttachment($item));
            });

            return $this->successResponse([
                "quotation"   => $quotation
            ]);
        } catch (\Throwable $e) {
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
        $QUOTPRICES = $this->hasPrivilege("QUOTPRICES");

        $quotation = Quotation::find($id);

        $pdf       = app('dompdf.wrapper');
        $pdf->loadView("documents.quotation", compact('quotation', 'QUOTPRICES'));

        return $pdf->download($quotation->invoice . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $quotation = Quotation::with(["quot_body.product.unit", "prospect", "client", "user", "attachments_img"])
                                ->find($id);

            $acquirers = $quotation->catalogue == Prospect::class ?
                         Catalogs::getProspects($this->hasPrivilege("ALLPRSP" ? $request->requester->id : null)) :
                         Catalogs::getClients($this->hasPrivilege("ALLCLNT" ? $request->requester->id : null));

            return $this->successResponse([
                "quotation" => $quotation,
                "acquirers" => $acquirers
            ]);
        } catch (\Throwable $e) {
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
    public function update(SaveQuotationRequest $request, $id)
    {
        try {
            $quotation = Quotation::find($id);
            $quotation->fill( $request->validated());

            $quotation->quot_body()->delete();
            $quotation->quot_body()->saveMany(collect($request->quot_body)->map(function($item){
                return new QuotationBody($item);
            }));
            $quotation->setAmounts();

            $attcKeep = collect($request->attachmentsserv);
            $quotation->attachments_img()->each(function($item) use($attcKeep){
                if(($key = $attcKeep->pluck("id")->search($item->id)) > -1){
                    $item->description = $attcKeep[$key]["description"];
                    $item->save();
                }else{
                    $item->delete();
                }
            });
            $quotation->attachments_img()->saveMany(collect($request->attachments)->map(function($item){
                return new NoteAttachment($item);
            }));

            return $this->successResponse([
                "quotation" => $quotation
            ]);
        } catch (\Throwable $e) {
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
        try {
            $quotation = Quotation::find($id);
            $quotation->note_observations()->delete();
            $quotation->attachments_img()->delete();
            $quotation->quot_body()->delete();
            $quotation->delete();

            return $this->successResponse([
                "quotation" => $quotation
            ]);
        } catch (\Throwable $e) {
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
        try {
            $quotation = Quotation::find($id);
            $quotation->status = -1;
            $quotation->save();

            return $this->successResponse([
                "quotation" => $quotation
            ]);
        } catch (\Throwable $e) {
           return $this->failedResponse($e);
        }
    }

    /**
     * Show acquirers propects or clients
     *
     * @param string $catalogo
     * @return void
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function catAcquirers( Request $request, $catalogue ){
        try {
            $acquirers = $catalogue == "Prospect" ?
                         Catalogs::getProspects($this->hasPrivilege("ALLPRSP") ? Auth::id() : null) :
                         Catalogs::getClients($this->hasPrivilege("ALLCLNT") ? Auth::id() : null);

            return $this->successResponse([
                "acquirers" => $acquirers
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Get note type observations
     *
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function getObsvTypes(){
        try {
            $note_status = NoteStatus::where("note_type", Quotation::class)
                                    ->get();

            return $this->successResponse([
                "note_status" => $note_status
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

       /**
     * Get sellers
     *
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function getSellers(){
        try {
            $sellers = Catalogs::getSellers();
            return $this->successResponse([
                "sellers" => $sellers
            ]);
        } catch (\Throwable $e) {
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
    public function getObservations($id){
        try {
            $note_observations = NoteObservation::with(["note_status", "user"])
                                        ->where("note_id",$id)
                                        ->where("note_type", Quotation::class)
                                        ->get();

            return $this->successResponse([
                "note_observations" => $note_observations
            ]);
        } catch (\Throwable $e) {
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
    public function createNoteObservation(SaveNoteRequest $request){
        try {
            $observation = NoteObservation::create($request->validated());
            $observation->note_status;
            $observation->user;
            return $this->successResponse([
                "observation" => $observation
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Convert a quotation to order
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function convertToOrder(Request $request, $id){
        try {
            $quotation  = Quotation::find($id);

            if($quotation->catalogue == Prospect::class){
                $prospect = Prospect::find($quotation->acquirer_id);
                $client   = Client::create($prospect->toArray());
                $prospect->quotations()->update([
                    "acquirer_id" => $client->id,
                    "catalogue"   => Client::class
                ]);
                $prospect->delete();
            }

            $order = Order::create(array_merge( $quotation->toArray(), [
                "client_id"  => $quotation->acquirer_id,
            ]));

            $order->addChargeTransaction($order->total);

            $quotation->quot_body()->each(function($qb) use($order){
                $order->order_body()->save(new OrderBody($qb->toArray()));
            });

            $quotation->attachments_img()->each(function($aimg) use($order, $quotation){
                $order->attachments_img()->save(new NoteAttachment($aimg->toArray()));
                Storage::disk("public")->copy(
                    Quotation::$folder."/$quotation->invoice/$aimg->attachment",
                    Order::$folder . "/$order->invoice/$aimg->attachment"
                );
            });

            $quotation->order_id = $order->id;
            $quotation->status   = 1;
            $quotation->save();

            return $this->successResponse([
                "order" => $order
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

     /**
     * Duplicate a quotation
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function duplicate($id){
        try {
            $quotation = Quotation::find($id);
            $new_quotation = $quotation->replicate();
            $new_quotation->save();

            $quotation->quot_body()->each(function($qb) use($new_quotation){
                $qbr = $qb->replicate();
                $qbr->quotation_id = $new_quotation->id;
                $qbr->save();
            });

            $quotation->attachments_img()->each(function($attch) use($quotation, $new_quotation){
                Storage::disk("public")->copy(
                    "quotations/$quotation->invoice/$attch->attachment",
                    "quotations/$new_quotation->invoice/$attch->attachment"
                );

                $attch->replicate()->fill([
                    "note_id"    => $new_quotation->id,
                    "created_by" => $new_quotation->created_by
                ])->save();
            });

            return $this->successResponse([
                "quotation" => $quotation
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Eports quotations listing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function exportQuotations( Request $request ){
        try {
            $report = Excel::download(new QuotationsExport(
                $request->start_date,
                $request->end_date,
                $request->seller_id,
                $request->status,
            ), "cotizaciones.xlsx", \Maatwebsite\Excel\Excel::XLSX);

            return $report;

        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }
}
?>
