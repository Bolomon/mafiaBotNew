<?php

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('webhook')->group(function () {
    Route::post('', [WebhookController::class, 'index']);
});

Route::get('test', function (){
    dd(\App\Models\UserScope::select(
        'telegram_user_id', 
        DB::raw('SUM(scope) as total_scope'), 
        DB::raw('RANK() OVER (ORDER BY SUM(scope) DESC) as ladder_seat')
        )->groupBy('telegram_user_id')->get()->toArray());
    // dd(storage_path('/storage/logo.png'));
    // dd(\Telegram\Bot\FileUpload\InputFile::create('/storage/logo.png', 'logo.png'));
    $response = Telegram::setWebhook(['url' => env('TELEGRAM_WEBHOOK_URL')]);
    // $response = Telegram::getMe();
    dd($response);
});