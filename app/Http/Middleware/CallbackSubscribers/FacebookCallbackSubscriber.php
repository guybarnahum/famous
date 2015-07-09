<?php

namespace App\Http\Middleware\CallbackSubscribers;


use Illuminate\Http\Request;
use App\Models\RealtimeUpdate;
    
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
        if (   isset( $q[ 'hub_mode' ] )){
            $object = $q[ 'hub_mode' ];
            
            $rtu = [ 'provider' => 'facebook',
                     'object'   => $object   ,
                     'json'     => $request->fullUrl(),
            ];

            // Allways store callbacks..
            RealtimeUpdate::create( $rtu );
            
            // handle subscribe challange response
            switch( $object ){
                case 'subscribe' : return $this->accept_subscribe( $q );
            }
            
            return $this->response( 'unknown hub_mode' );
        }
            
        $json    = $request->json();
        if (!is_string($json)){
            $json = json_encode( $request->fullUrl() );
        }
        
        $updates = json_decode($json, true);
        $object  = isset( $updates['object'] )?
                          $updates['object']  : '?';
        
        // validate request
        $signature = $request->header( 'X_HUB_SIGNATURE' );
        $ok        = true;

        if ( !empty($signature) ){
            $app_secret = env( 'FACEBOOK_CLIENT_SECRET' );
            $expected   = 'sha1=' . hash_hmac('sha1', $json, $app_secret );
            $ok = $signature == $expected;
        }

        if (!$ok){
            $object .= '!invalid';
        }
        
        $rtu = [ 'provider' => 'facebook',
                 'object'   => $object   ,
                 'json'     => $json     ,
        ];
        // Allways store callbacks..
        RealtimeUpdate::create( $rtu );

        return $this->response( 'ok' );
    }
}