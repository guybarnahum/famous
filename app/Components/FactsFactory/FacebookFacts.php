<?php namespace App\Components\FactsFactory;
    
use App\Components\FactsFactory\AccountFactsContract;
use App\Components\FactsFactory\AccountFacts;
    
use App\Models\Fact;
use App\Components\StringUtils;
    
    
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
    'facebook/family: */fct_name        : family/fct_name        :  fmt_family_type',
    
    // ........................................................ taggable_friends
    // obj name     : src_path          : tgt_path               : [fmt]
    'facebook/friend: */name            : friend/obj_name        ',
    'facebook/friend: */id              : friend/obj_provider_id ',
    'facebook/friend: */obj_id_type     : friend/obj_id_type     : !facebook.tag_uid',
    'facebook/friend: */fct_name        : friend/fct_name        : !friend.sns.facebook',
    
    // ................................................................... likes
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
    // TODO: use a database table for this conversion!
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
    
    private static $MAX_DATA = 128;
    
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
        $this->output('set_token:' . $token);

        $this->fb->setDefaultAccessToken( $token );
        return $this;
    }
    
    public function get_token()
    {
        return $this->fb->getDefaultAccessToken();
    }
    
    // ............................................................... graph_api

    public function graph_api( $endpoint, $limit = false, array $params = [] )
    {
        // there is always a limit
        if (!$limit ) $limit = self::$MAX_DATA;
        
        $data = array();
        $token = $this->fb->getDefaultAccessToken();

        do{ // first time + pagination ..
            
            // we post with method get so we can use param
            // to better shape our fb request
            $params[ 'method' ] = 'GET';

            $q = '';
            foreach( $params as $key => $val ) $q .= '&' . $key . '=' . $val;
            $q = substr($q,1);
            
            $this->output( 'graph_api::' . $endpoint . '?' . $q );
            
            try {
                $res   = $this->fb->post( $endpoint, $params, $token );
            }
            catch( \Exception $e ){
                
                $err = get_class($this) .' (' . $endpoint . ')' . $e->getMessage();
                
                $res = [ 'err'      => $err     ,
                         'endpoint' => $endpoint,
                         'params'   => $params
                    ];
                
                $this->output( $err );
            }
            
            
            
            $next = false;
            
            if ( $res instanceof \Facebook\FacebookResponse ){
                
                 $res = $res->getDecodedBody();
                
                 // get $next from paging section
                 if ( is_array( $res ) && isset( $res['paging']) ){
                     
                     $this->output( 'paging', $res['paging'] );
                     
                     $next = isset( $res['paging']['next'] )?
                                    $res['paging']['next']  : false;
                    
                     $this->output( 'next: ' . $next );
                     
                     $params = StringUtils::getUrlParams( $next );
                }

                if ( is_array( $res ) && isset( $res['data']) ){
                    $res = $res[ 'data' ];
                }
            }
            
            // add  $res page to $data
            foreach( $res as $key => $value ){
                
                // be careful with existing $key(s)!
                if ( is_int( $key ) ){
                    // numeric keys should not be preserved..
                    $data[      ] = $value;
                }
                else
                if ( !isset( $data[ $key ] )){
                    $data[ $key ] = $value;
                }
                else{
                    if (is_array( $data[ $key ] )){
                        $data[ $key ][] = $value;
                    }
                    else{
                        $tmp = $data[ $key ];
                        $data[ $key ] = array();
                        $data[ $key ][] = $tmp;
                        $data[ $key ][] = $value;
                    }
                }
            }
            
            unset( $res );
            
            if ( count($data) > $limit ){
                break;
            }
            
        }while( $next ); // pagination
        
        return $data;
    }
    
    // .............................................................. token_info
    
    public function token_info( $token )
    {
        $endpoint = '/debug_token';
        $params = [ 'input_token' => $token ];
        return $this->graph_api( $endpoint, $limit = false, $params );
    }
    
    // ............................................................... subscribe
    
    public function subscribe( $obj, $fields, $callback_url )
    {
        $app_id         = env('FACEBOOK_CLIENT_ID');
        $app_secret     = env('FACEBOOK_CLIENT_SECRET');
        $verify_token   = md5( $app_id );
        $token          = $app_id . '|' . $app_secret; // $this->fb->getDefaultAccessToken();
        $endpoint       = '/' . $app_id . '/subscriptions';
        
        $params         = [ 'object'       => $obj            ,
                            'callback_url' => $callback_url   ,
                            'fields'       => $fields         ,
                            'access_token' => $token          ,
                            'verify_token' => $verify_token
                        ];

        try {
            $res   = $this->fb->post( $endpoint, $params, $token );
        }
        catch( \Exception $e ){
            $err = get_class($this).' ('. $endpoint .')'. $e->getMessage();
            
            $res = [ 'err' => $err, 'endpoint' => $endpoint ];
            if ($params){
                $res[ 'params' ] = $params;
            }
        }
        
        return $res;
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
                
                $this->output( 'extend_token:new token=' , $token );
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
                $msg .= print_r( $token, true );
                
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
    
    public function prepare_post_fact( $post )
    {
        $fact = $this->prepare_one_fact();
        
        $created = isset( $post[ 'created_time' ] )? $post[ 'created_time' ] : false;
        $updated = isset( $post[ 'created_time' ] )? $post[ 'created_time' ] : false;
        
        if ( $created ) $fact[ 'created_time' ] = $created;
        if ( $updated ) $fact[ 'updated_time' ] = $updated;
        
        return $fact;
    }

    public function process_post_app  ( $post, $app  )
    {
        $facts = [];
        $fact  = $this->prepare_post_fact( $post );
        
        $app_id   =isset( $app['id'       ] )? $app['id'       ] : false;
        $app_name =isset( $app['name'     ] )? $app['name'     ] : false;
        $app_ns   =isset( $app['namespace'] )? $app['namespace'] : false;
        if ($app_ns ) $app_name = $app_ns. '.' . $app_name;
        
        if ($app_id ){
            
            $fact[ 'fct_name'        ] = 'app';
            $fact[ 'obj_provider_id' ] = $app_id;
            $fact[ 'obj_name'        ] = $app_name ;
            $fact[ 'obj_id_type'     ] = 'facebook.application.id';
            
            $facts[] = $fact;
        }
        
        return $facts;
    }
    
    public function process_post_place  ( $post, $place   )
    {
        $facts = [];
        $fact  = $this->prepare_post_fact( $post );
        
        $place_id   = isset( $place[ 'id' ] )? $place[ 'id' ] : false;
        $place_name = isset( $place['name'] )? $place['name'] : false;
        $location   = isset( $place['location'] )? $place['location'] : false;
        
        if ( $place_id ){
            
            $fact[ 'fct_name'        ] = 'place.visited';
            $fact[ 'obj_provider_id' ] = $place_id;
            $fact[ 'obj_name'        ] = $place_name;
            $fact[ 'obj_id_type'     ] = 'facebook.place.id';
        
            $facts[] = $fact;
        }
        
        if ($location){
            $fact[ 'fct_name'        ] = 'place.location';
            $fact[ 'obj_provider_id' ] = $place_id;
            $fact[ 'obj_name'        ] = json_encode($location);
            $fact[ 'obj_id_type'     ] = 'facebook.place.id';
            
            $facts[] = $fact;
        }
        
        return $facts;
    }
    
    public function process_post_comment( $post, $comment )
    {
        $facts = [];
        $fact  = $this->prepare_post_fact( $post );
        
        $created   = isset( $comment[ 'created_time' ] )? $comment[ 'created_time' ] : false;
        $src_id    = isset( $comment[ 'from' ][ 'id' ] )? $comment[ 'from' ][ 'id' ] : false;
        $src_name  = isset( $comment[ 'from' ]['name'] )? $comment[ 'from' ]['name'] : false;
        $likes_num = isset( $comment[ 'like_count'   ] )? $comment[ 'like_count'   ] : false;
        $user_like = isset( $comment[ 'user_like'    ] )? $comment[ 'user_like'    ] : false;
        
        if ( $created ) $fact[ 'fcreated_time' ] = $created;
        
        $fact[ 'fct_name'        ] = 'feed.comment'  ;
        $fact[ 'obj_provider_id' ] = $src_id;
        $fact[ 'obj_name'        ] = $src_name;
        $fact[ 'obj_id_type'     ] = 'facebook.uid';

        $facts[] = $fact;
        
        // src is not the account owner -- make a note of her existance
        if ( $src_id && ( $src_id != $this->act->uid )){
            
            $fact[ 'fct_name'        ] = 'friend.sns.facebook'  ;
            $fact[ 'obj_provider_id' ] = $src_id;
            $fact[ 'obj_name'        ] = $src_name;
            $fact[ 'obj_id_type'     ] = 'facebook.uid';
            
            $facts[] = $fact;
        }

        // we liked the comment made by $src
        if ( !empty($user_like) ){
            
            $fact[ 'fct_name'        ] = 'feed.liked' ;
            $fact[ 'obj_provider_id' ] = $src_id;
            $fact[ 'obj_name'        ] = $src_name;
            $fact[ 'obj_id_type'     ] = 'facebook.uid';
            
            $facts[] = $fact;
        }
        
        // $src comment was liked so many times
        if ( $likes_num ){
            
            $fact[ 'fct_name'        ] = 'feed.likes' ;
            $fact[ 'obj_provider_id' ] = $src_id;
            $fact[ 'obj_name'        ] = $src_name;
            $fact[ 'obj_id_type'     ] = 'facebook.likes';
            
            $facts[] = $fact;
        }
        
        return $facts;
    }
    
    public function process_any_post( $post )
    {
        $all_facts = [];
        $fact  = $this->prepare_post_fact( $post );
        
        $src_id  = isset( $post[ 'from' ][ 'id' ] )? $post[ 'from' ][ 'id' ] : false;
        $src_name= isset( $post[ 'from' ]['name'] )? $post[ 'from' ]['name'] : false;
        
        // src is not the account owner -- make a note of her existance
        if ( $src_id && ( $src_id != $this->act->uid )){
            
            $fact[ 'fct_name'        ] = 'friend.sns.facebook'  ;
            $fact[ 'obj_provider_id' ] = $src_id;
            $fact[ 'obj_name'        ] = $src_name;
            $fact[ 'obj_id_type'     ] = 'facebook.uid';
            
            $all_facts[] = $fact;
        }
        
        // do we have comments?
        $comments = isset( $post[ 'commets' ] )? $post[ 'commets' ]:false;
        if ($comments){
            // TODO: FIXME: handle paging!
            foreach( $commets['data'] as $commet ){
                $facts = $this->process_post_comment( $post, $commet );
                $all_facts = array_merge( $all_facts, $facts );
            }
        }
        
        $place = isset( $post[ 'place' ] )? $post[ 'place' ] : false;
        
        if ($place){
            $facts = $this->process_post_place( $post, $place );
            $all_facts = array_merge( $all_facts, $facts );
        }
        
        $app = isset( $post[ 'application' ] )? $post[ 'application' ] : false;
        if ($app){
            $facts = $this->process_post_app( $post, $app );
            $all_facts = array_merge( $all_facts, $facts );
        }
        
        return $all_facts;
    }
    
    public function process_shared_story_post( $post )
    {
        $facts = [];
        
        $fact = $this->prepare_post_fact( $post );
        $src_id  = isset( $post[ 'from' ][ 'id' ] )? $post[ 'from' ][ 'id' ] : false;

        // TODO: how do we know the type of src_id?
        $fact[ 'src_id'          ] = $src_id;
        $fact[ 'fct_name'        ] = 'feed.shared'  ;
        $fact[ 'obj_provider_id' ] = $post[ 'link' ];
        $fact[ 'obj_name'        ] = $post[ 'name' ];
        $fact[ 'obj_id_type'     ] = 'url';
        
        $facts[] = $fact;
        
        return $facts;
    }
    
    public function process_mobile_update_post( $post )
    {
        $facts = [];
        return $facts;
    }

    public function process_added_photos_post( $post )
    {
        $facts = [];
        return $facts;
    }

    public function process_wall_post_post( $post )
    {
        $facts = [];
        return $facts;
    }
    
    // ........................................................ process_one_post

    public function process_post( $post )
    {
        $this->output( 'post:', $post );
        
        // process common post facts
        $common_facts = $this->process_any_post( $post );
        $facts = [];
        
        $st = isset( $post[ 'status_type' ] )? $post[ 'status_type' ] : false;

        switch( $st ){
            case 'shared_story' :
                $facts = $this->process_shared_story_post( $post ); break;
            case 'mobile_status_update' :
                $facts = $this->process_mobile_update_post( $post ); break;
            case 'added_photos' :
                $facts = $this->process_added_photos_post ( $post ); break;
            case 'wall_post'    :
                $facts = $this->process_wall_post_post    ( $post ); break;
                
            default : $this->output( 'process_post unknown status_type', $post);
                    break;
        }
        
        $facts = array_merge( $facts, $common_facts );
        
        return $facts;
    }
    
    // ............................................................ process_feed

    public function process_feed()
    {
        try{
            $posts     = $this->graph_api( '/me/feed' );
            $all_facts = [];
            
            if ( is_array( $posts ) ){
                foreach( $posts as $post ){
                    $facts = $this->process_post( $post );
                    $all_facts = array_merge( $all_facts, $facts );
                }
            }
            else{
                $this->output( 'process_feed: invalid posts ' , $posts );
            }
        }
        catch( \Exception $e ){
            $this->output( 'process_feed: ' . $e->getMessage() );
        }
        
        $this->output( 'facts', $all_facts );

        return $this;
    }
    
    
    // ....................................................... process_subscribe

    public function process_subscribe()
    {
        try{
            // TODO: Get this from Dataset table!
            $object       = 'user';
            $fields       = 'about,work';
            $callback_url = 'http://famous-dev.happen.ly/api/callback/facebook';
            
            $res   = $this->subscribe( $object, $fields, $callback_url );
            $this->output( 'process_subscribe:', $res );
            
        }
        catch( \Exception $e ){
            $this->output( 'process_subscribe: ' . $e->getMessage() );
        }
        
        return $this;
    }

    // ........................................................... process_token

    public function process_token()
    {
        try{
            $this->set_token()
                 ->extend_token();
        }
        catch(\Exception $e)
        {
            $this->output( 'process_token: ' . $e->getMessage() );
        }
        
        return $this;
    }

    // ................................................................. process
    
    public function process()
    {
        // first things first! we can't do any open graph without a valid token
        $this->process_token();
        
        $x_option = $this->get_option( 'x' );
        $x_option = !empty( $x_option );
        
        if ( $x_option ){
            return $this->process_feed( );

        }
        
        $endpoints = [
                'facebook/user'    => '/me',
                'facebook/family'  => '/me/family',
                'facebook/likes'   => '/me/likes',
                'facebook/friend'  => '/me/taggable_friends',
        ];
        
        // Subscribe?
        if ( $this->get_option( 's' ) ){
            return $this->process_subscribe();
        }
        
        // Process endpoints
                
        foreach( $endpoints as $datamap_cname => $endpoint ){
            
            try{
                $res   = $this->graph_api( $endpoint );
                $this->output( 'res:', $res );
                $facts = $this->prcess_facts( $datamap_cname , $res, $store = true );
               
            }
            catch( \Exception $e ){
                $this->output( $e->getMessage() );
            }
        }

        return $this;
    }
}
    
