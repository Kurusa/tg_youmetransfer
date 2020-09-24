<?php
return [
    'create_record' => \App\Commands\CreateRecord\DepartureCity::class,
    'cancel' => \App\Commands\MainMenu::class,
    'back' => \App\Commands\Keyboard\Back::class,
    'car_order_no' => \App\Commands\CreateRecord\CarOrder::class,
    'car_order_yes' => \App\Commands\CreateRecord\CarOrder::class,
    'car_order_dont_allow' => \App\Commands\CreateRecord\CarOrder::class,
    'car_order_allow' => \App\Commands\CreateRecord\CarOrder::class,
    'telegram' => \App\Commands\CreateRecord\Contact::class,
    'phone' => \App\Commands\CreateRecord\Contact::class,
    'email' => \App\Commands\CreateRecord\Contact::class,

    'your_records' => \App\Commands\OrderButtons\OrderButtons::class,
    'create_admin' => \App\Commands\Callback\CreateAdmin::class,
    'analytic' => \App\Commands\Keyboard\AdminAnalytic::class,
    'calendar' => \App\Commands\Keyboard\CalendarMainButtons::class,
    'settings' => \App\Commands\Keyboard\Settings::class,
    'change_lang' => \App\Commands\Keyboard\SelectLanguage::class,
];

