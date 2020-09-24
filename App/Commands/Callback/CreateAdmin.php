<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\TgHelpers\TelegramKeyboard;

class CreateAdmin extends BaseCommand {

    function processCommand($par = false)
    {
        // create/delete admin
        $update = false;
        if ($this->parser::getByKey('a') == 'create_admin') {
            $chat_id = $this->parser::getByKey('id');
            $data = $this->db->table('userList')->where('chat_id', $chat_id)->select(['is_admin'])->results();
            $is_admin = !intval($data[0]['is_admin']);
            $this->db->table('userList')->where('chat_id', $chat_id)->update(['is_admin' => $is_admin]);
            $update = true;
        }

        $user_list = $this->db->query('SELECT chat_id, is_admin, user_name FROM userList WHERE id != ' . $this->chat_id)->select()->results();
        array_walk($user_list, function (&$key) {
            $user_name = $key['user_name'] ? $key['user_name'] : $key['chat_id'];
            $is_admin = $key['is_admin'] ? '✅' : '';
            $key['user_name'] = $user_name . $is_admin;
        });

        TelegramKeyboard::$columns = 1;
        TelegramKeyboard::$action = 'create_admin';
        TelegramKeyboard::$list = $user_list;
        TelegramKeyboard::$button_title = 'user_name';
        TelegramKeyboard::$id = 'chat_id';
        TelegramKeyboard::build();
        if ($update) {
            $this->tg->updateMessageKeyboard($this->parser::getMsgId(), $this->text['list'], TelegramKeyboard::get());
        } else {
            $this->tg->sendMessageWithInlineKeyboard($this->text['list'], TelegramKeyboard::get());
        }
    }

}