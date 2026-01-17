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

use Syscodes\Components\Support\Flowing;

trait ForObject
{
    /**
     * Resolves the given type object from the container.
     * 
     * @return static
     */
    public static function make()
    {
        return app(static::class);
    }
    
    /**
     * Resolves and executes the action.
     * 
     * @param  mixed  ...$arguments
     * 
     * @return mixed
     */
    public static function run(mixed ...$arguments): mixed
    {
        return static::make()->handle(...$arguments);
    }

    /**
     * Resolves and executes the action if the condition is met.
     * 
     * @param  bool  $boolean
     * @param  mixed  ...$arguments
     * 
     * @return mixed
     */
    public static function runIf(bool $boolean, mixed ...$arguments): mixed
    {
        return $boolean ? static::run(...$arguments) : new Flowing;
    }
    
    /**
     * Resolves and executes the action if some condition is not met.
     * 
     * @param  bool  $boolean
     * @param  mixed  ...$arguments
     * 
     * @return mixed
     */
    public static function runUnless(bool $boolean, mixed ...$arguments): mixed
    {
        return static::runIf( ! $boolean, ...$arguments);
    }
}