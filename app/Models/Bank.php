<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'order', 'iban', 'tax_id', 'description', 'token', 'account_id', 'watch'])]
#[Hidden(['token'])]
class Bank extends Model
{
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'watch' => 'boolean',
            'last_checked_at' => 'datetime',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function canWatch(): bool
    {
        return $this->watch && filled($this->token) && filled($this->account_id);
    }

    protected static function booted(): void
    {
        static::saving(function (Bank $bank) {
            if (! filled($bank->token) || ! filled($bank->account_id)) {
                $bank->watch = false;
            }
        });
    }
}
