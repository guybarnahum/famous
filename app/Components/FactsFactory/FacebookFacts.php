<?php namespace App\Components\FactsFactory;
    
use App\Components\FactsFactory\AccountFactsContract;
use App\Components\FactsFactory\AccountFacts;
    
use App\Models\Fact;
    
// use App\Components\FactsFactory\FacebookFactsDataFormatter;
    
class FacebookFactsDataFormatter
{
    // ............................................................ the data map
    private static $MAP = [
    
    // ............................................................... education
    // obj name    : src_path                : tgt_path                  : [fmt]
    
    'facebook/user : education/*/school/id   : education/obj_provider_id',
    'facebook/user : education/*/school/name : education/obj_name'       ,
    'facebook/user : education/*/obj_id_type : education/obj_id_type     : !facebook.entity.id',
    'facebook/user : education/*/type        : education/fct_name        : fmt_school_type',
    
    // ................................................................ hometown
    // obj name     : src_path               : tgt_path                 : [fmt]
    'facebook/user  : hometown/id            : hometown/obj_provider_id',
    'facebook/user  : hometown/name          : hometown/obj_name'       ,
    'facebook/user  : hometown/obj_id_type   : hometown/obj_id_type     : !facebook.place.id',
    'facebook/user  : hometown/fct_name      : hometown/fct_name        : !place.hometown',
    
    // ................................................................ location
    // obj name     : src_path               : tgt_path                 : [fmt]
    'facebook/user  : location/id            : location/obj_provider_id',
    'facebook/user  : location/name          : location/obj_name'       ,
    'facebook/user  : location/obj_id_type   : location/obj_id_type     : !facebook.place.id',
    'facebook/user  : location/fct_name      : location/fct_name        : !place.at',
    
    // .................................................................... work
    // obj name     : src_path               : tgt_path                 : [fmt]
    'facebook/user  : work/*/employer/id     : work/obj_provider_id'    ,
    'facebook/user  : work/*/employer/name   : work/obj_name'           ,
    'facebook/user  : work/*/obj_id_type     : work/obj_id_type         : !facebook.entity.id',
    'facebook/user  : work/*/fct_name        : work/fct_name            : !work',
    
    // .................................................................. gender
    // obj name     : src_path               : tgt_path                 : [fmt]
    'facebook/user  : gender                 : gender/obj_name'         ,
    'facebook/user  : gender/obj_id_type     : gender/obj_id_type       : !facebook.gender',
    'facebook/user  : gender/fct_name        : gender/fct_name          : !gender',
    'facebook/user  : gender/obj_provider_id : gender/obj_provider_id   : !facebook.gender.enum',
    
    // ....................................................... significant other
    // obj name     : src_path               : tgt_path                            : [fmt]
    'facebook/user  : significant_other/name : significant_other/obj_name'         ,
    'facebook/user  : significant_other/id   : significant_other/obj_provider_id'  ,
    'facebook/user  : significant_other/obj_id_type : significant_other/obj_id_type: !facebook.uid',
    'facebook/user  : significant_other/fct_name : significant_other/fct_name      : !family.significant_other',
    
    // ........................................................... family_status
    // obj name     : src_path                       : tgt_path                  : [fmt]
    'facebook/user  : relationship_status            : family_status/obj_name'   ,
    'facebook/user  : relationship_status/obj_id_type: family_status/obj_id_type : !facebook.rel' ,
    'facebook/user  : relationship_status/fct_name   : family_status/fct_name    : !family.status',
    'facebook/user  : relationship_status/obj_provider_id: family_status/obj_provider_id : !facebook.rel.enum',
    
    // .................................................................. family
    // obj name     : src_path          : tgt_path               : [fmt]
    'facebook/family: */name            : family/obj_name        ',
    'facebook/family: */id              : family/obj_provider_id ',
    'facebook/family: */obj_id_type     : family/obj_id_type     : !facebook.uid',
    'facebook/family: */relationship    : family/fct_name        :  fmt_family_type',
    
    // ................................................................... likes
    /*
     [0] => Array
     (
     [name] => זהבה גלאון Zehava Galon
     [category] => Politician
     [id] => 115028251920872
     [created_time] => 2015-06-12T03:55:53+0000
     */
    // obj name     : src_path          : tgt_path               : [fmt]
    'facebook/likes : */name            : likes/obj_name        ',
    'facebook/likes : */id              : likes/obj_provider_id ',
    'facebook/likes : */obj_id_type     : likes/obj_id_type      :!facebook.entity.id',
    'facebook/likes : */category        : likes/fct_name         : fmt_likes_type',
    
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
    
    public function fmt_family_type( $val, $cname, $src_path, $tgt_path )
    {
        if (($cname    != 'facebook/family' )||
            ($tgt_path != 'family/fct_name' ) ) return null;
        
        switch( $val ){
            case 'wife'   : $val = 'family.wife' ; break;
            case 'cousin' : $val = 'family.cousin'; break;
        }
        
        return $val;
    }
    
    // .......................................................... fmt_likes_type
    //
    // identify various cannonical like types, all the rest go it likes.#name
    // format
    
    public function fmt_likes_type( $val, $cname, $src_path, $tgt_path )
    {
        if (!is_string($val)) return null;
        
        if (($cname    != 'facebook/likes' )||
            ($tgt_path != 'likes/fct_name' ) ) return null;

        $search = array( ' ', '/', '|', '#' );
        
        $val = str_replace( $search, '_', strtolower( $val ) );
        
        switch( $val ){
            
            case 'politician'             : $val = 'politics.persona'   ; break;
            case 'political_ideology'     : $val = 'politics.movement'  ; break;
                
            case 'organization'           : $val = 'organization'       ; break;
            case 'community'              : $val = 'organization.community' ; break;
            case 'non-profit_organization': $val = 'organization.non_profit'; break;
            case 'computers_technology'   : $val = 'organization.tech'  ; break;
            case 'health_medical_pharmaceuticals'
                                          : $val = 'organization.health'; break;
                
            case 'professional_services'  : $val = 'services'           ; break;

            // media
            case 'musician_band'   : $val = 'media.music.artist'; break;
            case 'artist'          : $val = 'media.art.artist'  ; break;
            case 'book'            : $val = 'media.book'        ; break;
            case 'magazine'        : $val = 'media.magazine'    ; break;
            case 'website'         : $val = 'media.web'         ; break;
            case 'movie'           : $val = 'media.movies' ; break;
            
            default                : $val = '#'.$val ; break;
        }
        
        $val = 'likes.' . $val;
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
        
        $this->output( 'graph_api::' . $endpoint );
        
        try {
            $token = $this->fb->getDefaultAccessToken();
            $res   = $this->fb->get( $endpoint, $token );
        }
        catch( \Exception $e )
        {
            $err = get_class($this).' - ('.$endpoint.')'.$e->getMessage();
            $res = [ 'err' => $err, 'endpoint' => $endpoint ];
        }
        
        if ( $res instanceof \Facebook\FacebookResponse ){
            $res = $res->getDecodedBody();
            
            // TODO: handle paging!!
            if ( is_array( $res ) && isset($res['paging']) ){
                $paging = $res[ 'paging' ];
                $this->output( 'graph_api::' . $endpoint, $paging );
            }

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
        $endpoints = [
            'facebook/user'             => '/me',
            'facebook/family'           => '/me/family',
            'facebook/likes'            => '/me/likes',
            'facebook/taggable_friends' => '/me/taggable_friends',
            'facebook/invitable_friends'=> '/me/invitable_friends',
        ];
        
        // first things first : set the token so we can talk to graph api
        $this->set_token()
             ->extend_token();
 
        $store = true;
        
        foreach( $endpoints as $datamap_cname => $endpoint ){
            try{
                $res   = $this->graph_api( $endpoint );
                $this->output( 'res:' . $endpoint, $res );
                $facts = $this->prcess_facts( $datamap_cname , $res, $store );
            }
            catch( \Exception $e ){
                $this->output( $e->getMessage() );
            }
        }
        
        return $this;
        
        /*
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
        // handle errors
        // TODO: collect all errors into an error array
        */
    }
}
    
