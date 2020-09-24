<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Models\Request;
use App\Services\RecordStatusService;
use App\TgHelpers\GoogleClient;

/**
 * Class ThisRider
 * @package App\Commands\Callback
 */
class ThisRider extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $this->tg->deleteMessage($this->parser::getMsgId());
        $user_record_data = Record::where('id', $this->parser::getByKey('id'))->first();
        $google = new GoogleClient();
        $google->updateSheetRecordStatus($user_record_data->spreadsheet_id, true, 'закрыли заказ');
        //$google->cancelCalendarEvent($user_record_data->event_id);

        foreach ($this->user->requests as $request) {
            if ($request['creator_record_id'] == $user_record_data->id || $request['requested_record_id'] == $user_record_data->id) {
                // check if it isnt selected corider
                if ($request['creator_record_id'] == $user_record_data->id && $request['requested_record_id'] == $this->parser::getByKey('r_id')) {

                } else if ($request['requested_record_id'] == $user_record_data->id && $request['creator_record_id'] == $this->parser::getByKey('r_id')) {

                } else {
                    $this->triggerCommand(CancelRider::class, $request->id);
                }
            }
        }

        $user_record_data->status = RecordStatusService::CANCELED;
        $user_record_data->save();
        $this->tg->sendMessage('Ок, мы закрыли ваш заказ. В календаре он также отмечен, как закрытый.
Вы можете отменить вашу поездку в любое время и заказ снова станет актуальным');
    }

}