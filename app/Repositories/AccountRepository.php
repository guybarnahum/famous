<?php namespace App\Repositories;

use App\User;

class AccountRepository {
    
    public function findByAccountOrCreate( $userData )
    {
        $user = User::where('provider_id', '=', $userData->id)->first();
        if(!$user) {
            $user = User::create([
                                 'provider_id' => $userData->id,
                                 'name' => $userData->name,
                                 'username' => $userData->nickname,
                                 'email' => $userData->email,
                                 'avatar' => $userData->avatar,
                                 'active' => 1,
                                 ]);
        }
        
        $this->checkIfAccountNeedsUpdating( $userData, $user );
        return $user;
    }
    
    public function checkIfUserNeedsUpdating($userData, $user)
    {
        $socialData = [
        'avatar'   => $userData->avatar,
        'email'    => $userData->email,
        'name'     => $userData->name,
        'username' => $userData->nickname,
        ];
        
        $dbData = [
        'avatar'   => $user->avatar,
        'email'    => $user->email,
        'name'     => $user->name,
        'username' => $user->username,
        ];
        
        $diff   = array_diff($socialData, $dbData);
        $update = !empty( $diff );
        
        if ( $update ) {
            
            $user->avatar   = $userData->avatar;
            $user->email    = $userData->email;
            $user->name     = $userData->name;
            $user->username = $userData->nickname;
            
            $user->save();
        }
    }
};