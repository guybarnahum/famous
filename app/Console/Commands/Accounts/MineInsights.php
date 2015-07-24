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
    
    // This should be on the other side of the papi..
    private function get_trait_error( $trait )
    {
        $error = -1; // invalid
        
        switch( $trait ){
            
            // Precentile in the poplulation, with error range
            // also as a precentile of the population
            
            case "BIG5_Openness"        :
            case "BIG5_Conscientiousness": 
            case "BIG5_Extraversion"    :
            case "BIG5_Agreeableness"   :
            case "BIG5_Neuroticism"     : $error = 0.5; break;
        
            case "Satisfaction_Life"    : $error = 0.17; break;
                
            case "Intelligence"         : $error = 0.47; break;

            // Value in years, with +/- 0.75 year error
            case "Age"                  : $error = 0.75; break;

            // Probablity of being Female of Male, with 7%
            // error rate
            case "Female"               :
            case "Male"                 : $error = 0.93; break;
            
            // Value : if man what is the probablity of being gay?
            // Error : 12 % error rate
            case "Gay"                  : $error = 0.88; break;
                
            // Value If woman what is the probablity of being lesbian?
            // Error : 25 % error rate
            case "Lesbian"              : $error = 0.75; break;
        
            // Concentration group:
            // value_units group probablity (sum to 1)
            // err_units ?
            case "Concentration_Art"    :
            case "Concentration_Biology": 
            case "Concentration_Business": 
            case "Concentration_IT"     :
            case "Concentration_Education": 
            case "Concentration_Engineering": 
            case "Concentration_Journalism": 
            case "Concentration_Finance": 
            case "Concentration_History": 
            case "Concentration_Law"    :
            case "Concentration_Nursing": 
            case "Concentration_Psychology":
                                          $error = 0.72; break;
        
            // Politics group:
            // value_units group probablity (sum to 1)
            // err_units ?
            case "Politics_Liberal"     :
            case "Politics_Conservative": 
            case "Politics_Uninvolved"  :
            case "Politics_Libertanian" : $error = 0.79; break;
        
            // Religion group:
            // value_units group probablity (sum to 1)
            // err_units ?
            case "Religion_None"        :
            case "Religion_Christian_Other": 
            case "Religion_Catholic"    :
            case "Religion_Jewish"      :
            case "Religion_Lutheran"    :
            case "Religion_Mormon"      : $error = 0.76; break;
        
            // Relationship group:
            // value_units group probablity (sum to 1)
            // err_units ?
            case "Relationship_None"    :
            case "Relationship_Yes"     :
            case "Relationship_Married" : $error = 0.67; break;
        }
        
        return $error;
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
                    $error  = $this->get_trait_error( $trait );
                    
                    $p = [ 'uid'  => $uid   ,
                           'src'  => 'papi2',
                           'sys'  => 'papi2',
                           'name' => $trait ,
                           'value'=> $value ,
                           'error'=> $error ];
                    
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
