<?php

namespace App\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class JournalLine extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'journal_voucher_id',
        'account_id',
        'debit_minor',
        'credit_minor',
        'narration',
        'line_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'debit_minor' => 'integer',
            'credit_minor' => 'integer',
            'line_order' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(JournalVoucher::class, 'journal_voucher_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function getDebitMoneyAttribute(): Money
    {
        return Money::ofMinor($this->debit_minor, 'NGN');
    }

    public function getCreditMoneyAttribute(): Money
    {
        return Money::ofMinor($this->credit_minor, 'NGN');
    }
}
