<?php
namespace Maple\Component\Sampleshop\Administrator\View\Products;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $filterForm;
    protected $activeFilters;

    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state        = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        return parent::display($tpl);
    }

    protected function addToolbar()
    {
        $canDo = Factory::getApplication()->getIdentity()->authorise('core.create', 'com_sampleshop');

        ToolbarHelper::title(Text::_('COM_SAMPLESHOP_PRODUCTS_TITLE'), 'stack');

        if ($canDo)
        {
            ToolbarHelper::addNew('product.add');
        }

        if ($canDo)
        {
            ToolbarHelper::editList('product.edit');
        }

        if ($canDo)
        {
            ToolbarHelper::publish('products.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('products.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($canDo)
        {
            ToolbarHelper::deleteList('', 'products.delete', 'JTOOLBAR_DELETE');
        }

        if ($canDo)
        {
            ToolbarHelper::preferences('com_sampleshop');
        }
    }
} 