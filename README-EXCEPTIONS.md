# Front Controller Exception Handling

For each project, this section examines how exceptions and errors are handled
at and around the front controller, specifically the bootstrap script, the
front controller class hosting the invocation, and (where relevant) the
immediate framework error infrastructure they delegate to. Deeper framework
internals are out of scope.

## Project Narratives

**aura.** The [`web/index.php`](https://github.com/auraphp/Aura.Web_Project/blob/master/web/index.php)
bootstrap does nothing about errors or exceptions: no
`set_exception_handler()`, no `set_error_handler()`, no
`register_shutdown_function()`, no `try`/`catch`, no ini changes. It just
builds the kernel and invokes it. The
[`WebKernel::__invoke()`](https://github.com/auraphp/Aura.Web_Kernel/blob/master/src/WebKernel.php)
it calls runs the router, dispatcher, and response sender in sequence, also
without any exception handling. Anything thrown bubbles uncaught out of the
bootstrap to PHP's default handler.

**bear (BEAR.Sunday).** The
[`public/index.php`](https://github.com/bearsunday/BEAR.Sunday/blob/1.x/demo/public/index.php)
bootstrap wraps the front-controller invocation in a `try` block with three
`catch` arms: `ResourceNotFoundException` → `http_response_code(404)` and emit
`"Not found"`; `BadRequestException` → `http_response_code(400)` and emit
`"Bad request"`; `Throwable` → `http_response_code(500)`, emit
`"Server error"`, `error_log()` the exception, and `exit(1)`. No
`set_exception_handler()`, `set_error_handler()`, or
`register_shutdown_function()` is installed. The
[`BEAR\Resource\Resource`](https://github.com/bearsunday/BEAR.Resource/blob/1.x/src/Resource.php)
class hosting the call does no catching of its own; all exception handling
lives in the bootstrap.

**cakephp.** The
[`webroot/index.php`](https://github.com/cakephp/app/blob/5.x/webroot/index.php)
bootstrap installs no error/exception handlers, sets no ini directives, and
contains no `try`/`catch`; it just builds a `Server` around an
`App\Application` and runs `$server->emit($server->run())`. The
[`Cake\Http\Server::run()`](https://github.com/cakephp/cakephp/blob/5.x/src/Http/Server.php)
method also does no catching; it builds and dispatches the middleware queue.
The application's
[`middleware()`](https://github.com/cakephp/app/blob/5.x/src/Application.php)
registers `ErrorHandlerMiddleware` as the *outermost* layer of the queue with
the comment "Catch any exceptions in the lower layers, and make an error
page/response"; that middleware is where exceptions thrown by lower layers
are caught and converted to error responses. No `set_exception_handler()`,
`set_error_handler()`, or `register_shutdown_function()` is called within the
bootstrap or the front controller class.

**fatfree (Fat-Free Framework).** The
[`index.php`](https://github.com/bcosca/fatfree/blob/master/index.php)
bootstrap installs no error/exception handlers itself, sets only `DEBUG=1` on
the framework instance, and contains no `try`/`catch` around `$f3->run()`.
However,
[`Base::instance()`](https://github.com/bcosca/fatfree-core/blob/master/base.php),
the framework class hosting `run()`, registers three handlers in its
constructor: a `set_exception_handler()` that funnels the throwable into
`Base::error(500, ...)` with file, line, and trace; a `set_error_handler()`
that, for errors matching `error_reporting()`, also funnels into
`Base::error(500, ...)`; and `register_shutdown_function([$this, 'unload'],
getcwd())` for framework cleanup. `Base::run()` itself has no `try`/`catch`;
error/exception handling is entirely via the auto-installed handlers.

**flightphp.** The entry point
[`public/index.php`](https://github.com/flightphp/skeleton/blob/master/public/index.php)
does nothing but `require` the bootstrap; the
[`app/config/bootstrap.php`](https://github.com/flightphp/skeleton/blob/master/app/config/bootstrap.php)
installs no exception/error handlers and contains no `try`/`catch` around
`$app->start()` (though it does call `Flight::halt(500, ...)` if `config.php`
is missing). One hop into the framework,
[`flight\Engine::init()`](https://github.com/flightphp/core/blob/master/flight/Engine.php)
conditionally, guarded by the `flight.handle_errors` config, registers
`set_error_handler` and `set_exception_handler` pointing at the engine's own
`handleError`/`handleException` methods. `handleException()` optionally
`error_log()`s the throwable, then delegates to `_error()`, which fires a
`flight.error` event and sends a 500 response (falling back to `exit($msg)`
if the response itself throws). `_start()` (the implementation behind
`start()`) has no `try`/`catch` of its own. No `register_shutdown_function()`
is registered.

**fuelphp.** The
[`public/index.php`](https://github.com/fuel/fuel/blob/1.8/master/public/index.php)
bootstrap sets `error_reporting(-1)` and `ini_set('display_errors', 1)` but
installs no `set_exception_handler()`, `set_error_handler()`, or
`register_shutdown_function()`. It wraps the front-controller invocation
(a `$routerequest()` closure that ultimately calls
`Request::forge()->execute()`) in a `try` block with four specific catches:
`HttpBadRequestException` → `_400_` route; `HttpNoAccessException` → `_403_`
route; `HttpNotFoundException` → `_404_` route; `HttpServerErrorException`
→ `_500_` route. Each is dispatched as another `$routerequest()` call to the
corresponding error-route closure. There is **no** general `Throwable` (or
`Exception`) catch; anything outside those four types bubbles uncaught out
of the bootstrap. One hop into the framework,
[`Fuel\Core\Request::execute()`](https://github.com/fuel/core/blob/1.8/master/classes/request.php)
has its own `try`/`catch (\Exception $e)`, but only to reset request state
and restore language config before rethrowing; it does not absorb
exceptions.

**joomla.** Joomla's bootstrap is split across multiple files: the entry
point is
[`index.php`](https://github.com/joomla/joomla-cms/blob/5.4-dev/index.php),
the framework loader is
[`includes/framework.php`](https://github.com/joomla/joomla-cms/blob/5.4-dev/includes/framework.php),
and the application orchestration is
[`includes/app.php`](https://github.com/joomla/joomla-cms/blob/5.4-dev/includes/app.php)
(which is what § *Front Controller Script* cites). The `app.php` file itself
installs no handlers and contains no `try`/`catch` around `$app->execute()`.
The heavy lifting is in `framework.php`, which sets `error_reporting()` and
`ini_set('display_errors', 1)` (multiple times, conditional on debug/site
mode), calls `set_error_handler()` for `E_USER_DEPRECATED` (Joomla's own
deprecation handler), and configures a Joomla `$errorHandler` whose
`setExceptionHandler()` is wired to a
`\Symfony\Component\ErrorHandler\ErrorHandler` instance with a
`renderException` callback. No `register_shutdown_function()` calls appear
in the bootstrap chain.

**klein.** Klein has no canonical bootstrap script; it's a microframework
where the user instantiates `Klein` directly and calls `$klein->dispatch()`.
The relevant exception handling lives entirely in
[`Klein\Klein::dispatch()`](https://github.com/klein/klein.php/blob/master/src/Klein/Klein.php),
which wraps route execution in a `try` block with three catch arms:
`HttpExceptionInterface` → `httpError()`; `Throwable` → `error()`; `Exception`
(PHP 5 compat) → `error()`. The `error()` method runs user-registered
callbacks (added via `onError()`) or, if none, sets HTTP status 500, cleans
output buffers, and throws `UnhandledException`. The `httpError()` method
runs callbacks added via `onHttpError()`. Klein installs no
`set_exception_handler()` or `register_shutdown_function()` globally; one
`set_error_handler()` call exists but only as a temporary trap inside
`validateRegularExpression()` for catching regex-compile errors during route
registration.

**kohana.** The
[`index.php`](https://github.com/kohana/kohana/blob/3.3/master/index.php)
bootstrap sets `error_reporting(E_ALL | E_STRICT)` but installs no
`set_exception_handler()`, `set_error_handler()`, or
`register_shutdown_function()` of its own, except in the CLI/Minion branch,
which registers `set_exception_handler(['Minion_Exception', 'handler'])`. The
HTTP branch, `echo Request::factory(TRUE, array(), FALSE)->execute()->send_headers(TRUE)->body()`,
has no `try`/`catch`. One hop in,
[`application/bootstrap.php`](https://github.com/kohana/kohana/blob/3.3/master/application/bootstrap.php)
calls `Kohana::init([...])`; the framework's own `init()` (per its documented
`errors => TRUE` default) is what wires up error/exception handling
internally. The application bootstrap itself adds no further `try`/`catch`
or handler registrations.

**laminas.** The
[`public/index.php`](https://github.com/laminas/laminas-mvc-skeleton/blob/2.5.x/public/index.php)
bootstrap installs no error/exception handlers, sets no ini directives, and
contains no `try`/`catch` around `$app->run()` (it does
`throw new RuntimeException(...)` if the `Application` class can't be loaded,
which is a pre-flight check, not exception handling). One hop into the framework,
[`Laminas\Mvc\Application::run()`](https://github.com/laminas/laminas-mvc/blob/master/src/Application.php)
also has no `try`/`catch`, no
`set_exception_handler`/`set_error_handler`/`register_shutdown_function`
calls, and no direct references to `EVENT_DISPATCH_ERROR`/`EVENT_RENDER_ERROR`.
Errors are surfaced through the event-driven dispatch (`$event->getError()`
is checked at routing/dispatch boundaries, and the framework's standard
error listeners react via `EVENT_DISPATCH_ERROR`/`EVENT_RENDER_ERROR`);
those listeners live deeper in the framework than the one-hop scope.

**laravel.** The
[`public/index.php`](https://github.com/laravel/laravel/blob/12.x/public/index.php)
bootstrap defines a `LARAVEL_START` timestamp, optionally requires a
maintenance-mode script, autoloads Composer, requires
[`bootstrap/app.php`](https://github.com/laravel/laravel/blob/12.x/bootstrap/app.php),
and calls `$app->handleRequest(Request::capture())`. No `try`/`catch`, no
`set_*_handler`, no `register_shutdown_function`, no ini changes. One hop
in, `bootstrap/app.php` builds the application via
`Application::configure(...)->withRouting(...)->withMiddleware(...)->withExceptions(function (Exceptions $exceptions) { /* empty */ })->create()`.
The skeleton's `withExceptions` closure is empty; that closure is the user's
hook for customizing exception rendering and reporting. The actual catch
infrastructure (registering handlers, converting exceptions to responses)
lives inside `Illuminate\Foundation\Application` and
`Illuminate\Foundation\Exceptions\Handler`, beyond the one-hop scope.

**leafphp (Leaf MVC).** The
[`public/index.php`](https://github.com/leafsphp/leafMVC/blob/v4.x/public/index.php)
bootstrap does `chdir`, autoloads, handles cli-server static fallback, loads
env vars via `\Leaf\Core::loadApplicationEnv()`, and calls
`\Leaf\Core::runApplication()`. No `try`/`catch`, no `set_*_handler`, no
`register_shutdown_function`, no ini changes. One hop into the framework,
[`\Leaf\Core::runApplication()`](https://github.com/leafsphp/mvc-core/blob/main/src/Core.php)
just registers controller namespaces, requires the route files, and calls
`app()->run()`, also with no `try`/`catch` or handler installations.
Whatever exception handling Leaf provides lives in the underlying
`Leaf\App::run()`, beyond the one-hop scope.

**lightmvc.** The
[`public/index.php`](https://github.com/lightmvc/lightmvcskel/blob/master/public/index.php)
bootstrap is environment-conditional. In `production`, it registers
`set_error_handler(function ...)` (a no-op suppressor for errors when
`error_reporting()` returns 0) and wraps
`$app->initialize($baseConfig)->run()` in `try`/`catch (\Throwable $e)`,
logging the exception message to `logs/log.txt` and emitting
"A critical error has occurred. Please contact your system administrator."
In non-production, it instead `require_once`s
[`config/error_handling.config.php`](https://github.com/lightmvc/lightmvcskel/blob/master/config/error_handling.config.php)
(the secondary bootstrap noted in § *Front Controller Script*) and runs the
application without any top-level `try`/`catch`. Earlier in the bootstrap,
`try`/`catch (\Throwable $exception)` wraps `$sessionManager->start()` and
`die`s with the message on failure. No `set_exception_handler()` or
`register_shutdown_function()` is registered at the bootstrap level.

**lithium.** The
[`webroot/index.php`](https://github.com/UnionOfRAD/framework/blob/1.2/webroot/index.php)
bootstrap requires
[`config/bootstrap.php`](https://github.com/UnionOfRAD/framework/blob/1.2/config/bootstrap.php)
and then `echo`s `lithium\action\Dispatcher::run(new lithium\action\Request([...]))`
with no `try`/`catch` and no handler installations. The required
`config/bootstrap.php` does not call `set_*_handler`,
`register_shutdown_function`, `ini_set`, or `error_reporting`; it loads
`bootstrap/libraries.php` and `bootstrap/action.php` (plus `cache.php` /
`console.php` per SAPI), and the line that would load
`bootstrap/errors.php` is **commented out by default**. One hop into the
framework,
[`lithium\action\Dispatcher::run()`](https://github.com/UnionOfRAD/lithium/blob/1.2/action/Dispatcher.php)
has no top-level `try`/`catch`; a small `try`/`catch (ClassNotFoundException)`
exists in `_callable()` only to rewrap as `DispatchException`. Out of the box,
lithium installs no error/exception infrastructure unless the application
uncomments the errors bootstrap.

**mezzio.** The
[`public/index.php`](https://github.com/mezzio/mezzio-skeleton/blob/3.18.x/public/index.php)
bootstrap (a self-executing closure) loads the container, builds the
`Mezzio\Application`, runs the pipeline and routes scripts, and calls
`$app->run()`. No `try`/`catch`, no `set_*_handler`, no
`register_shutdown_function`. One hop in,
[`Mezzio\Application::run()`](https://github.com/mezzio/mezzio/blob/3.20.x/src/Application.php)
is a one-liner (`$this->runner->run()`). The catch layer is in the
middleware pipeline, configured by
[`config/pipeline.php`](https://github.com/mezzio/mezzio-skeleton/blob/3.18.x/config/pipeline.php),
which pipes `ErrorHandler::class` as the first (outermost) middleware,
explicitly to "catch all Exceptions" thrown by inner middleware.

**nette.** The
[`www/index.php`](https://github.com/nette-examples/quickstart/blob/v4.0/www/index.php)
bootstrap autoloads, builds the DI container via `App\Bootstrap`, retrieves
`Nette\Application\Application` from it, and calls `$application->run()`. No
`try`/`catch`, no `set_*_handler`, no `register_shutdown_function` in the
bootstrap itself. One hop into the framework,
[`Nette\Application\Application::run()`](https://github.com/nette/application/blob/master/src/Application/Application.php)
wraps its work in a top-level `try`/`catch (\Throwable $e)`: on a caught
throwable it `sendHttpCode($e)`, fires `$this->onError` callbacks, and, if
`$this->catchExceptions` is true and an error request can be constructed
(typically via `ErrorPresenter` / `error4xxPresenter`), re-processes the
request via `processRequest($req)` to render an error page, with a nested
inner `try`/`catch (\Throwable)` to absorb any failure during error
rendering. If error-rendering is disabled or fails, the original exception
is rethrown after firing `onShutdown`.

**phalcon.** The
[`public/index.php`](https://github.com/phalcon/tutorial/blob/master/public/index.php)
bootstrap sets up DI services and autoloading, then wraps the
front-controller invocation in `try`/`catch (Exception $e)` that does
`echo "Exception: ", $e->getMessage()` on any caught exception. Note that the
catch is for `Exception`, not `Throwable`, so PHP `Error`s would not be
caught. There is no general `Throwable` arm, no `set_*_handler`, no
`register_shutdown_function`, no ini changes. The framework class hosting
the call (`Phalcon\Mvc\Application`) is implemented in the Phalcon C
extension; its source is not directly reviewable as PHP, but per its return
contract `Application::handle()` returns `ResponseInterface|bool`.

**phpixie.** The
[`web/index.php`](https://github.com/dracony/PHPixie-Sample-App/blob/master/web/index.php)
bootstrap autoloads, configures namespace mappings on the loader,
instantiates `\App\Pixie`, and calls
`$pixie->bootstrap($root)->http_request()->execute()->send_headers()->send_body()`.
No `try`/`catch`, no `set_*_handler`, no `register_shutdown_function`, no ini
changes. Whatever exception handling phpixie provides lives in the
framework's `Pixie` / request / execute chain, beyond the bootstrap and
the immediate one-hop scope.

**silex.** Silex has no bootstrap script in the traditional sense; its
[README example](https://github.com/silexphp/Silex?tab=readme-ov-file#silex-a-simple-web-framework)
shows `$app = new Silex\Application(); ... $app->run();`. The
`Silex\Application` constructor registers `ExceptionHandlerServiceProvider`,
which wires in error-handler infrastructure on top of Symfony HttpKernel.
[`Application::run()`](https://github.com/silexphp/Silex/blob/master/src/Silex/Application.php)
calls `handle()` then `send()` then `terminate()`; `handle()` itself
delegates to `$this['kernel']->handle($request, $type, $catch)` with the
standard Symfony HttpKernel `$catch = true` default, meaning HttpKernel
catches exceptions and dispatches `kernel.exception` listeners. User error
handlers are registered via Silex's `error()` method (which adds listeners
to that event). No direct `set_*_handler` or `register_shutdown_function`
calls in `Application.php` itself.

**slim.** The
[`public/index.php`](https://github.com/slimphp/Slim-Skeleton/blob/main/public/index.php)
bootstrap is one of the most explicit in the survey. It builds a DI
container, instantiates the Slim app, instantiates a custom
`App\Application\Handlers\HttpErrorHandler`, instantiates a custom
`App\Application\Handlers\ShutdownHandler`, and registers the latter via
`register_shutdown_function($shutdownHandler)`. It then calls
`$app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails)`
and sets the `HttpErrorHandler` as the middleware's default error handler,
before running `$app->handle($request)` and emitting. No `try`/`catch` is
needed at the bootstrap level; error handling is layered via the shutdown
handler (catches PHP fatal errors via the registered shutdown function) and
the error middleware (catches exceptions in the Slim middleware pipeline).
No `set_exception_handler()` or `set_error_handler()` calls are made.

**symfony.** The demo
[`public/index.php`](https://github.com/symfony/demo/blob/main/public/index.php)
is a near-empty file: it requires `vendor/autoload_runtime.php` and `return`s
a closure (`function (array $context) { return new Kernel(...); }`) for the
runtime to invoke. The actual bootstrap orchestration happens in the
[`autoload_runtime.template`](https://github.com/symfony/runtime/blob/8.1/Internal/autoload_runtime.template)
generated by `symfony/runtime`, which loads the autoloader, loads the
application closure from the entry point, validates the result, parses
runtime options, and runs `$runtime->getRunner($app)->run()`, exiting with
the returned int code. The template itself has no `try`/`catch`, no
`set_*_handler`, and no `register_shutdown_function`; exception handling is
handled by the runtime classes (e.g., `SymfonyRuntime`) and the kernel
itself (via `kernel.exception` event listeners), both beyond the one-hop
scope.

**tempest.** The
[`public/index.php`](https://github.com/tempestphp/tempest-framework/blob/3.x/public/index.php)
bootstrap autoloads, calls `HttpApplication::boot(...)->run()`, and then
`exit()`. No `try`/`catch`, no `set_*_handler`, no
`register_shutdown_function`. One hop into the framework,
[`HttpApplication::run()`](https://github.com/tempestphp/tempest-framework/blob/3.x/packages/router/src/HttpApplication.php)
gets the router, request factory, and response sender from the container,
dispatches the request, sends the response, and shuts down the kernel,
also with no `try`/`catch` or handler installations of its own. (The
framework's `Tempest::boot()` method, which `HttpApplication::boot()`
delegates to, may register handlers during container construction, but
that's beyond the one-hop scope.)

**yii.** The
[`public/index.php`](https://github.com/yiisoft/demo/blob/master/blog/public/index.php)
bootstrap handles cli-server static fallback, autoloads, instantiates
`Yiisoft\Yii\Runner\Http\HttpApplicationRunner` with the root path, debug
flag, and environment, and calls `$runner->run()`. No `try`/`catch`, no
`set_*_handler`, no `register_shutdown_function` at the bootstrap level.
One hop into the runner,
[`HttpApplicationRunner::run()`](https://github.com/yiisoft/yii-runner-http/blob/master/src/HttpApplicationRunner.php)
delegates to `runInternal()`, which: registers a temporary error handler
via `registerErrorHandler()` for early-startup failures, builds the
application, swaps in the actual `ErrorHandler` from the container, then
wraps `$application->start()` and `$application->handle($request)` in a
`try`/`catch (Throwable)`. Caught throwables are routed through an
`ErrorCatcher` middleware (obtained from the container) to produce the
response. A `finally` block runs `afterEmit()` and `shutdown()`. This is
one of the most thorough exception-handling patterns in the survey.

## Cross-cutting patterns

**Architectural locus varies enormously.** Across the 23 projects, the
place where catching happens is not consistent. Eight loci are
represented: the bootstrap script itself (bear, fuelphp, lightmvc-prod,
phalcon); the front-controller class's own `run()` / `dispatch()` (klein,
nette); a runner or wrapper one hop deep (yii's `HttpApplicationRunner`);
error middleware or a framework error-handler component wired by the
bootstrap (cakephp, joomla, mezzio, slim); auto-installed PHP global
handlers in the framework constructor or init (fatfree, flightphp); event
listeners on a `kernel.exception`-style event deeper than one hop
(laminas, silex, symfony); deferred entirely to deeper framework
infrastructure beyond one hop (kohana, laravel, leafphp, phpixie,
tempest); and nothing in scope at all (aura; lithium, though lithium's
`bootstrap/errors.php` ships commented out, opt-in only). The directive's
permissiveness about *where* matches the survey's diversity; every
architectural choice listed above, other than "nothing" and the
auto-installed global handlers, would satisfy "MUST NOT allow a
`Throwable` to escape `run()`" without dictating a specific structure. A
global handler cannot: it fires only once the stack has unwound past the
front controller, by which point the `Throwable` has already escaped.

**Failure modes the directive prevents.** Five cases would fail the
directive as the skeleton is shipped: aura, which catches nothing within
bootstrap+one-hop scope and lets exceptions bubble to PHP's default
handler; lithium, which similarly ships with no error infrastructure
enabled (`bootstrap/errors.php` is commented out, opt-in only); phalcon,
whose bootstrap catches `Exception` but not `Throwable`, so PHP `Error`s
slip through; and fatfree and flightphp, whose only handling is an
auto-installed global handler, which fires only after the `Throwable`
has escaped. fuelphp is borderline; its bootstrap catches four specific
`HttpException` subtypes and lets anything else bubble, so most
`Throwable`s pass through unhandled. The phrasing "a `Throwable`"
(rather than "an exception") is what rules out phalcon's case
specifically.

**"Bootstrap configures, framework catches" is the dominant pattern.**
The majority of surveyed projects follow it: the bootstrap script's job
is to *configure* the catching infrastructure by registering middleware,
instantiating runners, building a container that supplies error handlers,
wiring up event listeners, or instantiating a framework whose
constructor auto-installs handlers, but the actual `try`/`catch` lives
one or more hops deeper than the bootstrap. Where that machinery is a
real `try`/`catch`, modern frameworks already satisfy the directive
trivially: an implementer's `FrontController::run()` need only delegate
to it. Where it is a global handler instead, delegating is not enough.

**Bootstrap-script richness correlates with explicit handling.** Minimal
bootstraps (aura, laminas, laravel, leafphp, mezzio, phpixie, symfony's
demo, tempest, yii) defer entirely or near-entirely to framework
infrastructure for error handling. Rich bootstraps (bear, fuelphp,
lightmvc, phalcon, slim) do explicit work that often includes catching.
The modern trajectory (laravel 12 / mezzio 3 / symfony+runtime) is
toward minimal bootstraps with catching pushed deeper, consistent with a
directive that permits any catch locus.

**PHP global handler usage is rare and uneven.** Direct calls to
`set_exception_handler` within bootstrap+one-hop scope appear in fatfree
(auto-installed in `Base` constructor), flightphp (conditional on the
`flight.handle_errors` config), kohana (CLI/Minion branch only), and
joomla (via the `\Symfony\Component\ErrorHandler\ErrorHandler` wiring).
Direct calls to `set_error_handler` appear in fatfree (auto), flightphp
(conditional), joomla (`E_USER_DEPRECATED` only), and lightmvc
(production, as a no-op suppressor when `error_reporting()` returns 0).
Direct calls to `register_shutdown_function` appear in fatfree (framework
cleanup, not principally for errors) and slim (a custom `ShutdownHandler`
registered specifically to catch PHP fatal errors). slim is the only
project in the survey using `register_shutdown_function` *for error
handling specifically*.

## Summary tables

The three tables below distill the per-project paragraphs along three
dimensions: where catching happens (locus), how it catches (mechanism),
and what is caught (scope). They are scannable summaries; the paragraphs
above remain the source of truth for citations and nuance.

### Locus

Where catching happens. Each project falls in exactly one bucket.

Columns:

- `none`: no exception/error handling exists for the project, even beyond one-hop scope.
- `bootstrap`: catching occurs in the bootstrap script itself.
- `front`: catching occurs inside the front-controller class's own method.
- `middleware`: catching is performed by error middleware or a framework error-handler component wired in by the bootstrap.
- `handlers`: PHP global handlers are registered by the framework's constructor or `init` method.
- `listeners`: catching is performed via event listeners on a `kernel.exception`-style event, deeper than one hop.
- `other`: catching occurs anywhere not covered above; typically deeper in the framework, either visible one hop in (e.g., a runner class) or deferred entirely beyond one-hop scope.

|           | none | bootstrap | front | middleware | handlers | listeners | other |
| --------- | ---- | --------- | ----- | ---------- | -------- | --------- | ----- |
| aura      | x    |           |       |            |          |           |       |
| bear      |      | x         |       |            |          |           |       |
| cakephp   |      |           |       | x          |          |           |       |
| fatfree   |      |           |       |            | x        |           |       |
| flightphp |      |           |       |            | x        |           |       |
| fuelphp   |      | x         |       |            |          |           |       |
| joomla    |      |           |       | x          |          |           |       |
| klein     |      |           | x     |            |          |           |       |
| kohana    |      |           |       |            |          |           | x     |
| laminas   |      |           |       |            |          | x         |       |
| laravel   |      |           |       |            |          |           | x     |
| leafphp   |      |           |       |            |          |           | x     |
| lightmvc  |      | x         |       |            |          |           |       |
| lithium   | x    |           |       |            |          |           |       |
| mezzio    |      |           |       | x          |          |           |       |
| nette     |      |           | x     |            |          |           |       |
| phalcon   |      | x         |       |            |          |           |       |
| phpixie   |      |           |       |            |          |           | x     |
| silex     |      |           |       |            |          | x         |       |
| slim      |      |           |       | x          |          |           |       |
| symfony   |      |           |       |            |          | x         |       |
| tempest   |      |           |       |            |          |           | x     |
| yii       |      |           |       |            |          |           | x     |

### Mechanism

How catching is implemented. A project may use more than one mechanism.

Columns:

- `try`/`catch`: an explicit `try`/`catch` block in the surveyed code.
- `set_exception_handler`: PHP's global exception handler is registered.
- `set_error_handler`: PHP's global error handler is registered.
- `register_shutdown_function`: PHP's shutdown function is registered for error handling specifically.
- `middleware`: error middleware or a framework error-handler component performs the catch.
- `event listener`: a framework event listener (e.g., `kernel.exception`) performs the catch.
- `none in scope`: no catching mechanism exists within the surveyed bootstrap+one-hop scope.

|             | `try`/`catch` | `set_exception_handler` | `set_error_handler` | `register_shutdown_function` | middleware | event listener | none in scope |
| ----------- | ------------- | ----------------------- | ------------------- | ---------------------------- | ---------- | -------------- | ------------- |
| aura        |               |                         |                     |                              |            |                | x             |
| bear        | x             |                         |                     |                              |            |                |               |
| cakephp     |               |                         |                     |                              | x          |                |               |
| fatfree     |               | x                       | x                   |                              |            |                |               |
| flightphp   |               | x (1)                   | x (1)               |                              |            |                |               |
| fuelphp     | x             |                         |                     |                              |            |                |               |
| joomla      |               | x (2)                   | x (3)               |                              |            |                |               |
| klein       | x             |                         |                     |                              |            |                |               |
| kohana      |               | x (4)                   |                     |                              |            |                |               |
| laminas     |               |                         |                     |                              |            | x              |               |
| laravel     |               |                         |                     |                              |            |                | x             |
| leafphp     |               |                         |                     |                              |            |                | x             |
| lightmvc    | x             |                         | x (5)               |                              |            |                |               |
| lithium     |               |                         |                     |                              |            |                | x             |
| mezzio      |               |                         |                     |                              | x          |                |               |
| nette       | x             |                         |                     |                              |            |                |               |
| phalcon     | x             |                         |                     |                              |            |                |               |
| phpixie     |               |                         |                     |                              |            |                | x             |
| silex       |               |                         |                     |                              |            | x              |               |
| slim        |               |                         |                     | x                            | x          |                |               |
| symfony     |               |                         |                     |                              |            | x              |               |
| tempest     |               |                         |                     |                              |            |                | x             |
| yii         | x             |                         |                     |                              |            |                |               |

Notes:

(1) The `flightphp` registrations are conditional on the
`flight.handle_errors` config.

(2) The `joomla` `set_exception_handler` is wired indirectly via the
`\Symfony\Component\ErrorHandler\ErrorHandler` component's own
`register()`, not by Joomla bootstrap directly.

(3) The `joomla` `set_error_handler` is registered for
`E_USER_DEPRECATED` only.

(4) The `kohana` `set_exception_handler` appears only in the
CLI/Minion branch; the HTTP path defers to `Kohana::init()` (beyond
one-hop scope).

(5) The `lightmvc` `set_error_handler` is registered only in the
production environment, as a no-op suppressor for errors when
`error_reporting()` returns 0.

`fatfree` also calls `register_shutdown_function`, but for framework
cleanup rather than error handling, so it is not marked in that
column. `slim` is the only project in the survey using
`register_shutdown_function` for error handling specifically.

### Scope caught

What is caught. For projects whose catching happens beyond the strict
one-hop scope, the mark reflects the project's documented framework
behavior, with footnote (2) flagging the out-of-scope caveat.

Columns:

- all `Throwable`: anything implementing `Throwable` is caught (both `Exception`s and `Error`s).
- `Exception` only: `Exception`s are caught but `Error`s slip through.
- specific subtypes only: only specific `Throwable` subtypes are caught (e.g., particular `Http*Exception`s); anything else bubbles.
- `none`: no catching at all, in scope or beyond.

|             | all `Throwable` | `Exception` only | specific subtypes only | none |
| ----------- | --------------- | ---------------- | ---------------------- | ---- |
| aura        |                 |                  |                        | x    |
| bear        | x               |                  |                        |      |
| cakephp     | x               |                  |                        |      |
| fatfree     | x               |                  |                        |      |
| flightphp   | x (1)           |                  |                        |      |
| fuelphp     |                 |                  | x                      |      |
| joomla      | x               |                  |                        |      |
| klein       | x               |                  |                        |      |
| kohana      | x (2)           |                  |                        |      |
| laminas     | x (2)           |                  |                        |      |
| laravel     | x (2)           |                  |                        |      |
| leafphp     | x (2)           |                  |                        |      |
| lightmvc    | x (3)           |                  |                        |      |
| lithium     |                 |                  |                        | x    |
| mezzio      | x               |                  |                        |      |
| nette       | x               |                  |                        |      |
| phalcon     |                 | x                |                        |      |
| phpixie     | x (2)           |                  |                        |      |
| silex       | x (2)           |                  |                        |      |
| slim        | x               |                  |                        |      |
| symfony     | x (2)           |                  |                        |      |
| tempest     | x (2)           |                  |                        |      |
| yii         | x               |                  |                        |      |

Notes:

(1) `flightphp` catching is conditional on the `flight.handle_errors`
config.

(2) The catch lives beyond the strict one-hop scope (event listeners
or deferred framework infrastructure). Marked per the project's
documented framework behavior; the per-project paragraph above flags
the out-of-scope locus.

(3) `lightmvc` catching applies only in the production environment.

## Handler Registration Beyond One Hop

The tables above observe the scope stated at the head of this document:
bootstrap, the front controller class, and the immediate error infrastructure
they delegate to. Within that scope, six projects register a global handler.

Looking one level further, into the framework packages each project depends
on, gives a different picture. It is recorded separately here rather than
folded into the tables above, which remain scoped as described.

Columns:

- `registers`: the project registers `set_exception_handler`,
  `set_error_handler`, or `register_shutdown_function` somewhere in its
  dependency stack.
- `prevents return`: a `Throwable` reaching that handler would stop the front
  controller's main method from returning to its caller.
- `returns anyway`: the handler exists but the main method still returns.
- `undetermined`: registration confirmed, effect on the return not traced.

|             | registers | prevents return | returns anyway | undetermined |
| ----------- | --------- | --------------- | -------------- | ------------ |
| aura        |           |                 |                |              |
| bear        |           |                 |                |              |
| cakephp     | x         |                 |                | x            |
| fatfree     | x         |                 |                | x            |
| flightphp   | x (1)     |                 |                | x            |
| fuelphp     | x         | x               |                |              |
| joomla      | x (2)     |                 |                | x            |
| klein       | x         |                 |                | x            |
| kohana      | x (3)     |                 |                | x            |
| laminas     |           |                 |                |              |
| laravel     | x         |                 |                | x            |
| leafphp     | x         | x               |                |              |
| lightmvc    | x (4)     | x (4)           | x (4)          |              |
| lithium     | x         |                 |                | x            |
| mezzio      | x (5)     |                 | x              |              |
| nette       | x         |                 |                | x            |
| phalcon     | x (6)     |                 | x              |              |
| phpixie     | x         | x (7)           |                |              |
| silex       | x (8)     | x               |                |              |
| slim        | x         | x (9)           |                |              |
| symfony     | x         |                 |                | x            |
| tempest     | x (10)    | x               |                |              |
| yii         | x         |                 |                | x            |

Notes:

(1) The `flightphp` registration is conditional on the `flight.handle_errors`
config.

(2) The `joomla` registration is wired indirectly by Symfony's
`ErrorHandler::register()` rather than by Joomla bootstrap directly.

(3) The `kohana` registration appears only in the CLI/Minion branch; the HTTP
path defers to `Kohana::init()`.

(4) The `lightmvc` behavior splits on environment. In production a
`set_error_handler` is registered whose body does nothing, and the bootstrap
`try`/`catch` still receives the `Throwable`. Outside production, Whoops is
registered with `allowQuit(true)` and terminates.

(5) The `mezzio` registration is request-scoped rather than global.
Stratigility's `ErrorHandler` middleware registers `set_error_handler` at the
top of `process()`, converts errors to `ErrorException`, catches them itself,
and restores the previous handler in a `finally`.

(6) The `phalcon` registrations live in `Phalcon\Support\Debug` and take
effect only if `Debug::listen()` is called, which the surveyed application
never does.

(7) The `phpixie` effect depends on the entry path. The sample application's
`web/index.php` chains its calls with no `try`/`catch`, so a converted error
escapes; `Pixie::handle_http_request()` catches and returns.

(8) The `silex` registration is via Monolog and gated on
`monolog.use_error_handler`, which defaults to production only.

(9) The `slim` registration is a `register_shutdown_function` in the skeleton
entry script; it fires only for fatals the error middleware cannot catch.

(10) The `tempest` registrations are skipped when the environment is testing.

Of the 23 projects, 20 register at least one of the three functions somewhere.
Only `aura`, `bear`, and `laminas` register none; the first two catch with an
ordinary `try`/`catch`, and `laminas` through MVC events.

Registration alone does not determine the outcome. Most of the 20 use the
handler as a backstop beneath a normal catching path, and the front controller
returns on every ordinary request. The effect was traced for nine of the 20:
six would prevent a return, two would not, and one splits by environment. The
remaining eleven are recorded as undetermined rather than assumed either way.

Several handlers terminate deliberately rather than letting PHP terminate
after them: Monolog's ends in `exit(255)`, and the Whoops variants used by
`leafphp` and `lightmvc` exit through `allowQuit`. The `mezzio` project
registers a handler and still returns, by scoping the registration to a single
`process()` call and restoring the previous handler in a `finally`.
