<?php namespace App\Console\Commands\Accounts;
    
use App\Models\Account;
use App\Models\PersonalityEntry;
    
use App\Components\FactsFactory\AccountFactsFactory;
use App\Components\FactsFactory\AccountFactsContract;
    
use Papi\Client\Exception\NoPredictionException;
use Papi\Client\Model\TraitName;
use Papi\Client\PapiClient;
use Papi\Client\Model\PredictionResult;
use Papi\Client\Model\Prediction;
    
use App\Repositories\UserRepository;
    
spl_autoload_register(
            function($className ){
                    
                $className = str_replace( '\\', '/' , $className );
                                         
                $dirs = [
                            'App/Components/Insights/',
                            '/var/www/famous-dev/app/Components/Insights/',
                        ];
                
                foreach( $dirs as $dir ){
                    $fileName = $dir . $className . '.php';
                    
                    if ( file_exists($fileName) ){
                        require_once $fileName;
                        return;
                    }
                }
                      
            });

class PapiInsights{
    
    private $url    = "http://api-v2.applymagicsauce.com";
    private $client;

    // This is what you got during registration
    private $appId  = 958;
    private $apiKey = "14mcrr8h6cadm98sv4d7lam05r";
    private $out    = false;
    private $token  = false;
    private $db     = false;
    
    public function output( $str, $obj = false )
    {
        if ( is_callable( $this->out ) ){
            $obj_str = print_r( $obj, true );
            call_user_func( $this->out, $str . ' ' .  $obj_str );
        }
        
        return $this;
    }
    
    public function set_output( $out )
    {
        $this->out = $out;
    }
    
    public function __construct()
    {
        $this->client = new PapiClient( $this->url );
        $this->db     = new UserRepository();
        
        $this->set_output( null );
        
    }
    
    public function getToken()
    {
        //
        // Authentication.
        // TODO: This token will be valid for at least one hour,
        // so we should to store it and re-use for further requests
        //
        if ( !$this->token ){
            $this->token = $this->client->getAuthResource()
                                  ->requestToken( $this->appId, $this->apiKey);
        }
        
        return $this->token ;
    }
    
    public function getLikes( Account $act, $where = [] )
    {
        $facts = $this->db->getUserFacts( $act->uid, $act->provider, $where );
    
        $likes_ids = [];
        
        foreach( $facts as $fact ){
            $likes_ids[] = $fact->obj_provider_id;
        }
        
        return $likes_ids;
    }
    
    private function process_result( $act, $res )
    {
        $uid = $act->uid;
        
        if ( $res instanceOf PredictionResult ){
        
            $predictions = $res->getPredictions();
         
            foreach( $predictions as $prediction ){
            
                if ( $prediction instanceOf Prediction ){
                    
                    $trait  = $prediction->getTrait();
                    $value  = $prediction->getValue();
                    
                    $p = [ 'uid'  => $uid   ,
                           'src'  => 'papi2',
                           'sys'  => 'papi2',
                           'name' => $trait ,
                           'value'=> $value ];
                    
                    $p_obj = PersonalityEntry::firstOrCreate( $p );
                    $this->output( 'Fact::firstOrCreate>>' . $p_obj->toString() );
                    
                }
            }
        }
    }
    
    public function process_one_account( Account $act, $options )
    {
        $err = false;
        
        if ( $act->provider != 'facebook' ){
            return $err = 'PapiInsights supports only facebook accounts!';
        }
        
        $token  = $this->getToken()->getTokenString();
        $uid    = $act->provider_uid;
        
        $likes  = $this->getLikes( $act, [ 'fct_name' => 'likes' ] );
        
        $traits = [ TraitName::BIG5,
                    TraitName::SATISFACTION_WITH_LIFE,
                    TraitName::INTELLIGENCE,
                    TraitName::FEMALE,
                    TraitName::GAY ,
                    TraitName::LESBIAN,
                    TraitName::CONCENTRATION,
                    TraitName::POLITICS,
                    TraitName::RELIGION,
                    TraitName::RELATIONSHIP
                ];

        try {

            $this->output( 'Prediction for uid:' . $uid . ', token:' . $token );
            
            $res = $this->client->getPredictionResource()
                                ->getByLikeIds( $traits, $token, $uid, $likes );
            
            $err = $this->process_result( $act, $res );
        }
        catch( NoPredictionException $e) {
            $err = $e->getMessage();
        }
        catch( \Exception $e ){
            $err= $e->getMessage();
        }

        return $err;
    }
};
    
class MineInsights extends Accounts {

    private $papi = null;
    
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mine:insights';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Mine user facts into insights';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
     
        $this->papi = new PapiInsights;
        $this->papi->set_output( [ $this, 'info' ] );
	}
    
    public function process_one_account( Account $act, $options )
    {
        return $this->papi->process_one_account( $act, $options );
    }
};
