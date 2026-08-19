<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['bank_id', 'name', 'phone_last4', 'amount'])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Public-facing routes bind on the random uuid, not the sequential id,
     * so invoice URLs can't be enumerated.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            $invoice->uuid ??= (string) Str::uuid();
        });
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(InvoiceComment::class)->latest();
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', 'unpaid');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * The payment reference embedded in the QR code and matched against
     * incoming bank statement comments. Deliberately the random uuid, not
     * the sequential id — a guessable reference would let someone edit
     * their own payment's description to match a different invoice's
     * number and get that invoice auto-marked as paid instead of theirs.
     */
    public function purpose(): string
    {
        return "Добровільний внесок #{$this->uuid}";
    }

    public function markPaid(): bool
    {
        if ($this->isPaid()) {
            return false;
        }

        $saved = $this->forceFill([
            'status' => 'paid',
            'paid_at' => now(),
        ])->save();

        if ($saved) {
            $this->addToDonorRoster();
        }

        return $saved;
    }

    /**
     * The first time this name+phone pays this bank in a given month, make
     * sure they're on that month's donor roster — even if no admin ever
     * added them there. Does nothing if an entry already exists (whether it
     * was admin-added or from an earlier payment that same month), so it
     * never overwrites an admin-set expected amount.
     */
    private function addToDonorRoster(): void
    {
        Donor::firstOrCreate([
            'bank_id' => $this->bank_id,
            'period' => $this->paid_at->copy()->startOfMonth()->toDateString(),
            'name' => $this->name,
            'phone_last4' => $this->phone_last4,
        ], [
            'added_by_admin' => false,
        ]);
    }
}
