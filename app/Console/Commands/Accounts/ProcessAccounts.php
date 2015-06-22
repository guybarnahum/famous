<?php namespace App\Console\Commands\Accounts;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Account;
use App\Models\User;

use App\Components\DateTimeUtils;
    
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
     * An abort flag on critical errors
     *
     * @var bool
     */
    protected $abort_req = false;
    
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
        
        if (!is_array($accts)){
            $this->info( count($accts) . ' accounts found' );
        }
        else{
            $this->warning( 'not accounts found!');
            return;
        }
        
        foreach( $accts as $act ){
            
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
            }
        }
	}

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
    //
    public function extend_token( $fb, $act )
    {
        $ok = $this->validate_account( $act );
        if (!$ok) return false;
        
        $info = $this->token_info( $fb, $act );
        $delta = time() - $info[ 'expires_at' ];
        
        $this->info( 'token info :' . DateTimeUtils::nice_time( $delta ) );
        
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
                
            $this->info( 'token set to expire at '    . $act[ 'expired_at' ] .
            ' ('  . DateTimeUtils::nice_time( $delta ) . ')'
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
    public function fb_graph_api( $fb, $endpoint, $fields = false)
    {
        if (!empty($fields)){
            $endpoint .= '?fields=' . $fields;
        }
        
        $this->error( '>>' . $endpoint );
        
        try {
            $token = $fb->getDefaultAccessToken();
            $res = $fb->get( $endpoint, $token );
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
        }
        
        return $res;
    }
    
    // .................................................................. revoke
    
    public function revoke($fb,$act)
    {
        // Activate DELETE /{user-id}/permissions
    }
    
    // ........................................................ process_facebook
    
    public function process_facebook( $act )
    {
        $ok = $this->validate_account( $act, 'facebook' );
        if ( !$ok ) return false;
        $provider = $act->provider;
        
        $fb = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $token = $act->access_token;
        
        // use the token we have and attempt to extend it
        $fb->setDefaultAccessToken( $token );
        $this->extend_token($fb, $act);

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

        $fact_owner = $provider . $u;
        
        $res = array( 'provider' => $provider, 'uid' => $act->provider_uid );
        
        $res[ 'user'   ] = $this->fb_graph_api( $fb, $u );
        $res[ 'bio'    ] = $this->fb_graph_api( $fb, $u , 'bio'  );
        $res[ 'friends'] = $this->fb_graph_api( $fb, $u . '/friends');
        $res[ 'family' ] = $this->fb_graph_api( $fb, $u . '/family' );
        $res[ 'likes'  ] = $this->fb_graph_api( $fb, $u . '/likes'  );
        $res[ 'albums' ] = $this->fb_graph_api( $fb, $u . '/albums' );
        $res[ 'photos' ] = $this->fb_graph_api( $fb, $u . '/photos' );
        $res[ 'cover'  ] = $this->fb_graph_api( $fb, $u . '/cover'  );
        
        // handle bio
        
        // handle likes
        
        // handle errors
        // TODO: collect all errors into an error array
        
        $this->info( 'result:' . print_r($res,true));
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
