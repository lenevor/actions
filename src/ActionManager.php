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

use Lenevor\Actions\Patterns\DesignPattern;
use Syscodes\Components\Core\Application;
use Syscodes\Components\Console\Application as Prime;
use Syscodes\Components\Routing\Router;

class ActionManager
{
    /**
     * The design patterns.
     * 
     * @var DesignPattern[]
     */
    protected array $designPatterns = [];

    /**
     * Extender an id type in the action.
     * 
     * @var bool[]
     */
    protected array $extended = [];

    /**
     * Get the backtrace limit.
     * 
     * @var bool
     */
    protected int $backtraceLimit = 10;

    /**
     * Constructor. Create a new ActionManager class instance.
     * 
     * @param  array  $designPatterns
     * 
     * @return void
     */
    public function __construct(array $designPatterns = [])
    {
        $this->setDesignPatterns($designPatterns);
    }

    /**
     * Sets the backtrace limit.
     * 
     * @param  int  $backtraceLimit
     * 
     * @return static
     */
    public function setBacktraceLimit(int $backtraceLimit): static
    {
        $this->backtraceLimit = $backtraceLimit;

        return $this;
    }

    /**
     * Sets the design patterns.
     * 
     * @param  array  $designPatterns
     * 
     * @return static
     */
    public function setDesignPatterns(array $designPatterns): static
    {
        $this->designPatterns = $designPatterns;

        return $this;
    }

    /**
     * Gets the design patterns.
     * 
     * @return array
     */
    public function getDesignPatterns(): array
    {
        return $this->designPatterns;
    }

    /**
     * Register the design pattern.
     * 
     * @param  \Lenevor\Actions\Patterns\DesignPattern  $designPattern
     * 
     * @return static
     */
    public function registerDesignPattern(DesignPattern $designPattern): static
    {
        $this->designPatterns[] = $designPattern;
        
        return $this;
    }

    /**
     * Get the design patterns matching.
     * 
     * @param  array  $usedTraits
     * 
     * @return array
     */
    public function getDesignPatternsMatching(array $usedTraits): array
    {
        $filter = function (DesignPattern $designPattern) use ($usedTraits) {
            return in_array($designPattern->getTrait(), $usedTraits);
        };

        return array_filter($this->getDesignPatterns(), $filter);
    }

    /**
     * Extender an id type in the actions.
     * 
     * @param  \Syscodes\Components\Core\Application  $app
     * @param  string  $id
     * 
     * @return void
     */
    public function extend(Application $app, string $id): void
    {
        if ($this->isExtending($id)) {
            return;
        }

        if ( ! $this->shouldExtend($id)) {
            return;
        }

        $app->extend($id, function ($instance) {
            return $this->identifyAndDecorate($instance);
        });

        $this->extended[$id] = true;
    }

    /**
     * Check if is extending?
     * 
     * @param  string  $id
     * 
     * @return bool
     */
    public function isExtending(string $id): bool
    {
        return isset($this->extended[$id]);
    }

    /**
     * Check if is should extend?
     * 
     * @param  string  $id
     * 
     * @return bool
     */
    public function shouldExtend(string $id): bool
    {
        $usedTraits = class_recursive($id);

        return ! empty($this->getDesignPatternsMatching($usedTraits));
    }

    /**
     * The identify of backtrace frame and decorate.
     * 
     * @param  mixed  $instance
     * 
     * @return mixed
     */
    public function identifyAndDecorate($instance)
    {
        $usedTraits = class_recursive($instance);

        if ( ! $designPattern = $this->identifyFromBacktrace($usedTraits, $frame)) {
            return $instance;
        }

        return $designPattern->decorate($instance, $frame);
    }

    /**
     * The identify of backtrace frame and design pattern.
     * 
     * @param  array  $usedTraits
     * @param  \Lenevor\Actions\Backtrtace|null  $frame
     * 
     * @return \Lenevor\Actions\Patterns\DesignPattern
     */
    public function identifyFromBacktrace($usedTraits, ?Backtrace &$frame = null): ?DesignPattern
    {
        $designPatterns = $this->getDesignPatternsMatching($usedTraits);

        $backtraceOptions = DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS;
        
        $ownNumberOfFrames = 2;

        $frames = array_slice(
            debug_backtrace($backtraceOptions, $ownNumberOfFrames + $this->backtraceLimit),
            $ownNumberOfFrames
        );

        foreach ($frames as $frame) {
            $frame = new Backtrace($frame);

            /** @var DesignPattern $designPattern */
            foreach ($designPatterns as $designPattern) {
                if ($designPattern->getFrame($frame)) {
                    return $designPattern;
                }
            }
        }

        return null;
    }

    /**
     * The register routes for action.
     * 
     * @param  string  $className
     * 
     * @return void
     */
    public function registerRoutesAction(string $className): void
    {
        $className::routes(app(Router::class));
    }

    /**
     * The register commands for action.
     * 
     * @param  string  $className
     * 
     * @return void
     */
    public function registerCommandsAction(string $className): void
    {
        Prime::starting(function ($prime) use ($className) {
            $prime->resolve($className);
        });
    }
}