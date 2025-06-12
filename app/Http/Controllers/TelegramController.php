<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\OrderSort;
use Illuminate\Http\Request;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Database\Eloquent\Model;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class TelegramController extends Controller
{
    public function handle(Nutgram $bot, string $command): void
    {
        match ($command){
            '/start' => $this->start($bot),
            '/register' => $this->store($bot),
        };
    }

    public function start(Nutgram $bot){
         $user = $bot->user();
        $bot->sendMessage(
            text: "Welcome! $user->first_name
                /list
                /register
            ",
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('A', callback_data: 'type:a'),
                    InlineKeyboardButton::make('B', callback_data: 'type:b')
                )
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

    public function list(Nutgram $bot, bool $edit = false){

         $ordered = OrderSort::with('operator')->orderBy('position')->get();

        if ($ordered->isEmpty()) {
            $bot->sendMessage("⚠️ Operatorlar mavjud emas.");
            return;
        }

        $text = "📋 Operatorlar ro‘yxati:\n";
        $keyboard = InlineKeyboardMarkup::make();

        foreach ($ordered as $index => $orderSort) {
            $operator = $orderSort->operator;
            $text .= ($index + 1) . ". 👤  {$operator->name}\n";

            $keyboard->addRow(
                InlineKeyboardButton::make("🔼", callback_data: "move_up_{$operator->id}"),
                InlineKeyboardButton::make("🔽", callback_data: "move_down_{$operator->id}"),
                InlineKeyboardButton::make("📞", callback_data: "contact_operator_{$operator->id}")
            );
        }

        if($edit){
            $bot->editMessageText(
            text: $text,
            reply_markup: $keyboard
        );
        }else{
            $bot->sendMessage(
                text: $text,
                reply_markup: $keyboard
            );
        }
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
        $this->list($bot, edit: true);

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
        $this->list($bot, edit: true);

    }

    public function normalizePosition(): void
    {
        $all = OrderSort::orderBy('position')->get();

        foreach ($all as $index => $item) {
            if ($item->position != $index) {
                $item->position = $index;
                $item->update();
            }
        }
    }

}
