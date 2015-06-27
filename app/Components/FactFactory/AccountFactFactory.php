<?php namespace App\Components\FactFactory;
    
class AccountFactFactory{

    // ........................................................ validate_account
    public static function validate_account( $act )
    {
        if ( ! $act instanceof \App\Models\Account )
            throw new \InvalidArgumentException('expected \App\Models\Account');
        
        if ( empty( $act->access_token) )
            throw new \InvalidArgumentException('invalid access token');
    }
    
    // .................................................................... make
    public static function make( $act )
    {
        self::validate_account( $act ); // emits InvalidArgumentException
        
        $provider = $act->provider;
        
        switch( $provider ){
            case 'facebook' : return new FacebookFacts();
        }
    
        $msg = 'unsupported account type ('. $provider . ')';
        throw new \InvalidArgumentException( $msg );
    }
}