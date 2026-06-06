<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'sku',
        'category',
        'category_id',
        'price',
        'compare_price',
        'stock',
        'stock_quantity',
        'approval_status',
        'description',
        'image',
        'images',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function scopeMarketplaceVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('approval_status', 'approved');
    }

    // Virtual status attribute to bridge with vendor-panel 'status' (Pending, Approved, Rejected)
    public function getStatusAttribute(): string
    {
        return ucfirst($this->approval_status ?? 'pending');
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['approval_status'] = strtolower($value);
    }
}
