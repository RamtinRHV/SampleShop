<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Service\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Service provider for the component's services
 */
class SampleshopServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\Maple\\Component\\Sampleshop'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Maple\\Component\\Sampleshop'));
        $container->registerServiceProvider(new RouterFactory('\\Maple\\Component\\Sampleshop'));
        
        $container->set(
            Registry::class,
            function (Container $container) {
                $registry = new Registry;
                $registry->register('sampleshop', new Sampleshop);
                
                return $registry;
            }
        );
    }
}
