<?php namespace App\Models;
    
use Illuminate\Database\Eloquent\Model;

class Fact extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    // This is a test!
    protected $table = 'facts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    
            // fact 'actors'
            'uid'         , // user id that is the subject of the fact
            'obj_id'      , // fact dependent obj_id
            'obj_provider_id',
            'obj_id_type' , // what kind of object:id
            'obj_name'    , // name of obj
    
            // who claims the fact
            'src_id'      , // who gave the answer? according to who?
            'src_id_type' , // format of src_id
            'act_id'      , // account id, who is the source
    
            // fact type
            'fct_id'    , // fact id (with us)
            'fct_name'  , // fact name
            'fct_type'  , // fact sub-type
            'fct_provider_id', // fact id with provider
    
            // responses to fact question
            'refuse'    , 'dont_know' ,
            'val_type'  , 'value'     ,
    
            // accuracy / confidence
            'error'     ,
            'score'     ,
            'confidence',
    
            'created_at',
            'updated_at',
    
        ];
    
    public function toString($long = false )
    {
        $str = $this->fct_name .
         '(' . $this->fct_type . ').' .
               $this->fct_provider_id . ':' .
               $this->uid      . ':' .
               $this->obj_name . ':' .
               $this->obj_id_type    ;
        
        if ($long){
            $str .= ':' . $this->value        . ':' . $this->val_type;
            $str .= ':' . $this->created_time . ':' . $this->updated_time;
        }
        
        return $str;
    }
}