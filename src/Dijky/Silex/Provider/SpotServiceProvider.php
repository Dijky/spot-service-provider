<?php

namespace Dijky\Silex\Provider;

use \Silex\Application;
use \Silex\ServiceProviderInterface;
use \Spot;
use \Pimple;

class SpotServiceProvider implements ServiceProviderInterface
{
	protected $servicePrefix;
	
	public function __construct($servicePrefix = null)
	{
		$servicePrefix = (string)$servicePrefix;
		
		if (strlen($servicePrefix)) {
			$servicePrefix .= '.';
		} else {
			$servicePrefix = '';
		}
		
		$this->servicePrefix = $servicePrefix;
	}
	
	public function register(Application $app)
	{
		$servicePrefix = $this->servicePrefix;
		
		$app[$servicePrefix . 'spot.connections.default'] = null;
	
		$app[$servicePrefix . 'spot'] = $app->share(function() use ($app, $servicePrefix) {
			return new Spot\Locator($app[$servicePrefix . 'spot.config']);
		});
		
		$app[$servicePrefix . 'spot.config'] = $app->share(function() use ($app, $servicePrefix) {
			$config = new Spot\Config();
			
			if (isset($app[$servicePrefix . 'spot.connections']))
			{
				$connections = $app[$servicePrefix . 'spot.connections'];
			}
			else
			{
				$connections = $app['spot.connections'];
			}
			
			// foreach does not work with a Pimple container
			// like the one exposed by DoctrineServiceProvider
			if($connections instanceof Pimple) {
				$keys = $connections->keys();
			} else {
				$keys = array_keys($connections);
			}
			
			foreach($keys as $key) {
				if(isset($app[$servicePrefix . 'spot.connections.default'])
					&& $key === $app[$servicePrefix . 'spot.connections.default']) {
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
	{}
}
