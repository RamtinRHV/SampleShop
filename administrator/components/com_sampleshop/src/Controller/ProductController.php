<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;

/**
 * Product Controller
 */
class ProductController extends AdminController
{
    /**
     * Method to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     */
    protected function allowAdd($data = [])
    {
        return Factory::getApplication()->getIdentity()->authorise('core.create', 'com_sampleshop');
    }

    /**
     * Method to check if you can edit a record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     */
    protected function allowEdit($data = [], $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = Factory::getApplication()->getIdentity();

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        // Check edit on the record asset (explicit or inherited)
        if ($user->authorise('core.edit', 'com_sampleshop.product.' . $recordId))
        {
            return true;
        }

        // Check edit own on the record asset (explicit or inherited)
        if ($user->authorise('core.edit.own', 'com_sampleshop.product.' . $recordId))
        {
            // Existing record already has an owner, get it
            $record = $this->getModel()->getItem($recordId);

            if (empty($record))
            {
                return false;
            }

            // Grant if current user is owner of the record
            return $user->id == $record->created_by;
        }

        return false;
    }
}