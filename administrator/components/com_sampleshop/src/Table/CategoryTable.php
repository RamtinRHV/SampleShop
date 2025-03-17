<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;

/**
 * Category Table class
 */
class CategoryTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database driver object.
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__sampleshop_categories', 'id', $db);
    }

    /**
     * Method to bind an associative array or object to the Table instance properties.
     *
     * @param   array|object  $src     An associative array or object to bind to the Table instance.
     * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
     *
     * @return  boolean  True on success.
     */
    public function bind($src, $ignore = [])
    {
        return parent::bind($src, $ignore);
    }

    /**
     * Method to store a row in the database from the Table instance properties.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     */
    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getUser();

        // Set created date if not set
        if (empty($this->created))
        {
            $this->created = $date->toSql();
        }

        // Set created by user if not set
        if (empty($this->created_by))
        {
            $this->created_by = $user->id;
        }

        // Set modified date
        if (!empty($this->id))
        {
            $this->modified = $date->toSql();
            $this->modified_by = $user->id;
        }

        return parent::store($updateNulls);
    }

    /**
     * Method to perform sanity checks on the Table instance properties to ensure
     * they are safe to store in the database.
     *
     * @return  boolean  True if the instance is sane and able to be stored in the database.
     */
    public function check()
    {
        // Check for valid name
        if (trim($this->name) == '')
        {
            $this->setError('COM_SAMPLESHOP_ERROR_CATEGORY_NAME');
            return false;
        }

        // Check for existing name under the same parent
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__sampleshop_categories'))
            ->where($db->quoteName('name') . ' = ' . $db->quote($this->name))
            ->where($db->quoteName('parent_id') . ' = ' . (int) $this->parent_id)
            ->where($db->quoteName('id') . ' != ' . (int) $this->id);
        
        $db->setQuery($query);
        $duplicate = (boolean) $db->loadResult();

        if ($duplicate)
        {
            $this->setError('COM_SAMPLESHOP_ERROR_CATEGORY_UNIQUE_NAME');
            return false;
        }

        // If the alias field is empty, set it to the name
        if (empty($this->alias))
        {
            $this->alias = $this->name;
        }

        $this->alias = \JFilterOutput::stringURLSafe($this->alias);

        return true;
    }
}
