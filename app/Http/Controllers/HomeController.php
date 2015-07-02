<?php namespace App\Http\Controllers;

use Session;
use App\Repositories\AccountRepository;
    
class HomeController extends Controller {

    public $accounts_repo;
    
	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $user     = Session::get( 'user'     );
        $accounts = Session::get( 'accounts' );
        $msg      = Session::get( 'msg'      );
        
		return view('home')->with( 'user'    , $user     )
                           ->with( 'accounts', $accounts )
                           ->with( 'msg'     , $msg      );
	}

    
    public function accountByProvider( $provider )
    {
        return $this->accounts( $provider );
    }
    
    public function accountsAll()
    {
}
