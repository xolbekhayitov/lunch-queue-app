<?php

use Illuminate\Support\Facades\Route;
use SergiX44\Nutgram\Nutgram;
use Symfony\Component\HttpFoundation\Request;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::post('/webhook', function (Request $request) {
//     $bot = app(Nutgram::class);
//     $bot->run();
// });

// Route::post('/webhook', function (Request $request) {
//     $bot = app(Nutgram::class);
//     $bot->run();
// })->withoutMiddleware(['auth', 'verified']);
Route::get('/webhook', function () {
    $bot = app(Nutgram::class);

    $bot->onCommand('/start', function (Nutgram $bot) {
        $bot->sendMessage('🎉 Laravel + Nutgram bot ishlamoqda!');
    });

    $bot->onText('/help', function (Nutgram $bot) {
        $bot->sendMessage('Yordam: /start - botni ishga tushirish');
    });

    $bot->run();
});
