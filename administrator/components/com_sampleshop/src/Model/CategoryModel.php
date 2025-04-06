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
use Joomla\String\StringHelper;
use Joomla\Database\ParameterType;
use Joomla\CMS\Filter\OutputFilter as JFilterOutput;

/**
 * Category Model
 */
class CategoryModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     */
    public $typeAlias = 'com_sampleshop.category';

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
    public function getTable($type = 'Category', $prefix = 'Maple\\Component\\Sampleshop\\Administrator\\Table', $config = array())
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
        $form = $this->loadForm('com_sampleshop.category', 'category', array('control' => 'jform', 'load_data' => $loadData));

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
        $data = Factory::getApplication()->getUserState('com_sampleshop.edit.category.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
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
        // Prepare the data
        $this->prepareData($data);

        // Save the category data
        $result = parent::save($data);

        if ($result)
        {
            // Update the path and level for all child categories
            $this->rebuildPath($this->getState('category.id'));
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

        // Calculate the level and path
        if (!empty($data['parent_id']))
        {
            $db = $this->getDatabase();
            $query = $db->getQuery(true)
                ->select('level, path')
                ->from($db->quoteName('#__sampleshop_categories'))
                ->where($db->quoteName('id') . ' = ' . (int) $data['parent_id']);
            
            $db->setQuery($query);
            $parent = $db->loadObject();
            
            if ($parent)
            {
                $data['level'] = $parent->level + 1;
                $data['path'] = $parent->path . '/' . $data['alias'];
            }
        }
        else
        {
            $data['level'] = 1;
            $data['path'] = $data['alias'];
        }
    }

    /**
     * Rebuild the path for category and its children
     *
     * @param   integer  $pk  The id of the parent category
     *
     * @return  boolean  True on success, false on failure
     */
    protected function rebuildPath($pk)
    {
        $category = $this->getItem($pk);
        
        if (!$category)
        {
            return false;
        }

        // Get all children categories
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__sampleshop_categories'))
            ->where($db->quoteName('parent_id') . ' = ' . (int) $pk);
        
        $db->setQuery($query);
        $children = $db->loadColumn();

        // Update child categories
        foreach ($children as $childId)
        {
            $child = $this->getItem($childId);
            
            // Update level and path
            $child->level = $category->level + 1;
            $child->path = $category->path . '/' . $child->alias;
            
            // Save without rebuilding paths again
            $this->save((array) $child);
            
            // Rebuild paths for child's children
            $this->rebuildPath($childId);
        }

        return true;
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
                    ->from($db->quoteName('#__sampleshop_categories'));
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
