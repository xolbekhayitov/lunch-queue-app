<?php

namespace App\Services;

use App\Models\OrderSort;
use App\Models\Operator;
use Illuminate\Support\Collection;
use App\Models\SupervisorSetting;
use SergiX44\Nutgram\Nutgram;

class LunchQueueService
{
    public function getQueue(): Collection
    {
        return OrderSort::with('operator')->orderBy('position')->get();
    }

    public function resetQueue(): void
    {
        OrderSort::query()->delete();
    }

    public function addToQueue(Operator $operator): void
    {
        OrderSort::firstOrCreate([
            'operator_id' => $operator->id,
        ], [
            'position' => OrderSort::max('position') + 1,
        ]);
    }

    public function removeFromQueue(Operator $operator): void
    {
        OrderSort::where('operator_id', $operator->id)->delete();
    }

    public function notifyNextOperator(Nutgram $bot): void
    {
        $next = OrderSort::with('operator')->orderBy('position')->first();

        if ($next) {
            $bot->sendMessage(
                chat_id: $next->operator->chat_id,
                text: "🍽 Sizning navbatingiz! Tushlikka chiqing."
            );
        }
    }

    public function remindOperator(Nutgram $bot, Operator $operator): void
    {
        $bot->sendMessage(
            chat_id: $operator->chat_id,
            text: "⏰ Tushlik vaqtingiz tugashiga 5 daqiqa qoldi!"
        );
    }

    public function getLunchLimit(): int
    {
        return (int)SupervisorSetting::getValue('lunch_limit', 3);
    }

    public function setLunchLimit(int $limit): void
    {
        SupervisorSetting::setValue('lunch_limit', $limit);
    }

    public function normalizePositions(): void
    {
        $all = OrderSort::orderBy('position')->get();

        foreach ($all as $index => $item) {
            if ($item->position != $index) {
                $item->update(['position' => $index]);
            }
        }
    }
}
