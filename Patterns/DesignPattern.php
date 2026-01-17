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

namespace Lenevor\Actions\Patterns;

use Lenevor\Actions\Backtrace;

abstract class DesignPattern
{
    /**
     * Get the trait.
     * 
     * @return string
     */
    abstract public function getTrait(): string;

    /**
     * Get the frame.
     * 
     * @param  \Lenevor\Actions\Backtrace  $frame
     * 
     * @return bool
     */
    abstract public function getFrame(Backtrace $frame): bool;

    /**
     * Get the decorate depending on their class.
     * 
     * @param  mixed  $instance
     * @param  \Lenevor\Actions\Backtrace  $frame
     * 
     * @return mixed
     */
    abstract public function decorate($instance, Backtrace $frame);
}