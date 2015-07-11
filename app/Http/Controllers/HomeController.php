<?php namespace App\Http\Controllers;

use Session;
use App\Repositories\UserRepository;
    
class HomeController extends Controller {

    public $accts;
    
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
        $this->db = new UserRepository;
	}

    // .............................................................. renderHtml
    
    private function renderHtml( $view, $with )
    {
        $html  = false;
        
        try{
            $html = view( $view, $with )->render();
        }
        catch( \Exception $e ){
            $html = $e->getMessage();
        }
        
        return $html;
    }
    
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        \Debugbar::info( 'HomeController::index()' );

        $uid = Session::get( 'uid' );
        $msg = Session::get( 'msg' );

        $user = $this->db->getUserInfo( $uid );
        
        return view('home')->with( 'user', $user )
                           ->with( 'msg' , $msg  );
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function show( $uid )
    {
        \Debugbar::info( 'HomeController::show(' . $uid . ')' );
        
        $user = $this->db->getUserInfo( $uid );
        $msg  = ($user === false) ? 'User not found!' : false;
        
        return view('home')->with( 'user', $user )
                           ->with( 'msg' , $msg  );
    }
    
    // ............................................................. getUserInfo
    
    public function getUserInfo( $uid )
    {
        \Debugbar::info( 'HomeController::getUserInfo(' . $uid . ')' );
        $user  = $this->db->getUserInfo( $uid );
        
        $with  = ['user' => $user ];

        return $this->renderHtml( 'user.info', $with);
    }
    
    // ................................................ getUserAccountByProvider
    
    public function getUserAccountByProvider( $provider )
    {
        \Debugbar::info( 'HomeController::getUserAccountByProvider(' . $provider . ')' );

        $uid  = Session::get( 'uid' );
        $accts = $this->db->getUserAccounts( $uid, $provider );
        
        $with  = [ 'accounts' => $accts ];

        return $this->renderHtml( 'user.accounts', $with);
    }
    
    // ......................................................... getUserAccounts
    
    public function getUserAccounts()
    {
        \Debugbar::info( 'HomeController::getUserAccounts()' );
        return $this->getUserAccountByProvider( false );
    }
    
    // ...................................................... getFactsByProvider
    public function fact_cmp_name( $a, $b )
    {
        $fct_name_a = is_array( $a )? $a[ 'fct_name' ] : $a->fct_name;
        $fct_name_b = is_array( $b )? $b[ 'fct_name' ] : $b->fct_name;
        
        return strcmp( $fct_name_a, $fct_name_b );
    }
    
    public function getUserFactsByProvider( $provider )
    {
        \Debugbar::info( 'HomeController::getUserFactsByProvider(' . $provider . ')' );

        $uid  = Session::get( 'uid' );
        $facts = $this->db->getUserFacts( $uid, $provider );
        
        // sort facts by 'fct_name'
        $cmp_fn = array( $this, 'fact_cmp_name'  );
        
        if ( is_callable($cmp_fn) ){
            usort( $facts, $cmp_fn );
        }
        
        // TODO: format further for display.. 
        
        $with  = [ 'facts' => $facts ];
        
        return $this->renderHtml( 'user.facts', $with);
    }
    
    // ............................................................ getUserFacts
    
    public function getUserFacts()
    {
        \Debugbar::info( 'HomeController::getUserFacts()' );
        return $this->getUserFactsByProvider( false );
    }
    
    // ............................................. generateUserFactsByProvider
 
    public function generateUserFactsByProvider( $provider )
    {
        \Debugbar::info( 'HomeController::generateUserFactsByProvider(' . $provider . ')' );

        $uid = Session::get( 'uid' );
        $res = $this->db->generateUserFacts( $uid, $provider );
        
        return json_encode($res);
    }
    
    // ....................................................... generateUserFacts

    public function generateUserFacts()
    {
        \Debugbar::info( 'HomeController::generateFactsByProvider()' );

        return $this->generateFactsByProvider( false );
    }
}
