<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\Record;
use App\Models\User;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use App\TgHelpers\GoogleCalendarEventTitle;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class RecordType
 * @package App\Commands\CreateRecord
 */
class RecordType extends BaseCommand {

    /**
     * ask if user trip will continue for one day or more
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::TRIP_TYPE) {
            $google = new GoogleClient();

            $record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
            $record->status = RecordStatusService::DONE;
            $record->event_id = $google->createCalendarEvent($record);
            $record->spreadsheet_id = $google->createSheetRecord($record);
            $record->save();

            if ($this->parser::getMessage() == $this->text['travel_two_times']) {
                // copy record but with different dep, dest and dep time
                $record_back = $record->replicate();
                $record_back->dep_id = $record->dest_id;
                $record_back->dest_id = $record->dep_id;
                $record_back->dep_date = $record->dep_date + (60 * 60 * 4);
                $record_back->save();

                $record_back->event_id = $google->createCalendarEvent($record_back);
                $record_back->spreadsheet_id = $google->createSheetRecord($record_back);
                $record->save();

                $record->record_back = $record_back->id;
                $record->save();

                $this->tg->sendMessage($this->text['final_msg_back']);
            }

            TelegramKeyboard::addButton($this->text['search_coriders'], ['a' => 'search_coriders', 'id' => $record->id]);
            $this->triggerCommand(MainMenu::class, $this->text['final_msg0']);
            $this->tg->sendMessageWithInlineKeyboard($this->text['final_msg'], TelegramKeyboard::get());

            $riders = $google->searchForCoriders($record, $this->user->id);
            foreach ($riders as $rider) {
                TelegramKeyboard::$buttons = [];
                TelegramKeyboard::addButton($record->title, [
                    'a' => 'reverse_corider_info',
                    'r_id' => $rider['creator_id'],
                    'id' => $record->id
                ]);
                $user = User::find($rider['user_id']);
                $this->tg->sendMessageWithInlineKeyboard('для вашей поездки найден новый попутчик из ' . $rider['dep'] . ' в ' . $rider['dest'], TelegramKeyboard::get(), $user->chat_id);
            }
        } else {
            $this->user->status = UserStatusService::TRIP_TYPE;
            $this->user->save();
            $this->tg->sendMessageWithKeyboard($this->text['trip_type'], [
                [$this->text['travel_one_time'], $this->text['travel_two_times']],
                [$this->text['back']],
                [$this->text['cancel']]
            ]);
        }
    }

}