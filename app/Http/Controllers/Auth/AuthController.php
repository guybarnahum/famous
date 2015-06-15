<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\AuthorizeSocialiteUser;
    
class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth      = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'logout']);
	}
    
    // .................................................... autorizeWithProvider
    // Issue
    public function autorizeWithProvider(AuthorizeSocialiteUser $au ,
                                         Request $req               ,
                                         $provider = null           )
    {
        return $au->autorizeWithProvider($req->all(), $this, $provider);
    }
    
    // .................................................. handleProviderCallback
    
    public function handleProviderCallback(AuthorizeSocialiteUser  $au,
                                           Request                 $req,
                                           $provider = null            )
    {
        return $au->handleProviderCallback($req->all(), $this, $provider);
    }
    
    // ...................................................... logoutFromProvider
    // What should we do here? Revoke the social login?
    // Disable the social account until it is re-enabled by user?
    // ??
    public function logoutFromProvider( AuthorizeSocialiteUser  $au,
                                        Request                 $req,
                                        $provider = null            )
    {
        \Debugbar::info('logoutFromProvider('. $provider . ')' );
        return \Redirect::back()->with('msg', $provider . ' logout');
        // return redirect('home');
        // return $au->logoutFromProvider($req->all(), $this, $provider);
    }
    
    // .................................................................. logout
    // Reset the session and forget the user
    
    public function logout()
    {
        \Auth::logout();
        \Session::flush();
        \Session::flash( 'msg', 'Goodbye!' );
        
        return redirect('home');
    }
    
    // .............................................................. updateUser
    
    public function updateUser( $user, $accounts, $msg=null)
    {
        \Session::put('user'    , $user     );
        \Session::put('accounts', $accounts );
        if (!empty($msg)) \Session::flash( 'msg', $msg );
        
        return redirect('home');
    }
}
