<?php namespace App\Models;
    
use Illuminate\Database\Eloquent\Model;

class RealtimeUpdate extends Model {
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'provider' ,
                            'object'   ,
                            'active'   ,
                            'json'     ,
                        ];
}
