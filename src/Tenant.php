<?php

namespace Dersam\Multitenant;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name'
    ];
}
