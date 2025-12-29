<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

// Test route to verify API routes are working
Route::get('/test', function () {
    return response()->json(['status' => 'API routes are working!', 'time' => now()]);
});

Route::post('/telegram/webhook', function (Nutgram $bot) {
    try {
        // Log incoming request for debugging
        Log::info('Telegram webhook received', [
            'payload' => request()->all(),
            'headers' => request()->headers->all(),
        ]);

        // Explicitly set webhook mode
        //        $bot->setRunningMode(Webhook::class);
        //
        //        $bot->run();

        return response()->json(['ok' => true]);
    } catch (\Throwable $e) {
        Log::error('Telegram webhook error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'payload' => request()->all(),
        ]);

        // Return 200 OK to prevent Telegram from retrying
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 200);
    }
})->name('telegram.webhook');
