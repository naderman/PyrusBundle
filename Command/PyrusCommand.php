<?php
/*
 * This file is part of phpBB.
 *
 * (c) phpBB Ltd.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace phpBB\PyrusBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use phpBB\PyrusBundle\Input\ArbitraryInputDefinition;
use phpBB\PyrusBundle\Pyrus;

/**
 * Pyrus command
 */
class PyrusCommand extends ContainerAwareCommand
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
        $pyrus = new Pyrus($this->getContainer( ));
        $pyrus->run($tokens);
    }
}
