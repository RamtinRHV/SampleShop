<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Product Controller
 */
class ProductController extends FormController
{
    protected $text_prefix = 'COM_SAMPLESHOP_PRODUCT';

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

        // Check edit on the record asset
        if ($user->authorise('core.edit', 'com_sampleshop.product.' . $recordId))
        {
            return true;
        }

        // Check edit own on the record asset
        if ($user->authorise('core.edit.own', 'com_sampleshop.product.' . $recordId))
        {
            // Get the owner
            $record = $this->getModel()->getItem($recordId);

            if (empty($record))
            {
                return false;
            }

            $ownerId = $record->created_by;

            // If the owner matches 'me' then do the test.
            if ($ownerId == $user->id)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Method override to check if you can save a new or existing record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     */
    protected function allowSave($data, $key = 'id')
    {
        $recordId = isset($data[$key]) ? $data[$key] : 0;

        if ($recordId)
        {
            return $this->allowEdit($data, $key);
        }
        else
        {
            return $this->allowAdd($data);
        }
    }

    public function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        
        return $append;
    }

    protected function getRedirectToListAppend()
    {
        $append = parent::getRedirectToListAppend();
        
        return $append;
    }
}