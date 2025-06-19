<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\OrderSort;
use Illuminate\Http\Request;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Database\Eloquent\Model;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use App\Models\LunchQueue;
use Carbon\Carbon;

class TelegramController extends Controller
{
      public function handle(Nutgram $bot): void
    {
        $bot->onCommand('start', fn(Nutgram $bot) => $this->start($bot));
        $bot->onCommand('register', fn(Nutgram $bot) => $this->store($bot));
        $bot->onCommand('list', fn(Nutgram $bot) => $this->list($bot));
        $bot->onCommand('buildqueue', fn(Nutgram $bot) => $this->buildQueue($bot));
        $bot->onCommand('notifyqueue', fn(Nutgram $bot) => $this->notifyUsers($bot));


        $bot->onCallbackQueryData('move_up_{id}', function (Nutgram $bot, $id) {
            $this->moveOperatorUp($bot, (int)$id);
        });

        $bot->onCallbackQueryData('move_down_{id}', function (Nutgram $bot, $id) {
            $this->moveOperatorDown($bot, (int)$id);
        });

        $bot->run();

    }

    public function start(Nutgram $bot){
         $user = $bot->user();
        $bot->sendMessage(
            text: "Welcome! $user->first_name
                /list
                /register
            ",
        );
    }


    public function store(Nutgram $bot){
         $user = $bot->user();

        $operator = Operator::updateOrCreate(
            ['chat_id' => $user->id],
            ['name' => $user->first_name]
        );


        OrderSort::firstOrCreate([
            'operator_id' => $operator->id
        ], [
            'position' => OrderSort::max('position') + 1
        ]);

        $bot->sendMessage("✅ Ro‘yxatdan o‘tildi");
    }

    public function list(Nutgram $bot){

         $ordered = OrderSort::with('operator')->orderBy('position')->get();

        if ($ordered->isEmpty()) {
            $bot->sendMessage("⚠️ Operatorlar mavjud emas.");
            return;
        }

        $text = "📋 Operatorlar ro‘yxati:\n";
        $keyboard = InlineKeyboardMarkup::make();

        foreach ($ordered as $index => $orderSort) {
            $operator = $orderSort->operator;
            // $text .= ($index + 1) . ". 👤  {$operator->name}\n";
            $count=$index+1;
            $keyboard->addRow(
                InlineKeyboardButton::make("{$count}.{$operator->name}", callback_data: "noop"),
                InlineKeyboardButton::make("🔼", callback_data: "move_up_{$operator->id}"),
                InlineKeyboardButton::make("🔽", callback_data: "move_down_{$operator->id}"),
            );
        }


        $bot->sendMessage(
            text: $text,
            reply_markup: $keyboard
        );

    }

    function moveOperatorUp(Nutgram $bot, int $operatorId)
    {
         $bot->answerCallbackQuery();

        $current = OrderSort::where('operator_id', $operatorId)->first();
        if (!$current) return;

        $above = OrderSort::where('position', '<', $current->position)
            ->orderByDesc('position')
            ->first();

        if ($above) {
            $temp = $current->position;
            $current->update(['position' => $above->position]);
            $above->update(['position' => $temp]);
        }

        $this->normalizePosition();
        $this->list($bot);

    }

    function moveOperatorDown(Nutgram $bot, int $operatorId)
    {

        $bot->answerCallbackQuery();

        $current = OrderSort::where('operator_id', $operatorId)->first();
        if (!$current) return;

        $below = OrderSort::where('position', '>', $current->position)
            ->orderBy('position')
            ->first();

        if ($below) {
            $temp = $current->position;
            $current->update(['position' => $below->position]);
            $below->update(['position' => $temp]);
        }

        $this->normalizePosition();
        $this->list($bot);

    }

    public function normalizePosition(): void
    {
        $all = OrderSort::orderBy('position')->get();
        foreach ($all as $index => $item) {
            $item->update(['position' => $index + 1]);
        }
    }

    public function buildQueue(Nutgram $bot)
        {
        $operator = Operator::where('chat_id', $bot->chatId())->first();

        if (!$operator || !$operator->is_supervisor) {
            $bot->sendMessage("❌ Sizda bu amal uchun ruxsat yo‘q.");
            return;
        }

        $allOperators = Operator::where('is_supervisor', false)->get();
        $groupSize = 5;
        $start = Carbon::createFromTime(12, 0);
        $interval = 15; // daqiqa
        $groupCount = ceil($allOperators->count() / $groupSize);

        LunchQueue::truncate(); // eski navbatni tozalash

        $index = 0;
        foreach ($allOperators->chunk($groupSize) as $groupIndex => $group) {
            $timeStart = $start->copy()->addMinutes($groupIndex * $interval);
            $timeEnd = $timeStart->copy()->addMinutes($interval);

            foreach ($group as $operator) {
                LunchQueue::create([
                    'operator_id' => $operator->id,
                    'group_number' => $groupIndex + 1,
                    'lunch_time_start' => $timeStart->format('H:i'),
                    'lunch_time_end' => $timeEnd->format('H:i'),
                ]);
            }
        }

        $bot->sendMessage("✅ Tushlik navbati tuzildi");
    }
    public function notifyUsers(Nutgram $bot)
    {
        $queues = LunchQueue::with('operator')->get();

        foreach ($queues as $item) {
            $bot->sendMessage(
                text :    "👤 Hurmatli {$item->operator->name}, tushlik vaqtingiz: {$item->lunch_time_start} - {$item->lunch_time_end}",
                chat_id:  $item->operator->chat_id
            );
        }
    }

}
