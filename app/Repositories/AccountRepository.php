<?php namespace App\Repositories;

use App\Models\Account;
use App\Models\User;
use App\Models\Dataset;
    
use Hash;
    
class AccountRepository {
    
    public function get_scopes( $provider )
    {
        $ds = Dataset::where( 'provider', '=', $provider )->first();
        $scopes = ( $ds instanceof Dataset )? $ds->scope : false;
        return $scopes;
    }
    
    public function find_userBySociliteUser( $userData      ,
                                             $update = true ,
                                             $create = false)
    {
        // attempt to locate the user for the socialite account
        // by email, need a better way to decide what is the current logged in user
        
        $user = User::where( 'emails', 'LIKE', '%' . $userData->email . '%' )->first();
        
        // no user for this socialite account?
        // if we are in create mode than make one!
        if ( !$user && $create ){
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
            $accounts = Account::where( 'uid', $user->id )->get();
        }
        
        $res = (object)[ 'user' => $user, 'accounts' => $accounts ];
        return $res;
    }
    
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
        
        $dbData = [
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
            
            $account->save();
        }
    }
};