<?php

namespace App\Commands\Keyboard;

use App\Commands\BaseCommand;

class AdminAnalytic extends BaseCommand {

    function processCommand($par = false)
    {
        $text = '<b>Сколько пользователей дошли до (количество человек):</b> ' . "\n" .
            '7 дней: ' . $this->peopleCount(7) . "\n" .
            '14 дней: ' . $this->peopleCount(14) . "\n" .
            '30 дней: ' . $this->peopleCount(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько пользователей дошли до (выбор машины):</b> ' . "\n" .
            '7 дней: ' . $this->carOrder(7) . "\n" .
            '14 дней: ' . $this->carOrder(14) . "\n" .
            '30 дней: ' . $this->carOrder(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько пользователей дошли до (оставить контакты):</b> ' . "\n" .
            '7 дней: ' . $this->contact(7) . "\n" .
            '14 дней: ' . $this->contact(14) . "\n" .
            '30 дней: ' . $this->contact(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько пользователей Дошли до конца:</b> ' . "\n" .
            '7 дней: ' . $this->done(7) . "\n" .
            '14 дней: ' . $this->done(14) . "\n" .
            '30 дней: ' . $this->done(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько пользователей оставили телефоны/ники/имейлы:</b> ' . "\n" .
            '7 дней: ' . $this->contactsDone(7) . "\n" .
            '14 дней: ' . $this->contactsDone(14) . "\n" .
            '30 дней: ' . $this->contactsDone(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько пользователей нажало отмену в процессе заполнения:</b> ' . "\n" .
            '7 дней: ' . $this->cancels(7) . "\n" .
            '14 дней: ' . $this->cancels(14) . "\n" .
            '30 дней: ' . $this->cancels(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько пользователей запросило машину:</b> ' . "\n" .
            '7 дней: ' . $this->carOrderTrue(7) . "\n" .
            '14 дней: ' . $this->carOrderTrue(14) . "\n" .
            '30 дней: ' . $this->carOrderTrue(30) . "\n";

        $text .= "\n";

        $text .= '<b>Сколько поездок создано:</b> ' . "\n" .
            '7 дней: ' . $this->travelCount(7) . "\n" .
            '14 дней: ' . $this->travelCount(14) . "\n" .
            '30 дней: ' . $this->travelCount(30) . "\n";

        $this->triggerCommand(MainMenu::class, $text);
    }

    private function travelCount($days)
    {
        $data = $this->db->query('SELECT COUNT(*) AS count FROM requestList 
        WHERE status != "peding" AND created_at  >= ( CURDATE() - INTERVAL ' . $days . ' DAY ) > 0')->results();
        return $data[0]['count'];
    }

    private function peopleCount($days)
    {
        $data = $this->db->query('
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE UL.mode != "dep_city" AND UL.mode != "dest_city" AND UL.mode != "dep_date" 
        OR (SELECT COUNT(*) FROM recordList 
            WHERE chat_id = UL.chat_id AND people_count > 0 AND created_at  >= ( CURDATE() - INTERVAL ' . $days . ' DAY )) > 0')->results();
        return $data[0]['count'];
    }

    private function carOrder($days)
    {
        $data = $this->db->query('
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE UL.mode = "done" OR UL.mode = "car_order" OR UL.mode = "trip_type"
        OR (SELECT COUNT(*) FROM recordList 
            WHERE chat_id = UL.chat_id AND created_at  >= ( CURDATE() - INTERVAL ' . $days . ' DAY )) > 0')->results();
        return $data[0]['count'];
    }

    private function contact($days)
    {
        $data = $this->db->query('
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE UL.mode = "done" OR UL.mode = "contact" OR UL.mode = "car_order"
        OR (SELECT COUNT(*) FROM recordList 
            WHERE chat_id = UL.chat_id AND created_at  >= ( CURDATE() - INTERVAL ' . $days . ' DAY )) > 0')->results();
        return $data[0]['count'];
    }

    private function done($days)
    {
        $data = $this->db->query("
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE (SELECT COUNT(*) FROM recordList 
                WHERE chat_id = UL.chat_id AND done = 1 AND created_at  >= ( CURDATE() - INTERVAL " . $days . " DAY )) > 0")->results();
        return $data[0]['count'];
    }

    private function contactsDone($days)
    {
        $data = $this->db->query("
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE UL.chat_id = (SELECT chat_id FROM recordList
                WHERE telegram != \"\" OR email != \"\" OR phone != \"\" AND created_at  >= ( CURDATE() - INTERVAL " . $days . " DAY )) > 0")->results();
        return $data[0]['count'];
    }

    private function cancels($days)
    {
        $data = $this->db->query("
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE (SELECT COUNT(*) FROM cancelList
                WHERE chat_id = UL.chat_id AND created_at  >= ( CURDATE() - INTERVAL " . $days . " DAY )) > 0")->results();
        return $data[0]['count'];
    }

    private function carOrderTrue($days)
    {
        $data = $this->db->query("
        SELECT COUNT(*) AS count FROM userList as UL 
        WHERE UL.chat_id = (SELECT chat_id FROM recordList
                WHERE  car_order = 1 AND created_at  >= ( CURDATE() - INTERVAL " . $days . " DAY )) > 0")->results();
        return $data[0]['count'];
    }

}