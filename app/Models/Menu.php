<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'routes',
    ];

    protected $casts = [
        'routes' => 'json',
    ];

    private $permissions = [];

    private $isDeveloper = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($user = auth()->user()) {
            $this->permissions = collect($user->getPermissions());
            $this->isDeveloper = $user->hasRole(['developer']);
        }
    }

    protected function routes(): Attribute
    {
        return new Attribute(
            get: function ($value) {
                $routes = collect(json_decode($value));

                return $routes->filter(function ($route) {
                    return $this->permissions->contains($route->permission);
                });
            }
        );
    }

    public function scopeRoute($query): Builder
    {
        return $query->when(! $this->isDeveloper, function ($q) {
            $this->permissions->each(function ($permission) use ($q) {
                $q->orWhereJsonContains('routes', ['permission' => $permission]);
            });
        });
    }
}
