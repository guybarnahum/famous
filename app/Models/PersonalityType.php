<?php namespace App\Models;
    
use Illuminate\Database\Eloquent\Model;

class PersonalityType extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'personality_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'sys', 'name', 'display', 'desc' ];
}
