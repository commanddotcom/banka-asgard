<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per (bank, month, name, phone last 4) — the expected/actual-donor
 * roster. Populated either by an admin CSV upload (added_by_admin=true) or
 * automatically the first time that name+phone pays an invoice to that bank
 * in that month (added_by_admin=false). The actual amount donated is not
 * stored here — it's computed by summing matching paid invoices, so it
 * always reflects reality even if more payments come in later.
 */
#[Fillable(['bank_id', 'period', 'name', 'phone_last4', 'expected_amount', 'added_by_admin'])]
class Donor extends Model
{
    protected function casts(): array
    {
        return [
            'period' => 'date',
            'expected_amount' => 'decimal:2',
            'added_by_admin' => 'boolean',
        ];
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
