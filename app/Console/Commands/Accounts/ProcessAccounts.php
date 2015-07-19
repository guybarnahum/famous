<?php namespace App\Console\Commands\Accounts;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Account;    
use App\Components\FactsFactory\AccountFactsFactory;
use App\Components\FactsFactory\AccountFactsContract;
    
class ProcessAccounts extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mine:accounts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Process user accounts into facts';
    
    /**
     * An abort flag on critical errors
     *
     * @var bool
     */
    protected $abort_req = false;
    protected $err       = false;
    
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
            
            $this->info( '--> Start processing ' . $act->toString() );
            $msg = '';
            
            try{
                $facts  = AccountFactsFactory::make( $act );
                
                if ( $facts instanceof AccountFactsContract ){
                    
                    $output = array($this, 'info');
                    
                    $facts->set_output ( $output  )
                          ->set_options( $options )
                          ->process    ( $act     );
                }
                else{
                    $msg = 'Failed to make fact factory for '. $act->toString();
                }
            }
            catch( \InvalidArgumentException $e )
            {
                $msg = '\InvalidArgumentException : ' . $e->getMessage();
            }
            catch( \Exception $e) {
                
                $msg = '\Exception : ' . $e->getMessage();
                $this->abort_request();
            }
            
            $this->info( $msg );
            $this->info( '<-- End processing ' . $act->toString());
            
            // When we find a problem that is beyond a single account problem..
            if ( $this->needsAbort() ){
                $this->error( 'Aborting! >> ' . $this->needsAbort() );
                break;
            }
        }
	}

    /**
     * Set an abort request on critical errors
     *
     *
     * @return $this
     */
    public function abort_request( $set = true )
    {
        $this->abort_req = $set;
        return $this;
    }
    
    /**
     *
     * Does it need an abort?
     *
     * @return bool
     */
    public function needsAbort()
    {
        return $this->abort_req;
    }
        
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['user'     , InputArgument::OPTIONAL, 'user identification', 'all' ],
            ['provider' , InputArgument::OPTIONAL, 'account provider '  , 'all' ],
       
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
            ['s', null, InputOption::VALUE_NONE, 'Subscribe callback to accounts.', null],
		];
	}

}
