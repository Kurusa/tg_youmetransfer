<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\City;
use App\Models\Record;
use App\Models\Request;
use App\Services\RequestStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class UserRequests
 * @package App\Commands\Callback
 */
class RequestsToUser extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $requests_to_user = Request::where('requested_user_id', $this->user->id)->where('status', RequestStatusService::PENDING)->get();
        if ($requests_to_user) {
            foreach ($requests_to_user as $request) {
                $record = Record::find($request->creator_record_id);
                $dep_city = City::find($record->dep_id);
                $dest_city = City::find($record->dest_id);
                TelegramKeyboard::addButton('Попутчик N' . $request->creator_record_id . ' прислал запрос на поездку из ' . $dep_city->title . ' в ' . $dest_city->title, [
                    'id' => $record->id,
                    'a' => 'more_info'
                ]);
            }

            $this->tg->sendMessageWithInlineKeyboard('запросы от попутчиков', TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['no_requests']);
        }
    }
}