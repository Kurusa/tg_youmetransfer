<?php

namespace App\Commands\OrderButtons;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class OrderButtons
 * @package App\Commands\OrderButtons
 */
class OrderButtons extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        TelegramKeyboard::addButton('ваши поездки', ['a' => 'travel_list']);
        TelegramKeyboard::addButton('запросы попутчиков', ['a' => 'request_list']);
        TelegramKeyboard::addButton('ваши заказы', ['a' => 'record_list']);
        $this->tg->sendMessageWithInlineKeyboard($this->text['your_records'], TelegramKeyboard::get());
    }

}