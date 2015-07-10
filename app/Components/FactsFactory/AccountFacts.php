<?php namespace App\Components\FactsFactory;
    
use App\Components\FactsFactory\AccountFactsContract;
use App\Components\DataMapper;

use App\Models\Fact;
    
abstract class AccountFacts implements AccountFactsContract{
    
    protected   $mapper;
    protected   $act             = null;
    protected   $options         = null;
    private     $output_callback = false;

    function __construct( $act )
    {
        $this->act     = $act;
        $this->mapper = new DataMapper();
    }
   
    // ................................................................. options
    public function set_options( $options )
    {
        $this->options = $options;
        return $this;
    }

    public function get_option( $option )
    {
        return isset($this->options[ $option ])? $this->options[ $option ]:null;
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
    
    protected function prepare_one_fact( $fact = [], $type = false )
    {
        // in case we did not get the fct_name through a formatter
        // on the object data..
        
        if ( !isset( $fact[ 'fct_name' ] ) && $type){
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

    // ................................................. process_fact_collection

    protected function process_fact_collection( $fc, $store = false )
    {
        if ( !is_array($fc) ){
            $this->output( 'process_fact_collection: invalid input - ', $fc );
            return $this;
        }
            
        foreach( $fc as $type => $facts ){
                
            foreach( $facts as $ix => $fact ){
                
                $fact = $this->prepare_one_fact( $fact, $type );
                
                if ( $this->validate_fact($fact) ){
                    $fc[ $type ][ $ix ] = $fact;
                }
                // failed to produce a valid fact from object
                else{
                    $this->output( 'invalid fact:', $fact );
                    unset( $fc[ $type ][ $ix ] );
                }
             }
        }

        if ( $store ) $this->save_facts( $fc );
        
        return $this;
    }
    
    // .............................................................. save_facts

    protected function save_facts( $fc )
    {
        foreach( $fc as $type => $facts ){
            
            $this->output( 'Saving type:' . $type );
            
            foreach( $facts as $fact ){
                try{
                    // attempt to avoid duplicates..
                    $res = Fact::firstOrCreate( $fact );
                    $this->output( 'Fact::firstOrCreate>>' . $res->toString() );
                }
                catch( \Exception $e ){
                    $this->output( 'Fact::firstOrCreate>>' . $e->getMessage(), $fact );
                }
            }
        }
        return $this;
    }
    
    // ........................................................... process_facts
    
    protected function prcess_facts( $cname, $obj, $store = false )
    {
        $fc = $this->mapper->map( $cname, $obj );
        return $this->process_fact_collection( $fc, $store );
    }
}
