<?php

namespace App\Models;

use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    use HasTranslations;

    protected $fillable = [
        'confession_id',
        'branch_id',
        'name',
        'description',
        'purpose',
        'min_amount',
        'max_amount',
        'currency',
        'emoji',
        'active',
        'order',
    ];

    public array $translatable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'min_amount' => 'integer',
            'max_amount' => 'integer',
            'order' => 'integer',
        ];
    }

    public function confession(): BelongsTo
    {
        return $this->belongsTo(Confession::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
