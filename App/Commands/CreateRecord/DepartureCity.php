<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\City;
use App\Models\Record;
use App\Services\UserStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class DepartureCity
 * @package App\Commands\CreateRecord
 */
class DepartureCity extends BaseCommand {

    /**
     * ask user to select departure city
     */
    function processCommand($par = false)
    {
        // if user selected some city
        if ($this->parser::getByKey('a') == 'dep_done') {
            $city_id = $this->parser::getByKey('id');
            $city_data = City::where('id', $city_id)->first();
            $this->tg->sendMessage($this->text['you_selected'] . $city_data->title);
            Record::create([
                'user_id' => $this->user->id,
                'dep_id' => $city_id
            ]);
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->triggerCommand(DestinationCity::class);
            exit();
        }

        // build buttons
        $search_limit = 18;
        $search_offset = $this->parser::getByKey('dep_o') ?: 0;
        $city_list = City::skip($search_offset)->take($search_limit)->orderBy('title')->get();
        TelegramKeyboard::$list = $city_list;
        TelegramKeyboard::$action = 'dep_done';
        TelegramKeyboard::build();
        // build buttons

        $check_city_list = City::skip($search_offset ? $search_offset + $search_limit : $search_limit)->take($search_limit)->orderBy('title')->get();
        if ($search_offset > 0) {
            TelegramKeyboard::addButton('<', ['a' => 'dep_prev', 'dep_o' => $search_offset - $search_limit]);
            if (count($check_city_list)) {
                TelegramKeyboard::addButton('>', ['a' => 'dep_next', 'dep_o' => $search_offset + $search_limit]);
            }
        } else {
            TelegramKeyboard::addButton('>', ['a' => 'dep_prev', 'dep_o' => $search_offset + $search_limit]);
        }

        if ($this->parser::getByKey('a') == 'dep_prev' || $this->parser::getByKey('a') == 'dep_next') {
            $this->tg->updateMessageKeyboard($this->parser::getMsgId(), $this->text['select_dep'], TelegramKeyboard::get());
        } else {
            $this->user->status = UserStatusService::DEPARTURE;
            $this->user->save();
            $this->tg->sendMessageWithKeyboard($this->text['select_dep'], [[$this->text['cancel']]]);
            $this->tg->sendMessageWithInlineKeyboard($this->text['list'], TelegramKeyboard::get());
        }

    }

}