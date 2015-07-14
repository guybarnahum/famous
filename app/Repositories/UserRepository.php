<?php namespace App\Repositories;

use App\Models\Account;
use App\Models\User;
use App\Models\Dataset;
use App\Models\Fact;
    
use App\Components\FactsFactory\AccountFactsFactory;
use App\Components\FactsFactory\AccountFacts;
    
use Hash;
    
class UserRepository {
    
    // ....................................................... getProviderScopes

    public function getProviderScopes( $provider )
    {
        $ds = Dataset::where( 'provider', '=', $provider )->first();
        $scopes = ( $ds instanceof Dataset )? $ds->scope : false;
        return $scopes;
    }
    
    // ....................................................... generateUserFacts

    public function generateUserFacts( $uid, $provider = false )
    {
        \Debugbar::info( 'UserRepository::generateUserFacts(uid:' . $uid . ',' . $provider . ')' );

        $accounts = false;
        
        if ( $provider ){
            $match = [ 'uid' => $uid, 'provider' => $provider ];
            $accounts = Account::where( $match )->get();
        }
        else{
            $accounts = Account::where( 'uid', $uid )->get();
        }
        
        $res = array();
        
        foreach( $accounts as $ix => $act ){
        
            $msg = '';
            try{
                $facts = AccountFactsFactory::make( $act );
            
                if ( $facts instanceof AccountFacts ){
                     $facts->process( $act );
                     $msg = $act->toString() . '- process facts';
                }
                else{
                    $msg = $act->toString() . '- could not process facts';
                }
            }
            catch( \Exception $e ){
                $msg  = $e->getMessage();
            }

            $res[ $ix ] = $msg;
        }
        
        return $res;
    }
    
    // ............................................................ getUserInfo
    //
    // TODO: optimize db to keep accounts info in user object so we don't need
    // to access accounts to recreate it
    //
    // TODO: Create getUserInfo by match that returns a list of users
    //
    public function getUserInfo( $uid )
    {
        \Debugbar::info( 'UserRepository::getUserInfo(uid:' . $uid . ')' );
        $match = [ 'id' => $uid ];
        
        $user = User::where( $match )->first();
        $acts = $this->getUserAccounts( $uid );

        
        if ( $user ){
            $providers = [];

            if ( is_array( $acts ) ){
                foreach( $acts as $act ){
                    $providers[ $act->provider ] = 1;
                }
            }

            $user->providers = $providers;
        }

        return $user;
    }
    
    
    public function getUserList( $uid, $q )
    {
        \Debugbar::info( 'UserRepository::getUerList(uid:' . $uid . ',' . $q . ')' );

        $ul = User::all();
        
        // TODO: BUG: HACK! TODO: BUG: HACK! TODO: BUG: HACK! TODO: BUG: HACK!
        //
        // Package results in an array -- lame!
        // Very inefficient! We call Keep providers array inside user record!
        // We call getUserInfo (again) to get accounts for each user..
        //
        // TODO: BUG: HACK! TODO: BUG: HACK! TODO: BUG: HACK! TODO: BUG: HACK!
        $user_list = [];
        foreach( $ul as $user ){
            $user_list[] = $this->getUserInfo( $user->id );
        }
        
        \Debugbar::info( 'UserRepository::getUerList(' . print_r($user_list,true) . ')' );

        return $user_list;
    }
    
    // ............................................................ getUserFacts
    
    public function getUserFacts( $uid, $provider = false )
    {
        \Debugbar::info( 'UserRepository::getUserFacts(uid:' . $uid . ',' . $provider . ')' );
        
        $match = [ 'uid' => $uid ];
        $facts = false;
        
        if ( $provider ){
            
            // locate $act that is of uid and provider
            $match[ 'provider' ] = $provider;
            $act = Account::where( $match )->first();
            
            // look for facts with the act_id of $act
            $facts = Fact::where( 'act_id', $act->id )->get();
        }
        else{
            // look for facts of uid
            $facts = Fact::where( 'uid'   , $uid     )->get();
        }
        
        // repackage as an array
        if ( !empty( $facts )){
            $a = array();
            foreach( $facts as $fact ){
                $a[] = $fact;
            }
            return $a;
        }
        
        // $facts is empty!
        return null;
    }
    
    // ......................................................... getUserAccounts

    public function getUserAccounts( $uid, $provider = false )
    {
        \Debugbar::info( 'UserRepository::getUserAccounts(uid:' . $uid . ',' . $provider . ')' );

        $match = ['uid' => $uid ];
        if ( $provider ) $match[ 'provider' ] = $provider;
        
        $accts = Account::where( $match )->get();
        
        // repackage as an array
        if (!empty( $accts )){
            $a = array();
            foreach( $accts as $act ){
                $a[] = $act;
            }
            return $a;
        }
        
        return null;
    }
    
    
    // ................................................. find_userBySociliteUser

    public function find_userBySociliteUser( $userData      ,
                                             $update = true ,
                                             $create = false)
    {
        \Debugbar::info( 'UserRepository::find_userBySociliteUser(' . $userData->email . ')' );

        // attempt to locate the user for the socialite account
        // by email, need a better way to decide what is the current logged in user
        
        $user = User::where( 'emails', 'LIKE', '%' . $userData->email . '%' )->first();
        
        // no user for this socialite account?
        // if we are in create mode than make one!
        if ( !$user && $create ){

                // TODO: Add errors for when db is malformed and we throw
                // an exception in the App\Models\User
                $user = User::create( [
                         'email'     => $userData->email,
                         'emails'    => $userData->email,
                         'password'  => Hash::make(''), // no password (yet?)
                         'name'      => $userData->name,
                         'slogan'    => '',
                     
                         'pri_photo_large' => $userData->avatar,
                         'pri_photo_medium'=> $userData->avatar,
                         'pri_photo_small' => $userData->avatar
                         ]);
        }
        
        // locate account
        $account = Account::where( 'provider_uid', $userData->id)->first();
        
        // no account?
        // if we have a user and in create mode than make one!
        
        if( !$account && isset($user->id) && $create ) {
            
            // TODO: Add errors for when db is malformed and we throw
            // an exception in the App\Models\Account

            $account = Account::create([
                         'uid'          => $user->id,
                         'provider'     => $userData->provider,
                         'provider_uid' => $userData->id,
                         'access_token' => $userData->token,
                         'scope_request'=> $userData->scope_request,
                         'name'         => $userData->name,
                         'username'     => $userData->nickname,
                         'email'        => $userData->email,
                         'avatar'       => $userData->avatar,
                         'active' => 1,
                         ]);
        }
        
        if ( $account && $update ){
            $this->update_accountBySociliteUser( $account, $userData );
        }

        return $user;
    }
    
    // ............................................ update_accountBySociliteUser

    public function update_accountBySociliteUser( $account, $userData )
    {        
        $socialiteData = [
                         'avatar'       => $userData->avatar,
                         'email'        => $userData->email,
                         'access_token' => $userData->token,
                         'scope_request'=> $userData->scope_request,
                         'username'     => $userData->nickname,
                         'name'         => $userData->name,
                         ];
        
        $dbData        = [
                         'avatar'       => $account->avatar,
                         'email'        => $account->email,
                         'access_token' => $account->token,
                         'scope_request'=> $account->scope_request,
                         'username'     => $account->username,
                         'name'         => $account->name,
                         ];
        
        $diff   = array_diff($socialiteData, $dbData);
        $update = !empty( $diff );
        
        if ( $update ) {
            
            $account->avatar       = $userData->avatar;
            $account->email        = $userData->email;
            $account->access_token = $userData->token;
            $account->scope_request= $userData->scope_request;
            $account->name         = $userData->name;
            $account->username     = $userData->nickname;
            
            // TODO: Add errors for when db is malformed and we throw
            // an exception in the App\Models\Account
            
            $account->save();
        }
    }
}