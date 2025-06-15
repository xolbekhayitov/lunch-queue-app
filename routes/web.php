<?php

use Illuminate\Support\Facades\Route;
use SergiX44\Nutgram\Nutgram;
use App\Http\Controllers\TelegramController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function(){
    return view('welcome');
});

Route::post('/webhook', [TelegramController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

// Route::get('/webhook', function (Nutgram $bot) {
//     $bot->registerCommand(TelegramController::class);
//     $bot->run();
// });
