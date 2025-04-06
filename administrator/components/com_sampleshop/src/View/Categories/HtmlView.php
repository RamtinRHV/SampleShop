<?php
namespace Maple\Component\Sampleshop\Administrator\View\Categories;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var  \Joomla\CMS\Pagination\Pagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var  \Joomla\CMS\Object\CMSObject
     */
    protected $state;

    /**
     * Form object for search filters
     *
     * @var  \Joomla\CMS\Form\Form
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     */
    public $activeFilters;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state        = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar
     *
     * @return  void
     */
    protected function addToolbar()
    {
        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_SAMPLESHOP_CATEGORIES_TITLE'), 'folder categories');

        // Get the user object
        $user = Factory::getApplication()->getIdentity();

        // Check if user can create
        if ($user->authorise('core.create', 'com_sampleshop'))
        {
            ToolbarHelper::addNew('category.add');
        }

        // Check if user can edit
        if ($user->authorise('core.edit', 'com_sampleshop') || $user->authorise('core.edit.own', 'com_sampleshop'))
        {
            ToolbarHelper::editList('category.edit');
        }

        // Check if user can change state
        if ($user->authorise('core.edit.state', 'com_sampleshop'))
        {
            ToolbarHelper::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('categories.archive');
            ToolbarHelper::checkin('categories.checkin');
        }

        // Check if user can delete
        if ($user->authorise('core.delete', 'com_sampleshop'))
        {
            ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'categories.delete', 'JTOOLBAR_DELETE');
        }

        // Check if user can admin
        if ($user->authorise('core.admin', 'com_sampleshop'))
        {
            ToolbarHelper::preferences('com_sampleshop');
        }
    }
} 