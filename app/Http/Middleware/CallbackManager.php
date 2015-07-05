<?php

namespace App\Http\Middleware;

use App\Components\ConfigManager;
use Illuminate\Http\Request;

use \Log;

class CallbackManager {

    private $config = null;

    public function __construct() {
        $this->config = ConfigManager::getInstance();
    }

    public function emit(Request $request, $namespace, $payload) {
        
        foreach ($this->config->getProviders() as $provider) {
            
            Log::info("[$provider] callback ns: $namespace");
            
            $class_prefix = ucfirst( $provider );
            $class_name = "\\App\\Http\\Middleware\\CallbackSubscribers\\{$class_prefix}CallbackSubscriber";
            $class = new $class_name();
            
            if ( $class->inspect( $request, $namespace ) ) {
                 Log::info("provider $provider interested, accepting callback request as own");
                 return $class->accept ( $request, $payload   );
            }
        }
        
        return (object) [ 'data' => 'unsupported namespace ('.$namespace.')',
                          'err'  => 200 ];
    }
}