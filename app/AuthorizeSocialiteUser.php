<?php namespace App;

use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\Repositories\AccountRepository;
use Request;

class AuthorizeSocialiteUser{
    
    private $socialite;
    private $auth;
    private $accounts;
    
    public function __construct(Socialite         $socialite    ,
                                Guard             $auth     ,
                                AccountRepository $accounts )
    {
        $this->socialite = $socialite;
        $this->accounts  = $accounts;
        $this->auth      = $auth;
    }
    
    private function get_socialiteUserData( $provider )
    {
        $s_user    = $this->socialite->with($provider)->user();
        $scope_req = $this->accounts->get_scopes( $provider );
        
        // enhace s_user with 'provider'
        if ( !empty( $s_user ) ){
            $s_user->provider     = $provider;
            $s_user->scope_request= $scope_req;
        }
        
        return $s_user;
    }
    
    public function autorizeWithProvider($request, $listener, $provider)
    {
        // setup autorization scopes
        $scope_request =  $this->accounts->get_scopes( $provider );
        
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
    
    public function handleProviderCallback($request, $listener, $provider)
    {
        // TODO: FIXME: add error checks for failures!
        // Follow https://github.com/SammyK/LaravelFacebookSdk#ioc-container
        //
        // attempt to obtain socialite user data from provider
        $err    = false;
        $s_user = false;
        
        //try{
            $s_user = $this->get_socialiteUserData( $provider );
            
            // validate $s_user
            if (!isset($s_user->token)){
                $err = 'Failed to autorize ' . $provider;
            }
        //}
        //catch( \Exception $e) {
        //   $err = $e->getMessage();
        // }
 
        // attempt to map to user from soclite user account
        // if not found create account / user from socilite account
        $res = (object)[];
        
        if ($s_user){
            $res = $this->accounts->find_userBySociliteUser( $s_user        ,
                                                             $update = true ,
                                                             $create = true );
        }
        
        $accounts = isset( $res->accounts )? $res->accounts : null;
        $user     = isset( $res->user     )? $res->user     : null;
        
        // finally login our user
        if ( $user instanceof App\Models\User ){
            $this->auth->login( $user, true );
        }
        
        if ($err){
            \Debugbar::info( $err );
        }
        
        return $listener->updateUser( $user, $accounts, $err );
    }
    
    public function logoutFromProvider($request, $listener, $provider)
    {
        // $this->auth->logout();
        // return $listener->updateUser( null, null );
    }
}
