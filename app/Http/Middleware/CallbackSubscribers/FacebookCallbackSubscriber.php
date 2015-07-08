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

    function response( $msg )
    {
        return (object) [ 'data' => $msg, 'err' => 200 ];
    }
    
    function accept_subscribe( $q )
    {
        // test verify token
        
        $app_id       = env( 'FACEBOOK_CLIENT_ID' );
        $verify_token = md5( $app_id );
        $sent_token   = isset( $q[ 'hub_verify_token'])?
                               $q[ 'hub_verify_token'] : '' ;
        
        // echo challange or reject
        if ( $verify_token == $sent_token ){
            $data = $q[ 'hub_challenge'];
        }
        else{
            $data = 'verify token failure';
        }

        return $this->response( $data );
    }
    
    /**
     * TODO: Create a parser and store it somewhere
     *
     * @param Request $request
     * @param $payload
     */
    function accept(Request $request, $payload )
    {
        $q = $request->all();
        
        // handle realtime updates
        $obj = isset( $q[ 'obj'      ] )? $q[ 'obj'      ] : 'unknown';
        $obj = isset( $q[ 'hub_mode' ] )? $q[ 'hub_mode' ] : '$obj'   ;

        $rtu = [ 'provider' => 'facebook',
                 'object'   => $obj      ,
                 'json'     => json_encode( $request->fullUrl()
            ];
                                  
        RealtimeUpdate::create( $rtu );

        // handle subscribe challange response
        $msg = 'ok';
        
        if ( isset( $q[ 'hub_mode'] ) ){
            switch( $q[ 'hub_mode'] ){
                case 'subscribe' :
                    return $this->accept_subscribe( $q );
                
                default :
                    $msg = 'unknown hub_mode option(' . $q[ 'hub_mode'] . ')';
                    break;
            }
        }
        
        return $this->response( $msg );
    }
//        $json_string = file_get_contents('php://input');
//        Log::info($json_string);
//        $obj = json_decode($json_string);
//        Log::info("object: {$obj->object}");
//        Log::info('entry: ');
//        Log::info(print_r($obj->entry, true));
//
}