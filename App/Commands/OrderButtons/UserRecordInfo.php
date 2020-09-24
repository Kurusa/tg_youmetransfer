<?php

namespace App\Commands\OrderButtons;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\TgHelpers\RecordInfoMessage;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class UserRecordInfo
 * @package App\Commands\Callback
 */
class UserRecordInfo extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record = Record::find($this->parser::getByKey('id'));
        if ($record) {
            TelegramKeyboard::addButton($this->text['search_coriders'], ['a' => 'user_coriders', 'id' => $record->id]);
            TelegramKeyboard::addButton($this->text['close_record_not_found'], ['a' => 'close_record_not_found', 'id' => $record->id]);
            TelegramKeyboard::addButton($this->text['close_record_found'], ['a' => 'close_record_found', 'id' => $record->id]);
            $this->tg->sendMessageWithInlineKeyboard(RecordInfoMessage::buildText($record, true), TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['cant_find']);
        }
    }

}