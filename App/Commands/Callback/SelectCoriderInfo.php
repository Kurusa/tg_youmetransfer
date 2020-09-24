<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\Request;
use App\TgHelpers\RecordInfoMessage;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class SelectCoriderInfo
 * @package App\Commands\Callback
 */
class SelectCoriderInfo extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $selected_request_data = Request::where('id', $this->parser::getByKey('r_id'))->first();
        $user_record_id = $this->parser::getByKey('id');
        if ($selected_request_data->requested_record_id == $user_record_id) {
            $selected_record_data = Record::find($selected_request_data->creator_record_id);
        } else {
            $selected_record_data = Record::find($selected_request_data->requested_record_id);
        }
        if ($selected_record_data) {
            $info = new RecordInfoMessage();
            TelegramKeyboard::addButton($this->text['this_rider'], [
                'a' => 'this_rider',
                'id' => $user_record_id,
                'r_id' => $selected_record_data->id
            ]);
            $this->tg->sendMessageWithInlineKeyboard($info->buildText($selected_record_data), TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['cant_find']);
        }
    }

}