<?php

namespace App\TgHelpers;

class TelegramKeyboard {

    static $columns = 2;
    static $list;

    static $button_title = 'title';

    static $action;
    static $id = 'id';
    static $set_id = false;
    static $add_id = null;

    static $buttons = [];

    static function build()
    {
        $one_row = [];

        foreach (self::$list as $key => $list_key) {
            $id = self::$set_id ? self::$id : (self::$id ? $list_key[self::$id] : $key);
            $callback_data = [
                'a' => self::$action,
                'id' => $id,
                'r_id' => self::$add_id ? $list_key[self::$add_id] : ''
            ];

            $one_row[] = [
                'text' => $list_key[self::$button_title],
                'callback_data' => json_encode($callback_data),
            ];

            if (count($one_row) == self::$columns) {
                self::$buttons[] = $one_row;
                $one_row = [];
            }
        }
        if (count($one_row) > 0) {
            self::$buttons[] = $one_row;
        }
    }

    static function addButton(string $text, array $callback)
    {
        self::$buttons[] = [[
            'text' => $text,
            'callback_data' => json_encode($callback),
        ]];
    }

    static function addButtonUrl(string $text, string $url)
    {
        self::$buttons[] = [[
            'text' => $text,
            'url' => $url
        ]];
    }

    static function get()
    {
        return self::$buttons;
    }

}