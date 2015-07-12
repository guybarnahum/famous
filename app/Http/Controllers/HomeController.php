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
    public function show( $uid = false )
    {
        \Debugbar::info( 'HomeController::show(' . $uid . ')' );
        
        if (empty( $uid )) $uid = Session::get( 'uid' );
        $user = $this->db->getUserInfo( $uid );
        $msg  = ($user === false) ? 'User not found!' : false;
        
        return view('home')->with( 'user', $user )
                           ->with( 'msg' , $msg  );
    }
    
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function showActive()
    {
        return $this->show();
    }
    
    // ............................................................. getUserInfo
    
    public function getUserInfo( $uid = false)
    {
        \Debugbar::info( 'HomeController::getUserInfo(' . $uid . ')' );
        
        if (empty( $uid )) $uid = Session::get( 'uid' );
        $user  = $this->db->getUserInfo( $uid );
        $with  = ['user' => $user ];

        return $this->renderHtml( 'user.info', $with);
    }
    
    // ......................................................... getUserAccounts
    
    public function getUserAccounts( $uid = false, $provider = false)
    {
        \Debugbar::info( 'HomeController::getUserAccountByProvider(' . $uid . ',' . $provider . ')' );

        if (empty( $uid      )) $uid      = Session::get( 'uid' );
        if (empty( $provider )) $provider = false;
        
        $accts = $this->db->getUserAccounts( $uid, $provider );
        if (empty( $accts )) $accts = [];
        $with  = [ 'accounts' => $accts ];

        return $this->renderHtml( 'user.accounts', $with);
    }
    
    public function getUserAccountsByUid( $uid )
    {
        return $this->getUserAccounts( $uid );
    }
   
    public function getActiveUserAccounts()
    {
        return $this->getUserAccounts();
    }

    // ............................................................ getUserFacts
    
    private function fact_cmp_name( $a, $b )
    {
        $fct_name_a = is_array( $a )? $a[ 'fct_name' ] : $a->fct_name;
        $fct_name_b = is_array( $b )? $b[ 'fct_name' ] : $b->fct_name;
        
        return strcmp( $fct_name_a, $fct_name_b );
    }
    
    public function getUserFacts( $uid = false, $provider = false)
    {
        \Debugbar::info( 'HomeController::getUserFacts(' . $uid . ',' . $provider . ')' );

        if (empty( $uid      )) $uid      = Session::get( 'uid' );
        if (empty( $provider )) $provider = false;

        $facts = $this->db->getUserFacts( $uid, $provider );
        if (empty( $facts )) $facts = [];

        // sort facts by 'fct_name'
        $cmp_fn = array( $this, 'fact_cmp_name'  );
        
        if ( is_callable($cmp_fn) ){
            usort( $facts, $cmp_fn );
        }
        
        // TODO: format further for display.. 
        
        $with  = [ 'facts' => $facts ];
        
        return $this->renderHtml( 'user.facts', $with);
    }

    public function getUserFactsByUid( $uid  )
    {
        return $this->getUserFacts( $uid );
    }
    
    public function getActiveUserFacts()
    {
        return $this->getUserFacts();
    }
    
    // ....................................................... generateUserFacts
 
    public function generateUserFacts( $uid = false, $provider = false)
    {
        \Debugbar::info( 'HomeController::generateUserFactsByProvider(' . $uid . ',' . $provider . ')' );

        if (empty( $uid      )) $uid      = Session::get( 'uid' );
        if (empty( $provider )) $provider = false;
        
        $res = $this->db->generateUserFacts( $uid, $provider );
        
        return json_encode($res);
    }
    
    public function generateUserFactsByUid( $uid  )
    {
        return $this->generateUserFacts( $uid );
    }

    public function generateActiveUserFacts()
    {
        return $this->generateUserFacts();
    }
    
}
