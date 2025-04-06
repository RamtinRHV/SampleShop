<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

/**
 * Category Controller
 */
class CategoryController extends FormController
{
    protected $text_prefix = 'COM_SAMPLESHOP_CATEGORY';

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

        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        // Check edit on the record asset
        if ($user->authorise('core.edit', 'com_sampleshop.category.' . $recordId))
        {
            return true;
        }

        // Check edit own on the record asset
        if ($user->authorise('core.edit.own', 'com_sampleshop.category.' . $recordId))
        {
            $record = $this->getModel()->getItem($recordId);

            if (empty($record))
            {
                return false;
            }

            return $user->id == $record->created_by;
        }

        return false;
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