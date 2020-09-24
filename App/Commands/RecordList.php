<?php

namespace App\Commands;

use App\TgHelpers\TelegramKeyboard;

class RecordList extends BaseCommand {

    /**
     * build user record list
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record_list = $this->db->table('recordList')
            ->where('chat_id', $this->chat_id)
            ->where('done', 1)
            ->select(['id', 'dep_date'])->results();

        if ($record_list[0]) {
            TelegramKeyboard::$columns = 1;
            TelegramKeyboard::$list = $record_list;
            TelegramKeyboard::$action = 'record_info';
            TelegramKeyboard::build();
            $this->tg->sendMessageWithInlineKeyboard($this->text['your_records'], TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['no_records']);
        }

    }

}