<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class Contact
 * @package App\Commands\CreateRecord
 */
class Contact extends BaseCommand {

    /**
     * ask user email or phone
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $buttons = [
            [$this->text['email'], $this->text['phone']],
            [$this->text['back'], $this->text['next']],
            [$this->text['cancel']]
        ];
        switch ($this->user->status) {
            case UserStatusService::CONTACT:
                switch ($this->parser::getMessage()) {
                    case $this->text['phone']:
                    case $this->text['email']:
                        $key = \array_flip($this->text)[$this->parser::getMessage()];
                        $this->tg->sendMessageWithKeyboard($this->text['ask_' . $key], [
                            [$this->text['back'], $this->text['next']],
                            [$this->text['cancel']]
                        ]);
                        $this->user->status = $key;
                        $this->user->save();
                        break;
                    default:
                        $this->triggerCommand(CarOrder::class);
                        break;
                }
                break;
            case 'phone':
            case 'email':
                if ($this->parser::getMessage() !== $this->text['back']) {
                    $checked = false;
                    if ($this->user->status == 'email') {
                        if (filter_var($this->parser::getMessage(), FILTER_VALIDATE_EMAIL)) {
                            $checked = true;
                            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                                $this->user->status => trim($this->parser::getMessage())
                            ]);
                        } else {
                            $this->tg->sendMessageWithKeyboard($this->text['enter_right_email'], $buttons);
                        }
                    } else {
                        $checked = true;
                        Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                            $this->user->status => trim($this->parser::getMessage())
                        ]);
                    }


                    $this->user->status = UserStatusService::CONTACT;
                    $this->user->save();
                    if ($checked) {
                        $this->tg->sendMessageWithKeyboard($this->text['contact_wrote'], $buttons);
                    }
                } else {
                    $this->user->status = UserStatusService::CONTACT;
                    $this->user->save();
                    $this->tg->sendMessageWithKeyboard($this->text['contact'], $buttons);
                }
                break;
            default:
                $this->user->status = UserStatusService::CONTACT;
                $this->user->save();
                $this->tg->sendMessageWithKeyboard($this->text['contact'], $buttons);
                break;
        }
    }

}