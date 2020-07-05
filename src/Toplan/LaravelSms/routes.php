<?php

$attributes = array_merge([
    'prefix' => 'sms',
], config('laravel-sms.routeAttributes', []));

Route::group($attributes, function () {
    Route::get('info', 'Toplan\Sms\SmsController@getInfo');
    Route::post('verify-code/rule/{rule}/mobile/{mobile?}', 'Toplan\Sms\SmsController@postSendCode');
    Route::post('voice-verify/rule/{rule}/mobile/{mobile?}', 'Toplan\Sms\SmsController@postVoiceVerify');
});
