<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Localization\NullBaseStringL11nType;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Localization\BaseStringL11nType */
$type  = $this->data['type'] ?? new NullBaseStringL11nType();
$isNew = $type->id === 0;

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <form id="materialForm"
                method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}item/material?csrf={$CSRF}'); ?>"
                <?= $isNew ? 'data-redirect="' . UriFactory::build('{/base}/item/material/view') . '?id={/0/response/id}"' : ''; ?>>
                <div class="portlet-head"><?= $this->getHtml('Material'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" value="<?= $this->printHtml($type->title); ?>">
                    </div>
                </div>

                <div class="portlet-foot">
                    <input type="hidden" name="id" value="<?= $type->id; ?>">
                    <?php if ($isNew) : ?>
                        <input id="iCreateSubmit" type="Submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                    <?php else : ?>
                        <input id="iSaveSubmit" type="Submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    <?php endif; ?>
                </div>
            </form>
        </section>
    </div>
</div>

<?php if (!$isNew) : ?>
<div class="row">
    <?= $this->data['l11nView']->render(
        $this->data['l11nValues'],
        [],
        '{/api}item/material/l11n?csrf={$CSRF}'
    );
    ?>
</div>
<?php endif; ?>