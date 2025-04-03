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
 * Products Model
 */
class ProductsModel extends ListModel
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
                'catid', 'a.catid',
                'price', 'a.price',
                'featured', 'a.featured',
                'published', 'a.published',
                'created', 'a.created',
                'ordering', 'a.ordering',
                'category_title'
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

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

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
        $id .= ':' . $this->getState('filter.category_id');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\DatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Select required fields
        $query->select(
            $db->quoteName(
                [
                    'a.id',
                    'a.name',
                    'a.alias',
                    'a.price',
                    'a.description',
                    'a.category_id',
                    'a.published',
                    'a.ordering',
                    'a.created',
                    'a.created_by',
                    'a.hits',
                    'c.name',
                ],
                [
                    'id',
                    'name',
                    'alias',
                    'price',
                    'description',
                    'category_id',
                    'published',
                    'ordering',
                    'created',
                    'created_by',
                    'hits',
                    'category_name'
                ]
            )
        );

        $query->from($db->quoteName('#__sampleshop_products', 'a'))
            ->join('LEFT', $db->quoteName('#__sampleshop_categories', 'c'), 
                $db->quoteName('c.id') . ' = ' . $db->quoteName('a.category_id'));

        // Filter by search
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            $query->where('(a.name LIKE ' . $search . ' OR a.description LIKE ' . $search . ')');
        }

        // Filter by category
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId)) {
            $query->where($db->quoteName('a.category_id') . ' = ' . (int) $categoryId);
        }

        // Filter by price range
        $priceFrom = $this->getState('filter.price_from');
        $priceTo = $this->getState('filter.price_to');
        
        if (is_numeric($priceFrom)) {
            $query->where($db->quoteName('a.price') . ' >= ' . (float) $priceFrom);
        }
        if (is_numeric($priceTo)) {
            $query->where($db->quoteName('a.price') . ' <= ' . (float) $priceTo);
        }

        // Add the list ordering clause
        $orderCol = $this->state->get('list.ordering', 'a.name');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}
