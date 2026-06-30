<?php

namespace App\Models;

use Database\Factories\TermConditionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\TermCondition
 *
 * @property int $id
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TermConditionTranslation|null $translation
 * @property-read Collection|TermConditionTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static TermConditionFactory factory(...$parameters)
 * @method static Builder|TermCondition newModelQuery()
 * @method static Builder|TermCondition newQuery()
 * @method static Builder|TermCondition query()
 * @method static Builder|TermCondition whereCreatedAt($value)
 * @method static Builder|TermCondition whereId($value)
 * @method static Builder|TermCondition whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TermCondition extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public const TYPE_USER   = 'user';
    public const TYPE_SELLER = 'seller';
    public const TYPE_DRIVER = 'driver';

    public const TYPES = [
        self::TYPE_USER   => self::TYPE_USER,
        self::TYPE_SELLER => self::TYPE_SELLER,
        self::TYPE_DRIVER => self::TYPE_DRIVER,
    ];

    // Translations
    public function translations(): HasMany
    {
        return $this->hasMany(TermConditionTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(TermConditionTranslation::class);
    }

}
