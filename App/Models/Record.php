<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model {

    protected $table = 'record';
    protected $fillable = [
        'user_id', 'dep_id', 'dest_id', 'dep_date', 'people_count', 'hotel_title', 'user_name',
        'flight_number', 'car_order', 'email', 'phone', 'record_back', 'event_id', 'spreadsheet_id', 'status'
    ];
    protected $with = ['user'];
    const UPDATED_AT = null;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getDepCityAttribute()
    {
        $city = City::where('id', $this->dep_id)->first();
        return $city->title;
    }

    public function getFormatedDepDateAttribute()
    {
        return date('c', $this->dep_date);
    }

    public function getDestCityAttribute()
    {
        $city = City::where('id', $this->dest_id)->first();
        return $city->title;
    }

    public function getContactsAttribute()
    {
        $text = '<b>Имя и фамилия:</b> ' . ($this->user_name ?: ' - ') . "\n";
        $text .= '<b>email:</b> ' . ($this->email ?: ' - ') . "\n";
        $text .= '<b>phone:</b> ' . ($this->phone ?: ' - ') . "\n";

        return $text;
    }

    public function getTitleAttribute()
    {
        return 'заказ N' . $this->id . ' от ' . date('Y-m-d H:i', $this->dep_date);
    }

    public function getInfoAttribute()
    {
        $text = 'N' . $this->id . ' || ';
        $text .= 'из ' . $this->dep_city;
        $text .= ' в ' . $this->dest_city . ' || ';
        $text .= 'чел: ' . $this->people_count;

        $description = 'Заказ создан ' . $this->created_at . "\n";
        $description .= 'Едут ' . $this->people_count . ' человека' . "\n";
        $description .= 'ID пользователя: ' . $this->user_id;

        return [
            'title' => $text,
            'description' => $description
        ];
    }

}