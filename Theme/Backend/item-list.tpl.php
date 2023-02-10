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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Media\Models\NullMedia;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
/** @var \Modules\ItemManagement\Models\Item[] $items */
$items = $this->getData('items') ?? [];

echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Items'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="iSalesItemList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td><?= $this->getHtml('Number'); ?>
                        <label for="iSalesItemList-sort-1">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-1">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-2">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-2">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Name'); ?>
                        <label for="iSalesItemList-sort-3">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-3">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-4">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-4">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Name'); ?>
                        <label for="iSalesItemList-sort-5">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-5">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-6">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-6">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="iSalesItemList-sort-7">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-7">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-8">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-8">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Price'); ?>
                        <label for="iSalesItemList-sort-9">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-9">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-10">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-10">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Available'); ?>
                        <label for="iSalesItemList-sort-11">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-11">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-12">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-12">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Reserved'); ?>
                        <label for="iSalesItemList-sort-13">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-13">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-14">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-14">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>
                    <td><?= $this->getHtml('Ordered'); ?>
                        <label for="iSalesItemList-sort-15">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-15">
                            <i class="sort-asc fa fa-chevron-up"></i>
                        </label>
                        <label for="iSalesItemList-sort-16">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-16">
                            <i class="sort-desc fa fa-chevron-down"></i>
                        </label>
                        <label>
                            <i class="filter fa fa-filter"></i>
                        </label>

                <tbody>
                <?php $count = 0; foreach ($items as $key => $value) : ++$count;
                $url         = UriFactory::build('{/lang}/{/app}/item/profile?{?}&id=' . $value->getId());
                $image       = $value->getFileByTypeName('item_profile_image');
                ?>
                <tr data-href="<?= $url; ?>">
                    <td><a href="<?= $url; ?>"><img alt="<?= $this->getHtml('IMG_alt_item'); ?>" width="30" loading="lazy" class="item-image"
                            src="<?= $image instanceof NullMedia ?
                                        'Web/Backend/img/logo_grey.png' :
                                        UriFactory::build($image->getPath()); ?>"></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->number); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name1')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name2')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name3')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->salesPrice->getCurrency()); ?></a>
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
