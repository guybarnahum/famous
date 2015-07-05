<?php

namespace App\Http\Middleware\CallbackSubscribers;

use Illuminate\Http\Request;

use \Log;

class FacebookCallbackSubscriber implements _ICallbackSubscriber {

    /**
     * NOTE: This check is kind of lame, keep it?
     *
     * @param Request $request
     * @param $namespace
     * @return bool
     */
    function inspect(Request $request, $namespace)
    {
        return ( $namespace == 'facebook' );
    }

    /**
     * TODO: Create a parser and store it somewhere
     *
     * @param Request $request
     * @param $payload
     */
    function accept(Request $request, $payload )
    {
        Log::info( $request->fullUrl() );
        
        return (object) [ 'data' => 'ok', 'err' => 200 ];
        
//        $json_string = file_get_contents('php://input');
//        Log::info($json_string);
//        $obj = json_decode($json_string);
//        Log::info("object: {$obj->object}");
//        Log::info('entry: ');
//        Log::info(print_r($obj->entry, true));
    }
}