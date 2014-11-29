<?php

namespace Dijky\Silex\Provider;

use \Silex\Application;
use \Silex\ServiceProviderInterface;
use \Spot;
use \Pimple;

class SpotServiceProvider implements ServiceProviderInterface
{
	public function register(Application $app)
	{
		$app['spot'] = $app->share(function() use ($app) {
			return new Spot\Locator($app['spot.config']);
		});
		
		$app['spot.config'] = $app->share(function() use ($app) {
			$config = new Spot\Config();
			
			$connections = $app['spot.connections'];
			
			// foreach does not work with a Pimple container
			// like the one exposed by DoctrineServiceProvider
			if($connections instanceof Pimple) {
				$keys = $connections->keys();
				foreach($keys as $key) {
					$config->addConnection($key, $connections[$key]);
				}
			} else {
				foreach($connections as $key => $connection) {
					$config->addConnection($key, $connection);
				}
			}
			
			return $config;
		});
	}
	
	public function boot(Application $app)
	{}
}
