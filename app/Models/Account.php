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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'uid'           ,
                            'provider'      ,
                            'provider_uid'  ,
                            'access_token'  ,
                            'email'         ,
                            'username'      ,
                            'name'          ,
                            'state'         ,
                            'provider_state'
    ];
}
