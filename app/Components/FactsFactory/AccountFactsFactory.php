<?php namespace App\Components\FactsFactory;
    
class AccountFactsFactory{

    // ........................................................ validate_account
    public static function validate_account( $act )
    {
        $msg = 'AccountFactsFactory::validate_account';
        if ( ! $act instanceof \App\Models\Account ){
            $msg .= '-expected \App\Models\Account';
            throw new \InvalidArgumentException( $msg );
        }
        
        if ( empty( $act->access_token) ){
            $msg .= '-invalid access token';
            throw new \InvalidArgumentException( $msg );
        }
    }
    
    // .................................................................... make
    public static function make( $act )
    {
        self::validate_account( $act ); // emits InvalidArgumentException
        
        $provider = $act->provider;
        
        switch( $provider ){
            case 'facebook' : return new FacebookFacts();
        }
    
        // unsupported provider!
        $msg  = 'AccountFactsFactory::make';
        $msg .= '-unsupported account type ('. $provider . ')';
        throw new \InvalidArgumentException( $msg );
        
        // Should not get here..
        return null;
    }
}