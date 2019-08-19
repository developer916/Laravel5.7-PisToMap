<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'pi_id', 'no', 'lat', 'lng', 'color'
    ];
}
