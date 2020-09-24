<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\Request;
use App\Models\User;
use App\Services\RequestStatusService;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class AcceptRider
 * @package App\Commands\Callback
 */
class AcceptRider extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $this->tg->deleteMessage($this->parser::getMsgId());
        $creator_record_id = $this->parser::getByKey('id');
        $request_data = Request::where('creator_record_id', $creator_record_id)->where('requested_user_id', $this->user->id)->first();
        $creator_user = User::where('id', $request_data->creator_user_id)->first();

        $google = new GoogleClient();
        $google->updateSheetTravelStatus($request_data->spreadsheet_id, 'заявка принята');

        $record = Record::where('id', $request_data->creator_record_id)->first();
        $google->updateSheetRecordStatus($record->spreadsheet_id, null, [['попутчик найден', '0', '1']]);
        $record = Record::where('id', $request_data->requested_record_id)->first();
        $google->updateSheetRecordStatus($record->spreadsheet_id, null, [['попутчик найден', '0', '1']]);

        $text = 'Заказчик N' . $request_data->requested_record_id . ' принял ваш запрос';
        $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $creator_user->chat_id);
        Request::where('creator_record_id', $creator_record_id)->where('requested_user_id', $this->user->id)->update([
            'status' => RequestStatusService::DONE
        ]);

        $this->triggerCommand(CreateTravel::class, $creator_record_id);
    }

}