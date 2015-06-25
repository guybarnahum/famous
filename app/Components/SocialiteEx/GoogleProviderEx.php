<?php namespace App\Components\SocialiteEx;
    
use Laravel\Socialite\Two\GoogleProvider;

class GoogleProviderEx extends GoogleProvider
{
    protected function getUserByToken($token)
    {
        $user = parent::getUserByToken($token);
        
        $res  = $this->getHttpClient()
                     ->get('https://www.googleapis.com/oauth2/v1/userinfo',
                           [
                                'headers' => [
                                    'Accept' => 'application/json',
                                    'Authorization' => 'Bearer ' . $token,
                                ],
                            ]);
        $res  = json_decode($res->getBody(), true);
        
        $photo_url = false;
        
        if ( is_array($res) && isset( $res[ 'photo' ])){
            $photo_url = $res[ 'photo' ];
        }
    
        if ( $photo_url ){
            
            if ( !isset   ( $user[ 'image' ] ) ||
                 !is_array( $user[ 'image' ] )  ) $user[ 'image' ] = array();
            
            $user[ 'image' ][ 'url' ] = $photo_url;
        }
    
        \Debugbar::info( 'GoogleProviderEx::getUserByToken=>' . print_r( $user, true ) );
        return $user;
    }
}