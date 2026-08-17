<?php

namespace Modules\Sales\Models;

// Framework & Database
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;
use Modules\Sales\Database\Factories\SaleFactory;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'customer_id',
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'tax_percentage',
        'total',
        'payment_method',
        'amount_paid',
        'change',
        'status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'discount'       => 'decimal:2',
            'tax'            => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'total'          => 'decimal:2',
            'amount_paid'    => 'decimal:2',
            'change'         => 'decimal:2',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): SaleFactory
    {
        return SaleFactory::new();
    }

    /**
     * Items belonging to this sale.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    /**
     * Customer of the sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * User/Seller who processed the sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for completed sales.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending/held sales.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for cancelled sales.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to search sales by sale number or customer name.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('number', 'like', "%{$term}%")
              ->orWhereHas('customer', function (Builder $cq) use ($term) {
                  $cq->where('name', 'like', "%{$term}%")
                     ->orWhere('document', 'like', "%{$term}%");
              });
        });
    }

    /**
     * Determine if the sale is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if the sale is pending / on hold.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Determine if the sale is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Generate sequential sale number (e.g. VTA-000001).
     */
    public static function generateNextNumber(): string
    {
        $lastSale = static::withTrashed()->latest('id')->first();
        $nextId = $lastSale ? $lastSale->id + 1 : 1;

        return 'VTA-' . str_pad((string)$nextId, 6, '0', STR_PAD_LEFT);
    }
}
