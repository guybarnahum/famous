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
            if ( $act->provider == 'facebook' ){
                $this->info( '--> processing ' . print_r($act,true));
                $res = $this->process_facebook( $act );
                $this->info( print_r( $res, true ) );
            }
        }
	}

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
    
    // ............................................................ extend_token
    //
    // TODO FIXME! Does not handle time zone correctly.. boo!
    //
    public function extend_token( $fb, $act )
    {
        $ok = $this->validate_account( $act );
        if (!$ok) return false;
        
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
                
            $this->info( 'token set to expire at '  . $act[ 'expired_at' ] .
                    ' (' . $this->nice_time($delta) . ')'
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
        
        if (!$token_expired){
            $this->info( 'token still good. ' . print_r($token,true));
        }
        
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
    public function fb_graph_api( $fb, $endpoint )
    {
        try {
            $token = $fb->getDefaultAccessToken();
            $res = $fb->get( $endpoint, $token );
            
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            $err = $e->getMessage();
            $res = (object)[ 'err'      => $err       ,
                             'endpoint' => $endpoint  ];
        }
        
        return $res;
    }
    
    // .................................................................. revoke
    
    public function revoke($fb,$act)
    {
        // Activate DELETE /{user-id}/permissions
    }
    
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
