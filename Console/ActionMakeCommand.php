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

namespace Lenevor\Actions\Console;

use Syscodes\Components\Console\GeneratorCommand;

class ActionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Action';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the template file for the generator.
     *
     * @return string
     */
    protected function getTemplate(): string
    {
        return $this->resolveTemplatePath('/templates/action.tpl');
    }

    /**
     * Resolve the fully-qualified path to the template.
     *
     * @param  string  $template
     * 
     * @return string
     */
    protected function resolveTemplatePath(string $template): string
    {
        return file_exists($customPath = $this->lenevor->basePath(trim($template, '/')))
            ? $customPath
            : __DIR__.$template;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * 
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Actions';
    }
}