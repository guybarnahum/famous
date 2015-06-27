<?php namespace App\Models;
    
use Illuminate\Database\Eloquent\Model;

class PersonalityEntry extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'personality_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'uid', 'ptid', 'name', 'value', 'error' ];
}