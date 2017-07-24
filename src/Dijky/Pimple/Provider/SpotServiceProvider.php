<?php

namespace Dijky\Pimple\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

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

	public function register(Container $c)
	{
		$servicePrefix = $this->servicePrefix;

		$c[$servicePrefix . 'spot.connections.default'] = null;

		$c[$servicePrefix . 'spot'] = function() use ($c, $servicePrefix) {
			return new \Spot\Locator($c[$servicePrefix . 'spot.config']);
		};

		$c[$servicePrefix . 'spot.config'] = function() use ($c, $servicePrefix) {
			$config = new \Spot\Config();

			if (isset($c[$servicePrefix . 'spot.connections']))
			{
				$connections = $c[$servicePrefix . 'spot.connections'];
			}
			else
			{
				$connections = $c['spot.connections'];
			}

			// foreach does not work with a Pimple container
			if($connections instanceof Container) {
				$keys = $connections->keys();
			} else {
				$keys = array_keys($connections);
			}

			foreach($keys as $key) {
				if(isset($c[$servicePrefix . 'spot.connections.default'])
					&& $key === $c[$servicePrefix . 'spot.connections.default']) {
					$default = true;
				} else {
					$default = false;
				}
				$config->addConnection($key, $connections[$key], $default);
			}

			return $config;
		};
	}
}
