<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

        $this->call('FactTypeTableSeeder');
        $this->call('PersonalityTypeTableSeeder');
        $this->call('DatasetTableSeeder');
        
//        $this->call('UserTableSeeder');
//        $this->call('AccountTableSeeder');
//        $this->call('PhotoTableSeeder');
        
	}
};
    
// ========================================================== class ParserSeeder

class ParserSeeder extends Seeder{
    
    private   $debug = false;
    
    protected $file  ;
    protected $fmt   ;
    protected $table ;
    protected $msg   ;
    protected $model ;
    
    // .............................................................. is_comment
    
    protected function is_comment( $line )
    {
        if (  empty($line)       ) return true;
        if (  $line[0] == ';'    ) return true;
        if (  $line[0] == '#'    ) return true;
        
        if ( ($line[0] == '/' ) &&
             ($line[1] == '/' )  ) return true;
        
        // not a comment line
        return false;
    }
    
    protected function is_option( $line )
    {
        if (( $line[0] != '-' )||
            ( $line[1] != '-' ) ) return false;
        
        // its an option line, yay!
        $options = explode( '--', $line );
        
        foreach( $options as $option ){
            
            // skip 'false' options..
            $option = trim( $option);
            if (empty($option)) continue;
            
            switch( $option ){
            case 'debug': case 'dbg': case 'verbose': $this->debug = true;break;
            default: $this->command->error('unknown option '.$option)    ;break;
            }
        }
        
        return true;
    }
    
    // ............................................................ process_line
    
    protected function process_line( $line )
    {
        $res   = array();
        $parts = explode( ':', $line );
        
        $ok = is_array($parts);
        if (!$ok) $res[ 'err' ] = 'syntax - line does not contain delimiter (:)';
        
        if ($ok){
            $num = count($parts);
            $ok = ( $num == count( $this->fmt ) );
            if (!$ok){
                $res[ 'err' ] ='systax - could not find parts in line (' . $num . ')' ;
            }
        }
        
        if ($ok){
            foreach( $parts as $ix => $part ){
                $name = $this->fmt[ $ix ];
                $res[ $name ] = trim( $part );
            }
        }
        
        return $res;
    }
    
    // ............................................................... get_lines
    
    protected function get_lines( $file )
    {
        $this->msg = '';
        
        $ok = ( false !== ($path = realpath( $file )));
        if (!$ok) $this->msg = $file . ' could not be located';
        
        if ($ok){
            $ok = file_exists( $path );
            if (!$ok) $this->msg = $path . ' does not exists';
        }
        
        if ($ok){
            $ok = ( false !== ($data = file_get_contents( $path ) ) );
            if (!$ok) $this->msg =  $path . ' could not be read' ;
        }
        
        $lines = explode("\n", $data);
        return $lines;
    }
    
    // ................................................................... parse
    
    protected function parse( $lines )
    {
        $ok = is_array( $this->fmt );
        if (!$ok ) $this->msg = 'invalid table row format';
        
        if ($ok ){
            $ok = is_array( $lines ) && ( count( $lines ) > 0 );
            if (!$ok) $this->msg = 'no lines found';
        }
        
        if ($ok){
            
            foreach( $lines as $ix => $line ){
                
                $line = trim( $line );

                // skip comments
                if ( $this->is_comment( $line ) ) continue;

                // process options and skip line
                if ( $this->is_option ( $line ) ) continue;
                
                // process lines
                $ds = $this->process_line( $line );
                
                $ok = !isset( $ds[ 'err' ] );
                if (!$ok){
                    $this->msg  = 'failed to process ' . $ix . ' line :\'' . $line . '\'';
                    $this->msg .= "\n" . $ds[ 'err' ];
                    break;
                }
                
                // debug output
                if ( $this->debug ){
                     $this->command->info( 'parse >> ' . $line );
                }
                
                // Create an db entry with $ds
                try{
                    $model_create = array($this->model, 'create');
                    $ok = is_callable( $model_create );
                    
                    if ($ok){
                        call_user_func( $model_create, $ds );
                    }
                    else{
                        $this->msg = $this->model . '::create is not callable';
                    }
                }
                catch( Exception $e ){
                    $this->msg = $e->getMessage();
                    $ok        = false;
                }
                
                if (!$ok){
                    break;
                }
            }
        }
        
        return $ok;
    }
    
    // ..................................................................... run

    public function run()
    {
        // get lines to process
        $lines = $this->get_lines( $this->file );
        $ok = is_array($lines) && (count( $lines ) > 0 );
        
        // do we have lines to process?
        if ($ok){
            DB::table( $this->table )->delete();
            $ok = $this->parse( $lines );
        }
        
        // done!
        if ($ok){
            $this->command->info( 'seeding from ' . $this->file . ' successful!' );
        }
        else{
            $this->msg .= "\n";
            $this->msg .= 'failed to process ' . $this->file;
            $this->command->error( $this->msg );
        }
    }
};
  
// ==================================================== class DatasetTableSeeder

class DatasetTableSeeder extends ParserSeeder {
    
    public function run()
    {
        $this->msg   = '';
        $this->file  = 'database/seeds/datasets.txt';
        $this->table = 'datasets';

        $this->fmt   = array( 'provider',
                              'api_key' , 'api_secret',
                              'driver'  ,
                              'oath_callback_uri', 'scope' );
        
        $this->model = 'App\Models\Dataset';
        
        ParserSeeder::run();
    }
};

// ============================================ class PersonalityTypeTableSeeder
    
class PersonalityTypeTableSeeder extends ParserSeeder {
        
    public function run()
    {
        $this->msg   = '';
        $this->file  = 'database/seeds/personality_types.txt';
        $this->table = 'personality_types';
        $this->fmt   = array( 'sys','name', 'display', 'desc' );
        $this->model = 'App\Models\PersonalityType';
            
        ParserSeeder::run();
    }
};
    
// =================================================== class FactTypeTableSeeder
    
class FactTypeTableSeeder extends ParserSeeder {
        
    public function run()
    {
        $this->msg   = '';
        $this->file  = 'database/seeds/fact_types.txt';
        $this->table = 'fact_types';
        $this->fmt   = array( 'name', 'statement_fmt', 'question_fmt', 'desc', 'val_type' );
        $this->model = 'App\Models\FactType';
            
        ParserSeeder::run();
    }
};


