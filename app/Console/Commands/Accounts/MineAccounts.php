<?php namespace App\Console\Commands\Accounts;

use App\Models\Account;
use App\Components\FactsFactory\AccountFactsFactory;
use App\Components\FactsFactory\AccountFactsContract;
    
class MineAccounts extends Accounts {

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
	protected $description = 'Mine user accounts into facts';
    
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
    public function process_one_account( Account $act, $options )
	{
        $err = false;
        
        $facts  = AccountFactsFactory::make( $act );
                
        if ( $facts instanceof AccountFactsContract ){
                    
            $output = array($this, 'info');
                    
            $facts->set_output ( $output  )
                  ->set_options( $options )
                  ->process    ( $act     );
        }
        else{
            $err = 'Failed to make fact factory for '. $act->toString();
        }
    
        return $err;
    }
}
