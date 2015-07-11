<?php namespace App\Http\Controllers;

use Session;
use App\Repositories\AccountRepository;
    
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
        $this->accts = new AccountRepository;
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
        $user     = Session::get( 'user'     );
        $accounts = Session::get( 'accounts' );
 
        $providers = [];
        
        foreach( $accounts as $account ){
            $providers[ $account->provider ] = 1;
        }
        $user->providers = $providers;
        
        $msg      = Session::get( 'msg'      );
        
        return view('home')->with( 'user', $user     )
                           ->with( 'msg'     , $msg      );
    }

    // ................................................ getUserAccountByProvider
    
    public function getUserAccountByProvider( $provider )
    {
        \Debugbar::info( 'getUserAccountByProvider(' . $provider . ')' );

        $user  = Session::get( 'user' );
        $accts = $this->accts->getUserAccounts( $user->id, $provider );
        
        $with  = ['user' => $user, 'accounts' => $accts ];

        return $this->renderHtml( 'user.accounts', $with);
    }
    
    // ......................................................... getUserAccounts
    
    public function getUserAccounts()
    {
        \Debugbar::info( 'getUserAccounts()' );
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
        \Debugbar::info( 'getUserFactsByProvider(' . $provider . ')' );

        $user  = Session::get( 'user' );
        $facts = $this->accts->getUserFacts( $user->id, $provider );
        
        // sort facts by 'fct_name'
        $cmp_fn = array( $this, 'fact_cmp_name'  );
        
        if ( is_callable($cmp_fn) ){
            usort( $facts, $cmp_fn );
        }
        
        // TODO: format further for display.. 
        
        $with  = ['user' => $user, 'facts' => $facts ];
        
        return $this->renderHtml( 'user.facts', $with);
    }
    
    // ............................................................ getUserFacts
    
    public function getUserFacts()
    {
        \Debugbar::info( 'getUserFacts()' );
        return $this->getUserFactsByProvider( false );
    }
    
    // ............................................. generateUserFactsByProvider
 
    public function generateUserFactsByProvider( $provider )
    {
        \Debugbar::info( 'generateUserFactsByProvider(' . $provider . ')' );

        $user  = Session::get( 'user' );
        $res = $this->accts->generateUserFacts( $user->id, $provider );
        
        return json_encode($res);
    }
    
    // ....................................................... generateUserFacts

    public function generateUserFacts()
    {
        \Debugbar::info( 'generateFactsByProvider()' );

        return $this->generateFactsByProvider( false );
    }
}
