<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class HotelTitle
 * @package App\Commands\CreateRecord
 */
class HotelTitle extends BaseCommand {

    /**
     * ask user to select departure city while creating record
     */
    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::HOTEL_TITLE) {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'hotel_title' => trim($this->parser::getMessage())
            ]);
            $this->triggerCommand(RecordType::class);
        } else {
            $this->user->status = UserStatusService::HOTEL_TITLE;
            $this->user->save();
            $this->tg->sendMessageWithKeyboard($this->text['hotel_name'], [
                [$this->text['back'], $this->text['next']],
                [$this->text['cancel']]
            ]);
        }
    }

}