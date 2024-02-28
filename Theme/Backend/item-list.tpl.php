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

use Modules\ItemManagement\Models\StockIdentifierType;
use phpOMS\Stdlib\Base\FloatInt;
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
/** @var \Modules\ItemManagement\Models\Item[] $items */
$items = $this->data['items'] ?? [];

echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Items'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="iSalesItemList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td><?= $this->getHtml('Number'); ?>
                        <label for="iSalesItemList-sort-1">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-1">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-2">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-2">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Name'); ?>
                        <label for="iSalesItemList-sort-3">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-3">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-4">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-4">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <label for="iSalesItemList-sort-5">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-5">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-6">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-6">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Price'); ?>
                        <label for="iSalesItemList-sort-9">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-9">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-10">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-10">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Available'); ?>
                        <label for="iSalesItemList-sort-11">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-11">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-12">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-12">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Reserved'); ?>
                        <label for="iSalesItemList-sort-13">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-13">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-14">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-14">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>
                    <td><?= $this->getHtml('Ordered'); ?>
                        <label for="iSalesItemList-sort-15">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-15">
                            <i class="sort-asc g-icon">expand_less</i>
                        </label>
                        <label for="iSalesItemList-sort-16">
                            <input type="radio" name="iSalesItemList-sort" id="iSalesItemList-sort-16">
                            <i class="sort-desc g-icon">expand_more</i>
                        </label>
                        <label>
                            <i class="filter g-icon">filter_alt</i>
                        </label>

                <tbody>
                <?php $count = 0; foreach ($items as $key => $value) : ++$count;
                $url         = UriFactory::build('{/base}/item/view?{?}&id=' . $value->id);
                $image       = $value->getFileByTypeName('item_profile_image');
                ?>
                <tr data-href="<?= $url; ?>">
                    <td><a href="<?= $url; ?>"><img alt="<?= $this->getHtml('IMG_alt_item'); ?>" width="30" loading="lazy" class="item-image"
                            src="<?= $image->id === 0
                                ? 'Web/Backend/img/logo_grey.png'
                                : UriFactory::build($image->getPath()); ?>"></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->number); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name1')->content); ?></a>
                    <td><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n('name2')->content); ?></a>
                    <td class="rT"><a href="<?= $url; ?>"><?= $this->getCurrency($value->salesPrice, symbol: ''); ?></a>
                    <?php if ($value->stockIdentifier === StockIdentifierType::NONE) : ?>
                        <td>
                        <td>
                        <td>
                    <?php else : ?>
                    <td class="rT"><a href="<?= $url; ?>">
                        <?php
                            $sum = 0;
                            foreach ($this->data['dists'][$value->id] ?? [] as $dist) {
                                $sum += $dist->quantity;
                            }
                            $total = new FloatInt($sum);

                            echo $total->getAmount(\reset($value->container)->quantityDecimals);
                        ?></a>

                    <td class="rT"><a href="<?= $url; ?>">
                        <?php
                            $total = new FloatInt($this->data['reserved'][$value->id] ?? 0);

                            echo $total->getAmount(\reset($value->container)->quantityDecimals);
                        ?></a>

                    <td class="rT"><a href="<?= $url; ?>">
                        <?php
                            $total = new FloatInt($this->data['ordered'][$value->id] ?? 0);

                            echo $total->getAmount(\reset($value->container)->quantityDecimals);
                        ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="9" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
        </section>
    </div>
</div>
