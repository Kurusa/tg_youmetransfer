<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\Request;
use App\Services\RequestStatusService;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class RequestRider
 * @package App\Commands\Callback
 */
class RequestRider extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        // requested record id
        $requested_record = Record::find($this->parser::getByKey('id'));
        if ($requested_record) {
            // record id for which the search is being performed
            $creator_record_id = $this->parser::getByKey('r_id');

            // check if user already sent request to to this record
            $possible_request = Request::where('creator_record_id', $creator_record_id)
                ->where('creator_user_id', $this->user->id)
                ->where('requested_user_id', $requested_record->user['chat_id'])
                ->where('requested_record_id', $requested_record->id)
                ->where('status', '!=', RequestStatusService::CANCELED)
                ->first();
            if ($possible_request) {
                TelegramKeyboard::addButton($this->text['request_list'], ['a' => 'request_list']);
                $this->tg->sendMessageWithInlineKeyboard($this->text['already_requested'], TelegramKeyboard::get());
            } else {
                // notify requested user about request
                TelegramKeyboard::addButton($this->text['more_info'], ['a' => 'more_info', 'id' => $creator_record_id]);
                TelegramKeyboard::addButton($this->text['accept_rider'] . 'N' . $creator_record_id, ['a' => 'accept_rider', 'id' => $creator_record_id]);
                TelegramKeyboard::addButton($this->text['cancel_rider'] . 'N' . $creator_record_id, ['a' => 'cancel_rider', 'id' => $creator_record_id]);

                $text = 'Попутчик N' . $creator_record_id . ' прислал запрос на добавление в вашу поездку N' . $requested_record->id;
                $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $requested_record->user['chat_id']);

                // send user message with button "your request list"
                TelegramKeyboard::$buttons = [];
                TelegramKeyboard::addButton($this->text['request_list'], ['a' => 'request_list']);
                $this->tg->sendMessageWithInlineKeyboard($this->text['we_send_request'], TelegramKeyboard::get());

                $request = Request::create([
                    'creator_user_id' => $this->user->id, 'requested_user_id' => $requested_record->user['id'],
                    'creator_record_id' => $creator_record_id, 'requested_record_id' => $requested_record->id
                ]);

                $google = new GoogleClient();
                $request->spreadsheet_id = $google->createSheetTravel($request);
                $request->save();
            }
            $this->tg->deleteMessage($this->parser::getMsgId());
        } else {
            $this->tg->sendMessage($this->text['cant_find']);
        }

    }

}