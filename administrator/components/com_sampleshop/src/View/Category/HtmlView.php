<?php
namespace Maple\Component\Sampleshop\Administrator\View\Category;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null)
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

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
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            Text::_('COM_SAMPLESHOP_CATEGORY_' . ($isNew ? 'NEW' : 'EDIT')),
            'folder category-' . ($isNew ? 'add' : 'edit')
        );

        ToolbarHelper::apply('category.apply');
        ToolbarHelper::save('category.save');
        ToolbarHelper::save2new('category.save2new');

        if (!$isNew)
        {
            ToolbarHelper::save2copy('category.save2copy');
        }

        ToolbarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
    }
} 