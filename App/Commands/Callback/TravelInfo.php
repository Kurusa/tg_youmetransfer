<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\User;

/**
 * Class TravelInfo
 * @package App\Commands\Callback
 */
class TravelInfo extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record = Record::find($this->parser::getByKey('id'));
        $data = explode('|', $this->parser::getByKey('r_id'));
        $user = User::find($data[1]);
        $text = 'Заказчик N' . $record->id . ' принял ваш запрос' . "\n";
        $text .= 'Создана поездка N' . $data[0] . ' (попутчики N' . $data[2] . ', N' . $data[3] . ')' . "\n";
        $text .= '<a href="tg://user?id=' . $user->chat_id . '">нажмите тут, чтобы открыть прямой чат с пользователем</a>' . "\n";
        $this->tg->sendMessage($text);
    }

}