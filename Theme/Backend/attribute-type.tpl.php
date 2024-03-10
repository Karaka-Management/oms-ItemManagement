<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Tasks
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Attribute\Models\AttributeValueType;
use Modules\Attribute\Models\NullAttributeType;
use phpOMS\Uri\UriFactory;

$types = AttributeValueType::getConstants();

$attribute = $this->data['attribute'] ?? new NullAttributeType();

$isNew = $attribute->id === 0;

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-md-6 col-xs-12">
        <section id="task" class="portlet">
            <form id="attributeForm" method="<?= $isNew ? 'PUT' : 'POST'; ?>" action="<?= UriFactory::build('{/api}item/attribute/type'); ?>">
            <div class="portlet-head"><?= $this->getHtml('Attribute', 'Attribute', 'Backend'); ?></div>
            <div class="portlet-body">
                <div class="form-group">
                    <label for="iId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                    <input type="text" value="<?= $this->printHtml((string) $attribute->id); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="iName"><?= $this->getHtml('Name', 'Attribute', 'Backend'); ?></label>
                    <input id="iNAme" type="text" value="<?= $this->printHtml($attribute->name); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="iType"><?= $this->getHtml('Datatype', 'Attribute', 'Backend'); ?></label>
                    <select id="iType" name="type" disabled>
                        <?php foreach ($types as $key => $type) : ?>
                            <option value="<?= $type; ?>"<?= $type === $attribute->datatype ? ' selected' : ''; ?>><?= $this->printHtml($key); ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="iPattern"><?= $this->getHtml('Pattern', 'Attribute', 'Backend'); ?></label>
                    <input id="iPattern" type="text" value="<?= $this->printHtml($attribute->validationPattern); ?>">
                </div>

                <div class="form-group">
                    <label class="checkbox" for="iRequired">
                        <input id="iRequired" type="checkbox" name="required" value="1"<?= $attribute->isRequired ? ' checked' : ''; ?>>
                        <span class="checkmark"></span>
                        <?= $this->getHtml('IsRequired', 'Attribute', 'Backend'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox" for="iCustom">
                        <input id="iCustom" type="checkbox" name="custom" value="1" <?= $attribute->custom ? ' checked' : ''; ?>>
                        <span class="checkmark"></span>
                        <?= $this->getHtml('CustomValue', 'Attribute', 'Backend'); ?>
                    </label>
                </div>
            </div>
            <div class="portlet-foot">
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

<div class="row">
    <?= $this->data['l11nView']->render(
        $this->data['l11nValues'],
        [],
        '{/api}item/attribute/l11n'
    );
    ?>
</div>