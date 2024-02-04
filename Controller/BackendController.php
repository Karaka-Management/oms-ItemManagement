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

namespace Modules\ItemManagement\Controller;

use Modules\Auditor\Models\AuditMapper;
use Modules\ClientManagement\Models\Attribute\ClientAttributeTypeMapper;
use Modules\ClientManagement\Models\Attribute\ClientAttributeValueL11nMapper;
use Modules\ItemManagement\Models\Attribute\ItemAttributeTypeL11nMapper;
use Modules\ItemManagement\Models\Attribute\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\Attribute\ItemAttributeValueL11nMapper;
use Modules\ItemManagement\Models\Attribute\ItemAttributeValueMapper;
use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemL11nMapper;
use Modules\ItemManagement\Models\ItemL11nTypeMapper;
use Modules\ItemManagement\Models\ItemMapper;
use Modules\ItemManagement\Models\MaterialTypeL11nMapper;
use Modules\ItemManagement\Models\MaterialTypeMapper;
use Modules\ItemManagement\Models\StockIdentifierType;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\MediaTypeMapper;
use Modules\Organization\Models\Attribute\UnitAttributeMapper;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\DataStorage\Database\Query\Where;
use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Utils\StringUtils;
use phpOMS\Views\View;

/**
 * ItemManagement controller class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeTypeList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-type-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeType[] $attributes */
        $attributes = ItemAttributeTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $view->data['attributes'] = $attributes;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeValues(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-value-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeValue[] $attributes */
        $attributes = ItemAttributeValueMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $view->data['attributes'] = $attributes;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeType(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-type');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeType $attribute */
        $attribute = ItemAttributeTypeMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $l11ns = ItemAttributeTypeL11nMapper::getAll()
            ->where('ref', $attribute->id)
            ->execute();

        $view->data['attribute'] = $attribute;
        $view->data['l11ns']     = $l11ns;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeValue(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-value');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\Attribute\Models\AttributeValue $attribute */
        $attribute = ItemAttributeValueMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $view->data['attribute'] = $attribute;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementItemList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response);

        /** @var \Modules\ItemManagement\Models\Item[] $items */
        $items = ItemMapper::getAll()
            ->with('container') // @todo change to only get the default sales container
            ->with('l11n')
            ->with('l11n/type')
            ->with('files')
            ->with('files/types')
            ->where('l11n/language', $response->header->l11n->language)
            ->where('l11n/type/title', ['name1', 'name2', 'name3'], 'IN')
            ->where('files/types/name', 'item_profile_image')
            ->limit(50)
            ->execute();

        $view->data['items'] = $items;

        // Stock distribution
        $dists    = [];
        $reserved = [];
        $ordered  = [];
        if ($this->app->moduleManager->isActive('WarehouseManagement')) {
            $itemIds = \array_map(function (Item $item) {
                return $item->id;
            }, $items);

            $distributions = \Modules\WarehouseManagement\Models\StockMapper::getStockDistribution($itemIds);

            $dists    = $distributions['dists'];
            $reserved = $distributions['reserved'];
            $ordered  = $distributions['ordered'];
        }

        $view->data['dists']    = $dists;
        $view->data['reserved'] = $reserved;
        $view->data['ordered']  = $ordered;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return $this->viewItemManagementItemList($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return $this->viewItemManagementItemList($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementItemCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return $this->viewItemManagementItem($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehousingList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return $this->viewItemManagementItemList($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehousingCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004807001, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return View
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : View
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/ItemManagement/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004803001, $request, $response);

        /** @var \Modules\ItemManagement\Models\Item */
        $view->data['item'] = ItemMapper::get()
            ->with('l11n')
            ->with('l11n/type')
            ->with('files')->limit(5, 'files')->sort('files/id', OrderType::DESC)
            ->with('notes')->limit(5, 'notes')->sort('notes/id', OrderType::DESC)
            ->with('files/types')
            ->with('attributes')
            ->with('attributes/type')
            ->with('attributes/type/l11n')
            ->with('attributes/value')
            //->with('attributes/value/l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->header->l11n->language)
            ->where('l11n/type/title', ['name1', 'name2', 'name3'], 'IN')
            ->where('attributes/type/l11n/language', $response->header->l11n->language)
            //->where('attributes/value/l11n/language', $response->header->l11n->language)
            ->execute();

        // Get item profile image
        // @feature Create a new read mapper function that returns relation models instead of its own model
        //      https://github.com/Karaka-Management/phpOMS/issues/320
        $query   = new Builder($this->app->dbPool->get());
        $results = $query->selectAs(ItemMapper::HAS_MANY['files']['external'], 'file')
            ->from(ItemMapper::TABLE)
            ->leftJoin(ItemMapper::HAS_MANY['files']['table'])
                ->on(ItemMapper::HAS_MANY['files']['table'] . '.' . ItemMapper::HAS_MANY['files']['self'], '=', ItemMapper::TABLE . '.' . ItemMapper::PRIMARYFIELD)
            ->leftJoin(MediaMapper::TABLE)
                ->on(ItemMapper::HAS_MANY['files']['table'] . '.' . ItemMapper::HAS_MANY['files']['external'], '=', MediaMapper::TABLE . '.' . MediaMapper::PRIMARYFIELD)
             ->leftJoin(MediaMapper::HAS_MANY['types']['table'])
                ->on(MediaMapper::TABLE . '.' . MediaMapper::PRIMARYFIELD, '=', MediaMapper::HAS_MANY['types']['table'] . '.' . MediaMapper::HAS_MANY['types']['self'])
            ->leftJoin(MediaTypeMapper::TABLE)
                ->on(MediaMapper::HAS_MANY['types']['table'] . '.' . MediaMapper::HAS_MANY['types']['external'], '=', MediaTypeMapper::TABLE . '.' . MediaTypeMapper::PRIMARYFIELD)
            ->where(ItemMapper::HAS_MANY['files']['self'], '=', $view->data['item']->id)
            ->where(MediaTypeMapper::TABLE . '.' . MediaTypeMapper::getColumnByMember('name'), '=', 'item_profile_image');

        /** @var \Modules\Media\Models\Media */
        $view->data['itemImage'] = MediaMapper::get()
            ->with('types')
            ->where('id', $results)
            ->limit(1)
            ->execute();

        $businessStart = UnitAttributeMapper::get()
            ->with('type')
            ->with('value')
            ->where('ref', $this->app->unitId)
            ->where('type/name', 'business_year_start')
            ->execute();

        $view->data['business_start'] = $businessStart->id === 0 ? 1 : $businessStart->value->getValue();

        $view->data['attributeView']                               = new \Modules\Attribute\Theme\Backend\Components\AttributeView($this->app->l11nManager, $request, $response);
        $view->data['attributeView']->data['default_localization'] = $this->app->l11nServer;

        $view->data['l11nView'] = new \Web\Backend\Views\L11nView($this->app->l11nManager, $request, $response);

        /** @var \phpOMS\Localization\BaseStringL11nType[] */
        $view->data['l11nTypes'] = ItemL11nTypeMapper::getAll()
            ->execute();

        /** @var \phpOMS\Localization\BaseStringL11n[] */
        $view->data['l11nValues'] = ItemL11nMapper::getAll()
            ->with('type')
            ->where('ref', $view->data['item']->id)
            ->execute();

        /** @var \Modules\Attribute\Models\AttributeType[] */
        $view->data['attributeTypes'] = ItemAttributeTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        /** @var \Modules\Organization\Models\Unit[] */
        $view->data['units'] = UnitMapper::getAll()
            ->execute();

        $view->data['hasBilling'] = $this->app->moduleManager->isActive('Billing');

        /** @var \Modules\Billing\Models\Price\Price[] */
        $view->data['prices'] = $view->data['hasBilling']
            ? \Modules\Billing\Models\Price\PriceMapper::getAll()
                ->where('item', $view->data['item']->id)
                ->where('client', null)
                ->execute()
            : [];

        $tmp = ItemAttributeTypeMapper::getAll()
            ->with('defaults')
            ->with('defaults/l11n')
            ->where('name', [
                'segment', 'section', 'sales_group', 'product_group', 'product_type',
                'sales_tax_code', 'purchase_tax_code',
                //'has_inventory', 'inventory_identifier', 'stocktaking_type',
            ], 'IN')
            ->where('defaults/l11n', (new Where($this->app->dbPool->get()))
                ->where(ItemAttributeValueL11nMapper::getColumnByMember('ref'), '=', null)
                ->orWhere(ItemAttributeValueL11nMapper::getColumnByMember('language'), '=', $response->header->l11n->language))
            ->execute();

        $defaultAttributeTypes = [];
        foreach ($tmp as $t) {
            $defaultAttributeTypes[$t->name] = $t;
        }

        $view->data['defaultAttributeTypes'] = $defaultAttributeTypes;

        $tmp = ClientAttributeTypeMapper::getAll()
            ->with('defaults')
            ->with('defaults/l11n')
            ->where('name', ['segment', 'section', 'client_group', 'client_type'], 'IN')
            ->where('defaults/l11n', (new Where($this->app->dbPool->get()))
                ->where(ClientAttributeValueL11nMapper::getColumnByMember('ref'), '=', null)
                ->orWhere(ClientAttributeValueL11nMapper::getColumnByMember('language'), '=', $response->header->l11n->language))
            ->execute();

        $clientSegmentationTypes = [];
        foreach ($tmp as $t) {
            $clientSegmentationTypes[$t->name] = $t;
        }

        $view->data['clientSegmentationTypes'] = $clientSegmentationTypes;

        /** @var \Modules\Auditor\Models\Audit[] */
        $view->data['audits'] = AuditMapper::getAll()
            ->where('type', StringUtils::intHash(ItemMapper::class))
            ->where('module', 'ItemManagement')
            ->where('ref', (string) $view->data['item']->id)
            ->execute();

        // @todo join audit with files, attributes, localization, prices, notes, ...

        /** @var \Modules\Media\Models\Media[] */
        $view->data['files'] = MediaMapper::getAll()
            ->with('types')
            ->join('id', ItemMapper::class, 'files') // id = media id, files = item relations
                ->on('id', $view->data['item']->id, relation: 'files') // id = item id
            ->execute();

        $view->data['media-upload'] = new \Modules\Media\Theme\Backend\Components\Upload\BaseView($this->app->l11nManager, $request, $response);
        $view->data['note']         = new \Modules\Editor\Theme\Backend\Components\Note\BaseView($this->app->l11nManager, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return View
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : View
    {
        return $this->viewItemManagementItem($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseItem(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return $this->viewItemManagementItem($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehouseItem(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        return $this->viewItemManagementItem($request, $response, $data);
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemPurchaseAnalysis(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $head  = $response->data['Content']->head;
        $nonce = $this->app->appSettings->getOption('script-nonce');

        $head->addAsset(AssetType::CSS, 'Resources/chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/chart.js', ['nonce' => $nonce]);
        $head->addAsset(AssetType::JSLATE, 'Modules/Sales/Controller/Controller.js', ['nonce' => $nonce, 'type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-analysis');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1001602001, $request, $response);

        $monthlySalesCosts = [];
        for ($i = 1; $i < 13; ++$i) {
            $monthlySalesCosts[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000),
                'net_costs' => (int) ($sales * \mt_rand(25, 55) / 100),
                'year'      => 2020,
                'month'     => $i,
            ];
        }

        $view->data['monthlySalesCosts'] = $monthlySalesCosts;

        /////
        $monthlySalesCustomer = [];
        for ($i = 1; $i < 13; ++$i) {
            $monthlySalesCustomer[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000),
                'customers' => \mt_rand(200, 400),
                'year'      => 2020,
                'month'     => $i,
            ];
        }

        $view->data['monthlySalesCustomer'] = $monthlySalesCustomer;

        $annualSalesCustomer = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualSalesCustomer[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000) * 12,
                'customers' => \mt_rand(200, 400) * 6,
                'year'      => 2020 - 10 + $i,
            ];
        }

        $view->data['annualSalesCustomer'] = $annualSalesCustomer;

        /////
        $monthlyCustomerRetention = [];
        for ($i = 1; $i < 10; ++$i) {
            $monthlyCustomerRetention[] = [
                'customers' => \mt_rand(200, 400),
                'year'      => \date('y') - 9 + $i,
            ];
        }

        $view->data['monthlyCustomerRetention'] = $monthlyCustomerRetention;

        /////
        $currentCustomerRegion = [
            'Europe'  => (int) (\mt_rand(200, 400) / 4),
            'America' => (int) (\mt_rand(200, 400) / 4),
            'Asia'    => (int) (\mt_rand(200, 400) / 4),
            'Africa'  => (int) (\mt_rand(200, 400) / 4),
            'CIS'     => (int) (\mt_rand(200, 400) / 4),
            'Other'   => (int) (\mt_rand(200, 400) / 4),
        ];

        $view->data['currentCustomerRegion'] = $currentCustomerRegion;

        $annualCustomerRegion = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualCustomerRegion[] = [
                'year'    => 2020 - 10 + $i,
                'Europe'  => $a = (int) (\mt_rand(200, 400) / 4),
                'America' => $b = (int) (\mt_rand(200, 400) / 4),
                'Asia'    => $c = (int) (\mt_rand(200, 400) / 4),
                'Africa'  => $d = (int) (\mt_rand(200, 400) / 4),
                'CIS'     => $e = (int) (\mt_rand(200, 400) / 4),
                'Other'   => $f = (int) (\mt_rand(200, 400) / 4),
                'Total'   => $a + $b + $c + $d + $e + $f,
            ];
        }

        $view->data['annualCustomerRegion'] = $annualCustomerRegion;

        /////
        $currentCustomersRep = [];
        for ($i = 1; $i < 13; ++$i) {
            $currentCustomersRep['Rep ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersRep, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->data['currentCustomersRep'] = $currentCustomersRep;

        $annualCustomersRep = [];
        for ($i = 1; $i < 13; ++$i) {
            $annualCustomersRep['Rep ' . $i] = [];

            for ($j = 1; $j < 11; ++$j) {
                $annualCustomersRep['Rep ' . $i][] = [
                    'customers' => (int) (\mt_rand(200, 400) / 12),
                    'year'      => 2020 - 10 + $j,
                ];
            }
        }

        $view->data['annualCustomersRep'] = $annualCustomersRep;

        /////
        $currentCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $country                                           = (string) ISO3166NameEnum::getRandom();
            $currentCustomersCountry[\substr($country, 0, 20)] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersCountry, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->data['currentCustomersCountry'] = $currentCustomersCountry;

        $annualCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $countryCode                                          = ISO3166CharEnum::getRandom();
            $countryName                                          = (string) ISO3166NameEnum::getByName('_' . $countryCode);
            $annualCustomersCountry[\substr($countryName, 0, 20)] = [];

            for ($j = 1; $j < 11; ++$j) {
                $annualCustomersCountry[\substr($countryName, 0, 20)][] = [
                    'customers' => (int) (\mt_rand(200, 400) / 12),
                    'year'      => 2020 - 10 + $j,
                    'name'      => $countryName,
                    'code'      => $countryCode,
                ];
            }
        }

        $view->data['annualCustomersCountry'] = $annualCustomersCountry;

        /////
        $customerGroups = [];
        for ($i = 1; $i < 7; ++$i) {
            $customerGroups['Group ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        $view->data['customerGroups'] = $customerGroups;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewMaterialList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/material-type-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1002901101, $request, $response);

        $view->data['types'] = MaterialTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewMaterialView(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/material-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1002901101, $request, $response);

        $view->data['type'] = MaterialTypeMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->header->l11n->language)
            ->execute();

        $view->data['l11nView'] = new \Web\Backend\Views\L11nView($this->app->l11nManager, $request, $response);

        /** @var \phpOMS\Localization\BaseStringL11n[] $l11nValues */
        $l11nValues = MaterialTypeL11nMapper::getAll()
            ->where('ref', $view->data['type']->id)
            ->execute();

        $view->data['l11nValues'] = $l11nValues;

        return $view;
    }
}
