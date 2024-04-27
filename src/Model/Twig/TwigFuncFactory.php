<?php
declare(strict_types = 1);
/****
 * Author: zeroc0de <98693638+zeroc0de2022@users.noreply.github.com>
 */

namespace Routim\Model\Twig;

use Twig\TwigFunction;

/**
 * Class TwigFuncFactory
 */
class TwigFuncFactory
{
    /**
     * Create a new TwigFunction instance.
     * This is a wrapper for the TwigFunction constructor.
     * @param ...$arguments
     * @return TwigFunction
     */
    public function create(...$arguments): TwigFunction
    {
        return new TwigFunction(...$arguments);
    }
}