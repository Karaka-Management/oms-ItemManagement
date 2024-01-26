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

/** @var \phpOMS\Views\View $this */
$items = $this->data['items'];

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Items'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="iPurchaseItemList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                        <label for="iPurchaseItemList-sort-1">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-1">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-2">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-2">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Name'); ?>
                        <label for="iPurchaseItemList-sort-3">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-3">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-4">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-4">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Name'); ?>
                        <label for="iPurchaseItemList-sort-5">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-6">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="iPurchaseItemList-sort-7">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-7">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-8">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-8">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Price'); ?>
                        <label for="iPurchaseItemList-sort-9">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-9">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-10">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-10">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Available'); ?>
                        <label for="iPurchaseItemList-sort-11">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-11">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-12">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-12">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Reserved'); ?>
                        <label for="iPurchaseItemList-sort-13">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-13">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-14">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-14">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Ordered'); ?>
                        <label for="iPurchaseItemList-sort-15">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-15">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iPurchaseItemList-sort-16">
                            <input type="radio" name="iPurchaseItemList-sort" id="iPurchaseItemList-sort-16">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                <tbody>
                <?php $count = 0; foreach ($items as $key => $value) : ++$count;
                $url         = UriFactory::build('{/base}/purchase/item/view?{?}&id=' . $value->id);
                $image       = $value->getFileByType('backend_image');
                ?>
                <tr data-href="<?= $url; ?>">
                    <td><a href="<?= $url; ?>"><img alt="<?= $this->getHtml('IMG_alt_item'); ?>" width="30" loading="lazy" class="item-image"
                            src="<?= $image->id === 0 ?
                                UriFactory::build('Web/Backend/img/user_default_' . \mt_rand(1, 6) .'.png') :
                                UriFactory::build('{/base}/' . $image->getPath()); ?>"></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->number); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name1')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name2')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name3')->description); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->purchasePrice->getCurrency()); ?></a>
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
