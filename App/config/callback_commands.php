<?php
return [
    'dep_city' => \App\Commands\CreateRecord\DepartureCity::class,
    'dep_done' => \App\Commands\CreateRecord\DepartureCity::class,
    'dep_prev' => \App\Commands\CreateRecord\DepartureCity::class,
    'dep_next' => \App\Commands\CreateRecord\DepartureCity::class,
    'dest_done' => \App\Commands\CreateRecord\DestinationCity::class,
    'dest_prev' => \App\Commands\CreateRecord\DestinationCity::class,
    'dest_next' => \App\Commands\CreateRecord\DestinationCity::class,
    'p_count' => \App\Commands\CreateRecord\PeopleCount::class,

    'travel_list' => \App\Commands\OrderButtons\TravelList::class,
    'request_list' => \App\Commands\OrderButtons\RequestListButtons::class,
    'record_list' => \App\Commands\OrderButtons\RecordList::class,
    'record_info' => \App\Commands\OrderButtons\UserRecordInfo::class,
    'close_record_not_found' => \App\Commands\Callback\CloseRecordNotFound::class,
    'user_coriders' => \App\Commands\Callback\UserCoriders::class,

    //
    'close_record_found' => \App\Commands\Callback\CloseRecordFound::class,
    'select_rider_info' => \App\Commands\Callback\SelectCoriderInfo::class,
    'this_rider' => \App\Commands\Callback\ThisRider::class,
    //

    //
    'search_coriders' => \App\Commands\Callback\SearchForCorider::class,
    'corider_info' => \App\Commands\Callback\CoriderTripInfo::class,
    'reverse_corider_info' => \App\Commands\Callback\ReverseCoriderInfo::class,
    'request_rider' => \App\Commands\Callback\RequestRider::class,
    'more_info' => \App\Commands\Callback\MoreInfoAboutCorider::class,
    'accept_rider' => \App\Commands\Callback\AcceptRider::class,
    'cancel_rider' => \App\Commands\Callback\CancelRider::class,
    //

    'cancel_request_pending' => \App\Commands\Callback\CancelRequestPending::class,
    'travel_info' => \App\Commands\Callback\TravelInfo::class,


    'create_admin' => \App\Commands\Callback\CreateAdmin::class,

    'search_trip' => \App\Commands\Callback\SearchForTripByNumber::class,

    'del_msg' => \App\Commands\Callback\DelMsg::class,
    'create_travel' => \App\Commands\Callback\CreateTravel::class,

    'user_requests' => \App\Commands\Callback\UserRequests::class,
    'requests_to_user' => \App\Commands\Callback\RequestsToUser::class,
    'accepted_request_info' => \App\Commands\Callback\AcceptedRequestInfo::class,
    'answer' => \App\Commands\Callback\Answer::class,
];