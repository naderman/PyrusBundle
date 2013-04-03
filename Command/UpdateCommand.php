<?php
/*
 *
 * (c) phpBB Ltd.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 * @author Tarjei Huse
 */

namespace phpBB\PyrusBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Input\ArrayInput;
use phpBB\PyrusBundle\Input\ArbitraryInputDefinition;
use phpBB\PyrusBundle\Pyrus;

/**
 * Pyrus command
 */
class UpdateCommand extends ContainerAwareCommand
{
    protected $inputDefinition;

    /**
     * Configures this command to accept arbitrary options and arguments.
     */
    protected function configure()
    {
        //$this->inputDefinition = new ArbitraryInputDefinition;

        $this
            ->setName('pyrus:update')
            ->setDescription("Upgrade pyrus deps to versions defined in deps.pyrus")
            ->setDefinition(array());
    }

    /**
     * Makes sure the Pear dependencies are installed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $root =  $this->getContainer()->getParameter('kernel.root_dir') . "/..";
        if (!file_exists("deps.pyrus")) {
            throw new \Exception("Could not find $root/deps.pyrus file needed");
        }
        $deps = file($root . "/deps.pyrus", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($deps as $package) {
            $this->installPackage($package, $output);
        }
    }

    private function installPackage($package, $output) {
        $pyrus = new Pyrus($this->getContainer( ));
        $pyrus->run(array('install', $package));
        return;
    }



}
