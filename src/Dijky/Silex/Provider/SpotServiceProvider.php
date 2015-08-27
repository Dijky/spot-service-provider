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
		$app['spot.connections.default'] = null;

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
			} else {
				$keys = array_keys($connections);
			}
			
			foreach($keys as $key) {
				if(isset($app['spot.connections.default']) && $key === $app['spot.connections.default']) {
					$default = true;
				} else {
					$default = false;
				}
				$config->addConnection($key, $connections[$key], $default);
			}
			
			return $config;
		});
	}
	
	public function boot(Application $app)
	{
        //create a spot instance for every connection
        $app['spots'] = function() use ($app) {
            $spots = new Pimple();

			$connections = $app['spot.connections'];
			if($connections instanceof Pimple) {
				$keys = $connections->keys();
			} else {
				$keys = array_keys($connections);
			}
            foreach ($keys as $key){
                $config = new Spot\Config();
                $config->addConnection('con', $connections[$key], true);
                $spots[$key] = new Spot\Locator($config);
            }
            return $spots;
        };
    }
}
