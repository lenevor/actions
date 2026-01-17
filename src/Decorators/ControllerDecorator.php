<?php

/**
 * Lenevor Actions
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file license.md.
 * It is also available through the world-wide-web at this URL:
 * https://lenevor.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@Lenevor.com so we can send you a copy immediately.
 *
 * @package     Lenevor Actions
 * @copyright   Copyright (c) 2026 Alexander Campo <jalexcam@gmail.com>
 * @license     https://opensource.org/licenses/BSD-3-Clause New BSD license or see https://lenevor.com/license or see /license.md
 */

namespace Lenevor\Actions\Decorators;

use Lenevor\Actions\ActionRequest;
use Lenevor\Actions\Concerns\DecorateAction;
use Syscodes\Components\Container\Container;
use Syscodes\Components\Routing\Concerns\DependencyResolver;
use Syscodes\Components\Routing\Route;
use Syscodes\Components\Support\Str;

class ControllerDecorator
{
    use DependencyResolver, 
        DecorateAction;

    /**
     * The container instance.
     * 
     * @var \Syscodes\Components\Container\Container
     */
    protected Container $container;
    
    /**
     * The execute at least one as instance.
     * 
     * @var bool
     */
    protected bool $executedAtLeastOne = false;

    /**
     * Get the middleware.
     * 
     * @var array
     */
    protected array $middleware = [];

    /**
     * The route instance.
     * 
     * @var \Syscodes\Components\Routing\Route
     */
    protected Route $route;

    /**
     * Constructor. Create a new decorator class instance.
     * 
     * @param  mixed  $action
     * @param  \Syscodes\Components\Routing\Route  $route
     * 
     * @return void
     */
    public function __construct($action, Route $route)
    {
        $this->container = Container::getInstance();
        $this->route = $route;
        $this->setAction($action);
        $this->replaceRouteMethod();

        if ($this->hasMethod('getControllerMiddleware')) {
            $this->middleware = $this->resolveAndCallMethod('getControllerMiddleware');
        }
    }

    /**
     * Get the route.
     * 
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Get the middleware.
     * 
     * @return array
     */
    public function getMiddleware(): array
    {
        return array_map(function ($middleware) {
            return [
                'middleware' => $middleware,
                'options' => [],
            ];
        }, $this->middleware);
    }

    /**
     * The call action for given a method response.
     * 
     * @param  string  $method
     * @param  array  $parameters
     * 
     * @return mixed
     */
    public function callAction($method, $parameters)
    {
        return $this->__invoke($method);
    }

    /**
     * Magic method.
     * 
     * Executes to the method name.
     * 
     * @param  string  $method
     * 
     * @return mixed
     */
    public function __invoke(string $method)
    {
        $this->refreshAction();
        $request = $this->refreshRequest();

        if ($this->shouldValidateRequest($method)) {
            $request->validate();
        }

        $response = $this->run($method);

        if ($this->hasMethod('jsonResponse') && $request->expectsJson()) {
            $response = $this->callMethod('jsonResponse', [$response, $request]);
        } elseif ($this->hasMethod('htmlResponse') && ! $request->expectsJson()) {
            $response = $this->callMethod('htmlResponse', [$response, $request]);
        }

        return $response;
    }

    /**
     * Allows the refresh action.
     * 
     * @return void
     */
    protected function refreshAction(): void
    {
        if ($this->executedAtLeastOne) {
            $this->setAction(app(get_class($this->action)));
        }

        $this->executedAtLeastOne = true;
    }

    /**
     * Allows the refresh request.
     * 
     * @return ActionRequest
     */
    protected function refreshRequest(): ActionRequest
    {
        app()->eraseInstance(ActionRequest::class);

        /** @var ActionRequest $request */
        $request = app(ActionRequest::class);
        $request->setAction($this->action);
        app()->instance(ActionRequest::class, $request);

        return $request;
    }

    /**
     * Allows the replace route method.
     * 
     * @return void
     */
    protected function replaceRouteMethod(): void
    {
        if ( ! isset($this->route->action['uses'])) {
            return;
        }

        $currentMethod = Str::afterLast($this->route->action['uses'], '@');
        $newMethod = $this->getDefaultRouteMethod();

        if ($currentMethod !== '__invoke' || $currentMethod === $newMethod) {
            return;
        }

        $this->route->action['uses'] = (string) Str::of($this->route->action['uses'])
            ->beforeLast('@')
            ->append('@' . $newMethod);
    }

    /**
     * Get the default route method.
     * 
     * @return string
     */
    protected function getDefaultRouteMethod(): string
    {
        if ($this->hasMethod('forController')) {
            return 'forController';
        }

        return $this->hasMethod('handle') ? 'handle' : '__invoke';
    }

    /**
     * This is explicit method?
     * 
     * @param  string  $method
     * 
     * @return bool
     */
    protected function isExplicitMethod(string $method): bool
    {
        return ! in_array($method, ['forController', 'handle', '__invoke']);
    }

    /**
     * Allows the run of call route and methods.
     * 
     * @param  string  $method
     * 
     * @return mixed
     */
    protected function run(string $method)
    {
        if ($this->hasMethod($method)) {
            return $this->resolveRouteAndCall($method);
        }
    }

    /**
     * Allows should the validation of request.
     * 
     * @param  string  $method
     * 
     * @return bool
     */
    protected function shouldValidateRequest(string $method): bool
    {
        return $this->hasAnyValidationMethod() && ! $this->isExplicitMethod($method);
    }

    /**
     * Check any validation in a method.
     * 
     * @return bool
     */
    protected function hasAnyValidationMethod(): bool
    {
        return $this->hasMethod('authorize')
            || $this->hasMethod('rules')
            || $this->hasMethod('withValidator')
            || $this->hasMethod('afterValidator')
            || $this->hasMethod('getValidator');
    }

    /**
     * Resolves the route and call of methods.
     * 
     * @param  string  $method
     * 
     * @return mixed
     */
    protected function resolveRouteAndCall(string $method)
    {
        $this->container = Container::getInstance();

        $arguments = $this->resolveObjectMethodDependencies(
            $this->route->parametersWithoutNulls(),
            $this->action,
            $method
        );

        return $this->action->{$method}(...array_values($arguments));
    }
}