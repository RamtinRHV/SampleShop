<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sampleshop
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$app = JFactory::getApplication();
$input = $app->input;
?>

<form action="<?php echo Route::_('index.php?option=com_sampleshop&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="product-form" class="form-validate">
    <div class="row">
        <div class="col-md-9">
            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_SAMPLESHOP_PRODUCT_DETAILS')); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->form->renderField('name'); ?>
                    <?php echo $this->form->renderField('alias'); ?>
                    <?php echo $this->form->renderField('catid'); ?>
                    <?php echo $this->form->renderField('price'); ?>
                    <?php echo $this->form->renderField('description'); ?>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'features', Text::_('COM_SAMPLESHOP_PRODUCT_FEATURES')); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->form->renderField('features'); ?>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>
        <div class="col-md-3">
            <div class="card card-light">
                <div class="card-body">
                    <?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
                    <?php echo $this->form->renderField('image'); ?>
                    <?php echo $this->form->renderField('featured'); ?>
                    <?php echo $this->form->renderField('published'); ?>
                    <?php echo $this->form->renderField('ordering'); ?>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>