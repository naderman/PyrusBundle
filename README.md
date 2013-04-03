PyrusBundle
-----------


Ever needed to use PEAR packages with Symfony2? This bundle gives you a helping hand.

### Setup

1. Add the bundle to deps:

         [PyrusBundle]
           git=git@github.com:tarjei/PyrusBundle.git
           target=/bundles/phpBB/PyrusBundle

2. Update your autoload.php, add the Pyrus bundle:

         $loader->registerNamespaces(array(
           ...
           'phpBB\\PyrusBundle' => __DIR__.'/../vendor/bundles',
         ));

3. Update your AppKernel with the new bundle:

         if ( PHP_SAPI == 'cli' ) {
           $bundles[] = new phpBB\PyrusBundle\phpBBPyrusBundle();
         }

4. Now you should have a new pyrus command:

         app/console pyrus:pyrus help

### Usage 

To install your dependencies, you first use pyrus to install the dependency and then add it to autoload.php:

     app/console pyrus:pyrus install pear/Net_Url2-beta

And in autoload.php:

     $loader->registerPrefixes(array(
          ...
       'Net_'             => __DIR__.'/../vendor/pear2/php',
     ));

Another option is to add the ../vendor/pear2 path to your include path and use require. 

## Updating dependencies

If you want to keep a specified list of dependecies installed, then you can use the command `pyrus:update`. 

Add the dependencies to a local deps.pyrus file, one on each line:

    pear/Net_Url2-beta
    pear/HTTP_Request2-beta

And run app/console pyrus:update to have pyrus ensure the packages are installed.


