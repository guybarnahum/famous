<?php namespace App\Components\FactFactory;
    
use App\Models\Fact;
    
class FacebookFacts{
    
    private $fb;
    public  $output;
    
    function __construct()
    {
        $this->fb = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $output   = array();
    }
    
    // ................................................. set_output / get_output
    public function set_output( $str, $obj = false )
    {
        if ( $obj ) $str .= ':' . print_r( $obj, true );
        $this->output[] = $str;
    }
    
    public function get_output()
    {
        return $this->output;
    }
    
    // ................................................ fb set_token / get_token
    public function set_token( $token )
    {
        $this->set_output('set_token'. $token);

        $this->fb->setDefaultAccessToken( $token );
        return $this;
    }
    
    public function get_token()
    {
        return $this->fb->getDefaultAccessToken();
    }

    // .................................................................... fact
    
    protected function fact( array $fields )
    {
        \App\Models\Fact::create(  $fields );
    }
    
    // ............................................................... graph_api
    
    public function graph_api( $endpoint, $fields = false)
    {
        if (!empty($fields)){
            $endpoint .= '?fields=' . $fields;
        }
        
        $this->output[] = 'graph_api::' . $endpoint;
        
        try {
            $token = $this->fb->getDefaultAccessToken();
            $res   = $this->fb->get( $endpoint, $token );
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
    
    // .............................................................. token_info
    
    public function token_info( $token )
    {
        $endpoint = '/debug_token?input_token=' . $token;
        return $this->graph_api( $endpoint );
    }
    
    // ............................................................ extend_token
    //
    // TODO FIXME! Does not handle time zone correctly.. boo!
    // TODO Revalidate expiration from facebook not our database.. boo!
    //
    public function extend_token( $token = null, $exp = null )
    {
        if ( empty($token)){
            $token = $this->get_token();
        }
        
        // obtain expiration time for token
        if ( empty( $exp ) ){
         
            $info = $this->token_info( $token );
            
            if (isset( $info['expires_at'] )){
                $exp = $info['expires_at'] ;
            }
        }
    
        $this->set_output('extend_token:exp='.$exp);
        
        $exp_time = empty( $exp )? (time() - 1): strtotime( $exp );
 
        $this->set_output( 'extend_token:(exp_time:' . $exp_time .'vs now:' . time() );
        
        // if we have exp_time check if we expired already (<= time())
        // if we could not find exp_time try to extend (true)
        $needs_extending = ($exp_time !== false)? $exp_time <= time() : true;
        $ok = true;
        
        $this->set_output( 'extend_token:needs_extending=' . ($needs_extending? 'Yes':'No'));
       
        if ( $needs_extending ){
            try {
                $token = $this->fb->getOAuth2Client()
                                  ->getLongLivedAccessToken( $token );
                
                $this->set_output( 'extend_token:new token=' . print_r($token,true) );
                $ok = !empty( $token );
                
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $ok = false;
            }
        }
        
        // we have a newer token!
        if ( $ok && $needs_extending ){
            
            // make sure we use updated token
            $this->set_token( $token );
            
            // update account with better token
            if ( $token instanceof \Facebook\Authentication\AccessToken ){
                $act->access_token = $token->getValue();
                $act->expired_at   = $token->getExpiresAt()->format("Y-m-d H:i:s");
            }
             else{
                $msg =  "unknown token object";
                throw new Facebook\Exceptions\FacebookSDKException( $msg );
            }
            
            $this->set_output( 'extend_token:act:save=' . $act->toString() );
            $act->save();
        }
        
        return $this;
    }
    
    public function process_birthday ( $act, $user )
    {
        return $this;
    }
    
    public function prepare_fact( $act, $val_type = 'bool', $value = 'true' )
    {
        $fact_fields = [
            'uid'         =>  $act->uid,
            // who claims the fact
            'act_id'      =>  $act->id,
        
            // responses to fact question
            'val_type'    =>  $val_type,
            'value'       =>  $value,
            
            // accuracy / confidence
            'error'       => '',
            'score'       => 0,
            'confidence'  => 0,
        ];
        
        return $fact_fields;
    }
    
    public function process_education( $act, $user )
    {
        $ok = isset( $user[ 'education' ] );
        
        if ($ok){
            
            foreach( $user[ 'education' ] as $entry ){
                
                $school_id   = $entry[ 'school' ][ 'id'   ];
                $school_name = $entry[ 'school' ][ 'name' ];
                
                switch( $entry[ 'type' ] ){
                    case 'College' : $fct_name = '.college'; break;
                }
                
                $fct_name = 'education' . $fct_name;
                
                $fields = [
                        'uid'         =>  $act->uid,
                        'obj_provider_id' => $school_id,
                        'obj_id_type' => 'facebook:education:id',
                        'obj_name'    =>  $school_name,
            
                        // who claims the fact
                        'act_id'      =>  $act->id,
            
                        // fact type
                        'fct_name'    =>  $fct_name,
            
                        // responses to fact question
                        'val_type'    =>  'bool',
                        'value'       =>  'true',
            
                        // accuracy / confidence
                        'error'       => '',
                        'score'       => 0,
                        'confidence'  => 0,
                ];
                
                // avoid duplicates..
                Fact::firstOrCreate( $fields );
            }
            
            $this->set_output( 'process_education:>>' . $fct_name . ',' .
                                                        $school_name    );
        }
        
        return $this;
    }
    
    // ................................................................. process
    
    public function process( $act )
    {
        // first things first : set the token so we can talk to graph api
        $this->set_token( $act->access_token )
             ->extend_token();
 
        $user = $this->graph_api( '/me' );
        
        $this->process_education( $act, $user )
             ->process_birthday ( $act, $user );
        
        
        $this->set_output('/me', $user );
        
        return $this->output;
        
        /*
        
         $u    = '/' . $act->provider_uid;
       // app status
        $ver        = $fb->getDefaultGraphVersion();;
        $this->res[ 'Graph API' ] = $ver;
        
        $perms = $this->graph_api( $fb, $u . '/permissions');
        
        if ( is_array($perms)){
            foreach( $perms as $perm ){
          //      $this->info( 'Permision `' . $perm[ 'permission'] .
          //                  '` was '      . $perm[ 'status'    ] );
            }
        }
        else{
       //     $this->error( 'Failed to obtain $perms' );
        //    $this->error( print_r($perms,true) );
        }
        */
        /*
         $res->rerequest_url = $fb->getReRequestUrl(['email']);
         
         $this->facebook_app_status($fb,$act);
         $res->graph_api = array();
         */
        
        $fact_owner = $provider . $u;
        
        $this->res[ 'provider' ] = $provider;
        $this->res[ 'uid'      ] = $act->provider_uid;
        $this->res[ 'user'   ] = $this->graph_api( $fb, $u );
        $this->res[ 'bio'    ] = $this->graph_api( $fb, $u , 'bio'  );
        $this->res[ 'friends'] = $this->graph_api( $fb, $u . '/friends');
        $this->res[ 'family' ] = $this->graph_api( $fb, $u . '/family' );
        $this->res[ 'likes'  ] = $this->graph_api( $fb, $u . '/likes'  );
        $this->res[ 'albums' ] = $this->graph_api( $fb, $u . '/albums' );
        $this->res[ 'photos' ] = $this->graph_api( $fb, $u . '/photos' );
        $this->res[ 'cover'  ] = $this->graph_api( $fb, $u . '/cover'  );
        
        // handle bio
        
        // handle likes
        
        // handle errors
        // TODO: collect all errors into an error array
        
        return $this->res;
    }
}
