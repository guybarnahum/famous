<?php namespace App;

use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\Repositories\UserRepository;
use Request;

class AuthorizeSocialiteUser{
    
    private $socialite;
    private $auth;
    private $accounts;
    
    public function __construct(Socialite         $socialite    ,
                                Guard             $auth     ,
                                UserRepository    $db )
    {
        $this->socialite = $socialite;
        $this->db        = $db;
        $this->auth      = $auth;
    }
    
    // ................................................... get_socialiteUserData
    
    private function get_socialiteUserData( $provider )
    {
        $s_user    = $this->socialite->with($provider)->user();
        $scope_req = $this->db->getProviderScopes( $provider );
        
        // enhace s_user with 'provider'
        if ( !empty( $s_user ) ){
            $s_user->provider     = $provider;
            $s_user->scope_request= $scope_req;
        }
        
        return $s_user;
    }
    
    // .................................................... autorizeWithProvider

    public function autorizeWithProvider($request, $listener, $provider)
    {
        // setup autorization scopes
        $scope_request =  $this->db->getProviderScopes( $provider );
        
        if (!empty($scope_request)){
            $scopes = explode(';',$scope_request);
            $this->socialite->with($provider)->scopes( $scopes );
        }
        
        // HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK!
        
        if ( $provider == 'facebook' ){
            $this->socialite->with($provider)->authType( 'reauthenticate' );
        }
        
        // HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK! HACK!
            
        // redirect for autorization from socialite provider
        return $this->socialite->with($provider)->redirect();
    }
    
    // .................................................. handleProviderCallback

    public function handleProviderCallback($request, $listener, $provider)
    {
        // TODO: FIXME: add error checks for failures!
        // Follow https://github.com/SammyK/LaravelFacebookSdk#ioc-container
        //
        // attempt to obtain socialite user data from provider
        $err    = '';
        $s_user = false;
        
        try{
            $s_user = $this->get_socialiteUserData( $provider );
            
            // validate $s_user
            if (!isset($s_user->token)){
                $err = 'Failed to autorize ' . $provider;
            }
        }
        catch( \Exception $e) {
           $err = $e->getMessage() . ' ';
        }
 
        // attempt to map to user from soclite user account
        // if not found create account / user from socilite account
        $user = false;
        
        $user = $this->db->find_userBySociliteUser( $s_user,
                                                    $update = true ,
                                                    $create = true );
        
        $uid = isset( $user->id )?  $user->id : false;
        
        // finally login our user
        if ( $uid ){
            $this->auth->login( $user, true );
        }
        else{
            $err .= ' Could not find ' . $provider . ' user';
            $err  = trim( $err );
        }
        
        if ( !empty($err) ){
            \Debugbar::info( $err );
        }
        
        return $listener->updateUser( $uid, $err );
    }
    
    // ...................................................... logoutFromProvider

    public function logoutFromProvider($request, $listener, $provider)
    {
        // $this->auth->logout();
        // return $listener->updateUser( false );
    }
}
