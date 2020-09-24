<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model {

    protected $table = 'travel';
    protected $fillable = ['id', 'first_record_id', 'second_record_id', 'notified', 'created_at'];
    const UPDATED_AT = null;

}