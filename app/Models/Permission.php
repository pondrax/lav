<?php

namespace App\Models;

use App\Traits\Filter;
use App\Traits\Uuid;
use Yajra\Acl\Models\Permission as Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Permission extends Model
{
    use Uuid, Filter;

    

    /**
     * User can belong many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        /** @var class-string $model */
        $model = config('acl.role');

        return $this->belongsToMany($model)
        ->withTimestamps()
        ->using(new class extends Pivot
        {
            use Uuid;
        });
    }
}
