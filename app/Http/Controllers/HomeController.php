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
        $input = \Input::all();
        \Debugbar::info( 'HomeController::index(' . print_r($input,true) . ')' );

        $uid = Session::get( 'uid' );
        $msg = Session::get( 'msg' );

        $user = $this->db->getUserInfo( $uid );
        
        $with = [ 'msg' => $msg, 'user' => $user ];

        $q = \Input::get( 'q' , false );
        
        if ($q){
            $user_list = $this->db->getUserList( $uid, $q );
            $with[ 'user_list' ] = $user_list ;
        }
        
        return view('home')->with( $with );
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
        $input = \Input::all();

        \Debugbar::info( 'HomeController::getUserInfo(' . $uid . ',' .
                                           print_r($input,true). ')' );
        
        if (empty( $uid )) $uid = Session::get( 'uid' );
        $user  = $this->db->getUserInfo( $uid );
        
        $with  = ['user' => $user ];
        if ( isset($input['mode'  ]) ) $with[ 'mode'   ] = $input[ 'mode'   ];
        if ( isset($input['action']) ) $with[ 'action' ] = $input[ 'action' ];
        
        return $this->renderHtml( 'user.info', $with );
    }
    
    public function getActiveUserInfo()
    {
        return $this->getUserInfo();
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
        if (is_array( $a )) $fct_name_a = $a['fct_name'] . '.' . $a['fct_type'];
        else                $fct_name_a = $a->fct_name   . '.' . $a->fct_type  ;
   
        if (is_array( $b )) $fct_name_b = $b['fct_name'] . '.' . $b['fct_type'];
        else                $fct_name_b = $b->fct_name   . '.' . $b->fct_type  ;
        
        return strcmp( $fct_name_a, $fct_name_b );
    }
    
    public function getUserFacts( $uid = false, $provider = false)
    {
        \Debugbar::info( 'HomeController::getUserFacts(' . $uid . ',' . $provider . ')' );

        if (empty( $uid      )) $uid      = Session::get( 'uid' );
        if (empty( $provider )) $provider = false;

        $facts = $this->db->getUserFacts( $uid, $provider );
        if (empty( $facts )) $facts = [];

        // sort facts by 'fct_name.fct_type'
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
    
    // ......................................................... getUserInsights
    
    private function insight_cmp_name( $a, $b )
    {
        if (is_array( $a )) $i_a = $a['sys'] . '.' . $a['name'];
        else                $i_a = $a->sys   . '.' . $a->name  ;
        
        if (is_array( $a )) $i_b = $b['sys'] . '.' . $b['name'];
        else                $i_b = $b->sys   . '.' . $b->name  ;
        
        return strcmp( $i_a, $i_b );
    }

    public function getUserInsights( $uid = false, $system = false)
    {
        \Debugbar::info( 'HomeController::getUserInsights(' . $uid . ',' . $system . ')' );
        
        if (empty( $uid    )) $uid    = Session::get( 'uid' );
        if (empty( $system )) $system = false;
        
        $insights = $this->db->getUserInsights( $uid, $system );
        if ( empty( $insights )) $insights = [];
        
        // sort $insights by 'sys.name'
        $cmp_fn = array( $this, 'insight_cmp_name'  );
        
        if ( is_callable($cmp_fn) ){
            usort( $insights, $cmp_fn );
        }
        
        // TODO: format further for display..
        
        $with  = [ 'insights' => $insights ];
        
        return $this->renderHtml( 'user.insights', $with );
    }

    
    public function getUserInsightsByUid( $uid )
    {
        return $this->getUserInsights( $uid );

    }
    
    
    public function getActiveUserInsights()
    {
        return $this->getUserInsights();
    }
}
