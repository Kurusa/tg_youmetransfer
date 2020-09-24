<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {

    protected $table = 'answer';
    protected $fillable = ['id', 'user_id', 'answer', 'travel_id'];
    const UPDATED_AT = null;

}