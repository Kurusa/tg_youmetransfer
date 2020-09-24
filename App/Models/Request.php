<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model {

    protected $table = 'request';
    protected $fillable = [
        'creator_user_id', 'requested_user_id',
        'creator_record_id', 'requested_record_id',
        'spreadsheet_id', 'status'
    ];
    const UPDATED_AT = null;

    public function getRequestedTitleAttribute()
    {
        return 'Попутчик N' . $this->requested_record_id;
    }

    public function getCreatorTitleAttribute()
    {
        return 'Попутчик N' . $this->creator_record_id;
    }

}