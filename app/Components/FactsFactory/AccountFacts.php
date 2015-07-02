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

    // ........................................................... validate_fact

    protected function validate_fact( $fact )
    {
        // TODO : Do a better job in validating $fact.. should we use ORM?
        $ok = is_array( $fact ) && isset( $fact['fct_name'] );
        return $ok;
    }
    
    // ........................................................ prepare_one_fact
    
    protected function prepare_one_fact( $type, $fact )
    {
        // sometime we fct_name through a formatter on the object data..
        if (!isset($fact['fct_name'])){
            $fact[ 'fct_name' ] = $type ;
        }
        
        $fact[ 'uid'       ] = $this->act->uid; // subject of the fact
        $fact[ 'act_id'    ] = $this->act->id ; // who claims the fact
        
        // object should be already filled
        
        // responses to fact question
        $fact[ 'val_type'  ] =  'bool';
        $fact[ 'value'     ] =  'true';
        
        // accuracy / confidence
        $fact[ 'error'     ] = '';
        $fact[ 'score'     ] = 0;
        $fact[ 'confidence'] = 0;
        
        return $fact;
    }

    // ........................................................... process_facts
    
    protected function prcess_facts( $cname, $obj, $store = false )
    {
        $facts_collection = $this->mapper->map( $cname, $obj );
        
        if ( is_array($facts_collection) ){
            foreach( $facts_collection as $type => $facts ){
                
                foreach( $facts as $ix => $fact ){
                    
                    $fact = $this->prepare_one_fact( $type, $fact );

                    if ( $this->validate_fact($fact) ){
                        $facts_collection[ $type ][ $ix ] = $fact;
                    }
                    // failed to produce a valid fact from object
                    else{
                        unset( $facts_collection[ $type ][ $ix ] );
                    }
                    
                    if ($store){
                        try{
                            // attempt to avoid duplicates..
                            $res = Fact::firstOrCreate( $fact );
                            $this->output( 'Fact::firstOrCreate>>' .
                                            $res->toString() );
                        }
                        catch( \Exception $e ){
                            $this->output( 'Fact::firstOrCreate>>' .
                                            $e->getMessage() );
                        }
                    }
                }
            }
        }
        
        return $facts_collection;
    }
}
