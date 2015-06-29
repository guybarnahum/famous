<?php namespace App\Components\FactsFactory;
    
use App\Components\FactsFactory\AccountFactsContract;
use App\Components\FactsFactory\AccountFacts;
    
use App\Models\Fact;
    
// use App\Components\FactsFactory\FacebookFactsDataFormatter;
    
class FacebookFactsDataFormatter
{
    // ............................................................ the data map
    private static $MAP = [
    
    // obj name    : src_path                : tgt_path                  : [fmt]
    // education
    'facebook/user : education/*/school/id   : education/obj_provider_id',
    'facebook/user : education/*/school/name : education/obj_name'       ,
    'facebook/user : education/*/obj_id_type : education/obj_id_type     : !facebook.entity.id',
    'facebook/user : education/*/type        : education/fct_name        : fmt_school_type',
    
    // hometown
    'facebook/user : hometown/id             : hometown/obj_provider_id' ,
    'facebook/user : hometown/name           : hometown/obj_name'        ,
    'facebook/user : hometown/obj_id_type    : hometown/obj_id_type      : !facebook.place.id',
    'facebook/user : hometown/fct_name       : hometown/fct_name         : !place.hometown',
    
    // location
    'facebook/user : location/id             : location/obj_provider_id' ,
    'facebook/user : location/name           : location/obj_name'        ,
    'facebook/user : location/obj_id_type    : location/obj_id_type      : !facebook.place.id',
    'facebook/user : location/fct_name       : location/fct_name         : !place.at',
    
    // work
    'facebook/user : work/*/employer/id      : work/obj_provider_id'    ,
    'facebook/user : work/*/employer/name    : work/obj_name'           ,
    'facebook/user : work/*/obj_id_type      : work/obj_id_type         : !facebook.entity.id',
    'facebook/user : work/*/fct_name         : work/fct_name            : !work',
    
    // gender
    'facebook/user : gender                  : gender/obj_name'         ,
    'facebook/user : gender/obj_id_type      : gender/obj_id_type       : !facebook.gender',
    'facebook/user : gender/fct_name         : gender/fct_name          : !gender',
    'facebook/user : gender/obj_provider_id  : gender/obj_provider_id   : !facebook.gender.enum',
    
    // significant other
    'facebook/user : significant_other/name  : significant_other/obj_name'         ,
    'facebook/user : significant_other/id    : significant_other/obj_provider_id'  ,
    'facebook/user : significant_other/obj_id_type : significant_other/obj_id_type : !facebook.uid',
    'facebook/user : significant_other/fct_name : significant_other/fct_name       : !family.significant_other',
    
    // relationship_status
    'facebook/user : relationship_status            : family_status/obj_name'   ,
    'facebook/user : relationship_status/obj_id_type: family_status/obj_id_type : !facebook.rel' ,
    'facebook/user : relationship_status/fct_name   : family_status/fct_name    : !family.status',
    'facebook/user : relationship_status/obj_provider_id: family_status/obj_provider_id   : !facebook.rel.enum',
    
    ];
    
    public static function get_map()
    {
        return self::$MAP;
    }
    
    // .................................................... formatter call backs
    // From the table above..
    public function fmt_school_type( $val, $cname, $src_path, $tgt_path )
    {
        if (($cname    != 'facebook/user'      )||
            ($tgt_path != 'education/fct_name' ) ) return null;
        
        switch( $val ){
            case 'College' : $val = 'education.school.college'; break;
        }
        
        return $val;
    }
    
    public function fmt_relationship_type( $val, $cname, $src_path, $tgt_path )
    {
        if (($cname    != 'facebook/user'      )||
            ($tgt_path != 'family/fct_name' ) ) return null;
        
        switch( $val ){
            case 'Married' : $val = 'family.status.married'; break;
        }
        
        return $val;
    }

}

class FacebookFacts extends AccountFacts{
    
    private $fb;
    private $fmt;
    private $facts;
    
    function __construct( $act )
    {
        parent::__construct( $act );
        $this->fb  = \App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $this->fmt = new FacebookFactsDataFormatter;
        
        // setup out DataMapper with our FacebookFactsDataFormatter
        $this->mapper->setup( $this->fmt->get_map(),  $this->fmt );
        $this->facts = array();
    }
    
    // ................................................... set_token / get_token
    public function set_token()
    {
        $token = $this->act->access_token;
        $this->output('set_token'. $token);

        $this->fb->setDefaultAccessToken( $token );
        return $this;
    }
    
    public function get_token()
    {
        return $this->fb->getDefaultAccessToken();
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
            $err = get_class($this).' - ('.$endpoint.')'.$e->getMessage();
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
    public function extend_token( $exp = null )
    {
        $token = $this->get_token();
        
        // obtain expiration time for token
        if ( empty( $exp ) ){
         
            $info = $this->token_info( $token );
            
            if (isset( $info->expires_at )){
                $exp = $info->expires_at ;
            }
        }
    
        // strtotime may be === false
        $exp_time = empty( $exp )? (time() - 1): strtotime( $exp );
        
        // if we have exp_time check if we expired already (<= time())
        // if we could not find exp_time try to extend (true)
        $needs_extending = ($exp_time !== false)? $exp_time <= time() : true;
        $ok = true;
        
        $msg = 'extend_token:needs_extending='.($needs_extending? 'Yes':'No');
        $this->output( $msg );
       
        if ( $needs_extending ){
            try {
                $token = $this->fb->getOAuth2Client()
                                  ->getLongLivedAccessToken( $token );
                
                $this->output( 'extend_token:new token=' . print_r($token,true));
                $ok = !empty( $token );
                
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $ok = false;
            }
        }
        
        // we have a newer token!
        if ( $ok && $needs_extending ){
            
            // update account with better token
            if ( $token instanceof \Facebook\Authentication\AccessToken ){
                $this->act->access_token = $token->getValue();
                $this->act->expired_at   = $token->getExpiresAt()->format("Y-m-d H:i:s");
            }
            else{
                $msg  = 'App\Components\FactFactory\FacebookFacts';
                $msg .= '::extend_token - unknown token object';
                throw new Facebook\Exceptions\FacebookSDKException( $msg );
            }
            
            // save into account db
            $msg = 'extend_token: act updated - ' . $this->act->toString();
            $this->output( $msg );
            $this->act->save();
            
            // make sure we use updated token
            $this->set_token();
        }
        
        return $this;
    }
        
    // ................................................................. process
    
    public function process()
    {
        // first things first : set the token so we can talk to graph api
        $this->set_token()
             ->extend_token();
 
        $user = $this->graph_api( '/me' );
        $this->output( '/me', $user );
        $this->prcess_facts( 'facebook/user', $user, $store = true );

        return $this;
        
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
    
