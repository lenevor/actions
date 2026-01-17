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

namespace Lenevor\Actions\Concerns;

trait DecorateAction
{
    /**
     * Register a action.
     * 
     * @var mixed
     */
    protected mixed $action = null;

    /**
     * Set the action.
     * 
     * @param  mixed  $action
     * 
     * @return self
     */
    public function setAction($action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Check if exist a trait.
     * 
     * @param  string  $trait
     * 
     * @return bool
     */
    protected function hasTrait(string $trait): bool
    {
        return in_array($trait, class_recursive($this->action));
    }

    /**
     * Check if exist a property.
     * 
     * @param  string  $property
     * 
     * @return bool
     */
    protected function hasProperty(string $property): bool
    {
        return property_exists($this->action, $property);
    }

    /**
     * Get the property of a action.
     * 
     * @param  string  $property
     * 
     * @return mixed
     */
    protected function getProperty(string $property)
    {
        return $this->action->{$property};
    }

    /**
     * Check if exist the method.
     * 
     * @param  string  $method
     * 
     * @return bool
     */
    protected function hasMethod(string $method): bool
    {
        return isset($this->action) && method_exists($this->action, $method);
    }

    /**
     * Allow the calls of a method.
     * 
     * @param  string  $method
     * @param  array  $parameters
     * 
     * @return mixed
     */
    protected function callMethod(string $method, array $parameters = [])
    {
        return call_user_func_array([$this->action, $method], $parameters);
    }

    /**
     * Resolves calling the given callable / class@method and inject its dependencies.
     * 
     * @param  string  $method
     * @param  array  $parameters
     * 
     * @retun mixed
     */
    protected function resolveAndCallMethod(string $method, array $parameters = [])
    {
        return app()->call([$this->action, $method], $parameters);
    }

    /**
     * Call of a method or value by default.
     * 
     * @param  string  $method
     * @param  array  $parameters
     * @param  mixed  $default
     * 
     * @return mixed
     */
    protected function actionMethod(string $method, array $parameters = [], $default = null)
    {
        return $this->hasMethod($method)
            ? $this->callMethod($method, $parameters)
            : value($default);
    }

    /**
     * Call a property or value by default.
     * 
     * @param  string  $property
     * @param  mixed  $default
     * 
     * @return mixed
     */
    protected function actionProperty(string $property, $default = null)
    {
        return $this->hasProperty($property)
            ? $this->getProperty($property)
            : value($default);
    }

    /**
     * Call a method with parameters, properties and values by default.
     * 
     * @param  string  $method
     * @param  string  $property
     * @param  mixed  $default
     * @param  array  $parameters
     * 
     * @return mixed
     */
    protected function actionMethodOrProperty(string $method, string $property, $default = null, array $parameters = [])
    {
        if ($this->hasMethod($method)) {
            return $this->callMethod($method, $parameters);
        }

        if ($this->hasProperty($property)) {
            return $this->getProperty($property);
        }

        return value($default);
    }
}