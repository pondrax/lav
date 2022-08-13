<?php

namespace App\Models;

use App\Traits\Filter;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Yajra\Acl\Models\Role as Model;

/**
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property bool $system
 */
class Role extends Model
{
    use Uuid, Filter;

    /**
     * Roles can hace many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        /** @var class-string $model */
        $model = config('acl.permission');

        return $this->belongsToMany($model)
      ->withTimestamps()
      ->using(new class extends Pivot
      {
          use Uuid;
      });
    }
}
