<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class User extends Model {

    protected $table = 'user';
    protected $fillable = ['id', 'chat_id', 'user_name', 'status', 'lang', 'is_admin'];
    const UPDATED_AT = null;

    public function getRequestsAttribute()
    {
        $requests = Request::where('creator_user_id', $this->id)->orWhere('requested_user_id', $this->id)->get();
        return $requests;
    }

    public function getTravelListAttribute()
    {
        $travels = DB::select(DB::raw("SELECT
first_record.user_id AS first_record_user_id, second_record.user_id AS second_record_user_id,
first_record.id AS first_record_id, second_record.id AS second_record_id,
dep_city.title AS dep_city, dest_city.title AS dest_city, first_record.dep_date,
travel.id AS travel_id
FROM travel
INNER JOIN record AS first_record ON first_record.id = travel.first_record_id
INNER JOIN record AS second_record ON second_record.id = travel.second_record_id
INNER JOIN city AS dep_city ON dep_city.id = first_record.dep_id
INNER JOIN city AS dest_city ON dest_city.id = first_record.dest_id
WHERE first_record.user_id = {$this->id}
OR second_record.user_id = {$this->id}
"));

        $data = [];
        foreach ($travels as $key => $travel) {
            $data[$key]['id'] = $travel->second_record_user_id == $this->id ? $travel->first_record_id : $travel->second_record_id;
            $id = ($travel->second_record_user_id == $this->id ? $travel->first_record_user_id : $travel->second_record_user_id);
            $data[$key]['add_id'] = $travel->travel_id . '|' . $id . '|' . $travel->first_record_id . '|' . $travel->second_record_id;
            $data[$key]['title'] = 'из ' . $travel->dep_city . ' в ' . $travel->dest_city . ' ' . date('Y-m-d', $travel->dep_date);
        }

        return $data;
    }
}