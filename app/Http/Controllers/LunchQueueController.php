<?php

namespace App\Http\Controllers;

use App\Models\LunchQueue;
use App\Models\Operator;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Support\Carbon;

class LunchQueueController extends Controller
{
    public function assign(Nutgram $bot)
    {
        $sender = Operator::where('chat_id', $bot->chatId())->first();

        if (!$sender || !$sender->is_supervisor) {
            $bot->sendMessage("⛔ Sizga bu komanda ruxsat etilmagan.");
            return;
        }

        $operators = Operator::where('is_supervisor', false)->inRandomOrder()->get();
        $perGroup = 5;
        $groups = ceil($operators->count() / $perGroup);

        LunchQueue::truncate();

        foreach ($operators as $index => $operator) {
            $group = intdiv($index, $perGroup) + 1;
            $position = $index % $perGroup + 1;
            $start_time = now()->setTime(12, 0)->addMinutes(15 * ($group - 1));
            $end_time = (clone $start_time)->addMinutes(15);

            LunchQueue::create([
                'operator_id' => $operator->id,
                'group_number' => $group,
                'position_in_group' => $position,
                'start_time' => $start_time,
                'end_time' => $end_time,
            ]);

            $bot->sendMessage(
                chat_id: $operator->chat_id,
                text: "🍽 Sizning tushlik vaqtingiz: {$start_time->format('H:i')} – {$end_time->format('H:i')}"
            );
        }

        $bot->sendMessage("✅ Tushlik navbati belgilandi.");
    }
}
