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

use Lenevor\Actions\Concerns\ValidateAction;
use Syscodes\Components\Core\Http\FormRequest;

class ActionRequest extends FormRequest
{
    use ValidateAction;

    /**
     * get the validate for resolved.
     * 
     * @return void
     */
    public function validateResolved(): void
    {
        // Cancel the auto-resolution trait.
    }

    /**
     * Get for default the validation of data.
     * 
     * @return array
     */
    public function getDefaultValidationData(): array
    {
        return $this->all();
    }
}