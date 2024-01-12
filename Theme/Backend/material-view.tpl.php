<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Localization\BaseStringL11nType */
$type  = $this->data['type'];

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="materialForm" method="POST" action="<?= UriFactory::build('{/api}item/material'); ?>"
                data-ui-container="#materialTable tbody"
                data-add-form="materialForm"
                data-add-tpl="#materialTable tbody .oms-add-tpl-material">
                <div class="portlet-head"><?= $this->getHtml('Material'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="code" id="iName" placeholder="" value="<?= $this->printHtml($type->title); ?>">
                    </div>
                </div>

                <div class="portlet-foot">
                    <input type="hidden" name="id" value="<?= $type->id; ?>">
                    <input id="iSubmit" name="submit" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <?= $this->data['l11nView']->render(
        $this->data['l11nValues'],
        [],
        '{/api}item/material/l11n'
    );
    ?>
</div>