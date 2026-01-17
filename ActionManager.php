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

use Syscodes\Components\Routing\Router;
use Lenevor\Actions\Concerns\ForCommand;
use Lenevor\Actions\Concerns\ForController;
use Lenevor\Actions\Patterns\DesignPattern;
use Syscodes\Components\Console\Application as Prime;

class ActionManager
{
    /** @var DesignPattern[] */
    protected array $designPatterns = [];

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

    public function setBacktraceLimit(int $backtraceLimit): ActionManager
    {
        $this->backtraceLimit = $backtraceLimit;

        return $this;
    }

    public function setDesignPatterns(array $designPatterns): ActionManager
    {
        $this->designPatterns = $designPatterns;

        return $this;
    }

    public function getDesignPatterns(): array
    {
        return $this->designPatterns;
    }

    public function registerDesignPattern(DesignPattern $designPattern): ActionManager
    {
        $this->designPatterns[] = $designPattern;
        
        return $this;
    }

    public function getDesignPatternsMatching(array $usedTraits): array
    {
        $filter = function (DesignPattern $designPattern) use ($usedTraits) {
            return in_array($designPattern->getTrait(), $usedTraits);
        };

        return array_filter($this->getDesignPatterns(), $filter);
    }

    public function isExtending(string $abstract): bool
    {
        return isset($this->extended[$abstract]);
    }

    public function identifyFromBacktrace($usedTraits, ?Backtrace &$frame = null): ?DesignPattern
    {
        $designPatterns = $this->getDesignPatternsMatching($usedTraits);
        $backtraceOptions = DEBUG_BACKTRACE_PROVIDE_OBJECT
            | DEBUG_BACKTRACE_IGNORE_ARGS;
        
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

    public function registerRoutesAction(string $className): void
    {
        $className::routes(app(Router::class));
    }

    public function registerCommandsAction(string $className): void
    {
        Prime::starting(function ($prime) use ($className) {
            $prime->resolve($className);
        });
    }
}