@include("documents.styles")


<!-- Encabezado -->
<table class="no-border">
    <tr>
        <td class="w100 p-1">
            <img src="assets/logo_color.png" class="" style="width: 120px;">
        </td>
        <td class="w270 p-1 text-left bg-gray1">
            <h3>{{ Config::get("app.BUSINESS_NAME") }}</h3>
            <p class="p-0 m-0 font-12">
                {{ Config::get("app.BUSINESS_ADDRESS_1") }} <br>
                {{ Config::get("app.BUSINESS_ADDRESS_2") }} <br>
                {{ Config::get("app.BUSINESS_PHONE_NUMBERS") }} <br>
                {{ Config::get("app.BUSINESS_MAIL") }} 
            </p>
        </td>
        <td class="p-1">
            <h1 style="font-size: 18px;">COTIZACIÓN</h1>
            <table>
                <tr>
                    <td class="text-left"> <h2>Folio:</h2> </td>
                    <td class="text-left font-12">{{ $quotation->invoice }}</td>
                </tr>
                <tr>
                    <td class="text-left"> <h2>Fecha:</h2> </td>
                    <td class="text-left font-12">{{  substr( $quotation->created_at, 0, 10 ) }}</td>
                </tr>
                <tr>
                    <td class="text-left"> <h2>Reclutador:</h2> </td>
                    <td class="text-left font-12"> {{ $quotation->seller ? $quotation->seller->name : "-" }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- end Encabezado -->
<br>

<!-- Tabla cliente -->
<div class="bg-gray1 header" >
    <div class="w-60 float-left p-1">
        <table class="no-border">
            <tr>
                <td class="text-left w100">
                    <b>CLIENTE:</b>    
                </td>
                <td class="text-left">
                    @if ($quotation->catalogue == \App\Models\Client::class)
                        {{ $quotation->client ? ($quotation->client->code . " " .$quotation->client->name ) : "-" }}
                    @elseif ($quotation->catalogue == \App\Models\Prospect::class)                      
                        {{ $quotation->prospect ? ($quotation->prospect->name ) : "-" }}
                    @endif
                </td>     
            </tr>

            <tr>
                <td class="text-left w100">
                    <b>DIRECCIÓN:</b>    
                </td>
                <td class="text-left">
                    @if ($quotation->catalogue == \App\Models\Client::class)
                        {{ $quotation->client ? $quotation->client->address : "-" }}
                    @elseif ($quotation->catalogue == \App\Models\Prospect::class)
                        {{ $quotation->prospect ? $quotation->prospect->address : "-" }}
                    @endif
                </td>     
            </tr>
            
            <tr>
                <td class="text-left">
                    <b>TELÉFONO:</b></td>
                <td class="text-left">
                    @if ($quotation->catalogue == \App\Models\Client::class)
                        {{ $quotation->client ? $quotation->client->phone : "-" }}
                    @elseif ($quotation->catalogue == \App\Models\Prospect::class)
                        {{ $quotation->prospect ? $quotation->prospect->phone : "-" }}
                    @endif
                </td>     
            </tr>

            <tr>
                <td class="text-left"><b>E-MAIL:</b></td>
                <td class="text-left">
                    @if ($quotation->catalogue == \App\Models\Client::class)
                        {{ $quotation->client ? $quotation->client->email : "-" }}
                    @elseif ($quotation->catalogue == \App\Models\Prospect::class)
                        {{ $quotation->prospect ? $quotation->prospect->email : "-" }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="w-40 float-left p-1">
        <table class="no-border">
            <tr>
                <td class="text-left">                        
                    @if ($quotation->catalogue == \App\Models\Client::class)
                        {{ $quotation->client ? $quotation->client->description : "" }}
                    @elseif ($quotation->catalogue == \App\Models\Prospect::class)
                        {{ $quotation->prospect ? $quotation->prospect->description : "" }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>
</div>
<!-- end Tabla cliente -->
<br>

<!-- Tabla productos -->
<table cellpadding="1" cellspacing="2">
    <tr class="bg-gray1 border-white">
        <td>COD.</td>
        <td>CANT.</td>
        <td style="width: 200px;">DESCRIPCIÓN</td>
        <td>PRECIO</td>
        <td>IMPORTE</td>
    </tr>

    @foreach ($quotation->quot_body as $item)
        <tr class="border-white">
             <td>{{ $item->product ? $item->product->code : "" }}</td>
             <td>{{ $item->quantity }}</td>
             <td>{{ $item->name }}</td>
             <td>${{ number_format($item->unit_price, 2) }}</td>
             <td>${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
        </tr>
    @endforeach

</table>

<br>

<!-- Totales -->
@if ($QUOTPRICES)    
    <div class="float-right">
        <table class="font-12 border-white">
            <tr>
                <td rowspan="5" class="text-left no-border pr-1"></td>
                <td class="w150 text-left bg-gray1 pl-1">SUBTOTAL</td>
                <td class="w150 text-right">${{ number_format($quotation->subtotal,2) }}</td>
            </tr>
            <tr>
                <td class="text-left bg-gray1 pl-1">IVA</td>
                <td class="text-right">${{ number_format($quotation->taxes,2) }}</td>
            </tr>
            <tr>
                <td class="text-left bg-gray1 pl-1">TOTAL</td>
                <td class="text-right bg-ray2">${{ number_format($quotation->total,2) }}</td>
            </tr>
            <tr>
                <td class="text-left bg-gray1 pl-1">PAGADO</td>
                <td class="text-right bg-ray2">${{ number_format($quotation->payed,2) }} </td>
            </tr>
            <tr>
                <td class="text-left bg-gray1 pl-1">POR PAGAR</td>
                <td class="text-right bg-ray2">${{ number_format((($quotation->total - $quotation->payed )),2) }}</td>
            </tr>
        </table>
    </div>
    <div class="clear-both"></div>
    <br>
@endif

<table cellpadding="1" cellspacing="2">
    @foreach ($quotation->quot_body as $item)
        @if ($item->observations)
            <tr>
                <td class="text-left no-border white-space-pre">{{ $item->observations }}</td>
            </tr>
        @endif
    @endforeach
</table>

@if ($quotation->observations)
    <p class="mt-10 text-justify">
        <b>Observaciones</b> <br>
        {{ $quotation->observations }}
    </p>
@endif

@if ($quotation->attachments_img)    
    <b>Adjuntos</b>
    <div class="mt-10">
        @foreach ($quotation->attachments_img as $idx => $img)
            <div class="w200 float-left m-3">
                <img src="{{ Storage::disk('public')->url('quotations/'.$quotation->invoice . '/'.$img->attachment) }}" class="img-max">
                <small>{{ $img->description }}
            </div>
            
            @if ( (($idx + 1) % 3) == 0)
                <div class="clear-both h-10"></div>
            @endif 

        @endforeach
    </div>
@endif

<div class="clear-both"></div>
<p class="text-justify font-12">
    {!! $quotation->termsAndConditions() !!}
</p>

