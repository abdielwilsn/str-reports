<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Deposit extends Model
{
    use HasFactory;

    public const STATUS_CONFIRMED = 'confirmed';

    protected $fillable = [
        'user_id',
        'cryptocurrency_id',
        'transaction_hash',
        'from_address',
        'to_address',
        'expected_amount',
        'actual_amount',
        'amount',
        'proof_of_payment',
        'status',
        'confirmations',
        'verification_error',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:18',
            'actual_amount' => 'decimal:18',
            'amount' => 'decimal:18',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    protected function proofOfPaymentUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->proof_of_payment
                ? Storage::disk('public')->url('proofs/'.$this->proof_of_payment)
                : null,
        );
    }
}
