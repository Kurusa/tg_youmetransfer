<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\Request;
use App\TgHelpers\RecordInfoMessage;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class MoreInfoAboutCorider
 * @package App\Commands\Callback
 */
class MoreInfoAboutCorider extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        // requested record
        $record = Record::find($this->parser::getByKey('id'));
        if ($record) {
            $request_data = Request::where('creator_record_id', $record->id)->where('requested_user_id', $this->user->id)->first();
            TelegramKeyboard::addButton($this->text['accept_rider'] . 'N' . $record->id, ['a' => 'accept_rider', 'id' => $request_data->creator_record_id]);
            TelegramKeyboard::addButton($this->text['cancel_rider'] . 'N' . $record->id, ['a' => 'cancel_rider', 'id' => $request_data->creator_record_id]);
            $this->tg->updateMessageKeyboard($this->parser::getMsgId(), RecordInfoMessage::buildText($record), TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['cant_find']);
        }

    }

}