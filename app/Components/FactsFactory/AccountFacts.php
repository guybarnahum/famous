<?php namespace App\Components\FactsFactory;
    
use App\Components\FactsFactory\AccountFactsContract;
use App\Components\DataMapper;

use App\Models\Fact;
    
abstract class AccountFacts implements AccountFactsContract{
    
    protected   $mapper;
    protected   $act             = null;
    private     $output_callback = false;

    function __construct( $act )
    {
        $this->act     = $act;
        $this->mapper = new DataMapper();
    }
    
    // .............................................................. set_output
    public function set_output( $output_callback )
    {
        if (!is_callable($output_callback)){
            $msg  = 'AccountFacts::set_output - argument is not callable!';
            throw new \InvalidArgumentException( $msg );
        }
        
        $this->output_callback = $output_callback;
        return $this;
    }
    
    // .................................................................. output
    protected function output( $str, $obj = false )
    {
        if (is_callable($this->output_callback)){
            if ( $obj ) $str .= ' : ' . print_r( $obj, true );
            call_user_func( $this->output_callback, $str );
        }
        return $this;
    }
    
    // .................................................................... fact
    
    public function process_birthday ( $user )
    {
        return $this;
    }
    
    public function prepare_fact( $val_type = 'bool', $value = 'true' )
    {
        $fact_fields = [
        'uid'         =>  $this->act->uid,
        // who claims the fact
        'act_id'      =>  $this->act->id,
        
        // responses to fact question
        'val_type'    =>  $val_type,
        'value'       =>  $value,
        
        // accuracy / confidence
        'error'       => '',
        'score'       => 0,
        'confidence'  => 0,
        ];
        
        return $fact_fields;
    }
    
    public function process_obj( $obj_name )
    {
        $ok = isset( $user[ 'education' ] );
        
        if ($ok){
            
            foreach( $user[ 'education' ] as $entry ){
                
                $school_id   = $entry[ 'school' ][ 'id'   ];
                $school_name = $entry[ 'school' ][ 'name' ];
                
                switch( $entry[ 'type' ] ){
                    case 'College' : $fct_name = '.college'; break;
                }
                
                $fct_name = 'education' . $fct_name;
                
                $fields = [
                'uid'         =>  $this->act->uid,
                'obj_provider_id' => $school_id,
                'obj_id_type' => 'facebook:education:id',
                'obj_name'    =>  $school_name,
                
                // who claims the fact
                'act_id'      =>  $act->id,
                
                // fact type
                'fct_name'    =>  $fct_name,
                
                // responses to fact question
                'val_type'    =>  'bool',
                'value'       =>  'true',
                
                // accuracy / confidence
                'error'       => '',
                'score'       => 0,
                'confidence'  => 0,
                ];
                
                // avoid duplicates..
                Fact::firstOrCreate( $fields );
            }
            
            $msg = 'process_education:>>' . $fct_name . ',' . $school_name;
            $this->output( $msg );
        }
        
        return $this;
    }
}
