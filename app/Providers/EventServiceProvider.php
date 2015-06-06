<?php namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
    
        // Using SocialiteProviders http://socialiteproviders.github.io/
    
        'SocialiteProviders\Manager\SocialiteWasCalled' => [
        'SocialiteProviders\LinkedIn\LinkedInExtendSocialite@handle',
//        'SocialiteProviders\Twitter\TwitterExtendSocialite@handle',
//        'SocialiteProviders\Facebook\FacebookExtendSocialite@handle',
//        'SocialiteProviders\Google\GoogleExtendSocialite@handle',
//        'SocialiteProviders\Instagram\InstagramExtendSocialite@handle',
//        'SocialiteProviders\YouTube\YouTubeExtendSocialite@handle',
        ],
    
        'event.name' => [
			'EventListener',
		],
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		//
	}

}
