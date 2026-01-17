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
use Lenevor\Actions\Exceptions\MissingCommandException;
use Syscodes\Components\Console\Command;
use Syscodes\Components\Console\View\Components\Factory;

class CommandDecorator extends Command
{
    use DecorateAction;

    /**
     * Constructor. Create a new decorator class instance
     * 
     * @param  mixed  $action
     * 
     * @return void
     */
    public function __construct($action)
    {
        $this->setAction($action);

        $this->signature = $this->actionMethodOrProperty('getCommandSignature', 'commandSignature');
        $this->name = $this->actionMethodOrProperty('getCommandName', 'commandName');
        $this->description = $this->actionMethodOrProperty('getCommandDescription', 'commandDescription');
        $this->help = $this->actionMethodOrProperty('getCommandHelp', 'commandHelp');

        if ( ! $this->signature) {
            throw new MissingCommandException($this->action);
        }

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->hasMethod('forCommand')) {
            return $this->resolveAndCallMethod('forCommand', ['command' => $this]);
        }

        if ($this->hasMethod('handle')) {
            return $this->resolveAndCallMethod('handle', ['command' => $this]);
        }
    }

    /**
     * Gets the components for view in console.
     * 
     * @return \Syscodes\Components\Console\View\Components\Factory
     */
    public function getComponents(): Factory
    {
        return $this->components;
    }
}