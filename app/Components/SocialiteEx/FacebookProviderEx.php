<?php namespace App\Components\SocialiteEx;

use Laravel\Socialite\Two\FacebookProvider;
    
class FacebookProviderEx extends FacebookProvider
{
    /**
     * Get the scopes of the requested access.
     *
     * @return array  $scopes
     */
    public function get_scopes()
    {
        return $this->scopes;
    }
    
    /**
     * Get the GET parameters for the code request.
     * add support for facebook specific codes, like auth_type, etc.
     *
     * @param  string|null  $state
     * @return array
     */
    protected function getCodeFields($state = null)
    {        
        $fields = parent::getCodeFields($state);
        
        if ($this->usesAuthType()){
            $fields[ 'auth_type' ] = $this->getAuthType();
        }
        
        return $fields;
    }
    
    // ............................................................... auth type
    
    /**
     * Force a reauthentication by user
     * and other facebook specific options..
     *
     * @var bool
     */
    protected $auth_type = false;
    
    /**
     * Set an auth_type hint for provider
     * Are we on git?
     * @param  string  $type
     * @return $this
     */
    public function authType( $type )
    {
        $this->auth_type = $type;
        return $this;
    }
    
    /**
     * Get the auth type string of the requested access.
     *
     * @return string
     */
    public function getAuthType()
    {
        return $this->auth_type;
    }
    
    /**
     * Indicates if auth type exists for requested access.
     *
     * @return bool
     */
    
    public function usesAuthType()
    {
        return $this->getAuthType() !== false;
    }
}