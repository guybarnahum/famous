<?php namespace App;
// AuthorizeSocialiteUser.php
use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\Repositories\AccountRepository;
use Request;

class AuthorizeSocialiteUser{
    
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
    
    private function get_socialiteUserData( $provider )
    {
        $s_user = $this->socialite->driver($provider)->user();
        
        if ( !empty( $s_user ) ){
            $s_user->provider = $provider;
        }
        
        return $s_user;
    }
    
    public function autorizeWithProvider($request, $listener, $provider)
    {
        // redirect for autorization from socialite provider
        return $this->socialite->driver($provider)->redirect();
    }
    
    public function handleProviderCallback($request, $listener, $provider)
    {
        // TODO: FIXME: add error checks for failures!
        
        // attempt to obtain socialite user data from provider
        $err = null;
        $ok  = true;
        
        try{
            $s_user = $this->get_socialiteUserData( $provider );
        }
        catch( Exception $e) {
            $err = $e->getMessage();
            $ok  = false;
        }
        
        // validate $s_user
        if ( $ok && !isset($s_user->token) ){
            $err = 'Failed to authorize ' . $provider;
            $ok  = false;
        }

        if (!$ok){
            return $listener->updateUser( null, $err );
        }
        
        // We have a valid $s_user from socialite!
        
        // attempt to map to user from soclite user account
        // if not found create account / user from socilite account
        $user = $this->accounts->find_userBySociliteUser( $s_user        ,
                                                          $update = true ,
                                                          $create = true );
        // we better have a user at this stage!
        
        // finally login our user
        
        // FIXME! HACK! FIXME! HACK! FIXME! HACK! FIXME! HACK! FIXME! HACK!
        //
        // Elequent is saving user into our db, including 'accounts' array.
        // Remove and restore 'accounts' to avoid SQL error..
        
        $accounts = $user->accounts;
        unset( $user->accounts );
        
        $this->auth->login( $user, true );
        
        $user->accounts = $accounts;
        
        // FIXME! HACK! FIXME! HACK! FIXME! HACK! FIXME! HACK! FIXME! HACK!

        return $listener->updateUser( $user, 'Welcome, ' . $user->name . '!' );
    }
    
    public function logoutFromProvider($request, $listener, $provider)
    {
        $this->auth->logout();
        return $listener->updateUser( null, 'logout from ' . $provider );
    }
}
