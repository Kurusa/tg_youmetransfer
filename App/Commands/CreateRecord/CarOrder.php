<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class CarOrder
 * @package App\Commands\Keyboard
 */
class CarOrder extends BaseCommand {

    /**
     * ask if user with to order a car
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::CAR_ORDER) {
            $text = trim($this->parser::getMessage());
            if ($text == $this->text['car_order_yes']) {
                $this->tg->sendMessageWithKeyboard($this->text['car_order_yes_reply'], [
                    [$this->text['car_order_allow'], $this->text['car_order_dont_allow']],
                    [$this->text['back']],
                    [$this->text['cancel']]
                ]);
            } else {
                if ($text == $this->text['car_order_allow']) {
                    Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                        'car_order' => 1
                    ]);
                    $this->triggerCommand(FlightNumber::class);
                } else {
                    $this->triggerCommand(RecordType::class);
                }
            }
        } else {
            $this->user->status = UserStatusService::CAR_ORDER;
            $this->user->save();
            $this->tg->sendMessageWithKeyboard($this->text['car_order'], [
                [$this->text['car_order_yes'], $this->text['car_order_no']],
                [$this->text['back']],
                [$this->text['cancel']]
            ]);
        }
    }

}