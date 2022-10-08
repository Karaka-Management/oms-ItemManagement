<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Media\Models\NullMedia;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
$items = $this->getData('items');

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Items'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="iWarehouseItemList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <input id="itemList-r1-asc" name="itemList-sort" type="radio"><label for="itemList-r1-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r1-desc" name="itemList-sort" type="radio"><label for="itemList-r1-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                    <td><?= $this->getHtml('Name'); ?>
                        <input id="itemList-r2-asc" name="itemList-sort" type="radio"><label for="itemList-r2-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r2-desc" name="itemList-sort" type="radio"><label for="itemList-r2-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                    <td><?= $this->getHtml('Name'); ?>
                        <input id="itemList-r3-asc" name="itemList-sort" type="radio"><label for="itemList-r3-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r3-desc" name="itemList-sort" type="radio"><label for="itemList-r3-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <input id="itemList-r4-asc" name="itemList-sort" type="radio"><label for="itemList-r4-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r4-desc" name="itemList-sort" type="radio"><label for="itemList-r4-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                    <td><?= $this->getHtml('Available'); ?>
                        <input id="itemList-r6-asc" name="itemList-sort" type="radio"><label for="itemList-r6-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r6-desc" name="itemList-sort" type="radio"><label for="itemList-r6-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                    <td><?= $this->getHtml('Reserved'); ?>
                        <input id="itemList-r7-asc" name="itemList-sort" type="radio"><label for="itemList-r7-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r7-desc" name="itemList-sort" type="radio"><label for="itemList-r7-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                    <td><?= $this->getHtml('Ordered'); ?>
                        <input id="itemList-r8-asc" name="itemList-sort" type="radio"><label for="itemList-r8-asc"><i class="sort-asc fa fa-chevron-up"></i></label>
                        <input id="itemList-r8-desc" name="itemList-sort" type="radio"><label for="itemList-r8-desc"><i class="sort-desc fa fa-chevron-down"></i></label>
                <tbody>
                <?php $count = 0; foreach ($items as $key => $value) : ++$count;
                $url         = UriFactory::build('{/prefix}warehouse/item/profile?{?}&id=' . $value->getId());
                $image       = $value->getFileByType('backend_image');
                ?>
                <tr data-href="<?= $url; ?>">
                    <td><a href="<?= $url; ?>"><img alt="<?= $this->printHtml($image->name); ?>" width="30" loading="lazy" class="item-image"
                            src="<?= $image instanceof NullMedia ?
                                        UriFactory::build('Web/Backend/img/user_default_' . \mt_rand(1, 6) .'.png') :
                                        UriFactory::build('{/prefix}' . $image->getPath()); ?>"></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->number); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name1')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name2')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name3')->description); ?></a>
                    <td>
                    <td>
                    <td>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="9" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>
