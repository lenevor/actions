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

namespace Lenevor\Actions\Exceptions;

use Exception;
use Throwable;

class MissingCommandException extends Exception
{
    /**
     * Constructor. Create a new exception class instance.
     * 
     * @param  string|object  $action
     * @param  int  $code
     * @param  Throwable|null  $previous
     * 
     * @return void
     */
    public function __construct($action, $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf(
            'The command signature is missing from your [%s] action',
            get_class($action)
        );

        parent::__construct($message, $code, $previous);
    }
}