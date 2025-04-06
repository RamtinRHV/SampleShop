<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;
use Joomla\String\StringHelper;
use Joomla\CMS\Filter\OutputFilter as JFilterOutput;

/**
 * Product Model
 */
class ProductModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     */
    public $typeAlias = 'com_sampleshop.product';

    protected $text_prefix = 'COM_SAMPLESHOP';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     */
    public function getTable($type = 'Product', $prefix = 'Maple\\Component\\Sampleshop\\Administrator\\Table', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|boolean  A Form object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_sampleshop.product', 'product', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        // Load custom fields
//        if (!empty($this->getState('params')->get('custom_fields_enable')))
//        {
//            $fieldsForm = new Form('com_fields.product');
/*            $fieldsForm->load('<?xml version="1.0" encoding="utf-8"?><form/>');*/
//
//            \JPluginHelper::importPlugin('fields');
//            Factory::getApplication()->triggerEvent('onCustomFieldsPrepareDom', array($fieldsForm, 'com_sampleshop.product'));
//
//            if (!empty($fieldsForm->getFieldsets()))
//            {
//                Form::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_fields/models/fields');
//                $form->load($fieldsForm->getXml());
//            }
//        }
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_sampleshop.edit.product.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  \stdClass|boolean  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if ($item)
        {
            // Load the product features
            $db = $this->getDatabase();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__sampleshop_features'))
                ->where($db->quoteName('product_id') . ' = :productId')
                ->bind(':productId', $item->id, ParameterType::INTEGER);

            $db->setQuery($query);
            $features = $db->loadObjectList();

            $item->features = $features;
        }

        return $item;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $features = $input->post->get('features', [], 'array');

        // Save the main product data
        $result = parent::save($data);

        if ($result)
        {
            $productId = $this->getState('product.id');
            
            // First, delete existing features
            $db = $this->getDatabase();
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__sampleshop_features'))
                ->where($db->quoteName('product_id') . ' = :productId')
                ->bind(':productId', $productId, ParameterType::INTEGER);
            
            $db->setQuery($query);
            $db->execute();
            
            // Then save new features
            if (!empty($features))
            {
                foreach ($features as $feature)
                {
                    if (!empty($feature['name']) && !empty($feature['value']))
                    {
                        $object = new \stdClass;
                        $object->product_id = $productId;
                        $object->name = $feature['name'];
                        $object->value = $feature['value'];
                        
                        $db->insertObject('#__sampleshop_features', $object);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Method to prepare the saved data.
     *
     * @param   array  &$data  The form data.
     *
     * @return  void
     */
    protected function prepareData(&$data)
    {
        // Automatically generate an alias if empty
        if (empty($data['alias']))
        {
            $data['alias'] = $data['name'];
        }

        $data['alias'] = \JFilterOutput::stringURLSafe($data['alias']);

        // Set created/modified times
        $user = Factory::getUser();
        $date = Factory::getDate();

        if (empty($data['id']))
        {
            $data['created'] = $date->toSql();
            $data['created_by'] = $user->id;
        }
        else
        {
            $data['modified'] = $date->toSql();
            $data['modified_by'] = $user->id;
        }
    }

    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        $table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
        $table->alias = $table->alias ?: StringHelper::increment($this->generateAlias($table->name), 'dash');

        if (empty($table->id))
        {
            // Set the values
            $table->created = $date->toSql();
            $table->created_by = $user->id;

            // Set ordering to the last item if not set
            if (empty($table->ordering))
            {
                $db = $this->getDbo();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->quoteName('#__sampleshop_products'));
                $db->setQuery($query);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            }
        }
        else
        {
            // Set the values
            $table->modified = $date->toSql();
            $table->modified_by = $user->id;
        }
    }

    protected function generateAlias($name)
    {
        return JFilterOutput::stringURLSafe($name);
    }
}
