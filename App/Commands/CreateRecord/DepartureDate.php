<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use DateTime;

/**
 * Class DepartureDate
 * @package App\Commands\CreateRecord
 */
class DepartureDate extends BaseCommand {

    /**
     * ask user to write departure date
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::DEPARTURE_DATE) {
            $date = $this->parser::getMessage();
            $dt = DateTime::createFromFormat('Y-m-d H:i', $date);
            // check date format
            if ($dt !== false && !array_sum($dt::getLastErrors())) {
                Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                    'dep_date' => strtotime($date)
                ]);
                $this->triggerCommand(PeopleCount::class);
            } else {
                $this->tg->sendMessage($this->text['wrong_dep_date']);
            }
        } else {
            $this->user->status = UserStatusService::DEPARTURE_DATE;
            $this->user->save();
            // text with date example
            $this->tg->sendMessageWithKeyboard($this->text['select_dep_date'] . '`' . date('Y-m-d H:i') . '`', [
                [$this->text['back']],
                [$this->text['cancel']]
            ]);
        }
    }

}