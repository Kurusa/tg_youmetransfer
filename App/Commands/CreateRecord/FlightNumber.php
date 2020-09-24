<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class FlightNumber
 * @package App\Commands\CreateRecord
 */
class FlightNumber extends BaseCommand {

    /**
     * ask user flight number
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::FLIGHT_NUMBER) {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'flight_number' => trim($this->parser::getMessage())
            ]);
            $this->triggerCommand(HotelTitle::class);
        } else {
            $this->user->status = UserStatusService::FLIGHT_NUMBER;
            $this->user->save();
            $this->tg->sendMessage($this->text['before_flight_number']);
            $this->tg->sendMessageWithKeyboard($this->text['flight_number'], [
                [$this->text['back'], $this->text['next']],
                [$this->text['cancel']]
            ]);
        }
    }

}