<?php

namespace App\TgHelpers;

use PHPtricks\Orm\Database;

class GoogleCalendarEventTitle {

    /**
     * @param Database $db
     * @param array $data
     * @param int $chatId
     * @return array
     */
    public function buildText(Database $db, array $data, int $chatId) :array
    {
        // build title for google calendar event
        // format - N672 || из Ratnapura в Badullah || чел: 3
        $city_data = $db->table('cityList')
            ->where('id', $data['dep_id'])
            ->orWhere('id', $data['dest_id'])
            ->select(['title'])->results();

        $text = 'N' . $data['id'] . ' || ';
        $text .= 'из ' . $city_data[0]['title'];
        $text .= ' в ' . $city_data[1]['title'] . ' || ';
        $text .= 'чел: ' . $data['people_count'];

        $description = 'Заказ создан ' . $data['created_at'] . "\n";
        $description .= 'Едут ' . $data['people_count'] . ' человека' . "\n";
        $description .= 'ID пользователя: ' . $chatId;

        return [
            'title' => $text,
            'description' => $description
        ];
    }

}