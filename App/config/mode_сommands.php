<?php

use App\Services\UserStatusService;

return [
    \App\Services\UserStatusService::DEPARTURE_DATE => \App\Commands\CreateRecord\DepartureDate::class,
    \App\Services\UserStatusService::HOTEL_TITLE => \App\Commands\CreateRecord\HotelTitle::class,
    \App\Services\UserStatusService::USER_NAME => \App\Commands\CreateRecord\UserName::class,
    \App\Services\UserStatusService::FLIGHT_NUMBER => \App\Commands\CreateRecord\FlightNumber::class,
    \App\Services\UserStatusService::CONTACT => \App\Commands\CreateRecord\Contact::class,
    \App\Services\UserStatusService::TRIP_TYPE => \App\Commands\CreateRecord\RecordType::class,
    \App\Services\UserStatusService::CAR_ORDER => \App\Commands\CreateRecord\CarOrder::class,
    'email' => \App\Commands\CreateRecord\Contact::class,
    'phone' => \App\Commands\CreateRecord\Contact::class,
    UserStatusService::SEARCH_IN_CALENDAR => \App\Commands\Callback\SearchForTripByNumber::class
];