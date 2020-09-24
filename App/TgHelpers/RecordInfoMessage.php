<?php

namespace App\TgHelpers;

use App\Models\Record;

/**
 * Class RecordInfoMessage
 * @package App\TgHelpers
 */
class RecordInfoMessage {

    /**
     * build trip info message
     * @param Record $data
     * @param bool $show_contacts
     * @return string
     */
    public static function buildText(Record $data, $show_contacts = false): string
    {
        $text = 'заказ N' . $data->id . ' от ' . date('Y-m-d H:i', $data->dep_date) . "\n" . "\n";
        $text .= '<b>Город отправления:</b> ' . $data->dep_city . "\n";
        $text .= '<b>Город прибытия:</b> ' . $data->dest_city . "\n";
        $text .= '<b>Дата отправления:</b> ' . date('Y-m-d H:i', $data->dep_date) . "\n";
        $text .= '<b>Количество людей:</b> ' . $data->people_count . "\n";
        $text .= '<b>Название отеля:</b> ' . ($data->hotel_title ?: ' - ') . "\n";
        $text .= '<b>Номер рейса:</b> ' . ($data->flight_number ?: ' - ') . "\n";
        $text .= '<b>Заказана ли машина:</b> ' . ($data->car_order ? 'да' : 'нет') . "\n";

        if ($show_contacts) {
            $text .= $data->contacts;
        }

        return $text;
    }

}