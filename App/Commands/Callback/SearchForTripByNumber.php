<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use App\TgHelpers\RecordInfoMessage;
use App\TgHelpers\TelegramKeyboard;

class SearchForTripByNumber extends BaseCommand {

    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::SEARCH_IN_CALENDAR) {
            $record = Record::find($this->parser::getMessage());
            if ($record && $record->user_id != $this->user->id && $record->status == RecordStatusService::DONE) {
                $this->triggerCommand(MainMenu::class, RecordInfoMessage::buildText($record) . '
Чтобы присоединиться к этому заказу, вам надо создать новый (тот же день, то же место отправления,прибытия). Бот сам определит, что вам по пути и соединит вас с попутчиком. 
кнопка: «создать заказ»  внизу'
                );
            } else {
                $this->tg->sendMessage('К сожалению, поездки не найдено');
            }
        } else {
            $this->user->status = UserStatusService::SEARCH_IN_CALENDAR;
            $this->user->save();
            $this->tg->sendMessageWithKeyboard($this->text['enter_trip_number'], [
                [$this->text['cancel']]
            ]);
        }
    }

}