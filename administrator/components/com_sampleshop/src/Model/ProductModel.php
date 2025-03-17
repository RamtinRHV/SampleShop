<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;

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

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     */
    public function getTable($type = 'Product', $prefix = 'Administrator', $config = [])
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
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_sampleshop.product',
            'product',
            [
                'control' => 'jform',
                'load_data' => $loadData
            ]
        );

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
        $app = Factory::getApplication();
        $data = $app->getUserState('com_sampleshop.edit.product.data', []);

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
}
