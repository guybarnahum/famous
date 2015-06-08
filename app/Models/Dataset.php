<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model {
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'provider'  ,
                            'api_key'   , 'api_secret',
                            'driver'    ,
                            'oath_callback_uri'
                        ];
}
