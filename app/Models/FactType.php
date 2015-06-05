<?php namespace App\Models;
    
use Illuminate\Database\Eloquent\Model;

class FactType extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fact_types';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name',
                            'statement_fmt',
                            'question_fmt',
                            'desc',
                            'val_type'
                        ];
}