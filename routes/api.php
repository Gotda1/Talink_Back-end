<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProspectsController;
use App\Http\Controllers\QuotationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    "prefix" => "auth"
], function() {
    Route::post('login', [AuthController::class, "login"]);
});

Route::resource("user", UsersController::class);
Route::get("prospect/{id}/convert-to-client", [ProspectsController::class, "convertToClient"]);
Route::resource("prospect", ProspectsController::class);

Route::get("client/report", [ClientsController::class, "exportClients"]);
Route::resource("client", ClientsController::class);

Route::resource("product", ProductsController::class);
Route::prefix("quotation")->group(function(){
    Route::get("/{id}/observations", [QuotationsController::class, "getObservations"]);
    Route::post("create-note", [QuotationsController::class, "createNoteObservation"]);
    Route::get("obsv-types", [QuotationsController::class, "getObsvTypes"]);
    Route::get("sellers", [OrdersController::class, "getSellers"]);
    Route::get("/{id}/convert-to-order", [QuotationsController::class, "convertToOrder"]);
    Route::get("/{id}/duplicate", [QuotationsController::class, "duplicate"]);
    Route::get("{catalogue}/cat-acquirers", [QuotationsController::class, "catAcquirers"]);
    Route::delete("{id}/cancel", [QuotationsController::class, "cancel"]);
});

Route::get("quotation/report", [QuotationsController::class, "exportQuotations"]);
Route::resource("quotation", QuotationsController::class);
Route::resource("goal", GoalsController::class);

Route::prefix("order")->group(function(){
    Route::get("obsv-types", [OrdersController::class, "getObsvTypes"]);
    Route::get("sellers", [OrdersController::class, "getSellers"]);
    Route::get("report", [OrdersController::class, "exportOrders"]);
    Route::get("payment-methods", [OrdersController::class, "getPaymentMethods"]);
    Route::get("/{id}/observations", [OrdersController::class, "getObservations"]);
    Route::post("create-note", [OrdersController::class, "createNoteObservation"]);
    Route::put("{id}/candidates", [OrdersController::class, "updateCandidates"]);
    Route::post("payment", [OrdersController::class, "createPayment"]);
    Route::delete("{id}/cancel", [OrdersController::class, "cancel"]);
});
Route::resource("order", OrdersController::class);

Route::prefix("dashboard")->group(function(){
    Route::get("sellers", [DashboardController::class, "sellers"]);
    Route::get("counters", [DashboardController::class, "counters"]);
    Route::get("clients/balance", [ReportsController::class, "reportsClients"]);
    Route::get("clients/transactions", [ReportsController::class, "clientsTransactions"]);
    Route::get("month/performance", [DashboardController::class, "monthPerformance"]);
    Route::get("year/performance", [DashboardController::class, "yearPerformance"]);
    Route::get("clients/performance", [DashboardController::class, "clientsPerformance"]);
    Route::get("clients/products", [DashboardController::class, "clientsProducts"]);
});
