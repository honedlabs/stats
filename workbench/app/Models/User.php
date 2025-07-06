<?php

declare(strict_types=1);

namespace App\Models;

use App\Overviews\UserOverview;
use Database\Factories\UserFactory;
use Honed\Stats\Attributes\UseOverview;
use Honed\Stats\Concerns\HasOverview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[UseOverview(UserOverview::class)]
class User extends Authenticatable
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory;

    /**
     * @use \Honed\Stats\Concerns\HasOverview<\App\Overviews\UserOverview>
     */
    use HasOverview;

    use Notifiable;

    /**
     * The factory for the model.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    protected static $factory = UserFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the products for the user.
     *
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the products that the user has.
     *
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
