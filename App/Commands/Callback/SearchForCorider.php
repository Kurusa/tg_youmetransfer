<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class SearchForCorider
 * @package App\Commands\OrderButtons
 */
class SearchForCorider extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record = Record::find($par ?: $this->parser::getByKey('id'));
        $google = new GoogleClient();
        $result = $google->searchForCalendarEvent($record, $this->user->id);
        if ($result) {
            // create button list with coriders,
            // where add_id||r_id = record id for which the search is being performed
            TelegramKeyboard::$list = $result;
            TelegramKeyboard::$add_id = 'creator_r_id'; // creator record id
            TelegramKeyboard::$id = 'id'; // requested record id
            TelegramKeyboard::$button_title = 'summary';
            TelegramKeyboard::$action = 'corider_info';
            TelegramKeyboard::$columns = 1;
            TelegramKeyboard::build();
            $this->tg->sendMessageWithInlineKeyboard($this->text['found_coriders'], TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['no_coriders']);
        }
    }

}