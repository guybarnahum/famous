<?php namespace App\Components\SocialiteEx;
    
use SocialiteProviders\Manager\SocialiteWasCalled;

class GoogleProviderExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param SocialiteProviders\Manager\SocialiteWasCalled $socialiteCalled
     */
    public function handle(SocialiteWasCalled $socialiteCalled)
    {
        $socialiteCalled->extendSocialite( 'google', __NAMESPACE__.'\GoogleProviderEx' );
    }
}
    
