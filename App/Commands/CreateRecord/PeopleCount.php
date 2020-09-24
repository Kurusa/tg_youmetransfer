<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class PeopleCount
 * @package App\Commands\CreateRecord
 */
class PeopleCount extends BaseCommand {

    /**
     * you can change this value
     * @var int
     */
    protected $people_count = 3;

    /**
     * ask user to select people count
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->parser::getByKey('a') == 'p_count') {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'people_count' => $this->parser::getByKey('id')
            ]);
            $this->tg->deleteMessage($this->parser::getMsgId());
            $this->tg->updateMessageKeyboard($this->parser::getMsgId(), $this->text['select_dest'], TelegramKeyboard::get());
            $this->tg->sendMessage('Вы указали ' . $this->parser::getByKey('id') . ' человека');
            $this->triggerCommand(UserName::class);
        } else {
            // build buttons
            $people_list = [];
            for ($i = 1; $i <= $this->people_count; $i++) {
                $people_list[] = [
                    'id' => $i,
                    'title' => $i
                ];
            }
            TelegramKeyboard::$columns = 1;
            TelegramKeyboard::$list = $people_list;
            TelegramKeyboard::$action = 'p_count';
            TelegramKeyboard::build();
            // build buttons

            $this->user->status = UserStatusService::PEOPLE_COUNT;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['select_people_count'], [
                [$this->text['back']],
                [$this->text['cancel']]
            ]);
            $this->tg->sendMessageWithInlineKeyboard($this->text['list'], TelegramKeyboard::get());
        }
    }

}