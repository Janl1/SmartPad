<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pad extends Model
{
    protected $fillable = ['user_id','heading','slug','password','clicks','status'];
}
