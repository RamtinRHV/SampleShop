<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

namespace Maple\Component\Sampleshop\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * Categories Model
 */
class CategoriesModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = [
                'id', 'a.id',
                'name', 'a.name',
                'alias', 'a.alias',
                'published', 'a.published',
                'parent_id', 'a.parent_id',
                'level', 'a.level',
                'path', 'a.path',
                'ordering', 'a.ordering',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     */
    protected function populateState($ordering = 'a.name', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $parentId = $this->getUserStateFromRequest($this->context . '.filter.parent_id', 'filter_parent_id');
        $this->setState('filter.parent_id', $parentId);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.parent_id');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\DatabaseQuery
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.name, a.alias, a.published, a.parent_id, ' .
                'a.level, a.path, a.ordering'
            )
        );
        $query->from($db->quoteName('#__sampleshop_categories', 'a'));

        // Join over the parent categories.
        $query->select($db->quoteName('p.name', 'parent_title'))
            ->join(
                'LEFT',
                $db->quoteName('#__sampleshop_categories', 'p') . ' ON p.id = a.parent_id'
            );

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published))
        {
            $query->where($db->quoteName('a.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        }
        elseif ($published === '')
        {
            $query->where($db->quoteName('a.published') . ' IN (0, 1)');
        }

        // Filter by parent category.
        $parentId = $this->getState('filter.parent_id');
        if (is_numeric($parentId))
        {
            $query->where($db->quoteName('a.parent_id') . ' = :parentId')
                ->bind(':parentId', $parentId, ParameterType::INTEGER);
        }

        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $search = '%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%');
            $query->where($db->quoteName('a.name') . ' LIKE :search')
                ->bind(':search', $search, ParameterType::STRING);
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'a.lft');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}
