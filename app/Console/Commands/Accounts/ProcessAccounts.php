<?php namespace App\Console\Commands\Accounts;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Account;    
use App\Components\FactFactory\AccountFactFactory;
    
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
            
            $this->info( '--> start processing ' . $act->toString() );
            $res = null;
            
            try{
                $facts  = AccountFactFactory::make( $act );
                $res    = $facts->process( $act ) ;
                $this->info( 'result:' . print_r($res,true));
            }
            catch( \InvalidArgumentException $e )
            {
                $this->info ( $e->getMessage() );
            }
            catch( \Exception $e) {
                $this->error( $e->getMessage() );
                $this->abort_request();
            }
            
            $this->info( '<-- end processing ' . $act->toString());
            
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
