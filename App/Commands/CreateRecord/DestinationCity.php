<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\City;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class DestinationCity
 * @package App\Commands\CreateRecord
 */
class DestinationCity extends BaseCommand {

    /**
     * ask user to select destination city
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'dest_done') {
            $city_id = $this->parser::getIdFromCallback();
            $city_data = City::where('id', $city_id)->first();
            $this->tg->sendMessage($this->text['you_selected'] . $city_data->title);
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'dest_id' => $city_id
            ]);
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->triggerCommand(DepartureDate::class);
            exit;
        }

        $search_limit = 18;
        $search_offset = $this->parser::getByKey('dest_o') ?: 0;
        $record_data = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
        $city_list = City::whereNotIn('id', [$record_data->dep_id])->skip($search_offset)->take($search_limit)->orderBy('title')->get();
        TelegramKeyboard::$list = $city_list;
        TelegramKeyboard::$action = 'dest_done';
        TelegramKeyboard::$id = 'id';
        TelegramKeyboard::build();

        $check_search_offset = $search_offset ? $search_offset + $search_limit : $search_limit;
        $check_city_list = City::whereNotIn('id', [$record_data->dep_id])->skip($check_search_offset)->take($check_search_offset)->orderBy('title')->get();
        if ($search_offset > 0) {
            TelegramKeyboard::addButton('<', ['a' => 'dest_prev', 'dest_o' => $search_offset - $search_limit]);
            if (count($check_city_list)) {
                TelegramKeyboard::addButton('>', ['a' => 'dest_next', 'dest_o' => $search_offset + $search_limit]);
            }
        } else {
            TelegramKeyboard::addButton('>', ['a' => 'dest_prev', 'dest_o' => $search_offset + $search_limit]);
        }


        if ($this->parser::getByKey('a') == 'dest_prev' || $this->parser::getByKey('a') == 'dest_next') {
            $this->tg->updateMessageKeyboard($this->parser::getMsgId(), $this->text['select_dest'], TelegramKeyboard::get());
        } else {
            $this->user->status = UserStatusService::DESTINATION;
            $this->user->save();
            $this->tg->sendMessageWithKeyboard($this->text['select_dest'], [
                [$this->text['back']],
                [$this->text['cancel']]
            ]);
            $this->tg->sendMessageWithInlineKeyboard($this->text['list'], TelegramKeyboard::get());
        }
    }

}