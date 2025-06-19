<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\LunchQueue;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SendLunchReminder extends Command
{
    protected $signature = 'lunch:reminder';
    protected $description = '15 daqiqa oldin operatorlarni ogohlantiradi';

    public function __invoke()
    {
        Log::info("Lunch reminder ishga tushdi: " . now());

        $queues = LunchQueue::with('operator')
            ->where('notified', false)
            ->get()
            ->filter(function ($queue) {
                $diff = Carbon::now()->diffInMinutes(Carbon::parse($queue->lunch_time_start), false);
                return $diff >= 14 && $diff <= 15;
        });

        Log::info("Lunch : {$queues->pluck('notified')}");


        $bot = new Nutgram(env('TELEGRAM_TOKEN'));

        foreach ($queues as $queue) {
            $bot->sendMessage(
                text: "⏰ Sizning tushlik vaqtingiz 15 daqiqa ichida boshlanadi!",
                chat_id: $queue->operator->chat_id,
                // reply_markup: InlineKeyboardMarkup::make()
                //     ->addRow(
                //         InlineKeyboardButton::make('', callback_data:"update_{$}"),
                //     )
            );

            $queues->update(['notified' => true]);
        }
    }
}
