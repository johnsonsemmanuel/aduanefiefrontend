<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CartDetail
 *
 * @property int $id
 * @property int $stock_id
 * @property int $user_cart_id
 * @property int $parent_id
 * @property int $quantity
 * @property float|null $price
 * @property float|null $discount
 * @property int $bonus
 * @property string|null $bonus_type
 * @property string $custom_name
 * @property int $custom_unit_id
 * @property string $custom_photo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property double $rate_price
 * @property double $rate_discount
 * @property-read Stock|null $stock
 * @property-read self|null $parent
 * @property-read Collection|self[] $children
 * @property-read UserCart $userCart
 * @property-read Unit $customUnit
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereBonus($value)
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self wherePrice($value)
 * @method static Builder|self whereQuantity($value)
 * @method static Builder|self whereStockId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @method static Builder|self whereUserCartId($value)
 * @mixin Eloquent
 */
class CartDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bonus'  => 'bool'
    ];

    public function getRatePriceAttribute(): float|int|null
    {
        return request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*') ?
            $this->price * $this->userCart?->cart?->rate :
            $this->price;
    }

    public function getRateDiscountAttribute(): float|int|null
    {
        return request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*') ?
            $this->discount * $this->userCart?->cart?->rate :
            $this->discount;
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function userCart(): BelongsTo
    {
        return $this->belongsTo(UserCart::class);
    }

    public function customUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'custom_unit_id');
    }
}
