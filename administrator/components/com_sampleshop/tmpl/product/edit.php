<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$app = Factory::getApplication();
$input = $app->input;

// Create shortcut to parameters
$params = clone $this->state->get('params');

$layout  = 'edit';
$tmpl = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
?>

<form action="<?php echo Route::_('index.php?option=com_sampleshop&task=product.save' . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="product-form" class="form-validate">

    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-6">
                    <?php echo $this->form->renderField('name'); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $this->form->renderField('alias'); ?>
                </div>
            </div>
            
            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_SAMPLESHOP_PRODUCT_TAB_DETAILS')); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->form->renderField('description'); ?>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $this->form->renderField('published'); ?>
                    <?php if (isset($this->form->getField('created')->value)) : ?>
                        <?php echo $this->form->renderField('created'); ?>
                    <?php endif; ?>
                    <?php if (isset($this->form->getField('created_by')->value)) : ?>
                        <?php echo $this->form->renderField('created_by'); ?>
                    <?php endif; ?>
                    <?php if (isset($this->form->getField('modified')->value)) : ?>
                        <?php echo $this->form->renderField('modified'); ?>
                    <?php endif; ?>
                    <?php if (isset($this->form->getField('modified_by')->value)) : ?>
                        <?php echo $this->form->renderField('modified_by'); ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>
        <div class="col-md-3">
            <div class="card card-light">
                <div class="card-body">
                    <?php echo $this->form->renderField('price'); ?>
                    <?php echo $this->form->renderField('category_id'); ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form> 