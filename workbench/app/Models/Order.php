<?php

declare(strict_types=1);

namespace App\Models;

use Honed\Stats\Concerns\HasOverview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /**
     * @use \Honed\Stats\Concerns\HasOverview
     */
    use HasOverview;

    /**
     * The attributes that cannot be mass assigned.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the product that was ordered.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that ordered the product.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
