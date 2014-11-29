spot-service-provider
=====================

`SpotServiceProvider` allows you to easily integrate the [Spot ORM](http://phpdatamapper.com/) with Silex.

Installation
-

Simply require the composer package in your application:

    composer require dijky/spot-service-provider

or in your `composer.json`:

    {
      ...
      "require": {
        "dijky/spot-service-provider": "dev-master"
      }
    }

If you don't use Composer, clone the Git repository or download the zipball.  
You will have to do autoloading yourself then.

Usage
-

Register the `SpotServiceProvider` in your application bootstrap file:

    $app->register(new Dijky\Silex\Provider\SpotServiceProvider(), array(
      'spot.connections' => array(
        'website' => '<dsn>',
        'forum' => '<dsn>'
      )
    ));

You can add as many connections as you like. 

The service provider also takes connections already configured for `DoctrineServiceProvider`:

    $app['spot.connections'] = function() use ($app) {
      return $app['dbs'];
    };

Notice the closure? This way, the `dbs` service will only be accessed (and initialized) as needed.  
You can still override or extend it later on in your bootstrap code.

The first connection in `$app['spot.connections']` will be set as default.  
The default connection can also be set to connection `abc` with

    $app['spot.connections.default'] = 'abc';


Services
-

`SpotServiceProvider` exposes the following services:

 - **spot**: The `Spot\Locator` instance to use the Spot ORM.
 - **spot.config**: The `Spot\Config` instance to configure the ****spot**** service.
 
It takes the following configuration values:

 - **spot.connections**: Set this to a key-value array (or Pimple container) with connection names as keys and connection strings (DSNs) or instances of `Doctrine\DBAL\Connection` as values.
 - **spot.connections.default** (optional): Set this to the connection name that should be set as default. Unset with `null`.
 
**Notice:** once the `spot` service is first accessed, changing `spot.config`, `spot.connections`, or `spot.connections.default` will have no effect on it.
