<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';
    
    /**
     * The name of the "created at" column.
     * We reused the create date as a default for the expiration for the token
     * with the expectation that it would be set to a valid value
     * @var string
     */
    const CREATED_AT = 'expired_at';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'uid'           ,
                            'provider'      ,
                            'provider_uid'  ,
                            'access_token'  ,
                            'expired_at'    ,
                            'scope_request' ,
                            'scope_granted' ,
                            'scope_denied'  ,
                            'email'         ,
                            'username'      ,
                            'name'          ,
                            'state'         ,
                            'provider_state'
    ];
    
    public function toString( $long = false )
    {
        $str = $this->provider . ':' .
               $this->email    . ':' .
               $this->provider_uid   ;
        
        if ($long){
            $str .= ':' . $this->token;
        }
        
        return $str;
    }
}
