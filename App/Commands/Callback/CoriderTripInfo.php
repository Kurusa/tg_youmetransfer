<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\TgHelpers\RecordInfoMessage;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class CoriderTripInfo
 * @package App\Commands\Callback
 */
class CoriderTripInfo extends BaseCommand {

    /**
     * found corider trip info with button to request this trip
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record = Record::find($this->parser::getByKey('id'));
        if ($record->count()) {
            TelegramKeyboard::addButton($this->text['send_request_to_rider'], [
                'a' => 'request_rider',
                'id' => $record->id,
                'r_id' => $this->parser::getByKey('r_id')
            ]);
            $this->tg->sendMessageWithInlineKeyboard(RecordInfoMessage::buildText($record), TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['cant_find']);
        }
    }

}