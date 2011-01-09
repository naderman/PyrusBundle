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
    protected $frontend;
    protected $config;

    /**
     * Sets Pyrus up for later use
     *
     * @param Kernel $kernel The application's kernel
     */
    public function __construct(Kernel $kernel, \PEAR2\Pyrus\ScriptFrontend\Commands $frontend = null)
    {
        if (!$frontend) {
            spl_autoload_register(array($this, 'pyrus_autoload'));
            $frontend = new \PEAR2\Pyrus\ScriptFrontend\Commands;
        }

        $this->kernel = $kernel;
        $this->frontend = $frontend;

        $configclass = $frontend::$configclass;
        $this->config = $configclass::singleton($this->getPyrusDir());

        $this->setupPyrus();
    }

    public function setupPyrus()
    {
        $settings = array(
            //'php_dir' => '@php_dir@',
            'ext_dir' => '@php_dir@/pyrus/ext',
            'doc_dir' => '@php_dir@/pyrus/docs',
            'bin_dir' => $this->kernel->getRootDir() . '/bin',
            //'data_dir' => '@php_dir@/pyrus/data',
            'cfg_dir' => '@php_dir@/pyrus/cfg',
            'www_dir' => $this->kernel->getRootDir() . '/web',
            'test_dir' => '@php_dir@/pyrus/tests',
            'src_dir' => '@php_dir@/pyrus/src',
            'auto_discover' => 1,
            'cache_dir' => '@php_dir@/pyrus/cache',
            'temp_dir' => '@php_dir@/pyrus/temp',
            'download_dir' => '@php_dir@/pyrus/downloads',
            'plugins_dir' => '@default_config_dir@',
        );

        $options = array('plugin' => false);

        foreach ($settings as $name => $desiredValue) {
            $currentValue = $this->getConfig($name);

            if ($currentValue != $desiredValue) {
                $this->setConfig($name, $desiredValue);
            }
        }
    }

    /**
     * Retrieves a value from the Pyrus configuration.
     *
     * @param  string $name The variable name
     * @return mixed        The configuration value
     */
    public function getConfig($name)
    {
        $this->config = \PEAR2\Pyrus\Config::current();
        return $this->config->__get($name);
    }

    /**
     * Overwrites a configuration value.
     *
     * @param  string $name  The variable name
     * @param  mixed  $value New value
     */
    public function setConfig($name, $value)
    {
        $this->config->__set($name, $value);
        $this->config->saveConfig();
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

        $this->frontend->run($argv);
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
