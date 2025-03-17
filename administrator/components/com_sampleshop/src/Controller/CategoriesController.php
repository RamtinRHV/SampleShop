<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;

/**
 * Categories Controller
 */
class CategoriesController extends AdminController
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     */
    public function getModel($name = 'Category', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Method to toggle the published setting of a list of categories.
     *
     * @return  void
     */
    public function publish()
    {
        parent::publish();
    }
}
