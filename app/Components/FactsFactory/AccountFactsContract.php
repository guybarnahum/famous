<?php namespace App\Components\FactsFactory;
    
interface AccountFactsContract
{
    public function set_output( $output );
    public function process();
}