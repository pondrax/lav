<?php

namespace App\Models;

use App\Traits\Filter;
use App\Traits\Uuid;
use Awobaz\Compoships\Database\Eloquent\Model as BaseModel;
// use Illuminate\Database\Eloquent\Model as BaseModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Model extends BaseModel
{
    use Uuid, Filter, HasFactory;
}
