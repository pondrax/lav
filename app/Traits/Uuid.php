<?php

namespace App\Traits;

use Hashids\Hashids;

trait Uuid
{
    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $hashids = new Hashids();
                // $model->{$model->getKeyName()} = $hashids->encode(ceil(hexdec(uniqid())/1000));
                $model->{$model->getKeyName()} = $hashids->encode(abs(crc32(uniqid())));

                // $model->{$model->getKeyName()} = str()->orderedUuid()->toString();
            }
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
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
