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

use ReflectionException;
use Lenevor\Actions\Console\ActionMakeCommand;
use Lenevor\Actions\Patterns\CommandPattern;
use Lenevor\Actions\Patterns\ControllerPattern;
use Lenevor\Actions\Patterns\ListenerPattern;
use Syscodes\Components\Core\Application;
use Syscodes\Components\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish templates File
            $this->publishes([
                __DIR__ . '/Console/templates/action.tpl' => base_path('templates/action.tpl'),
            ], 'templates');

            // Register the make:action generator command.
            $this->commands([
                ActionMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->scoped(ActionManager::class, function () {
            return new ActionManager([
                new ControllerPattern(),
                new ListenerPattern(),
                new CommandPattern(),
            ]);
        });

        $this->extendActions();
    }

    /**
     * Allows the extend of all the actions that creates the user.
     * 
     * @return void
     */
    protected function extendActions()
    {
        $this->app->beforeResolving(function ($abstract, $parameters, Application $app) {
            if ($abstract === ActionManager::class) {
                return;
            }

            try {
                $classExists = class_exists($abstract);
            } catch (ReflectionException) {
                return;
            }

            if ( ! $classExists || $app->resolved($abstract)) {
                return;
            }

            $app->make(ActionManager::class)->extend($app, $abstract);
        });
    }
}