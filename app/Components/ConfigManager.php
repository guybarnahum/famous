<?php

namespace App\Components;

/**
 * One-stop shop for all configuration data (kept in an ini having path /var/conf/famous.ini).
 *
 * NOTE: You can find the .dist ini file in the project root under /conf/. You will have to
 * copy the file to famous.ini and configure the data according to environment.
 *
 * Class ConfigManager
 * @package App\Components
 */
class ConfigManager {

    const INI_PATHS = '/var/conf/famous.ini:conf/famous.ini';
    /**
     * @ignore
     */
    const INI_FILE_NODE_FAMOUS = 'famous';
    /**
     * @ignore
     */
    const INI_PROVIDER_NAMESPACES = 'provider_ns';

    /**
     * @var ConfigManager
     */
    private static $instance = null;
    /**
     * @ignore
     * @var array|null
     */
    private $ini = null;
    /**
     * @ignore
     * @var array
     */
    private $namespaces = [];

    /**
     * @ignore
     *
     * News the class and sets the ini data
     *
     * @param array $ini
     */
    private function __construct(array $ini) {
        $this->ini = $ini;
    }

    /**
     * @param string $custom_path
     * @return ConfigManager
     * @throws ConfigDataNotFoundException
     */
    public static function getInstance( $custom_path = '' ) {
        if (!self::$instance) {
            
            $paths       = empty($custom_path)? self::INI_PATHS : $custom_path;
            $paths_array = explode(':', $paths );
            $searched    = '';
            $err         = '';
            
            foreach( $paths_array as $path ){
                
                if ( $path[0] != '/' ){
                    $path = $_SERVER[ 'DOCUMENT_ROOT' ] . '/../' . $path;
                    $path = realpath( $path );
                }
                
                // try this path!
                $ini = false;
                
                try{
                    $ini = parse_ini_file( $path, true );
                }
                catch( \Exception $e ){
                    // do noting!
                    $searched .= $path . ',';
                    $err .= $e->getMessage() . '. ';
                }
                
                // do we have a valid $ini from $path?
                if ( is_array( $ini ) ){
                    break;
                }
            }
            
            // boo! no $ini from all the $searched paths
            if ( empty( $ini ) ){
                
                $msg  = "Config data not found at $paths, did you copy and populate it?";
                $msg .= " (/var/conf/famous.ini.dist -> /var/conf/famous.ini)";
                $msg .= " Searched for famous.ini in $searched";
                $msg .= " Errors: $err";
                
                throw new ConfigDataNotFoundException($msg);
            }
            
            // generate the singlton
            self::$instance = new self($ini);
        }
        
        return self::$instance;
    }


    /**
     * Returns all (enabled) providers
     *
     * @return array
     */
    public function getProviders() {
        if (empty($this->namespaces)) {
            $node = $this->getNode(self::INI_FILE_NODE_FAMOUS);
            $ns = $node->get(self::INI_PROVIDER_NAMESPACES);
            $this->namespaces = explode(',', $ns);
        }
        return $this->namespaces;
    }

    /**
     * Returns an instance of {@link ConfigNode}, which holds config data for let's
     * say... Facebook api key and secret, LinkedIn key secret, etc.
     *
     * @param $name
     * @return ConfigNode
     */
    public function getNode($name) {
        if (isset($this->ini[$name])) {
            $node = new ConfigNode($name, $this->ini[$name]);
        }
        else $node = new ConfigNode($name, []);
        return $node;
    }
}

/**
 * Class ConfigNode
 * @package App\Components
 */
class ConfigNode {

    /**
     * @ignore
     * @var string
     */
    private $node_name = null;
    /**
     * @ignore
     * @var array
     */
    private $attributes = [];

    /**
     * @ignore
     * @param $node_name
     * @param $node_kvps
     */
    public function __construct($node_name, $node_kvps) {
        $this->node_name = $node_name;
        foreach ($node_kvps as $k => $v) {
            $this->attributes[$k] = $v;
        }
    }

    /**
     * Returns all keys within this node.
     *
     * If the node name was something like "facebook", it would return
     * keys (an example): "api_key", "api_secret", "challenge", etc.
     *
     * @return array
     */
    public function getKeys() {
        return array_keys($this->attributes);
    }

    /**
     * Allows fetch of a particular value
     *
     * @param $key
     * @return mixed
     * @throws ConfigDataNotFoundException
     */
    public function get($key) {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        else throw new ConfigDataNotFoundException("\"$key\" does not exist in node \"{$this->node_name}\"");
    }
}

/**
 * Class ConfigDataNotFoundException
 *
 * @package App\Components
 */
class ConfigDataNotFoundException extends \Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}