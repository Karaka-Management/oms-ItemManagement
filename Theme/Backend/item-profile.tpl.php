<?php
/**
 * Karaka
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

use Modules\Media\Models\NullMedia;
use phpOMS\Localization\ISO639Enum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Localization\Money;
use phpOMS\Localization\NullLocalization;
use phpOMS\Message\Http\HttpHeader;
use phpOMS\Uri\UriFactory;

/** @var \Modules\ItemManagement\Models\Item $item */
$item = $this->getData('item');

$attribute = $item->getAttributes();

$notes     = $item->getNotes();
$files     = $item->files;
$itemImage = $this->getData('itemImage') ?? new NullMedia();

$newestInvoices    = $this->getData('newestInvoices') ?? [];
$allInvoices       = $this->getData('allInvoices') ?? [];
$topCustomers      = $this->getData('topCustomers') ?? [[], []];
$regionSales       = $this->getData('regionSales') ?? [];
$countrySales      = $this->getData('countrySales') ?? [];
$monthlySalesCosts = $this->getData('monthlySalesCosts') ?? [];

$languages = ISO639Enum::getConstants();

/** @var \phpOMS\Localization\Localization $l11n */
$l11n = $this->getData('defaultlocalization') ?? new NullLocalization();

echo $this->getData('nav')->render();
?>

<div class="tabview tab-2">
    <div class="box wf-100 col-xs-12">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('Profile'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('Localization'); ?></label></li>
            <li><label for="c-tab-3"><?= $this->getHtml('Attributes'); ?></label></li>
            <li><label for="c-tab-4"><?= $this->getHtml('SalesPricing'); ?></label></li>
            <li><label for="c-tab-5"><?= $this->getHtml('Procurement'); ?></label></li>
            <li><label for="c-tab-6"><?= $this->getHtml('Production'); ?></label></li>
            <li><label for="c-tab-7"><?= $this->getHtml('QA'); ?></label></li>
            <li><label for="c-tab-8"><?= $this->getHtml('Packaging'); ?></label></li>
            <li><label for="c-tab-9"><?= $this->getHtml('Accounting'); ?></label></li>
            <li><label for="c-tab-10"><?= $this->getHtml('Stock'); ?></label></li>
            <li><label for="c-tab-11"><?= $this->getHtml('Disposal'); ?></label></li>
            <li><label for="c-tab-12"><?= $this->getHtml('Notes'); ?></label></li>
            <li><label for="c-tab-13"><?= $this->getHtml('Media'); ?></label></li>
            <li><label for="c-tab-14"><?= $this->getHtml('Bills'); ?></label></li>
            <li><label for="c-tab-15"><?= $this->getHtml('Logs'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-lg-3 last-lg">
                    <section class="portlet">
                        <form>
                            <div class="portlet-body">
                              <table class="layout wf-100">
                                    <tr><td><label for="iId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <tr><td><span class="input"><button type="button" formaction=""><i class="fa fa-book"></i></button><input type="number" id="iId" min="1" name="id" value="<?= $this->printHtml($item->number); ?>" disabled></span>
                                    <tr><td><label for="iName1"><?= $this->getHtml('Name1'); ?></label>
                                    <tr><td><input type="text" id="iName1" name="name1" value="<?= $this->printHtml($item->getL11n('name1')->content); ?>" spellcheck="false" required>
                                    <tr><td><label for="iName2"><?= $this->getHtml('Name2'); ?></label>
                                    <tr><td><input type="text" id="iName2" name="name2" value="<?= $this->printHtml($item->getL11n('name2')->content); ?>" spellcheck="false">
                                    <tr><td><label for="iName3"><?= $this->getHtml('Name3'); ?></label>
                                    <tr><td><input type="text" id="iName3" name="name3" value="<?= $this->printHtml($item->getL11n('name3')->content); ?>" spellcheck="false">
                              </table>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>" name="save-item">
                                <input class="right-xs cancel" type="submit" value="<?= $this->getHtml('Delete', '0', '0'); ?>" name="delete-item">
                            </div>
                        </form>
                    </section>

                    <section class="portlet">
                        <div class="portlet-body">
                            <img alt="<?= $this->printHtml($itemImage->name); ?>" width="100%" loading="lazy" class="item-image"
                                src="<?= $itemImage->id === 0
                                    ? 'Web/Backend/img/logo_grey.png'
                                    : UriFactory::build($itemImage->getPath()); ?>">
                        </div>
                    </section>

                    <section class="portlet highlight-4">
                        <div class="portlet-body">
                            <textarea class="undecorated"></textarea>
                        </div>
                    </section>
                </div>
                <div class="col-xs-12 col-lg-9 plain-grid">
                    <div class="row">
                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet highlight-1">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('YTDSales'); ?>:
                                            <td><?= $this->getCurrency($this->getData('ytd')); ?>
                                        <tr><td><?= $this->getHtml('MTDSales'); ?>:
                                            <td><?= $this->getCurrency($this->getData('mtd')); ?>
                                        <tr><td><?= $this->getHtml('ILV'); ?>:
                                            <td>
                                        <tr><td><?= $this->getHtml('MRR'); ?>:
                                            <td>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet highlight-2">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('LastOrder'); ?>:
                                            <td><?= $this->getData('lastOrder') !== null ? $this->getData('lastOrder')->format('Y-m-d H:i') : ''; ?>
                                        <tr><td><?= $this->getHtml('PriceChange'); ?>:
                                            <td>
                                        <tr><td><?= $this->getHtml('Created'); ?>:
                                            <td><?= $item->createdAt->format('Y-m-d H:i'); ?>
                                        <tr><td><?= $this->getHtml('Modified'); ?>:
                                            <td>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet highlight-3">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('SalesPrice'); ?>:
                                            <td><?= $this->getCurrency($item->salesPrice, format: 'medium'); ?>
                                        <tr><td><?= $this->getHtml('PurchasePrice'); ?>:
                                            <td><?= $this->getCurrency($item->purchasePrice); ?>
                                        <tr><td><?= $this->getHtml('Margin'); ?>:
                                            <td><?= $this->getNumeric(
                                                $item->salesPrice->getInt() === 0
                                                    ? 0
                                                    : ($item->salesPrice->getInt() - $item->purchasePrice->getInt()) / $item->salesPrice->getInt() * 100
                                                , 'short'); ?> %
                                        <tr><td><?= $this->getHtml('AvgPrice'); ?>:
                                            <td><?= $this->getCurrency($this->getData('avg')); ?>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head row middle-xs">
                                    <span><?= $this->getHtml('Notes'); ?></span>
                                    <label for="c-tab-12" class="right-xs btn"><i class="fa fa-pencil"></i></a>
                                </div>
                                <div class="slider">
                                <table id="iNotesItemList" class="default">
                                    <thead>
                                    <tr>
                                        <td class="wf-100"><?= $this->getHtml('Title'); ?>
                                        <td><?= $this->getHtml('CreatedAt'); ?>
                                    <tbody>
                                    <?php foreach ($notes as $note) :
                                        $url = UriFactory::build('{/base}/editor/single?{?}&id=' . $note->id);
                                        ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($note->title); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($note->createdAt->format('Y-m-d')); ?></a>
                                    <?php endforeach; ?>
                                </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head row middle-xs">
                                    <span><?= $this->getHtml('Files'); ?></span>
                                    <label for="c-tab-13" class="right-xs btn"><i class="fa fa-pencil"></i></a>
                                </div>
                                <div class="slider">
                                <table id="iFilesItemList" class="default">
                                    <thead>
                                    <tr>
                                        <td class="wf-100"><?= $this->getHtml('Title'); ?>
                                        <td>
                                        <td><?= $this->getHtml('CreatedAt'); ?>
                                    <tbody>
                                    <?php foreach ($files as $file) :
                                        $url = UriFactory::build('{/base}/media/single?{?}&id=' . $file->id);
                                        ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($file->name); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($file->extension); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($file->createdAt->format('Y-m-d')); ?></a>
                                    <?php endforeach; ?>
                                </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('RecentInvoices'); ?></div>
                                <div class="slider">
                                <table id="iSalesItemList" class="default">
                                    <thead>
                                    <tr>
                                        <td><?= $this->getHtml('Number'); ?>
                                        <td><?= $this->getHtml('Type'); ?>
                                        <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                        <td><?= $this->getHtml('Net'); ?>
                                        <td><?= $this->getHtml('Date'); ?>
                                    <tbody>
                                    <?php
                                    /** @var \Modules\Billing\Models\Bill $invoice */
                                    foreach ($newestInvoices as $invoice) :
                                        $url = UriFactory::build('{/base}/sales/bill?{?}&id=' . $invoice->id);
                                        ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($invoice->getNumber()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($invoice->type->getL11n()); ?></a>
                                        <td><a class="content" href="<?= UriFactory::build('{/base}/sales/client/profile?{?}&id=' . $invoice->client->id); ?>"><?= $this->printHtml($invoice->billTo); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->getCurrency($invoice->netSales); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($invoice->createdAt->format('Y-m-d')); ?></a>
                                    <?php endforeach; ?>
                                </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (!empty($topCustomers[0])) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('TopCustomers'); ?></div>
                                <table id="iSalesItemList" class="default">
                                    <thead>
                                    <tr>
                                        <td><?= $this->getHtml('Number'); ?>
                                        <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                        <td><?= $this->getHtml('Country'); ?>
                                        <td><?= $this->getHtml('Net'); ?>
                                    <tbody>
                                    <?php $i = -1; foreach (($topCustomers[0] ?? []) as $client) : ++$i;
                                        $url = UriFactory::build('{/base}/sales/client/profile?id=' . $client->id);
                                    ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($client->number); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($client->account->name1); ?> <?= $this->printHtml($client->account->name2); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($client->mainAddress->getCountry()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= (new Money((int) $topCustomers[1][$i]['net_sales']))->getCurrency(); ?></a>
                                    <?php endforeach; ?>
                                </table>
                            </section>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($monthlySalesCosts)) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Sales'); ?></div>
                                <div class="portlet-body">
                                    <canvas id="sales-region" data-chart='{
                                            "type": "bar",
                                            "data": {
                                                "labels": [
                                                    <?php
                                                        $temp = [];
                                                        foreach ($monthlySalesCosts as $monthly) {
                                                            $temp[] = $monthly['month'] . '/' . \substr((string) $monthly['year'], -2);
                                                        }
                                                    ?>
                                                    <?= '"' . \implode('", "', $temp) . '"'; ?>
                                                ],
                                                "datasets": [
                                                    {
                                                        "label": "<?= $this->getHtml('Margin'); ?>",
                                                        "type": "line",
                                                        "data": [
                                                            <?php
                                                                $temp = [];
                                                                foreach ($monthlySalesCosts as $monthly) {
                                                                    $temp[] = \round(((((int) $monthly['net_sales']) - ((int) $monthly['net_costs'])) / ((int) $monthly['net_sales'])) * 100, 2);
                                                                }
                                                            ?>
                                                            <?= \implode(',', $temp); ?>
                                                        ],
                                                        "yAxisID": "axis2",
                                                        "fill": false,
                                                        "borderColor": "rgb(255, 99, 132)",
                                                        "backgroundColor": "rgb(255, 99, 132)"
                                                    },
                                                    {
                                                        "label": "<?= $this->getHtml('Sales'); ?>",
                                                        "type": "bar",
                                                        "data": [
                                                            <?php
                                                                $temp = [];
                                                                foreach ($monthlySalesCosts as $monthly) {
                                                                    $temp[] = ((int) $monthly['net_sales']) / 1000;
                                                                }
                                                            ?>
                                                            <?= \implode(',', $temp); ?>
                                                        ],
                                                        "yAxisID": "axis1",
                                                        "backgroundColor": "rgb(54, 162, 235)"
                                                    }
                                                ]
                                            },
                                            "options": {
                                                "responsive": true,
                                                "scales": {
                                                    "axis1": {
                                                        "id": "axis1",
                                                        "display": true,
                                                        "position": "left",
                                                        "suggestedMin": 0,
                                                        "ticks": {
                                                            "stepSize": 1000
                                                        }
                                                    },
                                                    "axis2": {
                                                        "id": "axis2",
                                                        "display": true,
                                                        "position": "right",
                                                        "suggestedMin": 0,
                                                        "max": 100,
                                                        "title": {
                                                            "display": true,
                                                            "text": "<?= $this->getHtml('Margin'); ?> %"
                                                        },
                                                        "grid": {
                                                            "display": false
                                                        },
                                                        "beginAtZero": true,
                                                        "ticks": {
                                                            "stepSize": 10
                                                        }
                                                    }
                                                }
                                            }
                                    }'></canvas>
                                </div>
                            </section>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <?php if (!empty($regionSales)) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head">Regions</div>
                                <div class="portlet-body">
                                    <canvas id="sales-region" data-chart='{
                                        "type": "pie",
                                        "data": {
                                            "labels": [
                                                    "Europe", "America", "Asia", "Africa", "CIS", "Other"
                                                ],
                                            "datasets": [{
                                                "data": [
                                                    <?= (int) ($regionSales['Europe'] ?? 0) / 1000; ?>,
                                                    <?= (int) ($regionSales['America'] ?? 0) / 1000; ?>,
                                                    <?= (int) ($regionSales['Asia'] ?? 0) / 1000; ?>,
                                                    <?= (int) ($regionSales['Africa'] ?? 0) / 1000; ?>,
                                                    <?= (int) ($regionSales['CIS'] ?? 0) / 1000; ?>,
                                                    <?= (int) ($regionSales['Other'] ?? 0) / 1000; ?>
                                                ],
                                                "backgroundColor": [
                                                    "rgb(255, 99, 132)",
                                                    "rgb(255, 159, 64)",
                                                    "rgb(255, 205, 86)",
                                                    "rgb(75, 192, 192)",
                                                    "rgb(54, 162, 235)",
                                                    "rgb(153, 102, 255)"
                                                ]
                                            }]
                                        }
                                }'></canvas>
                                </div>
                            </section>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($countrySales)) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Countries'); ?></div>
                                <div class="portlet-body">
                                    <canvas id="sales-region" data-chart='{
                                        "type": "bar",
                                        "data": {
                                            "labels": [
                                                    <?= '"' . \implode('", "', \array_keys($countrySales)) . '"'; ?>
                                                ],
                                            "datasets": [{
                                                "label": "YTD",
                                                "type": "bar",
                                                "data": [
                                                    <?php
                                                        $temp = [];
                                                        foreach ($countrySales as $country) {
                                                            $temp[] = ((int) $country) / 1000;
                                                        }
                                                    ?>
                                                    <?= \implode(',', $temp); ?>
                                                ],
                                                "yAxisID": "axis1",
                                                "backgroundColor": "rgb(54, 162, 235)"
                                            }]
                                        },
                                        "options": {
                                            "responsive": true,
                                            "scales": {
                                                "axis1": {
                                                    "id": "axis1",
                                                    "display": true,
                                                    "position": "left",
                                                    "suggestedMin": 0,
                                                    "ticks": {
                                                        "stepSize": 1000
                                                    }
                                                }
                                            }
                                        }
                                }'></canvas>
                                </div>
                            </section>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-2" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="localizationForm" action="<?= UriFactory::build('{/api}itemmgmt/item'); ?>" method="post"
                            data-ui-container="#localizationTable tbody"
                            data-add-form="localizationForm"
                            data-add-tpl="#localizationTable tbody .oms-add-tpl-localization">
                            <div class="portlet-head"><?= $this->getHtml('Localization'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="iLocaliztionId"><?= $this->getHtml('ID'); ?></label>
                                        <input type="text" id="iLocaliztionId" name="id" data-tpl-text="/id" data-tpl-value="/id" disabled>
                                    </div>

                                    <label for="iLocalizationLanguage"><?= $this->getHtml('Language'); ?></label>
                                    <select id="iLocalizationLanguage" name="language" data-tpl-text="/language" data-tpl-value="/language">
                                        <?php foreach ($languages as $code => $language) : $code = ISO639x1Enum::getByName($code); ?>
                                            <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iLocalizationType"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iLocalizationType" name="type" data-tpl-text="/type" data-tpl-value="/type">
                                        <?php
                                            $types = $this->getData('l11nTypes') ?? [];
                                            foreach ($types as $type) : ?>
                                            <option value="<?= $type->id; ?>"><?= $this->printHtml($type->title); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iLocalizationText"><?= $this->getHtml('Text'); ?></label>
                                    <pre class="textarea contenteditable" id="iLocalizationText" data-name="content" data-tpl-value="/l11n" contenteditable="true"></pre>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="bLocalizationAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                <input id="bLocalizationSave" formmethod="post" type="submit" class="save-form hidden button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input type="submit" class="cancel-form hidden button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Localizations'); ?><i class="lni lni-download download btn end-xs"></i></div>
                        <div class="slider">
                        <table id="localizationTable" class="default sticky fixed-5"
                            data-tag="form"
                            data-ui-element="tr"
                            data-add-tpl=".oms-add-tpl-localization"
                            data-update-form="localizationForm">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td><?= $this->getHtml('Language'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td class="wf-100"><?= $this->getHtml('Localization'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                            <tbody>
                                <template class="oms-add-tpl-attribute">
                                    <tr data-id="" draggable="false">
                                        <td>
                                            <i class="fa fa-cogs btn update-form"></i>
                                            <input id="attributeTable-remove-0" type="checkbox" class="hidden">
                                            <label for="attributeTable-remove-0" class="checked-visibility-alt"><i class="fa fa-times btn form-action"></i></label>
                                            <span class="checked-visibility">
                                                <label for="attributeTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="attributeTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"></td>
                                        <td data-tpl-text="/type" data-tpl-value="/type" data-value=""></td>
                                        <td data-tpl-text="/language" data-tpl-value="/language"></td>
                                        <td data-tpl-text="/l11n" data-tpl-value="/l11n"></td>
                                    </tr>
                                </template>
                                <?php
                                $c        = 0;
                                $itemL11n = $this->getData('l11nValues');
                                foreach ($itemL11n as $value) : ++$c; ?>
                                    <tr data-id="<?= $value->id; ?>">
                                        <td>
                                            <i class="fa fa-cogs btn update-form"></i>
                                            <?php if (!$value->type->isRequired) : ?>
                                            <input id="localizationTable-remove-<?= $value->id; ?>" type="checkbox" class="hidden">
                                            <label for="localizationTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="fa fa-times btn form-action"></i></label>
                                            <span class="checked-visibility">
                                                <label for="localizationTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="localizationTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                            <?php endif; ?>
                                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                        <td data-tpl-text="/type" data-tpl-value="/type" data-value="<?= $value->type->id; ?>"><?= $this->printHtml($value->type->title); ?>
                                        <td data-tpl-text="/language" data-tpl-value="/language"><?= $this->printHtml($value->getLanguage()); ?>
                                        <td data-tpl-text="/l11n" data-tpl-value="/l11n" data-value="<?= \nl2br($this->printHtml($value->content)); ?>"><?= \nl2br($this->printHtml(\substr($value->content, 0, 100))); ?>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                <tr>
                                    <td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-3" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="attributeForm" action="<?= UriFactory::build('{/api}item/attribute'); ?>" method="post"
                            data-ui-container="#attributeTable tbody"
                            data-add-form="attributeForm"
                            data-add-tpl="#attributeTable tbody .oms-add-tpl-attribute">
                            <div class="portlet-head"><?= $this->getHtml('Attribute'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iAttributeId"><?= $this->getHtml('ID'); ?></label>
                                    <input type="text" id="iAttributeId" name="id" data-tpl-text="/id" data-tpl-value="/id" disabled>
                                </div>

                                <!--
                                <div class="form-group">
                                    <label for="iAttributesLanguage"><?= $this->getHtml('Language'); ?></label>
                                    <select id="iAttributesLanguage" name="language" data-tpl-text="/language" data-tpl-value="/language">
                                            <option value="">
                                        <?php foreach ($languages as $code => $language) : ?>
                                            <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                -->

                                <div class="form-group">
                                    <label for="iAttributesType"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iAttributesType" name="type" data-tpl-text="/type" data-tpl-value="/type">
                                        <?php
                                        $types = $this->getData('attributeTypes') ?? [];
                                        foreach ($types as $type) : ?>
                                            <option value="<?= $type->id; ?>"><?= $this->printHtml($type->getL11n()); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iAttributesUnit"><?= $this->getHtml('Unit'); ?></label>
                                    <select id="iAttributesUnit" name="unit" data-tpl-text="/unit" data-tpl-value="/unit">
                                        <option value="">
                                        <?php
                                        $units = $this->getData('units') ?? [];
                                        foreach ($units as $unit) : ?>
                                            <option value="<?= $unit->id; ?>"><?= $this->printHtml($unit->name); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iAttributeValue"><?= $this->getHtml('Value'); ?></label>
                                    <pre class="textarea contenteditable" id="iAttributeValue" data-name="value" data-tpl-value="/value" contenteditable></pre>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="bAttributeAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                <input id="bAttributeSave" formmethod="post" type="submit" class="save-form hidden button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input type="submit" class="cancel-form hidden button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Attributes'); ?><i class="lni lni-download download btn end-xs"></i></div>
                        <div class="slider">
                        <table id="attributeTable" class="default"
                            data-tag="form"
                            data-ui-element="tr"
                            data-add-tpl=".oms-add-tpl-attribute"
                            data-update-form="attributeForm">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td class="wf-100"><?= $this->getHtml('Value'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td><?= $this->getHtml('Unit'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                            <tbody>
                                <template class="oms-add-tpl-attribute">
                                    <tr data-id="" draggable="false">
                                        <td>
                                            <i class="fa fa-cogs btn update-form"></i>
                                            <input id="attributeTable-remove-0" type="checkbox" class="hidden">
                                            <label for="attributeTable-remove-0" class="checked-visibility-alt"><i class="fa fa-times btn form-action"></i></label>
                                            <span class="checked-visibility">
                                                <label for="attributeTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="attributeTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"></td>
                                        <td data-tpl-text="/type" data-tpl-value="/type" data-value=""></td>
                                        <td data-tpl-text="/value" data-tpl-value="/value"></td>
                                        <td data-tpl-text="/unit" data-tpl-value="/unit"></td>
                                    </tr>
                                </template>
                                <?php $c = 0;
                                foreach ($attribute as $key => $value) : ++$c; ?>
                                    <tr data-id="<?= $value->id; ?>">
                                        <td>
                                            <i class="fa fa-cogs btn update-form"></i>
                                            <?php if (!$value->type->isRequired) : ?>
                                            <input id="attributeTable-remove-<?= $value->id; ?>" type="checkbox" class="hidden">
                                            <label for="attributeTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="fa fa-times btn form-action"></i></label>
                                            <span class="checked-visibility">
                                                <label for="attributeTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="attributeTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                            <?php endif; ?>
                                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                        <td data-tpl-text="/type" data-tpl-value="/type" data-value="<?= $value->type->id; ?>"><?= $this->printHtml($value->type->getL11n()); ?>
                                        <td data-tpl-text="/value" data-tpl-value="/value"><?= $value->value->getValue() instanceof \DateTime ? $value->value->getValue()->format('Y-m-d') : $this->printHtml((string) $value->value->getValue()); ?>
                                        <td data-tpl-text="/unit" data-tpl-value="/unit" data-value="<?= $value->value->unit; ?>"><?= $this->printHtml($value->value->unit); ?>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                <tr>
                                    <td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-4" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="item-edit" action="<?= UriFactory::build('{/api}itemmgmt/item'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Pricing'); ?><i class="lni lni-download download btn end-xs"></i></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iAttributesLanguage"><?= $this->getHtml('CustomerGroup'); ?></label>
                                    <select id="iAttributesLanguage" name="language">
                                        <?php foreach ($languages as $code => $language) : ?>
                                            <option value="<?= $this->printHtml($code); ?>"><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iGeneralPriceStart"><?= $this->getHtml('Start'); ?></label>
                                    <input id="iGeneralPriceStart" name="generalpricestart" type="datetime-local" value="<?= $this->printHtml(''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iGeneralPriceEnd"><?= $this->getHtml('End'); ?></label>
                                    <input id="iGeneralPriceEnd" name="generalpriceend" type="datetime-local" value="<?= $this->printHtml(''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iPQuantity"><?= $this->getHtml('Quantity'); ?></label>
                                    <input id="iPQuantity" name="quantity" type="text" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="iGeneralPrice"><?= $this->getHtml('Price'); ?></label>
                                    <input id="iGeneralPrice" name="generalprice" type="number" step="0.0001" value="<?= $this->printHtml('0.00'); ?>">
                                    <!-- todo: maybe add promotion key/password here for online shop to provide special prices for certain customer groups -->
                                </div>

                                <div class="form-group">
                                    <label for="iDiscount"><?= $this->getHtml('Discount'); ?></label>
                                    <input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="iDiscountP"><?= $this->getHtml('DiscountP'); ?></label>
                                    <input id="iDiscountP" name="discountp" type="number" step="any" min="0" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="iBonus"><?= $this->getHtml('Bonus'); ?></label>
                                    <input id="iBonus" name="bonus" type="number" step="any" min="0" placeholder="">
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Prices'); ?><i class="lni lni-download download btn end-xs"></i></div>
                        <div class="slider">
                        <table id="iSalesItemList" class="default">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td><?= $this->getHtml('Price'); ?>
                                    <td><?= $this->getHtml('Quantity'); ?>
                                    <td><?= $this->getHtml('Discount'); ?>
                                    <td><?= $this->getHtml('DiscountP'); ?>
                                    <td><?= $this->getHtml('Bonus'); ?>
                                    <td><?= $this->getHtml('ItemGroup'); ?>
                                    <td><?= $this->getHtml('ItemSegment'); ?>
                                    <td><?= $this->getHtml('ItemSection'); ?>
                                    <td><?= $this->getHtml('ItemType'); ?>
                                    <td><?= $this->getHtml('ClientGroup'); ?>
                                    <td><?= $this->getHtml('ClientSegment'); ?>
                                    <td><?= $this->getHtml('ClientSection'); ?>
                                    <td><?= $this->getHtml('ClientType'); ?>
                                    <td><?= $this->getHtml('Country'); ?>
                                    <td><?= $this->getHtml('Start'); ?>
                                    <td><?= $this->getHtml('End'); ?>
                            <tbody>
                                <?php
                                $c      = 0;
                                $prices = $this->getData('prices');
                                foreach ($prices as $key => $value) : ++$c;
                                    $url = UriFactory::build('{/base}/admin/group/settings?{?}&id=' . $value->id); ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="#"><i class="fa fa-times"></i></a>
                                        <td><a href="<?= $url; ?>"><?= $value->id; ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($value->price->getAmount()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->quantity); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->discount); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->discountPercentage); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->bonus); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->itemgroup->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->itemsegment->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->itemsection->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->itemtype->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->clientgroup->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->clientsegment->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->clientsection->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->clienttype->getL11n()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->clientcountry); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->start?->format('Y-m-d')); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $value->end?->format('Y-m-d')); ?></a>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr>
                                        <td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-5" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form action="<?= UriFactory::build('{/api}...'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Purchase'); ?></div>
                            <div class="portlet-body">
                                <table class="layout wf-100">
                                    <tbody>
                                        <tr>
                                            <td><label for="iPVariation"><?= $this->getHtml('Stock'); ?></label>
                                        <tr>
                                            <td><select id="iPVariation" name="pvariation">
                                                    <option value="0">
                                                </select>
                                        <tr>
                                            <td><label for="iPName"><?= $this->getHtml('ReorderLevel'); ?></label>
                                        <tr>
                                            <td><input id="iPName" name="pname" type="text" placeholder="">
                                        <tr>
                                            <td><label for="iPName"><?= $this->getHtml('MinimumLevel'); ?></label>
                                        <tr>
                                            <td><input id="iPName" name="pname" type="text" placeholder="">
                                        <tr>
                                            <td><label for="iPName"><?= $this->getHtml('MaximumLevel'); ?></label>
                                        <tr>
                                            <td><input id="iPName" name="pname" type="text" placeholder="">
                                        <tr>
                                            <td><label for="iPName"><?= $this->getHtml('Leadtime'); ?></label>
                                        <tr>
                                            <td><input id="iPName" name="pname" type="number" min="0" step="1" placeholder="">
                                        <tr>
                                            <td>
                                </table>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>" name="save-item">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="item-edit" action="<?= UriFactory::build('{/api}itemmgmt/item'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Pricing'); ?><i class="lni lni-download download btn end-xs"></i></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iAttributesLanguage"><?= $this->getHtml('CustomerGroup'); ?></label>
                                    <select id="iAttributesLanguage" name="language">
                                        <?php foreach ($languages as $code => $language) : ?>
                                            <option value="<?= $this->printHtml($code); ?>"><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iGeneralPriceStart"><?= $this->getHtml('Start'); ?></label>
                                    <input id="iGeneralPriceStart" name="generalpricestart" type="datetime-local" value="<?= $this->printHtml(''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iGeneralPriceEnd"><?= $this->getHtml('End'); ?></label>
                                    <input id="iGeneralPriceEnd" name="generalpriceend" type="datetime-local" value="<?= $this->printHtml(''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="iPQuantity"><?= $this->getHtml('Quantity'); ?></label>
                                    <input id="iPQuantity" name="quantity" type="text" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="iGeneralPrice"><?= $this->getHtml('Price'); ?></label>
                                    <input id="iGeneralPrice" name="generalprice" type="number" step="0.0001" value="<?= $this->printHtml('0.00'); ?>">
                                    <!-- todo: maybe add promotion key/password here for online shop to provide special prices for certain customer groups -->
                                </div>

                                <div class="form-group">
                                    <label for="iDiscount"><?= $this->getHtml('Discount'); ?></label>
                                    <input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="iDiscountP"><?= $this->getHtml('DiscountP'); ?></label>
                                    <input id="iDiscountP" name="discountp" type="number" step="any" min="0" placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="iBonus"><?= $this->getHtml('Bonus'); ?></label>
                                    <input id="iBonus" name="bonus" type="number" step="any" min="0" placeholder="">
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Prices'); ?><i class="lni lni-download download btn end-xs"></i></div>
                        <table id="iSalesItemList" class="default">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                                    <td class="wf-100"><?= $this->getHtml('Name'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                            <tbody>
                                <?php $c = 0;
                                $l11ns   = [];
                                foreach ($l11ns as $key => $value) : ++$c;
                                    $url = UriFactory::build('{/base}/admin/group/settings?{?}&id=' . $value->id); ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="#"><i class="fa fa-times"></i></a>
                                        <td><a href="<?= $url; ?>"><?= $value->id; ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr>
                                        <td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                <?php endif; ?>
                        </table>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-6" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('PartsList'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('UsedIn'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('ProductionSteps'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Machines'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Documents'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-7" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Issues'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('UsedIn'); ?></div>
                        <div class="portlet-body"></div>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-8" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form action="<?= UriFactory::build('{/api}...'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('General'); ?></div>
                            <div class="portlet-body">
                                <table class="layout wf-100">
                                    <tbody>
                                        <tr>
                                            <td><label for="iPVariation"><?= $this->getHtml('Container'); ?></label>
                                        <tr>
                                            <td><select id="iPVariation" name="pvariation">
                                                    <option value="0">
                                                </select>
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('Quantity'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('GrossWeight'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('NetWeight'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('Width'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('Height'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('Length'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                        <tr>
                                            <td><label for="iDiscount"><?= $this->getHtml('Volume'); ?></label>
                                        <tr>
                                            <td><input id="iDiscount" name="discount" type="number" step="any" min="0" placeholder="">
                                </table>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
        <input type="radio" id="c-tab-9" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="box wf-100">
                        <header>
                            <h1><?= $this->getHtml('General'); ?></h1>
                        </header>
                        <div class="inner">
                            <form id="item-edit" action="<?= UriFactory::build('{/api}itemmgmt/item'); ?>" method="post">
                                <table class="layout wf-100">
                                    <tbody>
                                        <tr>
                                            <td><label for="iEarningIndicator"><?= $this->getHtml('EarningIndicator'); ?></label>
                                        <tr>
                                            <td>
                                                <select id="iEarningIndicator" name="settings_1000000020">
                                                    <?php foreach ($languages as $code => $language) : ?>
                                                        <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                                        <?php endforeach; ?>
                                                </select>
                                        <tr>
                                            <td><label for="iCostIndicator"><?= $this->getHtml('CostIndicator'); ?></label>
                                        <tr>
                                            <td>
                                                <select id="iCostIndicator" name="settings_1000000020">
                                                    <?php foreach ($languages as $code => $language) : ?>
                                                        <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                                        <?php endforeach; ?>
                                                </select>
                                        <tr>
                                            <td><label for="iCostCenter"><?= $this->getHtml('CostCenter'); ?></label>
                                        <tr>
                                            <td>
                                                <select id="iCostCenter" name="settings_1000000020">
                                                    <?php foreach ($languages as $code => $language) : ?>
                                                        <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                                        <?php endforeach; ?>
                                                </select>
                                        <tr>
                                            <td><label for="iCostObject"><?= $this->getHtml('CostObject'); ?></label>
                                        <tr>
                                            <td>
                                                <select id="iCostObject" name="settings_1000000020">
                                                    <?php foreach ($languages as $code => $language) : ?>
                                                        <option value="<?= $this->printHtml($code); ?>" <?= $this->printHtml(\strtolower($code) === $l11n->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                                        <?php endforeach; ?>
                                                </select>
                                </table>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-10" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
            </div>
        </div>

        <input type="radio" id="c-tab-11" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
            </div>
        </div>

        <input type="radio" id="c-tab-12" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
            </div>
        </div>

        <input type="radio" id="c-tab-13" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <?= $this->getData('medialist')->render($this->getData('files')); ?>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-14" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('RecentInvoices'); ?></div>
                        <table id="iSalesItemList" class="default">
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('Number'); ?>
                                <td><?= $this->getHtml('Type'); ?>
                                <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                <td><?= $this->getHtml('Net'); ?>
                                <td><?= $this->getHtml('Date'); ?>
                            <tbody>
                            <?php
                            /** @var \Modules\Billing\Models\Bill $invoice */
                            foreach ($allInvoices as $invoice) :
                                $url = UriFactory::build('{/base}/sales/bill?{?}&id=' . $invoice->id);
                                ?>
                            <tr data-href="<?= $url; ?>">
                                <td><a href="<?= $url; ?>"><?= $invoice->getNumber(); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->type->getL11n(); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->billTo; ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getCurrency($invoice->netSales); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->createdAt->format('Y-m-d'); ?></a>
                            <?php endforeach; ?>
                        </table>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-15" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <div class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Audits', 'Auditor'); ?><i class="lni lni-download download btn end-xs"></i></div>
                        <div class="slider">
                        <table class="default">
                            <colgroup>
                                <col style="width: 75px">
                                <col style="width: 150px">
                                <col style="width: 100px">
                                <col>
                                <col>
                                <col style="width: 125px">
                                <col style="width: 75px">
                                <col style="width: 150px">
                            </colgroup>
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('ID', '0', '0'); ?>
                                <td><?= $this->getHtml('Module', 'Auditor'); ?>
                                <td><?= $this->getHtml('Type', 'Auditor'); ?>
                                <td><?= $this->getHtml('Trigger', 'Auditor'); ?>
                                <td><?= $this->getHtml('Content', 'Auditor'); ?>
                                <td><?= $this->getHtml('By', 'Auditor'); ?>
                                <td><?= $this->getHtml('Ref', 'Auditor'); ?>
                                <td><?= $this->getHtml('Date', 'Auditor'); ?>
                            <tbody>
                            <?php
                                $count    = 0;
                                $audits   = $this->getData('audits') ?? [];
                                $previous = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \reset($audits)->id . '&ptype=p#{\#}';
                                $next     = empty($audits) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \end($audits)->id . '&ptype=n#{\#}';

                                foreach ($audits as $key => $audit) : ++$count;
                                    $url = UriFactory::build('{/base}/admin/audit/single?{?}&id=' . $audit->id); ?>
                                <tr tabindex="0" data-href="<?= $url; ?>">
                                    <td><?= $audit->id; ?>
                                    <td><?= $this->printHtml($audit->module); ?>
                                    <td><?= $audit->type; ?>
                                    <td><?= $this->printHtml($audit->trigger); ?>
                                    <td><?= $this->printHtml((string) $audit->content); ?>
                                    <td><?= $this->printHtml($audit->createdBy->login); ?>
                                    <td><?= $this->printHtml((string) $audit->ref); ?>
                                    <td><?= $audit->createdAt->format('Y-m-d H:i'); ?>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                                <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                        <div class="portlet-foot">
                            <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                            <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>