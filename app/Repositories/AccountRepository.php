<?php namespace App\Repositories;

use App\Models\Account;
use App\Models\User;
use App\Models\Dataset;
use App\Models\Fact;
    
use App\Components\FactsFactory\AccountFactsFactory;
use App\Components\FactsFactory\AccountFacts;
    
use Hash;
    
class AccountRepository {
    
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
        \Debugbar::info( '>> generateUserFacts(uid:' . $uid . ',' . $provider . ')' );

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
    
    // ............................................................ getUserFacts
    
    public function getUserFacts( $uid, $provider = false )
    {
        \Debugbar::info( 'getUserFacts(uid:' . $uid . ',' . $provider . ')' );
        
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
        
        return $facts;
    }
    
    // ......................................................... getUserAccounts

    public function getUserAccounts( $uid, $provider = false )
    {
        \Debugbar::info( 'getUserAccounts(uid:' . $uid . ',' . $provider . ')' );

        $match = ['uid' => $uid ];
        if ( $provider ) $match[ 'provider' ] = $provider;
        
        return Account::where( $match )->get();
    }
    
    
    // ................................................. find_userBySociliteUser

    public function find_userBySociliteUser( $userData      ,
                                             $update = true ,
                                             $create = false)
    {
        \Debugbar::info( 'find_userBySociliteUser(' . $userData->email . ')' );

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
    
        // get all the account(s) information we have for user
        if ($user){
            $accounts = $this->getUserAccounts( $user->id );
        }
        
        $res = (object)[ 'user' => $user, 'accounts' => $accounts ];
        return $res;
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