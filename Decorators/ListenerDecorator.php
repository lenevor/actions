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

use Lenevor\Actions\Concerns\DecorateAction;
use Syscodes\Components\Container\Container;
use Syscodes\Components\Routing\Concerns\DependencyResolver;

class ListenerDecorator
{
    use DependencyResolver,
        DecorateAction;

    /**
     * @var \Syscodes\Components\Container\Container
     */
    protected $container;

    /**
     * Constructor. Create a new decorator class instance.
     * 
     * @param  mixed  $action
     * 
     * @return void
     */
    public function __construct($action)
    {
        $this->setAction($action);
        $this->container = new Container;
    }

    /**
     * Execute the handle method.
     *
     * @return void
     */
    public function handle(...$arguments)
    {
        if ($this->hasMethod('forListener')) {
            return $this->resolveArgumentsAndCall('forListener', $arguments);
        }

        if ($this->hasMethod('handle')) {
            return $this->resolveArgumentsAndCall('handle', $arguments);
        }
    }

    /**
     * Resolves the call of methods and arguments.
     * 
     * @param  string  $method
     * @param  array  $arguments
     * 
     * @return mixed
     */
    protected function resolveArgumentsAndCall($method, $arguments)
    {
        $arguments = $this->resolveObjectMethodDependencies($arguments, $this->action, $method);

        return $this->action->{$method}(...array_values($arguments));
    }
}