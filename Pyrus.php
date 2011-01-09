<?php
/*
 * This file is part of phpBB.
 *
 * (c) phpBB Ltd.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\Phpbb\PyrusBundle;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Provides simplified access to Pyrus in symfony environment
 */
class Pyrus
{
    protected $kernel;

    /**
     * Sets Pyrus up for later use
     *
     * @param Kernel $kernel The application's kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
        spl_autoload_register(array($this, 'pyrus_autoload'));
    }

    /**
     * Returns the directory used for Pyrus packages
     *
     * @return string The bundle directory
     */
    public function getPyrusDir()
    {
        $bundleDirs = $this->kernel->getBundleDirs();

        if (!isset($bundleDirs['Bundle'])) {
            throw new \RunTimeException("No BundleDir for namespace Bundle defined in Application Kernel.");
        }

        return $bundleDirs['Bundle'];
    }

    /**
     * Executes a Pyrus command through its CLI frontend in the bundle directory.
     *
     * @param array $argv The list of options and arguments, not including the
     *                    executing script.
     */
    public function run(array $argv)
    {
        array_unshift($argv, $this->getPyrusDir());

        $frontend = new \PEAR2\Pyrus\ScriptFrontend\Commands;
        $frontend->run($argv);
    }

    /**
     * The Pyrus autoloader taking classes from the phar file within this Bundle.
     *
     * @param string $class The class name
     */
    public function pyrus_autoload($class)
    {
        $class = str_replace(array('_','\\'), '/', $class);
        if (file_exists('phar://' . __DIR__ . '/pyrus.phar/PEAR2_Pyrus-2.0.0a2/php/' . $class . '.php')) {
            include 'phar://' . __DIR__ . '/pyrus.phar/PEAR2_Pyrus-2.0.0a2/php/' . $class . '.php';
        }
    }
}
