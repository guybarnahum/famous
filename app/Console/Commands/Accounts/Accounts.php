<?php namespace App\Console\Commands\Accounts;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Account;    
    
class Accounts extends Command {
    
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

    // ...............................................................
    public function getAccounts()
    {
        $user    = $this->argument( 'user' );
        $provider= $this->argument( 'provider' );
        
        if ( $user     == 'all' ) $user     = false;
        if ( $provider == 'all' ) $provider = false;
        
        // build account filter
        $where = [];
        if ( $provider ){
            $where[ 'provider' ] = $provider;
        }
        
        // detect type of uid
        if ( $user ){
            
            if (is_numeric( $user ))
                $where[ 'uid'   ] = $user ;
            else
            if( filter_var( $user, FILTER_VALIDATE_EMAIL))
                $where[ 'email' ] = $user ;
            else
                $where[ 'name' ]  = $user ;
        }
        
        // get accounts
        if ( count( $where ) == 0 ){
            $this->info( 'Getting all accounts..' );
            $accts = Account::All();
        }
        else{
            $msg  = $provider? (',' . $provider . ' provider') : '';
            $msg .= $user? (', user is ' . $user) : '';
            $msg  = substr( $msg, 1);
            
            $this->info( 'Getting accounts with ' . $msg );
            $accts = Account::where( $where )->get();
        }
        
        return $accts;
    }
    
    public function process_one_account( Account $act, $options )
    {
        $this->info( 'Accounts::process_one_account(' . $act->toString() . ')');
        return $this;
    }
    
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
    public function handle()
	{
        $options = $this->option();
        $accts   = $this->getAccounts();
        
        if ( empty( $accts ) ){
            $this->error( 'No accounts found!' );
            return;
        }
        
        foreach( $accts as $act ){
            
            $abort = false;
            $err   = false;

            $this->info( '--> Start processing ' . $act->toString() );
            
            try{
                $this->process_one_account( $act, $options );
            }
            catch( \InvalidArgumentException $e )
            {
                $err = '\InvalidArgumentException : ' . $e->getMessage();
            }
            catch( \Exception $e) {
                
                $err   = '\Exception : ' . $e->getMessage();
                $abort = true;
            }
            
            if ( $err) $this->error( $err );
            $this->info( '<-- End processing ' . $act->toString());
            
            // When we find a problem that is beyond a single account problem..
            if ( $abort ){
                $this->error( 'Aborting! >> ' );
                break;
            }
        }
	}
        
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['user'    , InputArgument::OPTIONAL, 'user identification', 'all'],
            ['provider', InputArgument::OPTIONAL, 'account provider '  , 'all'],
		];
	}

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['x', null, InputOption::VALUE_NONE, 'Invoke only experimental options.', null],
            ['s', null, InputOption::VALUE_NONE, 'Subscribe callback to accounts.'  , null],
        ];
    }
}