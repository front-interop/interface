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
- [LeafPHP](https://github.com/leafsphp/leafMVC) (leafphp)
- [LightMVC](https://github.com/lightmvc/lightmvcskel/) (lightmvc)
- [Lithium](https://github.com/UnionOfRAD/framework/) (lithium)
- [Mezzio](https://github.com/mezzio/mezzio-skeleton/) (mezzio)
- [Nette](https://github.com/nette-examples/quickstart/) (nette)
- [Phalcon](https://github.com/phalcon/tutorial/) (phalcon)
- [PHPixie](https://github.com/dracony/PHPixie-Sample-App/) (phpixie)
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

These are the scripts in each project bootstrap that invoke the front
controller. (They are not necessarily entry-point scripts; some projects use
multiple files for bootstrapping.)

|               | Front Controller Script                                                                                  |
| ------------- | -------------------------------------------------------------------------------------------------------- |
| aura          | [`web/index.php`](https://github.com/auraphp/Aura.Web_Project/blob/master/web/index.php)                 |
| bear          | [`public/index.php`](https://github.com/bearsunday/BEAR.Sunday/blob/1.x/demo/public/index.php)           |
| cakephp       | [`webroot/index.php`](https://github.com/cakephp/app/blob/5.x/webroot/index.php)                         |
| fatfree       | [`index.php`](https://github.com/bcosca/fatfree/blob/master/index.php)                                   |
| flightphp (1) | [`app/config/bootstrap.php`](https://github.com/flightphp/skeleton/blob/master/app/config/bootstrap.php) |
| fuelphp (2)   | [`public/index.php`](https://github.com/fuel/fuel/blob/1.8/master/public/index.php)                      |
| joomla (3)    | [`includes/app.php`](https://github.com/joomla/joomla-cms/blob/5.4-dev/includes/app.php)                 |
| klein         | [(documentation)](https://github.com/klein/klein.php?tab=readme-ov-file#example)                         |
| kohana (4)    | [`index.php`](https://github.com/kohana/kohana/blob/3.3/master/index.php)                                |
| laminas       | [`public/index.php`](https://github.com/laminas/laminas-mvc-skeleton/blob/2.5.x/public/index.php)        |
| laravel       | [`public/index.php`](https://github.com/laravel/laravel/blob/12.x/public/index.php)                      |
| leafphp       | [`public/index.php`](https://github.com/leafsphp/leafMVC/blob/v4.x/public/index.php)                     |
| lightmvc (5)  | [`public/index.php`](https://github.com/lightmvc/lightmvcskel/blob/master/public/index.php)              |
| lithium (6)   | [`webroot/index.php`](https://github.com/UnionOfRAD/framework/blob/1.2/webroot/index.php)                |
| mezzio        | [`public/index.php`](https://github.com/mezzio/mezzio-skeleton/blob/3.18.x/public/index.php)             |
| nette         | [`www/index.php`](https://github.com/nette-examples/quickstart/blob/v4.0/www/index.php)                  |
| phalcon       | [`public/index.php`](https://github.com/phalcon/tutorial/blob/master/public/index.php)                   |
| phpixie       | [`web/index.php`](https://github.com/dracony/PHPixie-Sample-App/blob/master/web/index.php)               |
| silex         | [(documentation)](https://github.com/silexphp/Silex?tab=readme-ov-file#silex-a-simple-web-framework)     |
| slim          | [`public/index.php`](https://github.com/slimphp/Slim-Skeleton/blob/main/public/index.php)                |
| symfony (7)   | [(template)](https://github.com/symfony/runtime/blob/8.1/Internal/autoload_runtime.template)             |
| tempest       | [`public/index.php`](https://github.com/tempestphp/tempest-framework/blob/3.x/public/index.php)          |
| yii           | [`public/index.php`](https://github.com/yiisoft/demo/blob/master/blog/public/index.php)                  |

Notes:

(1) The `flightphp` entry point is [public/index.php](https://github.com/flightphp/skeleton/blob/master/public/index.php),
but it delegates directly to the above bootstrap script, which itself includes
other scripts before running the front controller.

(2) The `fuelphp` URL above uses the branch name `1.8/master`. The slash in
the branch name is literal, not a path separator or a typo.

(3) The `joomla` entry point is [index.php](https://github.com/joomla/joomla-cms/blob/5.4-dev/index.php);
other bootstrap files are [includes/framework.php](https://github.com/joomla/joomla-cms/blob/5.4-dev/includes/framework.php)
and [libraries/bootstrap.php](https://github.com/joomla/joomla-cms/blob/5.4-dev/libraries/bootstrap.php).

(4) The `kohana` URL above uses the branch name `3.3/master`, in the same
literal-slash style as `fuelphp`.

(5) The `lightmvc` project also includes [config/error_handling.config.php](https://github.com/lightmvc/lightmvcskel/blob/master/config/error_handling.config.php)
as part of its bootstrap.

(6) The `lithium` project also has [config/bootstrap.php](https://github.com/UnionOfRAD/framework/blob/1.2/config/bootstrap.php)
as part of its bootstrap, which in turn calls several other scripts. The
`lithium\action\Dispatcher` class itself lives in the separate library
repository at <https://github.com/UnionOfRAD/lithium>; the URL above points
to `UnionOfRAD/framework`, which is the application skeleton.

(7) The `symfony` entry point is [public/index.php](https://github.com/symfony/demo/blob/main/public/index.php)
but its operation is not obvious. Please review <https://symfony.com/doc/current/components/runtime.html>
to see how it actually works.

## Front Controller Call

The following table shows the front controller invocation calls for each
project. Calls below show the front-controller invocation only; emit chains,
return-value handling, and surrounding scaffolding are omitted for brevity.

The `Static?` column indicates if the front controller method call itself is static.

|             | Static? | Front Controller Call |
| ----------- | ------- | --------------------- |
| aura        |         | `$kernel();` |
| bear        |         | `$app->resource->{$request->method}->uri($request->path)($request->query);` |
| cakephp     |         | `$server->run();` |
| fatfree     |         | `$f3->run();` |
| flightphp   |         | `$app->start();` |
| fuelphp (1) |         | `Request::forge()->execute();` |
| joomla      |         | `$app->execute();` |
| klein       |         | `$klein->dispatch();` |
| kohana      |         | `Request::factory(TRUE, array(), FALSE)->execute();` |
| laminas     |         | `$app->run();` |
| laravel     |         | `$app->handleRequest(Request::capture());` |
| leafphp     | x       | `\Leaf\Core::runApplication();` |
| lightmvc    |         | `$app->initialize($baseConfig)->run();` |
| lithium     | x       | `lithium\action\Dispatcher::run(/* ... */);` |
| mezzio      |         | `$app->run();` |
| nette       |         | `$application->run();` |
| phalcon     |         | `$application->handle($_SERVER["REQUEST_URI"]);` |
| phpixie     |         | `$pixie->bootstrap($root)->http_request()->execute();` |
| silex       |         | `$app->run();` |
| slim        |         | `$app->handle($request);` |
| symfony     |         | `$runtime->getRunner($app)->run();` |
| tempest     |         | `HttpApplication::boot(/* ... */)->run();` |
| yii         |         | `$runner->run();` |

Notes:

(1) The `fuelphp` call is wrapped in a `$routerequest` closure, which is then
invoked as `$response = $routerequest();`.

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
| leafphp   |              |             |            |              | x       |           |
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

The term `run` is a clear majority at 12 uses; all other variations together number only 11.

## Front Controller Return

The project front controllers most often return nothing at all; some return a
response to be sent by the bootstrap script, and one (`symfony`) returns an
integer exit code. Note that just because the front controller returns something
does not mean the bootstrap code actually does anything with that value.

|             | `null`/`void` | Response | `int` | `never` | other |
| ----------- | ------------- | -------- | ----- | ------- | ----- |
| aura        | x             |          |       |         |       |
| bear        |               | x        |       |         |       |
| cakephp     |               | x        |       |         |       |
| fatfree (1) |               |          |       |         | x     |
| flightphp   | x             |          |       |         |       |
| fuelphp (2) |               |          |       |         | x     |
| joomla      | x             |          |       |         |       |
| klein       | x             |          |       |         |       |
| kohana      |               | x        |       |         |       |
| laminas (3) |               |          |       |         | x     |
| laravel     | x             |          |       |         |       |
| leafphp     | x             |          |       |         |       |
| lightmvc    | x             |          |       |         |       |
| lithium (4) |               | x        |       |         | x     |
| mezzio      | x             |          |       |         |       |
| nette       | x             |          |       |         |       |
| phalcon (5) |               | x        |       |         | x     |
| phpixie     |               | x        |       |         |       |
| silex       | x             |          |       |         |       |
| slim        |               | x        |       |         |       |
| symfony (6) |               |          | x     |         |       |
| tempest     |               |          |       | x       |       |
| yii         | x             |          |       |         |       |

Notes:

(1) The `fatfree` declared return is `mixed`; `Base::run()` returns the matched route handler's value, or `FALSE` on no match.

(2) The `fuelphp` method `Request::execute()` returns `$this` (the `Request`); the caller obtains the response via `->response`.

(3) The `laminas` method `Application::run()` returns `$this` (fluent
self-return); no declared return type.

(4) The `lithium` method `Dispatcher::run()` is declared as
`string|\lithium\action\Response`; the bootstrap `echo`s the result.

(5) The `phalcon` method `Application::handle()` returns `ResponseInterface|bool`.

(6) In `symfony`, the returned `int` actually comes from
`Symfony\Component\Runtime\RunnerInterface::run()`, not from the application or
kernel; `HttpKernel::handle()` itself returns a `Response`. The Runtime template
wraps the kernel and converts the response into an exit code.
