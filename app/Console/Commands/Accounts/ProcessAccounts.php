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
	protected $name = 'accounts:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
    
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

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
    public function handle()
	{
        $accts = Account::All();
        
        foreach( $accts as $act ){
            
            $this->info( '--> Start processing ' . $act->toString() );
            $msg = '';
            
            try{
                $facts  = AccountFactsFactory::make( $act );
                
                if ( $facts instanceof AccountFactsContract ){
                    
                    $output = array($this, 'info');
                    
                    $facts->set_output( $output )
                          ->process   ( $act    );
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
//			['example', InputArgument::REQUIRED, 'An example argument.'],
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
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
