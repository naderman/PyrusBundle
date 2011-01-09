<?php
/*
 * This file is part of phpBB.
 *
 * (c) phpBB Ltd.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\Phpbb\PyrusBundle\Input;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
*
*/
class ArbitraryInputDefinition extends InputDefinition
{
    protected $inputOrder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->arg = 0;
        $this->definition = array();
    }

    public function getArguments()
    {
        return array();
    }

    public function hasArgument($name)
    {
        return true;
    }

    public function getArgument($name)
    {
        $argument = new InputArgument($name, InputArgument::OPTIONAL);
        $this->inputOrder[] = $argument;

        return $argument;
    }

    public function getOption($name)
    {
        $option = new InputOption($name, null, InputOption::VALUE_OPTIONAL);
        $this->inputOrder[] = $option;

        return $option;
    }

    public function hasOption($name)
    {
        return true;
    }

    public function getTokens(InputInterface $input)
    {
        $tokens = array();

        foreach ($this->inputOrder as $parameter) {
            $name = $parameter->getName();

            if ($parameter instanceof InputOption) {
                if (strlen($name) == 1) {
                    $tokens[] = '-' . $name;
                } else {
                    $tokens[] = '--' . $name;
                }
                $tokens[] = $input->getOption($name);
            } else if ($parameter instanceof InputArgument) {
                $tokens[] = $input->getArgument($name);
            }
        }

        return $tokens;
    }
}
