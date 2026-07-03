<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'invoice_number',
        'invoice_date',
        'due_date',
        'sender_name',
        'sender_email',
        'sender_phone',
        'sender_address',
        'sender_additional',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'client_additional',
        'items',
        'tax_rate',
        'discount_rate',
        'shipping_cost',
        'subtotal',
        'total',
        'currency',
        'notes',
        'terms',
        'template_id',
        'theme_color',
        'status',
    ];

    /**
     * Get the user that owns this invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'items' => 'array',
            'sender_additional' => 'array',
            'client_additional' => 'array',
            'tax_rate' => 'decimal:2',
            'discount_rate' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->uuid)) {
                $invoice->uuid = (string) Str::uuid();
            }
        });
    }
}
