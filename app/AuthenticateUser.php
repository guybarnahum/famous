<?php namespace App;
// AuthenticateUser.php
use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;
use App\Repositories\AccountRepository;
use Request;

class AuthenticateUser {
    
    private $socialite;
    private $auth;
    private $accounts;
    
    public function __construct(Socialite     $socialite    ,
                                Guard             $auth     ,
                                AccountRepository $accounts )
    {
        $this->socialite = $socialite;
        $this->accounts  = $accounts;
        $this->auth      = $auth;
    }
    
    private function get_socialiteAuthorization( $provider )
    {
        return $this->socialite->driver($provider)->redirect();
    }
    
    private function get_socialiteUserData( $provider )
    {
        $s_user = $this->socialite->driver($provider)->user();
        
        if ( !empty( $s_user ) ){
            $s_user->provider = $provider;
        }
        
        return $s_user;
    }
    
    public function execute($request, $listener, $provider)
    {
        // we need a request
        if (!$request){
            return $this->get_socialiteAuthorization( $provider );
        }
        
        //
        $s_user = $this->get_socialiteUserData( $provider );
        
        // attempt to map to user from soclite user account
        // if not found create account / user from socilite account
        $user = $this->accounts->find_userBySociliteUser( $s_user        ,
                                                          $update = true ,
                                                          $create = true );
        // we better have a user at this stage!
        
        // finally login our user
        $this->auth->login( $user, true );
        return $listener->userHasLoggedIn( $user );
    }
}
