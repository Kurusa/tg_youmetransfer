<?php

namespace App\Commands\OrderButtons;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class RecordList
 * @package App\Commands\OrderButtons
 */
class RecordList extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record_list = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::DONE)->get();
        if ($record_list->count()) {
            TelegramKeyboard::$columns = 1;
            TelegramKeyboard::$list = $record_list;
            TelegramKeyboard::$action = 'record_info';
            TelegramKeyboard::build();
            $this->tg->sendMessageWithInlineKeyboard($this->text['your_records'], TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['no_records']);
        }
    }
}