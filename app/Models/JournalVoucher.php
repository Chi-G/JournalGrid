<?php

namespace App\Models;

use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class JournalVoucher extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'voucher_no',
        'voucher_date',
        'type',
        'narration',
        'status',
        'total_debit_minor',
        'total_credit_minor',
        'created_by',
        'posted_by',
        'posted_at',
        'reversal_of_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'voucher_date' => 'date',
            'type' => VoucherType::class,
            'status' => VoucherStatus::class,
            'total_debit_minor' => 'integer',
            'total_credit_minor' => 'integer',
            'posted_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class)->orderBy('line_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(JournalVoucher::class, 'reversal_of_id');
    }

    public function getTotalDebitMoneyAttribute(): Money
    {
        return Money::ofMinor($this->total_debit_minor, 'NGN');
    }

    public function getTotalCreditMoneyAttribute(): Money
    {
        return Money::ofMinor($this->total_credit_minor, 'NGN');
    }
}
