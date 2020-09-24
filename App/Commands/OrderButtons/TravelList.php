<?php

namespace App\Commands\OrderButtons;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class TravelList
 * @package App\Commands\OrderButtons
 */
class TravelList extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->user->travel_list) {
            TelegramKeyboard::$list = $this->user->travel_list;
            TelegramKeyboard::$action = 'travel_info';
            TelegramKeyboard::$add_id = 'add_id';
            TelegramKeyboard::$columns = 1;
            TelegramKeyboard::build();
            $this->tg->sendMessageWithInlineKeyboard('объединенные поездки', TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage('У вас нету поездок');
        }
    }

}