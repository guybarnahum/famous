<?php
/**
 * Created by PhpStorm.
 * User: vchoy
 * Date: 6/17/15
 * Time: 6:25 PM
 */

namespace App\Http\Middleware\CallbackSubscribers;


use Illuminate\Http\Request;

class FacebookCallbackSubscriber implements _ICallbackSubscriber {

    function inspect(Request $request)
    {
        $referrer = $request->server('HTTP_REFERER');
        if (strpos($referrer, 'facebook') !== false) return true;
        return false;
    }

    function accept($payload)
    {
        // do stuff
    }
}