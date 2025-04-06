<?php
namespace Maple\Component\Sampleshop\Administrator\View\Product;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit a product
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The Form object
     *
     * @var  \Joomla\CMS\Form\Form
     */
    protected $form;

    /**
     * The active item
     *
     * @var  object
     */
    protected $item;

    /**
     * The model state
     *
     * @var  \Joomla\CMS\Object\CMSObject
     */
    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        // Initialize properties
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        // Check for errors
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // Set the toolbar
        $this->addToolbar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar
     *
     * @return  void
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $canDo = \JHelperContent::getActions('com_sampleshop');

        ToolbarHelper::title(
            Text::_('COM_SAMPLESHOP_PRODUCT_' . ($isNew ? 'NEW' : 'EDIT')),
            'cube product-' . ($isNew ? 'add' : 'edit')
        );

        // Since we don't track these assets at the item level, use the category permissions.
        if ($canDo->get('core.create') || count($this->getUser()->getAuthorisedCategories('com_sampleshop', 'core.create')) > 0)
        {
            ToolbarHelper::apply('product.apply');
            ToolbarHelper::save('product.save');
            ToolbarHelper::save2new('product.save2new');
        }

        if (!$isNew && ($canDo->get('core.create') || count($this->getUser()->getAuthorisedCategories('com_sampleshop', 'core.create')) > 0))
        {
            ToolbarHelper::save2copy('product.save2copy');
        }

        if (empty($this->item->id))
        {
            ToolbarHelper::cancel('product.cancel');
        }
        else
        {
            ToolbarHelper::cancel('product.cancel', 'JTOOLBAR_CLOSE');
        }

        ToolbarHelper::divider();
        ToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER_EDIT');
    }
} 