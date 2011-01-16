<?php
/*
 * This file is part of phpBB.
 *
 * (c) phpBB Ltd.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\phpBB\PyrusBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Bundle\Phpbb\PyrusBundle\Input\ArbitraryInputDefinition;
use Bundle\Phpbb\PyrusBundle\Pyrus;

/**
 * Pyrus command
 */
class PyrusCommand extends Command
{
    protected $inputDefinition;

    /**
     * Configures this command to accept arbitrary options and arguments.
     */
    protected function configure()
    {
        $this->inputDefinition = new ArbitraryInputDefinition;

        $this
            ->setName('pyrus')
            ->setDefinition($this->inputDefinition);
    }

    /**
     * Passes the given options and arguments to Pyrus for execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tokens = $this->inputDefinition->getTokens($input);
        array_shift($tokens); // remove pyrus

        $pyrus = new Pyrus($this->application->getKernel());
        $pyrus->run($tokens);
    }
}
