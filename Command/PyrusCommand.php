<?php
/*
 * This file is part of phpBB.
 *
 * (c) phpBB Ltd.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\Phpbb\PyrusBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Bundle\Phpbb\PyrusBundle\Input\ArbitraryInputDefinition;

/**
 * Pyrus command
 */
class PyrusCommand extends Command
{
    protected $inputDefinition;

    protected function configure()
    {
        $this->inputDefinition = new ArbitraryInputDefinition;

        $this
            ->setName('pyrus')
            ->setDefinition($this->inputDefinition);
    }

    protected function getPyrusDir()
    {
        $bundleDirs = $this->application->getKernel()->getBundleDirs();

        if (!isset($bundleDirs['Bundle'])) {
            throw new \RunTimeException("No BundleDir for namespace Bundle defined in Application Kernel.");
        }

        return $bundleDirs['Bundle'];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tokens = $this->inputDefinition->getTokens($input);
        array_shift($tokens); // remove pyrus

        array_unshift($tokens, $this->getPyrusDir());

        spl_autoload_register(array($this, 'pyrus_autoload'));
        $frontend = new \PEAR2\Pyrus\ScriptFrontend\Commands;
        $frontend->run($tokens);
    }

    function pyrus_autoload($class)
    {
        $class = str_replace(array('_','\\'), '/', $class);
        if (file_exists('phar://' . __DIR__ . '/../pyrus.phar/PEAR2_Pyrus-2.0.0a2/php/' . $class . '.php')) {
            include 'phar://' . __DIR__ . '/../pyrus.phar/PEAR2_Pyrus-2.0.0a2/php/' . $class . '.php';
        }
    }

}
