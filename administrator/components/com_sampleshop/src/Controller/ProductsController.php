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
 * Products Controller
 */
class ProductsController extends AdminController
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
    public function getModel($name = 'Product', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Method to toggle the featured setting of a list of products.
     *
     * @return  void
     */
    public function featured()
    {
        // Check for request forgeries
        $this->checkToken();

        $ids    = $this->input->get('cid', [], 'array');
        $values = ['featured' => 1, 'unfeatured' => 0];
        $task   = $this->getTask();
        $value  = \array_key_exists($task, $values) ? $values[$task] : 0;

        // Get the model
        $model = $this->getModel();

        // Access check
        if (!$this->allowEdit())
        {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');
            $this->setRedirect(
                'index.php?option=com_sampleshop&view=products'
            );
            return;
        }

        // Publish the items
        try
        {
            $model->featured($ids, $value);
            $message = Text::plural('COM_SAMPLESHOP_N_ITEMS_FEATURED', \count($ids));
            $this->setMessage($message);
        }
        catch (\Exception $e)
        {
            $this->setMessage($e->getMessage(), 'error');
        }

        $this->setRedirect('index.php?option=com_sampleshop&view=products');
    }

    /**
     * Method to toggle the published setting of a list of products.
     *
     * @return  void
     */
    public function publish()
    {
        parent::publish();
    }
}
