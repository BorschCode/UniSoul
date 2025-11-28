<?php

namespace App\Telegram\Conversations;

use App\Models\Donation;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Payment\LabeledPrice;

class DonateConversation extends Conversation
{
    /**
     * Get all active donations, optionally filtered by confession or branch
     */
    public static function getDonations(?int $confessionId = null, ?int $branchId = null)
    {
        $query = Donation::query()->where('active', true);

        if ($confessionId) {
            $query->where('confession_id', $confessionId);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('order')->get();
    }

    /**
     * Get a specific donation by ID
     */
    public static function getDonationById(int $donationId): ?Donation
    {
        return Donation::query()->where('active', true)->find($donationId);
    }

    /**
     * Get donations by purpose (e.g., 'candle', 'sorokoust', 'general')
     */
    public static function getDonationsByPurpose(string $purpose, ?int $confessionId = null)
    {
        $query = Donation::query()
            ->where('active', true)
            ->where('purpose', $purpose);

        if ($confessionId) {
            $query->where('confession_id', $confessionId);
        }

        return $query->orderBy('order')->get();
    }

    public function start(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: message('donate.main'),
            parse_mode: ParseMode::HTML,
            disable_web_page_preview: true,
        );

        $this->next('getAmount');

        stats('command.donate');
    }

    public function getAmount(Nutgram $bot): void
    {
        // get the amount
        $amount = (int) $bot->message()?->text;

        if ($amount < 1) {
            $bot->sendMessage(
                text: message('donate.invalid'),
                parse_mode: ParseMode::HTML,
            );
            $this->next('getAmount');

            return;
        }

        $this->bot->sendInvoice(
            title: trans('donate.donation'),
            description: trans('donate.support_by_donating'),
            payload: 'donation',
            provider_token: '',
            currency: 'XTR',
            prices: [LabeledPrice::make("$amount XTR", $amount)]
        );

        $this->end();

        stats('donate.invoice', value: ['value' => $amount]);
    }
}
