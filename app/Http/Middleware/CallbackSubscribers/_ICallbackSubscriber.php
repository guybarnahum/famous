<?php

namespace App\Http\Middleware\CallbackSubscribers;

use Illuminate\Http\Request;

interface _ICallbackSubscriber {
    function inspect(Request $request);
    function accept($payload);
} 