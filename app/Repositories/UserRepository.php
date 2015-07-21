<?php namespace App\Repositories;

use App\Models\Account;
use App\Models\User;
use App\Models\Dataset;
use App\Models\Fact;
    
use App\Components\FactsFactory\AccountFactsFactory;
use App\Components\FactsFactory\AccountFacts;
use App\Components\StringUtils;
    
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
    
    // ........................................................ getUserProviders
    
    public function getUserProviders( $uid )
    {
        $providers = '';
        
        $acts = $this->getUserAccounts( $uid );
            
        if ( is_array( $acts ) ){
 
            $p = [];
                    
            foreach( $acts as $act ){
                $p[] = $act->provider;
            }
                    
            $providers = implode(',',$p);
        }
        
        return $providers;
    }
    
    // ............................................................. getUserInfo
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
        
        return $user;
    }
    
    // ............................................................. getUserList
    
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
    
    public function getUserFacts( $uid, $provider = false, $where = [] )
    {
        // disable to eliminate debug message..
        if ( true ){
            $where_str = '';
            if ( !empty($where) ){
                
                foreach( $where as $field => $val ){
                    $where_str .= ',' . $field . '=' . $val;
                }
            }
            
            \Debugbar::info( 'UserRepository::getUserFacts(uid:' . $uid . ','  .
                                                                   $provider   .
                                                                    $where_str . ')' );
        }
        
        $match[ 'uid' ] = $uid;
        $facts = false;
        
        if ( $provider ){
            
            // locate $act that is of uid and provider
            $match[ 'provider' ] = $provider;
            $act = Account::where( $match )->first();
            
            if ( $act instanceof Account ){
                $where[ 'act_id' ]   = $act->id;
                // look for facts with the act_id of $act
                $facts = Fact::where( $where )->get();
            }
        }
        else{
            // look for facts of uid
            $where[ 'uid' ] = $uid;
            $facts = Fact::where( $where )->get();
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
    
    // ................................................................ findUser

    public function findUser( $data )
    {
        // email is the primary way to locate existing user
        $user = User::where( 'emails', 'LIKE', '%'.$data->email.'%' )->first();
        
        if (!$user){
            // try and match user signature, a combination of device
            $signature = StringUtils::getDeviceSignature( $data->name );
            $user = User::where( 'signatures', 'LIKE', '%'.$signature.'%' )->first();
        }
        
        return $user;
    }
    
    // ................................................................. getUser

    public function getUser( $data, $update = true, $create = false)
    {
        // sanity check data
        if (!$data){
            \Debugbar::info( 'UserRepository::getUser( false )' );
            return false;
        }
        
        if ( !isset($data->email) || !isset($data->name) ){
            \Debugbar::info( 'UserRepository::getUser( invalid data! )' );
            return false;
        }
        
        \Debugbar::info( 'UserRepository::getUser(' . $data->email . ',' .
                                                      $data->name  . ')' );

        // .....................................................................
        //
        // Attempt to locate the user for the socialite account
        // We do this by the following heuristics:
        //
        // First look for her email in existing user entry
        // Then we look for her name on the same device(!)
        // If not found we generate a new user
        //
        // TODO: handle the case where we find a user but discover another user
        // on the same device with same name!
        //
        // .....................................................................
                            
        $user      = $this->findUser( $data );
        $signature = StringUtils::getDeviceSignature( $data->name );
        
        // no user for this socialite account?
        // if we are in create mode than make one!
        if ( !$user && $create ){
            
                // TODO: Add errors for when db is malformed and we throw
                // an exception in the App\Models\User
                $user = User::create( [
                         'email'     => $data->email,
                         'emails'    => $data->email,
                         'signatures'=> $signature,
                         'password'  => Hash::make(''), // no password (yet?)
                         'name'      => $data->name,
                         'slogan'    => '',
                         'providers' => $data->provider,
                         'pri_photo_large' => $data->avatar,
                         'pri_photo_medium'=> $data->avatar,
                         'pri_photo_small' => $data->avatar
                         ]);
        }
        
        // locate account
        $account_update = $update;
        $account = Account::where( 'provider_uid', $data->id)->first();
        
        // no account?
        // if we have a user and in create mode than make one!
        
        if( !$account && isset($user->id) && $create ) {
            
            // TODO: Add errors for when db is malformed and we throw
            // an exception in the App\Models\Account

            $account = Account::create([
                         'uid'          => $user->id,
                         'provider'     => $data->provider,
                         'provider_uid' => $data->id,
                         'access_token' => $data->token,
                         'scope_request'=> $data->scope_request,
                         'name'         => $data->name,
                         'username'     => $data->nickname,
                         'email'        => $data->email,
                         'avatar'       => $data->avatar,
                         'active' => 1,
                         ]);
            
            // newly created account is up to date
            $account_update = false;
        }
        
        // need to update existing account with latest data?
        // for example when a user changes avatar, etc.
        if ( $account && $account_update ){
            $this->updateAccount( $account, $data );
        }
        
        // need to updates user with new providers, emails or signatures?
        // not yet..
        $user_update = false;

        // $data->provider is not empty
        if ( $account && strpos( $user->providers, $data->provider ) === false ){
            $user->providers = $this->getUserProviders( $user->id );
            $user_update = $update;
        }
        
        // email may be empty! (i.e. twitter)
        if ( $user && !empty($data->email) && strpos( $user->emails, $data->email) === false ){
            $user->emails .= ',' . $data->email;
            $user_update = $update;
        }
        
        // signature and $user->signatures are not empty
        if ( $user && strpos( $user->signatures, $signature) === false ){
            $user->signatures .= ',' . $signature;
            $user_update = $update;
        }
        
        if ( $user && $user_update ){
            $user->save();
        }

        return $user;
    }
    
    // ............................................ update_accountBySociliteUser

    public function updateAccount( $account, $data )
    {        
        $socialiteData = [
                         'avatar'       => $data->avatar,
                         'email'        => $data->email,
                         'access_token' => $data->token,
                         'scope_request'=> $data->scope_request,
                         'username'     => $data->nickname,
                         'name'         => $data->name,
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
            
            $account->avatar       = $data->avatar;
            $account->email        = $data->email;
            $account->access_token = $data->token;
            $account->scope_request= $data->scope_request;
            $account->name         = $data->name;
            $account->username     = $data->nickname;
            
            // TODO: Add errors for when db is malformed and we throw
            // an exception in the App\Models\Account
            
            $account->save();
        }
    }
}