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
    protected $fillable = [ 'uid', 'src', 'sys', 'name', 'value', 'error' ];
    
    public function toString( $long = false )
    {
        $str =       'uid.' . $this->uid        . ':' .
               $this->sys   . '.' . $this->name . ':' .
               $this->value ;
        
        if (!empty($this->error))  $str .= '(' . $this->error . ')';
        
        if ($long ){
            if ( !empty($this->src) ) $str .= ':src' . $this->src;
        }
        
        return $str;
    }
}