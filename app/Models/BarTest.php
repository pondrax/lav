<?php

namespace App\Models;

use App\Traits\Filter;
use App\Traits\Uuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarTest extends Model
{
    use Uuid, Filter, HasFactory;

    protected $table = 'bar_tests';

    protected $fillable = [
        'title',
        'body',
        'slug',
        'published_at'
    ];
}
