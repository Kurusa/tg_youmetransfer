<?php

namespace App\Commands\Keyboard;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramKeyboard;

class CalendarMainButtons extends BaseCommand {

    function processCommand($par = false)
    {
        TelegramKeyboard::addButtonUrl($this->text['go_to_calendar'], 'https://calendar.google.com/calendar/embed?src=youmetransfer%40gmail.com&ctz=Europe%2FKiev');
        TelegramKeyboard::addButton($this->text['search_for_trip'], ['a' => 'search_trip']);

        $this->tg->sendMessageWithInlineKeyboard('тут можно посмотреть календарь со всеми заказами от всех попутчиков, выбрать подходящего и отправить запрос. Для этого нажмите "присоединиться к поездке"',
            TelegramKeyboard::get());
    }

}