<?php
require __DIR__ . '/../../../vendor/autoload.php';

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$tg = new \App\TgHelpers\TelegramApi();

$travels = \App\Models\Travel::where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->where('notified', 0)->get();

foreach ($travels as $travel) {
    $first_record = \App\Models\Record::find($travel->first_record_id);
    $second_record = \App\Models\Record::find($travel->second_record_id);

    $first_user = \App\Models\User::find($first_record->user_id);
    $second_user = \App\Models\User::find($second_record->user_id);

    \App\TgHelpers\TelegramKeyboard::$buttons = [];
    \App\TgHelpers\TelegramKeyboard::addButton('пока в процессе', ['a' => 'answer', 'ans' => 'pending', 'id' => $travel->id]);
    \App\TgHelpers\TelegramKeyboard::addButton('попутчик не отвечает', ['a' => 'answer', 'ans' => 'no_reply', 'id' => $travel->id]);
    \App\TgHelpers\TelegramKeyboard::addButton('да, мы договорились', ['a' => 'answer', 'ans' => 'replied', 'id' => $travel->id]);

    $first_text = 'Вы договорились с попутчиком <a href="tg://user?id=' . $first_user->chat_id . '">' . $first_user->user_name . '</a> на поездку из ' . $first_record->dep_city . ' в ' . $first_record->dest_city . '?';
    $second_text = 'Вы договорились с попутчиком <a href="tg://user?id=' . $second_user->chat_id . '">' . $second_user->user_name . '</a> на поездку из ' . $second_record->dep_city . ' в ' . $second_record->dest_city . '?';
    $tg->sendMessageWithInlineKeyboard($first_text, \App\TgHelpers\TelegramKeyboard::get(), $second_user->chat_id);
    $tg->sendMessageWithInlineKeyboard($second_text, \App\TgHelpers\TelegramKeyboard::get(), $first_user->chat_id);
}
