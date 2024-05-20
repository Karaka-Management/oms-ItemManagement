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

use Modules\Billing\Models\Price\PriceType;
use Modules\Billing\Models\SalesBillMapper;
use Modules\ItemManagement\Models\ItemStatus;
use Modules\ItemManagement\Models\NullItem;
use Modules\Media\Models\NullMedia;
use Modules\WarehouseManagement\Models\StockLocationMapper;
use Modules\WarehouseManagement\Models\StockMapper;
use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Localization\ISO4217CharEnum;
use phpOMS\Localization\ISO639Enum;
use phpOMS\Localization\Money;
use phpOMS\Localization\RegionEnum;
use phpOMS\Message\Http\HttpHeader;
use phpOMS\Stdlib\Base\FloatInt;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\System\File\ExtensionType;
use phpOMS\System\File\FileUtils;
use phpOMS\Uri\UriFactory;

/** @var \Modules\ItemManagement\Models\Item $item */
$item  = $this->data['item'] ?? new NullItem();
$isNew = $item->id === 0;

$logs      = $this->data['logs'] ?? [];
$itemImage = $this->getData('itemImage') ?? new NullMedia();

$allInvoices  = $this->data['allInvoices'] ?? [];
$topCustomers = $this->getData('topCustomers') ?? [[], []];

$languages  = ISO639Enum::getConstants();
$regions    = RegionEnum::getConstants();
$countries  = ISO3166CharEnum::getConstants();
$currencies = ISO4217CharEnum::getConstants();

$itemStatus = ItemStatus::getConstants();

// @performance The client, supplier and item views should not use actual tabs but individual pages for better performance
//      Tabs require too many models to be loaded. Implement and then use a tab navigation if it doesn't already exist.
//      https://github.com/Karaka-Management/oms-ItemManagement/issues/13

echo $this->data['nav']->render();
?>

<div class="tabview tab-2">
    <?php if (!$isNew) : ?>
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('Profile'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('Localization'); ?></label>
            <li><label for="c-tab-3"><?= $this->getHtml('Attributes'); ?></label>
            <!--<li><label for="c-tab-16"><?= $this->getHtml('Container'); ?></label>-->
            <li><label for="c-tab-4"><?= $this->getHtml('SalesPricing'); ?></label>
            <li><label for="c-tab-5"><?= $this->getHtml('Procurement'); ?></label>
            <!--<li><label for="c-tab-6"><?= $this->getHtml('Materials'); ?></label>-->
            <!--<li><label for="c-tab-8"><?= $this->getHtml('Packaging'); ?></label>-->
            <li><label for="c-tab-9"><?= $this->getHtml('Accounting'); ?></label>
            <li><label for="c-tab-10"><?= $this->getHtml('Stock'); ?></label>
            <li><label for="c-tab-12"><?= $this->getHtml('Notes'); ?></label>
            <li><label for="c-tab-13"><?= $this->getHtml('Files'); ?></label>
            <li><label for="c-tab-14"><?= $this->getHtml('Bills'); ?></label>
            <?php if (!empty($logs)) : ?><li><label for="c-tab-15"><?= $this->getHtml('Logs'); ?></label><?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $isNew || $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <?php if (!empty($item->notes) && ($warning = $item->getEditorDocByTypeName('item_backend_warning'))->id !== 0) : ?>
            <!-- If note warning exists -->
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet hl-1">
                        <div class="portlet-body"><?= $this->printHtml($warning->plain); ?></div>
                    </section>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12 col-lg-3 last-lg">
                    <section class="portlet">
                        <form id="itemForm"
                        method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                        action="<?= UriFactory::build('{/api}item?csrf={$CSRF}'); ?>"
                        <?= $isNew ? 'data-redirect="' . UriFactory::build('{/base}/item/view') . '?id={/0/response/id}"' : ''; ?>>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <?= $this->getHtml('ID', '0', '0'); ?></label>
                                    <span class="input"><button type="button" formaction=""><i class="g-icon">book</i></button><input type="text" id="iId" min="1" name="id" value="<?= $this->printHtml($item->number); ?>"<?= $isNew ? ' required' : ' disabled'; ?>></span>
                                </div>

                                <div class="form-group">
                                    <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                                    <select id="iStatus" name="status">
                                        <?php foreach ($itemStatus as $status) : ?>
                                            <option value="<?= $status; ?>"<?= $item->status === $status ? ' selected': ''; ?>><?= $this->getHtml(':status-' . $status); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iName1"><?= $this->getHtml('Name1'); ?></label>
                                    <input type="text" id="iName1" name="name1" value="<?= $this->printHtml($item->getL11n('name1')->content); ?>" spellcheck="false" required>
                                </div>

                                <div class="form-group">
                                    <label for="iName2"><?= $this->getHtml('Name2'); ?></label>
                                    <input type="text" id="iName2" name="name2" value="<?= $this->printHtml($item->getL11n('name2')->content); ?>" spellcheck="false">
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <?php if ($isNew) : ?>
                                    <input type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>" name="create-item">
                                <?php else : ?>
                                    <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>" name="save-item">
                                    <input class="cancel end-xs" type="submit" value="<?= $this->getHtml('Delete', '0', '0'); ?>" name="delete-item">
                                <?php endif; ?>
                            </div>
                        </form>
                    </section>

                    <?php if (!$isNew) : ?>
                    <section class="portlet">
                        <div class="portlet-body">
                            <img alt="<?= $this->printHtml($itemImage->name); ?>" width="100%" loading="lazy" class="item-image"
                                src="<?= $itemImage->id === 0
                                    ? 'Web/Backend/img/logo_grey.png'
                                    : UriFactory::build($itemImage->getPath()); ?>">
                        </div>
                    </section>

                    <section class="portlet hl-4">
                        <div class="portlet-body">
                            <textarea class="undecorated"></textarea>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>

                <?php if (!$isNew) : ?>
                <div class="col-xs-12 col-lg-9 plain-grid">
                    <?php if ($this->data['hasBilling'] ?? false) : ?>
                    <div class="row">
                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet hl-7">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('YTDSales'); ?>:
                                            <td><?= $this->getCurrency(SalesBillMapper::getItemNetSales($item->id, SmartDateTime::startOfYear($this->data['business_start']), new \DateTime('now')), symbol: '', format: 'medium'); ?>
                                        <tr><td><?= $this->getHtml('MTDSales'); ?>:
                                            <td><?= $this->getCurrency(SalesBillMapper::getItemNetSales($item->id, SmartDateTime::startOfMonth(), new \DateTime('now')), symbol: '', format: 'medium'); ?>
                                        <tr><td><?= $this->getHtml('ILV'); ?>:
                                            <td><?= $this->getCurrency(SalesBillMapper::getILVHistoric($item->id), symbol: '', format: 'medium'); ?>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet hl-2">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('LastOrder'); ?>:
                                            <td><?= SalesBillMapper::getItemLastOrder($item->id)?->format('Y-m-d'); ?>
                                        <tr><td><?= $this->getHtml('PriceChange'); ?>:
                                            <td>
                                        <tr><td><?= $this->getHtml('Created'); ?>:
                                            <td><?= $item->createdAt->format('Y-m-d H:i'); ?>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-lg-4">
                            <section class="portlet hl-3">
                                <div class="portlet-body">
                                    <table class="wf-100">
                                        <tr><td><?= $this->getHtml('SalesPrice'); ?>:
                                            <td><?= $this->getCurrency($item->salesPrice, symbol: '', format: 'medium'); ?>
                                        <tr><td><?= $this->getHtml('PurchasePrice'); ?>:
                                            <td><?= $this->getCurrency($item->purchasePrice, symbol: '', format: 'medium'); ?>
                                        <tr><td><?= $this->getHtml('Margin'); ?>:
                                            <td><?= $this->getNumeric(
                                                $item->salesPrice->getInt() === 0
                                                    ? 0
                                                    : ($item->salesPrice->getInt() - $item->purchasePrice->getInt()) / $item->salesPrice->getInt() * 100
                                                , 'short'); ?> %
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head row middle-xs">
                                    <span><?= $this->getHtml('Notes'); ?></span>
                                    <label for="c-tab-12" class="right-xs btn"><i class="g-icon">edit</i></a>
                                </div>
                                <div class="slider">
                                <table id="iNotesItemList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td class="wf-100"><?= $this->getHtml('Title'); ?>
                                        <td><?= $this->getHtml('CreatedAt'); ?>
                                    <tbody>
                                    <?php
                                    $count = 0;
                                    foreach ($item->notes as $note) :
                                        ++$count;
                                        $url = UriFactory::build('{/base}/editor/view?{?}&id=' . $note->id);
                                    ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($note->title); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($note->createdAt->format('Y-m-d')); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($count === 0) : ?>
                                    <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                                </table>
                                </div>
                            </section>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <section class="portlet">
                                <div class="portlet-head row middle-xs">
                                    <span><?= $this->getHtml('Files'); ?></span>
                                    <label for="c-tab-13" class="right-xs btn"><i class="g-icon">edit</i></a>
                                </div>
                                <div class="slider">
                                <table id="iFilesItemList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td class="wf-100"><?= $this->getHtml('Title'); ?>
                                        <td>
                                        <td><?= $this->getHtml('CreatedAt'); ?>
                                    <tbody>
                                    <?php
                                    $count = 0;
                                    foreach ($item->files as $file) :
                                        ++$count;
                                        $url           = UriFactory::build('{/base}/media/view?{?}&id=' . $file->id);
                                        $extensionType = FileUtils::getExtensionType($file->extension);
                                    ?>
                                    <tr data-href="<?= $url; ?>"
                                        <?= \in_array($extensionType, [ExtensionType::IMAGE, ExtensionType::PDF]) ? 'data-preview="' . UriFactory::build('{/api}media/export?id=' . $file->id . '&type=html&csrf={$CSRF}') . '"' : ''; ?>>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($file->name); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($file->extension); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($file->createdAt->format('Y-m-d')); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($count === 0) : ?>
                                    <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                                </table>
                                </div>
                            </section>
                        </div>
                    </div>

                    <?php if ($this->data['hasBilling'] ?? false) : ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('RecentInvoices'); ?></div>
                                <div class="slider">
                                <table id="iSalesItemList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td><?= $this->getHtml('Number'); ?>
                                        <td><?= $this->getHtml('Type'); ?>
                                        <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                        <td><?= $this->getHtml('Net'); ?>
                                        <td><?= $this->getHtml('Date'); ?>
                                    <tbody>
                                    <?php
                                    $newestInvoices = SalesBillMapper::getItemBills($item->id, (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'), 5);

                                    $count = 0;

                                    /** @var \Modules\Billing\Models\Bill $invoice */
                                    foreach ($newestInvoices as $invoice) :
                                        ++$count;
                                        $url       = UriFactory::build('{/base}/sales/bill/view?{?}&id=' . $invoice->id);
                                        $clientUrl = UriFactory::build('{/base}/sales/client/view?{?}&id=' . $invoice->client->id);
                                        ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($invoice->getNumber()); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($invoice->type->getL11n()); ?></a>
                                        <td><a class="content" href="<?= $clientUrl; ?>"><?= $this->printHtml($invoice->billTo); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->getCurrency($invoice->netSales, symbol: ''); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($invoice->performanceDate->format('Y-m-d')); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($count === 0) : ?>
                                    <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                                    <?php endif; ?>
                                </table>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($this->data['hasBilling'] ?? false) :
                        $topCustomers = SalesBillMapper::getItemTopClients($item->id, (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'), 5);
                    ?>
                    <div class="row">
                        <?php if (!empty($topCustomers[0])) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('TopCustomers'); ?></div>
                                <table id="iSalesItemList" class="default sticky">
                                    <thead>
                                    <tr>
                                        <td><?= $this->getHtml('Number'); ?>
                                        <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                        <td><?= $this->getHtml('Country'); ?>
                                        <td><?= $this->getHtml('Net'); ?>
                                    <tbody>
                                    <?php $i = -1; foreach (($topCustomers[0] ?? []) as $client) : ++$i;
                                        $url = UriFactory::build('{/base}/sales/client/view?id=' . $client->id);
                                    ?>
                                    <tr data-href="<?= $url; ?>">
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($client->number); ?></a>
                                        <td><a class="content" href="<?= UriFactory::build('{/base}/sales/client/view?{?}&id=' . $client->id); ?>"><?= $this->printHtml($client->account->name1); ?> <?= $this->printHtml($client->account->name2); ?></a>
                                        <td><a href="<?= $url; ?>"><?= $this->printHtml($client->mainAddress->country); ?></a>
                                        <td><a href="<?= $url; ?>"><?= (new Money((int) $topCustomers[1][$i]['net_sales']))->getCurrency(); ?></a>
                                    <?php endforeach; ?>
                                </table>
                            </section>
                        </div>
                        <?php endif; ?>

                        <?php
                        $monthlySalesCosts = SalesBillMapper::getItemMonthlySalesCosts([$item->id], (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));
                        if (!empty($monthlySalesCosts)) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Sales'); ?></div>
                                <div class="portlet-body">
                                    <div style="position: relative; width: 100%; height: 100%; aspect-ratio: 2;">
                                    <canvas id="sales-history" data-chart='{
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
                                                                    $temp[] = \round(((int) $monthly['net_sales']) === 0 ? 0 : ((((int) $monthly['net_sales']) - ((int) $monthly['net_costs'])) / ((int) $monthly['net_sales'])) * 100, 2);
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
                                                                    $temp[] = ((int) $monthly['net_sales']) / 10000;
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
                                                            "precision": 0
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
                                </div>
                            </section>
                        </div>
                        <?php endif; ?>

                        <?php
                        $countrySales = SalesBillMapper::getItemCountrySales($item->id, (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'), 5);
                        if (!empty($countrySales)) : ?>
                        <div class="col-xs-12 col-lg-6">
                            <section class="portlet">
                                <div class="portlet-head"><?= $this->getHtml('Countries'); ?></div>
                                <div class="portlet-body">
                                    <div style="position: relative; width: 100%; height: 100%; aspect-ratio: 2;">
                                    <canvas id="sales-countries" data-chart='{
                                        "type": "bar",
                                        "data": {
                                            "labels": [
                                                <?php
                                                    $temp = [];
                                                    foreach ($countrySales as $country) {
                                                        $temp[] = ISO3166NameEnum::getBy2Code($country['country']);
                                                    }
                                                ?>
                                                <?= '"' . \implode('", "', $temp) . '"'; ?>
                                                ],
                                            "datasets": [{
                                                "label": "YTD",
                                                "type": "bar",
                                                "data": [
                                                    <?php
                                                        $temp = [];
                                                        foreach ($countrySales as $country) {
                                                            $temp[] = (float) (((int) $country['net_sales']) / FloatInt::DIVISOR);
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
                                                        "precision": 0
                                                    }
                                                }
                                            }
                                        }
                                    }'></canvas>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$isNew) : ?>
        <input type="radio" id="c-tab-2" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <?= $this->data['l11nView']->render(
                    $this->data['l11nValues'],
                    $this->data['l11nTypes'] ?? [],
                    '{/api}item/l11n?csrf={$CSRF}'
                );
                ?>
            </div>
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <?= $this->data['attributeView']->render(
                    $item->attributes,
                    $this->data['attributeTypes'] ?? [],
                    $this->data['units'] ?? [],
                    '{/api}item/attribute?csrf={$CSRF}',
                    $item->id
                );
                ?>
            </div>
        </div>

        <input type="radio" id="c-tab-16" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="itemContainerForm" action="<?= UriFactory::build('{/api}bill/price?csrf={$CSRF}'); ?>" method="post"
                            data-ui-container="#itemContainerTable tbody"
                            data-add-form="itemContainerForm"
                            data-add-tpl="#itemContainerTable tbody .oms-add-tpl-itemContainer">
                            <div class="portlet-head"><?= $this->getHtml('Container'); ?></div>
                            <div class="portlet-body">
                                <input id="iContainerId" class="vh" name="id" type="number" data-tpl-text="/id" data-tpl-value="/id">
                                <input id="iContainerItemId" class="vh" name="item" type="text" value="<?= $item->id; ?>">
                                <input id="iContainerItemType" class="vh" name="type" type="text" value="<?= PriceType::SALES; ?>">

                                <div class="form-group">
                                    <label for="iContainerName"><?= $this->getHtml('Name'); ?></label>
                                    <input id="iContainerName" name="name" type="text" data-tpl-text="/name" data-tpl-value="/name">
                                </div>

                                <div class="form-group">
                                    <label for="iContainerQuantity"><?= $this->getHtml('Quantity'); ?></label>
                                    <input id="iContainerQuantity" name="quantity" type="number" step="any" data-tpl-text="/quantity" data-tpl-value="/quantity">
                                </div>

                                <div class="form-group">
                                    <label for="iContainerWeight"><?= $this->getHtml('Weight'); ?></label>
                                    <input id="iContainerWeight" name="weight" type="number" step="any" data-tpl-text="/weight" data-tpl-value="/weight">
                                </div>

                                <div class="form-group">
                                    <label for="iContainerWidth"><?= $this->getHtml('WidthLength'); ?></label>
                                    <input id="iContainerWidth" name="width" type="number" step="any" data-tpl-text="/width" data-tpl-value="/width">
                                </div>

                                <div class="form-group">
                                    <label for="iContainerHeight"><?= $this->getHtml('Height'); ?></label>
                                    <input id="iContainerHeight" name="height" type="number" step="any" data-tpl-text="/height" data-tpl-value="/height">
                                </div>

                                <div class="form-group">
                                    <label for="iContainerDepth"><?= $this->getHtml('Depth'); ?></label>
                                    <input id="iContainerDepth" name="depth" type="number" step="any" data-tpl-text="/depth" data-tpl-value="/depth">
                                </div>

                                <div class="form-group">
                                    <label for="iContainerVolume"><?= $this->getHtml('Volume'); ?></label>
                                    <input id="iContainerVolume" name="volume" type="number" step="any" data-tpl-text="/volume" data-tpl-value="/volume">
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="bPriceItemAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                <input id="bPriceItemSave" formmethod="post" type="submit" class="save-form vh button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input id="bPriceItemCancel" type="submit" class="cancel-form vh button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Container'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table id="itemContainerTable" class="default sticky"
                            data-tag="form"
                            data-ui-element="tr"
                            data-add-tpl=".oms-add-tpl-itemContainer"
                            data-update-form="itemContainerForm">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Quantity'); ?>
                                    <td><?= $this->getHtml('Decimals'); ?>
                                    <td><?= $this->getHtml('Weight'); ?>
                                    <td><?= $this->getHtml('WidthLength'); ?>
                                    <td><?= $this->getHtml('Height'); ?>
                                    <td><?= $this->getHtml('Depth'); ?>
                                    <td><?= $this->getHtml('Volume'); ?>
                            <tbody>
                                <template class="oms-add-tpl-itemContainer">
                                    <tr class="animated medium-duration greenCircleFade" data-id="" draggable="false">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <input id="itemContainerTable-remove-0" type="checkbox" class="vh">
                                            <label for="itemContainerTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="itemContainerTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="itemContainerTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"></td>
                                        <td data-tpl-text="/name" data-tpl-value="/name" data-value=""></td>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"></td>
                                        <td data-tpl-text="/decimals" data-tpl-value="/decimals"></td>
                                        <td data-tpl-text="/weight" data-tpl-value="/weight"></td>
                                        <td data-tpl-text="/width" data-tpl-value="/width"></td>
                                        <td data-tpl-text="/height" data-tpl-value="/height"></td>
                                        <td data-tpl-text="/depth" data-tpl-value="/discount"></td>
                                        <td data-tpl-text="/volume" data-tpl-value="/volume"></td>
                                    </tr>
                                </template>
                                <?php
                                $c          = 0;
                                $containers = $this->data['containers'] ?? [];
                                foreach ($containers as $key => $value) : ++$c;
                                ?>
                                    <tr data-id="<?= $value->id; ?>">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <?php if ($value->name !== 'default') : ?>
                                            <input id="itemContainerTable-remove-<?= $value->id; ?>" type="checkbox" class="vh">
                                            <label for="itemContainerTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="itemContainerTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="itemContainerTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                            <?php endif; ?>
                                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                        <td data-tpl-text="/name" data-tpl-value="/name"><?= $this->printHtml($value->name); ?>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"><?= $this->printHtml($value->promocode); ?>
                                        <td data-tpl-text="/decimals" data-tpl-value="/decimals"><?= $this->printHtml($value->promocode); ?>
                                        <td data-tpl-text="/weight" data-tpl-value="/weight"><?= $this->printHtml($value->price->getAmount()); ?>
                                        <td data-tpl-text="/width" data-tpl-value="/width"><?= $this->printHtml($value->currency); ?>
                                        <td data-tpl-text="/height" data-tpl-value="/height"><?= $this->printHtml((string) $value->quantity); ?>
                                        <td data-tpl-text="/depth" data-tpl-value="/depth"><?= $this->printHtml((string) $value->discount); ?>
                                        <td data-tpl-text="/volume" data-tpl-value="/volume"><?= $this->printHtml((string) $value->discountPercentage); ?>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr>
                                        <td colspan="22" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
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
                        <form id="itemSalesPriceForm" action="<?= UriFactory::build('{/api}bill/price?csrf={$CSRF}'); ?>" method="post"
                            data-ui-container="#itemSalesPriceTable tbody"
                            data-add-form="itemSalesPriceForm"
                            data-add-tpl="#itemSalesPriceTable tbody .oms-add-tpl-itemSalesPrice">
                            <div class="portlet-head"><?= $this->getHtml('Pricing'); ?></div>
                            <div class="portlet-body">
                                <input id="iPriceId" class="vh" name="id" type="number" data-tpl-text="/id" data-tpl-value="/id">
                                <input id="iPriceItemId" class="vh" name="item" type="text" value="<?= $item->id; ?>">
                                <input id="iPriceItemType" class="vh" name="type" type="text" value="<?= PriceType::SALES; ?>">

                                <div class="form-group">
                                    <label for="iPriceName"><?= $this->getHtml('Name'); ?></label>
                                    <input id="iPriceName" name="name" type="text" data-tpl-text="/name" data-tpl-value="/name">
                                </div>

                                <div class="form-group">
                                    <label for="iPricePromo"><?= $this->getHtml('Promocode'); ?></label>
                                    <input id="iPricePromo" name="promocode" type="text" data-tpl-text="/promocode" data-tpl-value="/promocode">
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPricePrice"><?= $this->getHtml('Price'); ?></label>
                                            <div class="flex-line wf-100">
                                                <div class="fixed">
                                                    <select id="iPriceCurrency" name="currency" data-tpl-text="/currency" data-tpl-value="/currency">
                                                        <?php foreach ($currencies as $currency) : ?>
                                                        <option value="<?= $currency; ?>"<?= $this->data['attributeView']->data['default_localization']->currency === $currency ? ' selected' : ''; ?>><?= $this->printHtml($currency); ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <input id="iPricePrice" class="wf-100" name="price_new" type="number" step="any" data-tpl-text="/price" data-tpl-value="/price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceQuantity"><?= $this->getHtml('Quantity'); ?></label>
                                            <input id="iPriceQuantity" name="quantity" type="number" step="any" data-tpl-text="/quantity" data-tpl-value="/quantity">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceDiscount"><?= $this->getHtml('Discount'); ?></label>
                                            <input id="iPriceDiscount" name="discount" type="number" step="any" data-tpl-text="/discount" data-tpl-value="/discount">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceDiscountR"><?= $this->getHtml('DiscountP'); ?></label>
                                            <input id="iPriceDiscountR" name="discountPercentage" type="number" step="any" data-tpl-text="/discountr" data-tpl-value="/discountr">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="iPriceBonus"><?= $this->getHtml('Bonus'); ?></label>
                                    <input id="iPriceBonus" name="bonus" type="number" step="any" data-tpl-text="/bonus" data-tpl-value="/bonus">
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceItemSegment"><?= $this->getHtml('ItemSegment'); ?></label>
                                            <select id="iPriceItemSegment" name="itemsegment" data-tpl-text="/item_segment" data-tpl-value="/item_segment">
                                                <option selected>
                                                <?php
                                                $types = $this->data['defaultAttributeTypes']['segment'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceItemSection"><?= $this->getHtml('ItemSection'); ?></label>
                                            <select id="iPriceItemSection" name="itemsection" data-tpl-text="/item_section" data-tpl-value="/item_section">
                                                <option selected>
                                                <?php
                                                $types = $this->data['defaultAttributeTypes']['section'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceItemSalesGroup"><?= $this->getHtml('ItemSalesGroup'); ?></label>
                                            <select id="iPriceItemSalesGroup" name="itemsalesgroup" data-tpl-text="/item_salesgroup" data-tpl-value="/item_salesgroup">
                                                <option selected>
                                                <?php
                                                $types = $this->data['defaultAttributeTypes']['sales_group'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceItemProductGroup"><?= $this->getHtml('ItemProductGroup'); ?></label>
                                            <select id="iPriceItemProductGroup" name="itemproductgroup" data-tpl-text="/item_productgroup" data-tpl-value="/item_productgroup">
                                                <option selected>
                                                <?php
                                                $types = $this->data['defaultAttributeTypes']['product_group'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceItemType"><?= $this->getHtml('ItemType'); ?></label>
                                            <select id="iPriceItemType" name="itemtype" data-tpl-text="/item_producttype" data-tpl-value="/item_producttype">
                                                <option selected>
                                                <?php
                                                $types = $this->data['defaultAttributeTypes']['product_type'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceClientSegment"><?= $this->getHtml('ClientSegment'); ?></label>
                                            <select id="iPriceClientSegment" name="clientsegment" data-tpl-text="/item_account_segment" data-tpl-value="/item_account_segment">
                                                <option selected>
                                                <?php
                                                $types = $this->data['clientSegmentationTypes']['segment'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceClientSection"><?= $this->getHtml('ClientSection'); ?></label>
                                            <select id="iPriceClientSection" name="clientsection" data-tpl-text="/item_account_section" data-tpl-value="/item_account_section">
                                                <option selected>
                                                <?php
                                                $types = $this->data['clientSegmentationTypes']['section'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceClientSalesGroup"><?= $this->getHtml('ClientGroup'); ?></label>
                                            <select id="iPriceClientSalesGroup" name="clientgroup" data-tpl-text="/item_account_salesgroup" data-tpl-value="/item_account_salesgroup">
                                                <option selected>
                                                <?php
                                                $types = $this->data['clientSegmentationTypes']['sales_group'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceClientType"><?= $this->getHtml('ClientType'); ?></label>
                                            <select id="iPriceClientType" name="clienttype" data-tpl-text="/item_account_type" data-tpl-value="/item_account_type">
                                                <option selected>
                                                <?php
                                                $types = $this->data['clientSegmentationTypes']['product_type'] ?? null;
                                                foreach ($types?->defaults ?? [] as $value) : ?>
                                                    <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getL11n()); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="iPriceClientRegion"><?= $this->getHtml('Region'); ?></label>
                                            <select id="iPriceClientRegion" name="region" data-tpl-text="/item_account_region" data-tpl-value="/item_account_type">
                                                <option selected>
                                                <?php
                                                foreach ($regions as $value) : ?>
                                                    <option value="<?= $value; ?>"><?= $this->printHtml($value); ?>
                                                <?php endforeach; ?>
                                                <?php
                                                foreach ($countries as $value) : ?>
                                                    <option value="<?= $value; ?>"><?= $this->printHtml(ISO3166NameEnum::getByName('_' . $value)); ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceItemStart"><?= $this->getHtml('Start'); ?></label>
                                            <input id="iPriceItemStart" name="start" type="datetime-local" data-tpl-text="/item_start" data-tpl-value="/item_start">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPriceItemEnd"><?= $this->getHtml('End'); ?></label>
                                            <input id="iPriceItemEnd" name="end" type="datetime-local" data-tpl-text="/item_end" data-tpl-value="/item_end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input id="bPriceItemAdd" formmethod="put" type="submit" class="add-form" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                                <input id="bPriceItemSave" formmethod="post" type="submit" class="save-form vh button save" value="<?= $this->getHtml('Update', '0', '0'); ?>">
                                <input id="bPriceItemCancel" type="submit" class="cancel-form vh button close" value="<?= $this->getHtml('Cancel', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Prices'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table id="itemSalesPriceTable" class="default sticky"
                            data-tag="form"
                            data-ui-element="tr"
                            data-add-tpl=".oms-add-tpl-itemSalesPrice"
                            data-update-form="itemSalesPriceForm">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Promocode'); ?>
                                    <td><?= $this->getHtml('Price'); ?>
                                    <td>
                                    <td><?= $this->getHtml('Quantity'); ?>
                                    <td><?= $this->getHtml('Discount'); ?>
                                    <td><?= $this->getHtml('DiscountP'); ?>
                                    <td><?= $this->getHtml('Bonus'); ?>
                                    <td><?= $this->getHtml('ItemSegment'); ?>
                                    <td><?= $this->getHtml('ItemSection'); ?>
                                    <td><?= $this->getHtml('ItemSalesGroup'); ?>
                                    <td><?= $this->getHtml('ItemProductGroup'); ?>
                                    <td><?= $this->getHtml('ItemType'); ?>
                                    <td><?= $this->getHtml('ClientSegment'); ?>
                                    <td><?= $this->getHtml('ClientSection'); ?>
                                    <td><?= $this->getHtml('ClientGroup'); ?>
                                    <td><?= $this->getHtml('ClientType'); ?>
                                    <td><?= $this->getHtml('Region'); ?>
                                    <td><?= $this->getHtml('Start'); ?>
                                    <td><?= $this->getHtml('End'); ?>
                            <tbody>
                                <template class="oms-add-tpl-itemSalesPrice">
                                    <tr class="animated medium-duration greenCircleFade" data-id="" draggable="false">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <input id="itemSalesPriceTable-remove-0" type="checkbox" class="vh">
                                            <label for="itemSalesPriceTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="itemSalesPriceTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="itemSalesPriceTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"></td>
                                        <td data-tpl-text="/name" data-tpl-value="/name" data-value=""></td>
                                        <td data-tpl-text="/promo" data-tpl-value="/promo" data-value=""></td>
                                        <td data-tpl-text="/price" data-tpl-value="/price"></td>
                                        <td data-tpl-text="/currency" data-tpl-value="/currency"></td>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"></td>
                                        <td data-tpl-text="/discount" data-tpl-value="/discount"></td>
                                        <td data-tpl-text="/discountr" data-tpl-value="/discountr"></td>
                                        <td data-tpl-text="/bonus" data-tpl-value="/bonus"></td>
                                        <td data-tpl-text="/item_segment" data-tpl-value="/item_segment"></td>
                                        <td data-tpl-text="/item_section" data-tpl-value="/item_section"></td>
                                        <td data-tpl-text="/item_salesgroup" data-tpl-value="/item_salesgroup"></td>
                                        <td data-tpl-text="/item_productgroup" data-tpl-value="/item_productgroup"></td>
                                        <td data-tpl-text="/item_producttype" data-tpl-value="/item_producttype"></td>
                                        <td data-tpl-text="/item_account_segment" data-tpl-value="/item_account_segment"></td>
                                        <td data-tpl-text="/item_account_section" data-tpl-value="/item_account_section"></td>
                                        <td data-tpl-text="/item_account_group" data-tpl-value="/item_account_group"></td>
                                        <td data-tpl-text="/item_account_type" data-tpl-value="/item_account_type"></td>
                                        <td data-tpl-text="/item_account_region" data-tpl-value="/item_account_region"></td>
                                        <td data-tpl-text="/item_start" data-tpl-value="/item_start"></td>
                                        <td data-tpl-text="/item_end" data-tpl-value="/item_end"></td>
                                    </tr>
                                </template>
                                <?php
                                $c = 0;
                                foreach ($this->data['prices'] as $key => $value) :
                                    if ($value->type !== PriceType::SALES) {
                                        continue;
                                    }
                                    ++$c;
                                ?>
                                    <tr data-id="<?= $value->id; ?>">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <?php if ($value->name !== 'default') : ?>
                                            <input id="itemSalesPriceTable-remove-<?= $value->id; ?>" type="checkbox" class="vh">
                                            <label for="itemSalesPriceTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="itemSalesPriceTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="itemSalesPriceTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                            <?php endif; ?>
                                        <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                        <td data-tpl-text="/name" data-tpl-value="/name"><?= $this->printHtml($value->name); ?>
                                        <td data-tpl-text="/promocode" data-tpl-value="/promocode"><?= $this->printHtml($value->promocode); ?>
                                        <td data-tpl-text="/price" data-tpl-value="/price"><?= $this->printHtml($value->priceNew->getAmount()); ?>
                                        <td data-tpl-text="/currency" data-tpl-value="/currency"><?= $this->printHtml($value->currency); ?>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"><?= $value->quantity->getAmount(); ?>
                                        <td data-tpl-text="/discount" data-tpl-value="/discount"><?= $value->discount->getAmount(); ?>
                                        <td data-tpl-text="/discountr" data-tpl-value="/discountr"><?= $this->getPercentage($value->discountPercentage); ?>
                                        <td data-tpl-text="/bonus" data-tpl-value="/bonus"><?= $value->bonus->getAmount(); ?>
                                        <td data-tpl-text="/item_segment" data-tpl-value="/item_segment"><?= $this->printHtml((string) $value->itemsegment->getL11n()); ?>
                                        <td data-tpl-text="/item_section" data-tpl-value="/item_section"><?= $this->printHtml((string) $value->itemsection->getL11n()); ?>
                                        <td data-tpl-text="/item_salesgroup" data-tpl-value="/item_salesgroup"><?= $this->printHtml((string) $value->itemsalesgroup->getL11n()); ?>
                                        <td data-tpl-text="/item_productgroup" data-tpl-value="/item_productgroup"><?= $this->printHtml((string) $value->itemproductgroup->getL11n()); ?>
                                        <td data-tpl-text="/item_producttype" data-tpl-value="/item_producttype"><?= $this->printHtml((string) $value->itemtype->getL11n()); ?>
                                        <td data-tpl-text="/item_account_segment" data-tpl-value="/item_account_segment"><?= $this->printHtml((string) $value->clientsegment->getL11n()); ?>
                                        <td data-tpl-text="/item_account_section" data-tpl-value="/item_account_section"><?= $this->printHtml((string) $value->clientsection->getL11n()); ?>
                                        <td data-tpl-text="/item_account_group" data-tpl-value="/item_account_group"><?= $this->printHtml((string) $value->clientgroup->getL11n()); ?>
                                        <td data-tpl-text="/item_account_type" data-tpl-value="/item_account_type"><?= $this->printHtml((string) $value->clienttype->getL11n()); ?>
                                        <td data-tpl-text="/item_account_region" data-tpl-value="/item_account_region"><?= $this->printHtml((string) $value->clientcountry); ?>
                                        <td data-tpl-text="/item_start" data-tpl-value="/item_start"><?= $value->start?->format('Y-m-d'); ?>
                                        <td data-tpl-text="/item_end" data-tpl-value="/item_end"><?= $value->end?->format('Y-m-d'); ?>
                                <?php endforeach; ?>
                                <?php if ($c === 0) : ?>
                                    <tr>
                                        <td colspan="22" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
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
                        <form id="itemProcurementForm" action="<?= UriFactory::build('{/api}item/procurement?csrf={$CSRF}'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Procurement'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iPriceLeadTime"><?= $this->getHtml('LeadTime'); ?></label>
                                    <input id="iPriceLeadTime" name="leadtime" type="number" data-tpl-text="/leadtime" data-tpl-value="/leadtime">
                                </div>

                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPName"><?= $this->getHtml('MinimumLevel'); ?></label>
                                            <input id="iPName" name="pname" type="text">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPName"><?= $this->getHtml('MaximumLevel'); ?></label>
                                            <input id="iPName" name="pname" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPName"><?= $this->getHtml('MaximumOrderInterval'); ?></label>
                                            <input id="iPName" name="pname" type="text">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPName"><?= $this->getHtml('MinimumOrderQuantity'); ?></label>
                                            <input id="iPName" name="pname" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>" name="save-item">
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="itemPurchasePriceForm" action="<?= UriFactory::build('{/api}bill/price?csrf={$CSRF}'); ?>" method="post"
                            data-ui-container="#itemPurchasePriceTable tbody"
                            data-add-form="itemPurchasePriceForm"
                            data-add-tpl="#itemPurchasePriceTable tbody .oms-add-tpl-itemPurchasePrice">
                            <div class="portlet-head"><?= $this->getHtml('Pricing'); ?><i class="g-icon download btn end-xs">download</i></div>
                            <div class="portlet-body">
                                <input id="iPurchasePriceId" class="vh" name="id" type="number" data-tpl-text="/id" data-tpl-value="/id">
                                <input id="iPurchasePriceItemId" class="vh" name="item" type="text" value="<?= $item->id; ?>">
                                <input id="iPurchasePriceItemType" class="vh" name="type" type="text" value="<?= PriceType::PURCHASE; ?>">

                                <div class="form-group">
                                    <label for="iPurchasePriceName"><?= $this->getHtml('Name'); ?></label>
                                    <input id="iPurchasePriceName" name="name" type="text" data-tpl-text="/name" data-tpl-value="/name">
                                </div>

                                <div class="form-group">
                                    <label for="iPurchasePriceSupplier"><?= $this->getHtml('Supplier'); ?></label>
                                    <input id="iPurchasePriceSupplier" name="supplier" type="text" data-tpl-text="/supplier" data-tpl-value="/supplier">
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPurchasePricePrice"><?= $this->getHtml('Price'); ?></label>
                                            <div class="flex-line wf-100">
                                                <div class="fixed">
                                                    <select id="iPurchasePriceCurrency" name="currency" data-tpl-text="/currency" data-tpl-value="/currency">
                                                        <?php foreach ($currencies as $currency) : ?>
                                                        <option value="<?= $currency; ?>"<?= $this->data['attributeView']->data['default_localization']->currency === $currency ? ' selected' : ''; ?>><?= $this->printHtml($currency); ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <input id="iPurchasePricePrice" class="wf-100" name="price_new" type="number" step="any" data-tpl-text="/price" data-tpl-value="/price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-group">
                                            <label for="iPurchasePriceQuantity"><?= $this->getHtml('Quantity'); ?></label>
                                            <input id="iPurchasePriceQuantity" name="quantity" type="number" step="any" data-tpl-text="/quantity" data-tpl-value="/quantity">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPurchasePriceDiscount"><?= $this->getHtml('Discount'); ?></label>
                                            <input id="iPurchasePriceDiscount" name="discount" type="number" step="any" data-tpl-text="/discount" data-tpl-value="/discount">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPurchasePriceDiscountP"><?= $this->getHtml('DiscountP'); ?></label>
                                            <input id="iPurchasePriceDiscountP" name="discountPercentage" type="number" step="any" data-tpl-text="/discountr" data-tpl-value="/discountr">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="iPurchasePriceBonus"><?= $this->getHtml('Bonus'); ?></label>
                                    <input id="iPurchasePriceBonus" name="bonus" type="number" step="any" data-tpl-text="/bonus" data-tpl-value="/bonus">
                                </div>
                            </div>
                            <div class="portlet-separator"></div>
                            <div class="portlet-body">
                                <div class="flex-line">
                                    <div>
                                        <div class="form-group">
                                            <label for="iPurchaseItemStart"><?= $this->getHtml('Start'); ?></label>
                                            <input id="iPurchaseItemStart" name="start" type="datetime-local" data-tpl-text="/purchase_start" data-tpl-value="/purchase_start">
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-group">
                                            <label for="iPurchaseItemEnd"><?= $this->getHtml('End'); ?></label>
                                            <input id="iPurchaseItemEnd" name="end" type="datetime-local" data-tpl-text="/purchase_end" data-tpl-value="/purchase_end">
                                        </div>
                                    </div>
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
                        <div class="portlet-head"><?= $this->getHtml('Suppliers'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table id="itemPurchasePriceTable" class="default sticky"
                            data-tag="form"
                            data-ui-element="tr"
                            data-add-tpl=".oms-add-tpl-itemPurchasePrice"
                            data-update-form="itemPurchasePriceForm">
                            <thead>
                                <tr>
                                    <td>
                                    <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Name'); ?><i class="sort-asc g-icon">expand_less</i><i class="sort-desc g-icon">expand_more</i>
                                    <td><?= $this->getHtml('Supplier'); ?>
                                    <td><?= $this->getHtml('Price'); ?>
                                    <td>
                                    <td><?= $this->getHtml('Quantity'); ?>
                                    <td><?= $this->getHtml('Discount'); ?>
                                    <td><?= $this->getHtml('DiscountP'); ?>
                                    <td><?= $this->getHtml('Bonus'); ?>
                                    <td><?= $this->getHtml('Start'); ?>
                                    <td><?= $this->getHtml('End'); ?>
                            <tbody>
                                <template class="oms-add-tpl-itemPurchasePrice">
                                    <tr class="animated medium-duration greenCircleFade" data-id="" draggable="false">
                                        <td>
                                            <i class="g-icon btn update-form">settings</i>
                                            <input id="itemPurchasePriceTable-remove-0" type="checkbox" class="vh">
                                            <label for="itemPurchasePriceTable-remove-0" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                            <span class="checked-visibility">
                                                <label for="itemPurchasePriceTable-remove-0" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                                <label for="itemPurchasePriceTable-remove-0" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                            </span>
                                        <td data-tpl-text="/id" data-tpl-value="/id"></td>
                                        <td data-tpl-text="/name" data-tpl-value="/name" data-value=""></td>
                                        <td data-tpl-text="/promo" data-tpl-value="/promo" data-value=""></td>
                                        <td data-tpl-text="/price" data-tpl-value="/price"></td>
                                        <td data-tpl-text="/currency" data-tpl-value="/currency"></td>
                                        <td data-tpl-text="/quantity" data-tpl-value="/quantity"></td>
                                        <td data-tpl-text="/discount" data-tpl-value="/discount"></td>
                                        <td data-tpl-text="/discountr" data-tpl-value="/discountr"></td>
                                        <td data-tpl-text="/bonus" data-tpl-value="/bonus"></td>
                                        <td data-tpl-text="/item_start" data-tpl-value="/item_start"></td>
                                        <td data-tpl-text="/item_end" data-tpl-value="/item_end"></td>
                                    </tr>
                                </template>
                                <?php
                                $c = 0;
                                foreach ($this->data['prices'] as $key => $value) :
                                    if ($value->type !== PriceType::PURCHASE) {
                                        continue;
                                    }
                                    ++$c;
                                ?>
                                <tr data-id="<?= $value->id; ?>">
                                    <td>
                                        <i class="g-icon btn update-form">settings</i>
                                        <?php if ($value->name !== 'default') : ?>
                                        <input id="itemPurchasePriceTable-remove-<?= $value->id; ?>" type="checkbox" class="vh">
                                        <label for="itemPurchasePriceTable-remove-<?= $value->id; ?>" class="checked-visibility-alt"><i class="g-icon btn form-action">close</i></label>
                                        <span class="checked-visibility">
                                            <label for="itemPurchasePriceTable-remove-<?= $value->id; ?>" class="link default"><?= $this->getHtml('Cancel', '0', '0'); ?></label>
                                            <label for="itemPurchasePriceTable-remove-<?= $value->id; ?>" class="remove-form link cancel"><?= $this->getHtml('Delete', '0', '0'); ?></label>
                                        </span>
                                        <?php endif; ?>
                                    <td data-tpl-text="/id" data-tpl-value="/id"><?= $value->id; ?>
                                    <td data-tpl-text="/name" data-tpl-value="/name"><?= $this->printHtml($value->name); ?>
                                    <td data-tpl-text="/supplier" data-tpl-value="/supplier"><?= $this->printHtml($value->supplier->number); ?>
                                    <td data-tpl-text="/price" data-tpl-value="/price"><?= $value->priceNew->getAmount(); ?>
                                    <td data-tpl-text="/currency" data-tpl-value="/currency"><?= $this->printHtml($value->currency); ?>
                                    <td data-tpl-text="/quantity" data-tpl-value="/quantity"><?= $value->quantity->getAmount(); ?>
                                    <td data-tpl-text="/discount" data-tpl-value="/discount"><?= $value->discount->getAmount(); ?>
                                    <td data-tpl-text="/discountr" data-tpl-value="/discountr"><?= $this->getPercentage($value->discountPercentage); ?>
                                    <td data-tpl-text="/bonus" data-tpl-value="/bonus"><?= $value->bonus->getAmount(); ?>
                                    <td data-tpl-text="/item_start" data-tpl-value="/item_start"><?= $value->start?->format('Y-m-d'); ?>
                                    <td data-tpl-text="/item_end" data-tpl-value="/item_end"><?= $value->end?->format('Y-m-d'); ?>
                            <?php endforeach; ?>
                            <?php if ($c === 0) : ?>
                                <tr>
                                    <td colspan="22" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-8" name="tabular-2" checked>
        <div class="tab">
        </div>

        <input type="radio" id="c-tab-9" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="itemAccounting" action="<?= UriFactory::build('{/api}item/accounting?csrf={$CSRF}'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Accounting'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iItemEarningIndicator"><?= $this->getHtml('EarningIndicator'); ?></label>
                                    <select id="iItemEarningIndicator" name="earningindicator">
                                        <option>
                                        <?php
                                            $attr = $this->data['defaultAttributeTypes']['sales_tax_code'] ?? null;
                                            foreach ($attr?->defaults ?? [] as $value) : ?>
                                            <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getValue()); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iItemCostIndicator"><?= $this->getHtml('CostIndicator'); ?></label>
                                    <select id="ItemCostIndicator" name="costindicator">
                                        <option>
                                        <?php
                                            $attr = $this->data['defaultAttributeTypes']['purchase_tax_code'] ?? null;
                                            foreach ($attr?->defaults ?? [] as $value) : ?>
                                            <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getValue()); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iItemCostCenter"><?= $this->getHtml('CostCenter'); ?></label>
                                    <select id="iItemCostCenter" name="costcenter">
                                        <option>
                                        <?php
                                        $costcenters = \Modules\Accounting\Models\CostCenterMapper::getAll()
                                            ->with('l11n')
                                            ->where('l11n/language', $this->response->header->l11n->language)
                                            ->executeGetArray();
                                        foreach ($costcenters as $cc) : ?>
                                            <option value="<?= $cc->id; ?>"><?= $this->printHtml($cc->code . ' ' . $cc->getL11n()); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iItemCostObject"><?= $this->getHtml('CostObject'); ?></label>
                                    <select id="iItemCostObject" name="costobject">
                                        <option>
                                        <?php
                                        $costobjects = \Modules\Accounting\Models\CostObjectMapper::getAll()
                                            ->with('l11n')
                                            ->where('l11n/language', $this->response->header->l11n->language)
                                            ->executeGetArray();
                                        foreach ($costobjects as $co) : ?>
                                            <option value="<?= $co->id; ?>"><?= $this->printHtml($co->code . ' ' . $co->getL11n()); ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-10" name="tabular-2" checked>
        <div class="tab">
        <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="itemAccounting" action="<?= UriFactory::build('{/api}item/stock?csrf={$CSRF}'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Stock'); ?></div>
                            <div class="portlet-body">
                                <!--
                                <div class="form-group">
                                    <label for="iItemStockDefault"><?= $this->getHtml('DefaultStock'); ?></label>
                                    <select id="iItemStockDefault" name="stockdefaultstock">
                                    <?php
                                        $stocks = StockMapper::getAll()->executeGetArray();
                                        foreach ($stocks as $stock) :
                                    ?>
                                        <option value="<?= $stock->id; ?>"><?= $this->printHtml($stock->name); ?>
                                    <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iItemStockLocation"><?= $this->getHtml('DefaultStockLocation'); ?></label>
                                    <select id="iItemStockLocation" name="stockdefaultlocation">
                                    <?php
                                        $stocks = StockLocationMapper::getAll()->executeGetArray();
                                        foreach ($stocks as $stock) :
                                    ?>
                                        <option value="<?= $stock->id; ?>"><?= $this->printHtml($stock->name); ?>
                                    <?php endforeach; ?>
                                    </select>
                                    </select>
                                </div>
                                -->

                                <div class="form-group">
                                    <label for="iItemStockInventory"><?= $this->getHtml('Inventory'); ?></label>
                                    <select id="iItemStockInventory" name="stockinventory">
                                    <?php
                                        $attr = $this->data['defaultAttributeTypes']['has_inventory'] ?? null;
                                        foreach ($attr?->defaults ?? [] as $value) : ?>
                                        <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getValue()); ?>
                                    <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iItemStockIdentifier"><?= $this->getHtml('Identifier'); ?></label>
                                    <select id="iItemStockIdentifier" name="stockidentifier">
                                    <?php
                                        $attr = $this->data['defaultAttributeTypes']['inventory_identifier'] ?? null;
                                        foreach ($attr?->defaults ?? [] as $value) : ?>
                                        <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getValue()); ?>
                                    <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iItemStockTaking"><?= $this->getHtml('Stocktaking'); ?></label>
                                    <select id="iItemStockTaking" name="stocktaking">
                                    <?php
                                        $attr = $this->data['defaultAttributeTypes']['stocktaking_type'] ?? null;
                                        foreach ($attr?->defaults ?? [] as $value) : ?>
                                        <option value="<?= $value->id; ?>"><?= $this->printHtml($value->getValue()); ?>
                                    <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="portlet-foot">
                                <input type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-12" name="tabular-2" checked>
        <div class="tab col-simple">
            <?= $this->data['note']->render('item-note', 'notes', $item->notes); ?>
        </div>

        <input type="radio" id="c-tab-13" name="tabular-2" checked>
        <div class="tab col-simple">
            <?= $this->data['media-upload']->render('item-file', 'files', '', $this->data['files']); ?>
        </div>

        <input type="radio" id="c-tab-14" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('RecentInvoices'); ?></div>
                        <div class="slider">
                        <table id="iSalesItemList" class="default sticky">
                            <thead>
                            <tr>
                                <td><?= $this->getHtml('Number'); ?>
                                <td><?= $this->getHtml('Type'); ?>
                                <td class="wf-100"><?= $this->getHtml('Name'); ?>
                                <td><?= $this->getHtml('Net'); ?>
                                <td><?= $this->getHtml('Date'); ?>
                                <td><?= $this->getHtml('Created'); ?>
                            <tbody>
                            <?php
                            $allInvoices = SalesBillMapper::getItemBills($item->id, SmartDateTime::startOfYear($this->data['business_start']), new SmartDateTime('now'));

                            $count = 0;

                            /** @var \Modules\Billing\Models\Bill $invoice */
                            foreach ($allInvoices as $invoice) :
                                ++$count;
                                $url       = UriFactory::build('{/base}/sales/bill/view?{?}&id=' . $invoice->id);
                                $clientUrl = UriFactory::build('{/base}/sales/client/view?{?}&id=' . $invoice->client->id);
                                ?>
                            <tr data-href="<?= $url; ?>">
                                <td><a href="<?= $url; ?>"><?= $invoice->getNumber(); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->type->getL11n(); ?></a>
                                <td><a class="content" href="<?= $clientUrl; ?>"><?= $invoice->billTo; ?></a>
                                <td><a href="<?= $url; ?>"><?= $this->getCurrency($invoice->netSales, symbol: ''); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->performanceDate->format('Y-m-d'); ?></a>
                                <td><a href="<?= $url; ?>"><?= $invoice->createdAt->format('Y-m-d'); ?></a>
                            <?php endforeach; ?>
                            <?php if ($count === 0) : ?>
                            <tr><td colspan="5" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                            <?php endif; ?>
                        </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <?php if (!empty($logs)) : ?>
        <input type="radio" id="c-tab-15" name="tabular-2" checked>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12">
                    <section class="portlet">
                        <div class="portlet-head"><?= $this->getHtml('Logs', 'Auditor'); ?><i class="g-icon download btn end-xs">download</i></div>
                        <div class="slider">
                        <table class="default sticky">
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
                                $previous = empty($logs) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \reset($logs)->id . '&ptype=p#{\#}';
                                $next     = empty($logs) ? HttpHeader::getAllHeaders()['Referer'] ?? 'admin/module/settings?id={?id}#{\#}' : 'admin/module/settings?{?}&audit=' . \end($logs)->id . '&ptype=n#{\#}';

                                foreach ($logs as $key => $audit) : ++$count;
                                    $url = UriFactory::build('{/base}/admin/audit/view?{?}&id=' . $audit->id); ?>
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
                        <!--
                        <div class="portlet-foot">
                            <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                            <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
                        </div>
                        -->
                    </section>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>