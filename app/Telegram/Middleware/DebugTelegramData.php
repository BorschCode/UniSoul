<?php

namespace App\Telegram\Middleware;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class DebugTelegramData
{
    public function __invoke(Nutgram $bot, $next): void
    {
        // Log the raw update data
        $update = $bot->update();

        if ($update) {
            Log::channel('daily')->info('Raw Telegram Update', [
                'update_id' => $update->update_id ?? null,
                'message' => $update->message ?? null,
                'callback_query' => $update->callback_query ?? null,
                'raw_data' => json_encode($update, JSON_PRETTY_PRINT),
            ]);

            // Log user data specifically
            if ($bot->user()) {
                Log::channel('daily')->info('Telegram User Data', [
                    'id' => $bot->user()->id ?? 'NULL',
                    'id_type' => gettype($bot->user()->id ?? 'unknown'),
                    'first_name' => $bot->user()->first_name ?? null,
                ]);
            }
        }

        $next($bot);
    }
}
