<?php namespace App\Console\Commands\Accounts;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Account;
use App\Models\User;

class ProcessAccounts extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'accounts:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
    
<<<<<<< Updated upstream
=======
    /**
     * An abort flag on critical errors
     *
     * @var bool
     */
    protected $abort_req = false;
    
>>>>>>> Stashed changes
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
    public function handle()
	{
		// print all users
        $users = User::All();
        $accts = Account::All();
        
        foreach( $accts as $act ){
<<<<<<< Updated upstream
            if ( $act->provider == 'facebook' ){
                $this->info( '--> processing ' . print_r($act,true));
                $res = $this->process_facebook( $act );
                $this->info( print_r( $res, true ) );
=======
            
            $this->info( '--> start processing ' . $act->toString() );
        
            if ( $act->provider == 'facebook' ){
                $ok = $this->process_facebook( $act );
            }
        
            $this->info( '<-- end processing ' . $act->toString());
            
            // Sometimes we find a problem that is beyond and account problem
            // ..
            if ( $this->needsAbort() ){
                $this->error( 'Aborting! >> ' . $this->needsAbort() );
                break;
>>>>>>> Stashed changes
            }
        }
	}

<<<<<<< Updated upstream
=======
    /**
     * Set an abort request on critical errors
     *
     *
     * @return $this
     */
    public function abort_request( $set = true )
    {
        $this->abort_req = $set;
        return $this;
    }
    
    /**
     *
     * Does it need an abort?
     *
     * @return bool
     */
    public function needsAbort()
    {
        return $this->abort_req;
    }
    
>>>>>>> Stashed changes
    // ............................................................... nice_time
    public function nice_time( $seconds )
    {
        $periods = array("second", "minute", "hour", "day",
                         "week", "month", "year", "decade");
        
        $ratios  = array("60","60","24","7","4.35","12","10");
        
        $tense =    ( $seconds > 0 )? "ago":"from now";
        $value = abs( $seconds );
    
        for($j = 0; $value >= $ratios[$j] && $j < count($ratios)-1; $j++) {
            $value /= $ratios[$j];
        }
        
        $value = round($value);
        
        if($value != 1) {
            $periods[$j].= "s";
        }
        
        return "$value $periods[$j] $tense";
    }
    
    // ........................................................ validate_account
    public function validate_account( $act, $provider = null )
    {
        $ok = $act instanceof Account;
        $ok = $ok && (!empty( $act->access_token));

        if (  $ok && (!empty( $provider ) )){
              $ok = $act->provider == $provider;
        }

        return $ok;
    }
    
<<<<<<< Updated upstream
    // ............................................................ extend_token
    //
    // TODO FIXME! Does not handle time zone correctly.. boo!
=======
    // .............................................................. token_info
    public function token_info( $fb, $act )
    {
        $endpoint = '/debug_token?input_token=' . $act->access_token;
        return $this->fb_graph_api( $fb, $endpoint );
    }
    
    // ............................................................ extend_token
    //
    // TODO FIXME! Does not handle time zone correctly.. boo!
    // TODO Revalidate expiration from facebook not our database.. boo!
>>>>>>> Stashed changes
    //
    public function extend_token( $fb, $act )
    {
        $ok = $this->validate_account( $act );
        if (!$ok) return false;
        
<<<<<<< Updated upstream
=======
        $info = $this->token_info( $fb, $act );
        $delta = time() - $info[ 'expires_at' ];
        
        $this->info( 'token info :' . $this->nice_time($delta) );
        
>>>>>>> Stashed changes
        $token_expired  = true;
        $token          = $act->access_token;
        $oauth_client   = $fb->getOAuth2Client();
        
        $ok = isset( $act->expired_at );
    
        if ($ok){
            $expired_at_time = strtotime( $act[ 'expired_at' ] );
            $ok = ( $expired_at_time !== false );
        }
        
        if ($ok){
            $delta = time() - $expired_at_time;
            $token_expired = $delta > 0;
                
<<<<<<< Updated upstream
            $this->info( 'token set to expire at '  . $act[ 'expired_at' ] .
                    ' (' . $this->nice_time($delta) . ')'
=======
            $this->info( 'token set to expire at '    . $act[ 'expired_at' ] .
                    ' (' . $this->nice_time( $delta ) . ')'
>>>>>>> Stashed changes
                    );
        
            // try to extend token
            
            if ( $token_expired ){
            
                $this->info( 'attempt to extend token' );
            
                try {
                    $token = $oauth_client->getLongLivedAccessToken( $token );
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    $ok = false;
                    $this->error( $e->getMessage() );
                }
            }
        }
        
<<<<<<< Updated upstream
        if (!$token_expired){
            $this->info( 'token still good. ' . print_r($token,true));
        }
        
=======
>>>>>>> Stashed changes
        if ( $ok && $token_expired ){
            
            // make sure we use updated token
            $fb->setDefaultAccessToken( $token );
            
            // update account with better token

            if ( $token instanceof \Facebook\Authentication\AccessToken ){
                $act->access_token = $token->getValue();
                $act->expired_at   = $token->getExpiresAt()->format("Y-m-d H:i:s");
            }
            else
            if ( is_string( $token )){
                $act->access_token = $token;
            }
            else{
                // should not happen
                $this->error( "unknown token object");
            }
            
            $this->info( 'saving new token into account:' . print_r($token,true));
            $act->save();
        }

        if (!$ok) $token = false;
        return $token;
    }
    
    // ............................................................ fb_graph_api
    // '/me?fields=id,name,email'
<<<<<<< Updated upstream
    public function fb_graph_api( $fb, $endpoint )
    {
=======
    public function fb_graph_api( $fb, $endpoint, $fields = false)
    {
        if (!empty($fields)){
            $endpoint .= '?fields=' . $fields;
        }
        
        $this->error( '>>' . $endpoint );
        
>>>>>>> Stashed changes
        try {
            $token = $fb->getDefaultAccessToken();
            $res = $fb->get( $endpoint, $token );
            
<<<<<<< Updated upstream
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            $err = $e->getMessage();
            $res = (object)[ 'err'      => $err       ,
                             'endpoint' => $endpoint  ];
=======
        }
        catch( \Exception $e )
        {
            $err = $e->getMessage();
            $res = (object)[ 'err' => $err, 'endpoint' => $endpoint ];
        }
        
        if ( $res instanceof \Facebook\FacebookResponse ){
             $res = $res->getDecodedBody();
             if ( is_array( $res ) && isset($res['data']) ){
                 $res = $res[ 'data' ];
             }
>>>>>>> Stashed changes
        }
        
        return $res;
    }
    
    // .................................................................. revoke
    
    public function revoke($fb,$act)
    {
        // Activate DELETE /{user-id}/permissions
    }
    
<<<<<<< Updated upstream
    // .................................................. facebook_app_status
    // facebook app status for account
    
    public function facebook_app_status( $fb, $act )
    {
        $ok = $this->validate_account( $act );
        
        // GET graph.facebook.com/debug_token?
        // input_token={token-to-inspect}
        // &access_token={app-token-or-admin-token}
        
        $endpoint_req = '/debug_token?input_token=' . $act->access_token;
        $res = $this->fb_graph_api( $fb, $endpoint_req);
    
        // analyze app status
        //
        // TODO: how do we react to issues with our app status? maybe just
        // park it into permission issues database and have a separate job
        // handle those?
        //
        $this->info( print_r($res,true));
        
        return $ok;
    }
    
=======
>>>>>>> Stashed changes
    // ........................................................ process_facebook
    
    public function process_facebook( $act )
    {
        $ok = $this->validate_account( $act, 'facebook' );
        if ( !$ok ) return false;
        
        $fb = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $token = $act->access_token;
        
        // use the token we have and attempt to extend it
        $fb->setDefaultAccessToken( $token );
        $this->extend_token($fb, $act);

<<<<<<< Updated upstream
        $res = (object)[];
        
        // api version
        $res->api        = $fb->getDefaultGraphVersion();;
        $res->app_status = $this->facebook_app_status($fb,$act);
        
        /*
        $res->rerequest_url = $fb->getReRequestUrl(['email']);
        $fb_uid = $act->provider_uid;
        
        $this->facebook_app_status($fb,$act);
        $res->graph_api = array();
        $res->graph_api[ $fb_uid . '/permissions'   ] = $this->fb_graph_api( $fb, $fb_uid . '/permissions'   );
        $res->graph_api[ '/' . $fb_uid ] = $this->fb_graph_api( $fb, '/' . $fb_uid );
        $res->graph_api[ '/me/friends' ] = $this->fb_graph_api( $fb, '/me/friends' );
         */
=======
        $u = '/' . $act->provider_uid;

        // app status
        $ver        = $fb->getDefaultGraphVersion();;
        $this->info( 'Graph API/' . $ver );
        $perms = $this->fb_graph_api( $fb, $u . '/permissions');

        if ( is_array($perms)){
                foreach( $perms as $perm ){
                    $this->info( 'Permision `' . $perm[ 'permission'] .
                                 '` was '      . $perm[ 'status'    ] );
                }
        }
        else{
            $this->error( 'Failed to obtain $perms' );
            $this->error( print_r($perms,true) );
        }
        
        /*
        $res->rerequest_url = $fb->getReRequestUrl(['email']);
        
        $this->facebook_app_status($fb,$act);
        $res->graph_api = array();
         */

        $res = (object)[];
        $res->graph_api[ $u ] = $this->fb_graph_api( $fb, $u );
        $res->graph_api[ '/me/bio'    ] = $this->fb_graph_api( $fb, $u , 'bio'  );
        $res->graph_api[ '/me/friends'] = $this->fb_graph_api( $fb, $u . '/friends');
        $res->graph_api[ '/me/family' ] = $this->fb_graph_api( $fb, $u . '/family' );
        $res->graph_api[ '/me/likes'  ] = $this->fb_graph_api( $fb, $u . '/likes'  );
        $res->graph_api[ '/me/albums' ] = $this->fb_graph_api( $fb, $u . '/albums' );
        $res->graph_api[ '/me/photos' ] = $this->fb_graph_api( $fb, $u . '/photos' );
        $res->graph_api[ '/me/cover'  ] = $this->fb_graph_api( $fb, $u . '/cover'  );
        
        $this->info( 'result:' . print_r($res,true));
>>>>>>> Stashed changes
        return $res;
    }
    
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
//			['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
