<?php

namespace App\Commands\OrderButtons;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Services\RequestStatusService;
use App\TgHelpers\TelegramKeyboard;

class RequestListButtons extends BaseCommand {

    function processCommand($par = false)
    {
        TelegramKeyboard::addButton('ваши запросы к попутчикам', ['a' => 'user_requests']);
        TelegramKeyboard::addButton('запросы от попутчиков', ['a' => 'requests_to_user']);
        $this->tg->sendMessageWithInlineKeyboard('запросы попутчиков', TelegramKeyboard::get());
    }

}