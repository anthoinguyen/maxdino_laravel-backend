<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reaction extends BaseModel
{
    //
    protected $table = 'reactions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ask_id', 'status'
    ];
}
