<?php namespace App\Components\SocialiteEx;

use SocialiteProviders\Manager\SocialiteWasCalled;

class FacebookProviderExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param SocialiteProviders\Manager\SocialiteWasCalled $socialiteCalled
     */
    public function handle(SocialiteWasCalled $socialiteCalled)
    {
        $socialiteCalled->extendSocialite( 'facebook', __NAMESPACE__.'\FacebookProviderEx' );
    }
}

