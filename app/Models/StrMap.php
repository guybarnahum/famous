<?php namespace App\Models;
    
use Illuminate\Database\Eloquent\Model;

class StrMap extends Model {
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name','key', 'value', 'count' ];
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'str_map';
    
    /*
     * We don't need timestamps for map
     */
    public $timestamps = false;
}