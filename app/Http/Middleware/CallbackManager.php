<?php

namespace App\Http\Middleware;

use App\Components\ConfigManager;
use Illuminate\Http\Request;

class CallbackManager {

    private $config = null;

    public function __construct() {
        $this->config = ConfigManager::getInstance();
    }

    public function distribute(Request $request, $payload) {
        foreach ($this->config->getProviders() as $provider) {
            $class_prefix = ucfirst($provider);
            $class_name = "\\App\\Http\\Middleware\\CallbackSubscribers\\{$class_prefix}CallbackSubscriber";
            $class = new $class_name();
            if ($class->inspect($request)) {
                $class->accept($payload);
                break;
            }
        }
    }
}