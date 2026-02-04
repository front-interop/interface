# Research

Front-Interop is based on research into the following projects that provide
front controller functionality:

- [Aura](https://github.com/auraphp/Aura.Web_Project/) (aura)
- [BEAR.Sunday](https://github.com/bearsunday/BEAR.Sunday/) (bear)
- [Cake](https://github.com/cakephp/app/) (cakephp)
- [Fat-Free Framework](https://github.com/bcosca/fatfree/) (fatfree)
- [FlightPHP](https://github.com/flightphp/skeleton/) (flightphp)
- [FuelPHP](https://github.com/fuel/fuel/) (fuelphp)
- [Joomla](https://github.com/joomla/joomla-cms/) (joomla)
- [Klein](https://github.com/klein/klein.php/) (klein)
- [Kohana](https://github.com/kohana/kohana/) (kohana)
- [Laminas MVC](https://github.com/laminas/laminas-mvc-skeleton/) (laminas)
- [Laravel](https://github.com/laravel/laravel/) (laravel)
- [LightMVC](https://github.com/lightmvc/lightmvcskel/) (lightmvc)
- [Lithium](https://github.com/UnionOfRAD/framework/) (lithium)
- [Mezzio](https://github.com/mezzio/mezzio-skeleton/) (mezzio)
- [Nette](https://github.com/nette-examples/quickstart/) (nette)
- [Phalcon](https://github.com/phalcon/tutorial/) (phalcon)
- [PHPixie](https://github.com/dracony/PHPixie-Sample-App/) (pixie)
- [Silex](https://github.com/silexphp/Silex) (silex)
- [Slim](https://github.com/slimphp/Slim-Skeleton/) (slim)
- [Symfony](https://github.com/symfony/demo/) (symfony)
- [Tempest](https://github.com/tempestphp/tempest-framework/) (tempest)
- [Yii](https://github.com/yiisoft/demo/) (yii)

The above links are to the respective skeleton, demo, or example repositories
or documentation showing how to bootstrap the system and then invoke the front
controller.

The following projects were considered but eventually excluded:

- [Code Igniter](https://github.com/bcit-ci/CodeIgniter/) has no discernible
  front controller (other than its bootstrapping scripts themselves).

## Front Controller Script

These are the scripts in each project bootstrap that invoke the front controller.

|           | Front Controller Script                                                                                  |
| --------- | -------------------------------------------------------------------------------------------------------- |
| aura      | [`web/index.php`](https://github.com/auraphp/Aura.Web_Project/blob/master/web/index.php)                 |
| bear      | [`public/index.php`](https://github.com/bearsunday/BEAR.Sunday/blob/1.x/demo/public/index.php)           |
| cakephp   | [`webroot/index.php`](https://github.com/cakephp/app/blob/5.x/webroot/index.php)                         |
| fatfree   | [`index.php`](https://github.com/bcosca/fatfree/blob/master/index.php)                                   |
| flightphp | [`app/config/bootstrap.php`](https://github.com/flightphp/skeleton/blob/master/app/config/bootstrap.php) |
| fuelphp   | [`public/index.php`](https://github.com/fuel/fuel/blob/1.8/master/public/index.php)                      |
| joomla    | [`includes/app.php`](https://github.com/joomla/joomla-cms/blob/5.4-dev/includes/app.php)                 |
| klein     | [(documentation)](https://github.com/klein/klein.php?tab=readme-ov-file#example)                           |
| kohana    | [`index.php`](https://github.com/kohana/kohana/blob/3.3/master/index.php)                                |
| laminas   | [`public/index.php`](https://github.com/laminas/laminas-mvc-skeleton/blob/2.5.x/public/index.php)        |
| laravel   | [`public/index.php`](https://github.com/laravel/laravel/blob/12.x/public/index.php)                      |
| lightmvc  | [`public/index.php`](https://github.com/lightmvc/lightmvcskel/blob/master/public/index.php)              |
| lithium   | [`webroot/index.php`](https://github.com/UnionOfRAD/framework/blob/1.2/webroot/index.php)                |
| mezzio    | [`public/index.php`](https://github.com/mezzio/mezzio-skeleton/blob/3.18.x/public/index.php)            |
| nette     | [`www/index.php`](https://github.com/nette-examples/quickstart/blob/v4.0/www/index.php)              |
| phalcon   | [`public/index.php`](https://github.com/phalcon/tutorial/blob/master/public/index.php)                  |
| phpixie   | [`web/index.php`](https://github.com/dracony/PHPixie-Sample-App/blob/master/web/index.php)           |
| silex     | [(documentation)](https://github.com/silexphp/Silex?tab=readme-ov-file#silex-a-simple-web-framework) |
| slim      | [`public/index.php`](https://github.com/slimphp/Slim-Skeleton/blob/main/public/index.php)               |
| symfony   | [(template)](https://github.com/symfony/runtime/blob/8.1/Internal/autoload_runtime.template)    |
| tempest   | [`public/index.php`](https://github.com/tempestphp/tempest-framework/blob/3.x/public/index.php)         |
| yii       | [`public/index.php`](https://github.com/yiisoft/demo/blob/master/blog/public/index.php)                 |

Note that these are not necessarily the entry point scripts, and that some
projects use multiple files for their bootstrap:

- The `flightphp` entry point is [public/index.php](https://github.com/flightphp/skeleton/blob/master/public/index.php),
  but it delegates directly to the above bootstrap script, which itself includes
  other scripts before running the front controller.

- The `joomla` entry point is [index.php](https://github.com/joomla/joomla-cms/blob/5.4-dev/index.php);
  other bootstrap files are [includes/framework.php](https://github.com/joomla/joomla-cms/blob/5.4-dev/includes/framework.php)
  and [libraries/bootstrap.php](https://github.com/joomla/joomla-cms/blob/5.4-dev/libraries/bootstrap.php).

- The `lightmvc` project also includes [config/error_handling.config.php](https://github.com/lightmvc/lightmvcskel/blob/master/config/error_handling.config.php)
  as part of its bootstrap.

- The `lithium` project also has [config/bootstrap.php](https://github.com/UnionOfRAD/framework/blob/1.2/config/bootstrap.php)
  as part of its bootstrap, which in turn calls several other scripts.

- The `symfony` entry point is [public/index.php](https://github.com/symfony/demo/blob/main/public/index.php)
  but its operation is not obvious. Please review <https://symfony.com/doc/current/components/runtime.html>
  to see how it actually works.

## Front Controller Call

The following table shows the actual front controller invocation calls for each
project.

|           | Static? | Front Controller Call |
| --------- | ------- | --------------------- |
| aura      |         | `$kernel();` |
| bear      |         | `$app->resource->{$request->method}->uri($request->path)($request->query);` |
| cakephp   |         | `$server->run();` |
| fatfree   |         | `$f3->run();` |
| flightphp |         | `$app->start();` |
| fuelphp   | x       | `Request::forge()->execute();` |
| joomla    |         | `$app->execute();` |
| klein     |         | `$klein->dispatch();` |
| kohana    | x       | `Request::factory(TRUE, array(), FALSE)->execute();` |
| laminas   |         | `$app->run();` |
| laravel   |         | `$app->handleRequest(Request::capture());` |
| lightmvc  |         | `$app->initialize($baseConfig)->run();` |
| lithium   | x       | `lithium\action\Dispatcher::run(/* ... */);` |
| mezzio    |         | `$app->run();` |
| nette     |         | `$application->run();` |
| phalcon   |         | `$application->handle($_SERVER["REQUEST_URI"]);` |
| phpixie   |         | `$pixie->bootstrap($root)->http_request()->execute();` |
| silex     |         | `$app->run();` |
| slim      |         | `$app->handle($request);` |
| symfony   |         | `$runtime->getRunner($app)->run();` |
| tempest   | x       | `HttpApplication::boot(/* ... */)->run();` |
| yii       |         | `$runner->run();` |

## Front Controller Method

The projects each use some variation on this verb for the front controller main method:

|           | `dispatch()` | `execute()` | `handle()` | `__invoke()` | `run()` | `start()` |
| --------- | ------------ | ----------- | ---------- | ------------ | ------- | --------- |
| aura      |              |             |            | x            |         |           |
| bear      |              |             |            | x            |         |           |
| cakephp   |              |             |            |              | x       |           |
| fatfree   |              |             |            |              | x       |           |
| flightphp |              |             |            |              |         | x         |
| fuelphp   |              | x           |            |              |         |           |
| joomla    |              | x           |            |              |         |           |
| klein     | x            |             |            |              |         |           |
| kohana    |              | x           |            |              |         |           |
| laminas   |              |             |            |              | x       |           |
| laravel   |              |             | x          |              |         |           |
| lightmvc  |              |             |            |              | x       |           |
| lithium   |              |             |            |              | x       |           |
| mezzio    |              |             |            |              | x       |           |
| nette     |              |             |            |              | x       |           |
| phalcon   |              |             | x          |              |         |           |
| phpixie   |              | x           |            |              |         |           |
| silex     |              |             |            |              | x       |           |
| slim      |              |             | x          |              |         |           |
| symfony   |              |             |            |              | x       |           |
| tempest   |              |             |            |              | x       |           |
| yii       |              |             |            |              | x       |           |

The term `run` is a clear majority at 11 uses; all others together number only 7.

## Front Controller Return

The project front controllers most often return nothing at all; some return a
response to be sent by the bootstrap script, and one (`symfony`) returns an
integer exit code.

|           | Nothing   | Response | `int` |
| --------- | --------- | -------- | ----- |
| aura      | x         |          |       |
| bear      |           | x        |       |
| cakephp   |           | x        |       |
| fatfree   | x         |          |       |
| flightphp | x         |          |       |
| fuelphp   |           | x        |       |
| joomla    | x         |          |       |
| klein     | x         |          |       |
| kohana    |           | x        |       |
| laminas   | x         |          |       |
| laravel   | x         |          |       |
| lightmvc  | x         |          |       |
| lithium   | x         |          |       |
| mezzio    | x         |          |       |
| nette     | x         |          |       |
| phalcon   |           | x        |       |
| phpixie   |           | x        |       |
| silex     | x         |          |       |
| slim      |           | x        |       |
| symfony   |           |          | x     |
| tempest   | x         |          |       |
| yii       | x         |          |       |
