<?php

namespace App\Models\Base;

// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

use Awobaz\Compoships\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Model extends BaseModel
{
    // use Cachable;
    use HasFactory;
    use SoftDeletes;

    protected $perPage = 10;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected static $rules = [];

    protected static $customMessage = [];

    protected static $customAttribute = [];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });

        static::retrieved(function ($model) {
            //var_dump($model->id);
        });

        static::retrieved(function ($model) {
            //var_dump($model->id);
        });

        static::saving(function ($model) {
            if (request()->has('validate')) {
                return false;
            }
        });

        static::created(function ($model) {
        });

        static::updated(function ($model) {
        });

        static::deleting(function ($model) {
            //var_dump($model);
            //die();
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'id';
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    public static function validate($data, $method, $id = null)
    {
        //$data = array_filter($data);
        $rules = static::$rules[$method];
        $data = collect($data)->only(array_keys($rules))->all();
        foreach ($rules as $key => $rule) {
            // if has ID (update) modify set unique value & skip required
            if ($id != null) {
                $rule = str_replace(['required', ':id'], ['', $id], $rule);
            //var_dump($rule);
            } else {
                $rule = str_replace(',:id', '', $rule);
            }
            $rules[$key] = trim(str_replace('||', '|', $rule), '|');
        }
        //var_dump($rules);
        // return \Validator::validate($data, $rules, static::$customMessage, static::$customAttribute);
    }

    public function scopeAuth($props)
    {
        return $this;
    }

    public function scopeOfDaily($query, $date = null)
    {
        if ($date == null) {
            $date = now();
        }

        return $query->whereRaw("DATE(`created_at`) = DATE('$date')");
    }

    public function scopeOfWeekly($query, $date)
    {
        if ($date == null) {
            $date = now();
        }

        return $query->whereRaw("WEEK(`created_at`) = WEEK('$date') AND DATE(`created_at`) <= DATE('$date')");
    }

    public function scopeOfMonthly($query, $date)
    {
        if ($date == null) {
            $date = now();
        }

        return $query->whereRaw("MONTH(`created_at`) = MONTH('$date') AND DATE(`created_at`) <= DATE('$date')");
    }

    public function scopeOfAll($query, $date)
    {
        if ($date == null) {
            $date = now();
        }

        return $query->whereRaw("DATE(`created_at`) <= DATE('$date')");
    }

    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}
