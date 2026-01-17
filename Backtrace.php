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

namespace Lenevor\Actions;

use Syscodes\Components\Support\Arr;

class Backtrace
{
    /**
     * Get the class.
     * 
     * @var string|null
     */
    public ?string $class;

    /**
     * Get the function.
     * 
     * @var string|null
     */
    public ?string $function;

    /**
     * Check if is static.
     * 
     * @var bool
     */
    public bool $isStatic;

    /**
     * Get the object.
     * 
     * @var mixed
     */
    public mixed $object;

    /**
     * Constructor. Create a new backtrace class instance.
     * 
     * @param  array  $frame
     * 
     * @return void
     */
    public function __construct(array $frame)
    {
        $this->class = Arr::get($frame, 'class');
        $this->function = Arr::get($frame, 'function');
        $this->isStatic = Arr::get($frame, 'type') === '::';
        $this->object = Arr::get($frame, 'object');
    }

    /**
     * Check if exist a class.
     * 
     * @return bool
     */
    public function hasClass(): bool
    {
        return ! is_null($this->class);
    }

    /**
     * Available instance of the class.
     * 
     * @param  string  $class
     * 
     * @return bool
     */
    public function instanceOf(string $class): bool
    {
        if ( ! $this->hasClass()) {
            return false;
        }

        return $this->class === $class
            || is_subclass_of($this->class, $class);
    }

    /**
     * Determine if a class this instanced and if the class is static.
     * 
     * @param  string  class
     * @param  string  $method
     * @param  bool|null  $isStatic
     * 
     * @return bool
     */
    public function matches(string $class, string $method, ?bool $isStatic = null): bool
    {
        $matchesStatic = is_null($isStatic) || $this->isStatic === $isStatic;

        return $this->instanceOf($class)
            && $this->function === $method
            && $matchesStatic;
    }

    /**
     * Get the object.
     * 
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
}