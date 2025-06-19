<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Http\Controllers\TelegramController;
use Nutgram\Laravel\Facades\Telegram;

// $bot->onText('~^/(\w+)(?:@\w+)?~', function (Nutgram $bot, $matches) {
//     $command = $matches[1];
//     app(TelegramController::class)->handle($bot, $command);
// });

$bot->onCommand('start', [TelegramController::class, 'start']);
$bot->onCommand('register', [TelegramController::class, 'store']);
$bot->onCommand('list', [TelegramController::class, 'list']);


$bot->onCallbackQueryData('move_up_{id}', [TelegramController::class, 'moveOperatorUp']);
$bot->onCallbackQueryData('move_down_{id}', [TelegramController::class, 'moveOperatorDown']);



// $bot->onCommand('start', function (Nutgram $bot) {
//     $bot->sendMessage("Hello, world!");
// })->description('The start command!');
