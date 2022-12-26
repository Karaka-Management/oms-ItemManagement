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

namespace Modules\ItemManagement\Controller;

use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\SettingsEnum;
use Modules\Billing\Models\BillTransferType;
use Modules\Billing\Models\SalesBillMapper;
use Modules\ItemManagement\Models\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\ItemAttributeValueMapper;
use Modules\ItemManagement\Models\ItemMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Localization\ISO3166CharEnum;
use phpOMS\Localization\ISO3166NameEnum;
use phpOMS\Localization\Money;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Views\View;

/**
 * ItemManagement controller class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeTypes(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-type-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response));

        /** @var \Modules\ItemManagement\Models\ItemAttributeType[] $attributes */
        $attributes = ItemAttributeTypeMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->addData('attributes', $attributes);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeValues(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-value-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response));

        /** @var \Modules\ItemManagement\Models\ItemAttributeValue[] $attributes */
        $attributes = ItemAttributeValueMapper::getAll()
            ->with('l11n')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->addData('attributes', $attributes);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeType(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-type');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response));

        /** @var \Modules\ItemManagement\Models\ItemAttributeType $attribute */
        $attribute = ItemAttributeTypeMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->addData('attribute', $attribute);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementAttributeValue(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/attribute-value');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004801001, $request, $response));

        /** @var \Modules\ItemManagement\Models\ItemAttributeValue $attribute */
        $attribute = ItemAttributeValueMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->addData('attribute', $attribute);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        /** @var \Modules\ItemManagement\Models\Item[] $items */
        $items = ItemMapper::getAll()
            ->with('l11n')
            ->with('l11n/type')
            ->where('l11n/language', $response->getLanguage())
            ->where('l11n/type/title', ['name1', 'name2', 'name3'], 'IN')
            ->limit(25)
            ->execute();

        $view->addData('items', $items);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/purchase-item-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        $items = ItemMapper::getAll()
            ->with('l11n')
            ->with('l11n/type')
            ->where('l11n/language', $response->getLanguage())
            ->where('l11n/type/title', ['name1', 'name2', 'name3'], 'IN')
            ->limit(25)
            ->execute();

        $view->addData('items', $items);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehousingList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/stock-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004807001, $request, $response));

        $items = ItemMapper::getAll()->execute();
        $view->addData('items', $items);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehousingCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004807001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return View
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : View
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/ItemManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        /** @var \Modules\ItemManagement\Models\Item $item */
        $item = ItemMapper::get()
            ->with('l11n')
            ->with('l11n/type')
            ->with('files')
            ->with('notes')
            ->where('id', (int) $request->getData('id'))
            ->where('l11n/language', $response->getLanguage())
            ->where('l11n/type/title', ['name1', 'name2', 'name3'], 'IN')
            ->limit(5, 'files')->sort('files/id', OrderType::DESC) // @todo: limit not working!!!
            ->limit(5, 'notes')->sort('notes/id', OrderType::DESC)
            ->execute();

        $view->addData('item', $item);

        /** @var \Model\Setting $settings */
        $settings = $this->app->appSettings->get(null, [
            SettingsEnum::DEFAULT_LOCALIZATION,
        ]);

        $view->setData('defaultlocalization', LocalizationMapper::get()->where('id', (int) $settings->getId())->execute());

        // stats
        if ($this->app->moduleManager->isActive('Billing')) {
            $ytd = SalesBillMapper::getSalesByItemId($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $mtd = SalesBillMapper::getSalesByItemId($item->getId(), new SmartDateTime('Y-m-01'), new SmartDateTime('now'));
            $avg = SalesBillMapper::getAvgSalesPriceByItemId($item->getId(), (new SmartDateTime('now'))->smartModify(-1), new SmartDateTime('now'));

            $lastOrder = SalesBillMapper::getLastOrderDateByItemId($item->getId());

            $newestInvoices = SalesBillMapper::getAll()
                ->with('type')
                ->where('type/transferType', BillTransferType::SALES)
                ->sort('id', OrderType::DESC)
                ->limit(5)
                ->execute();

            $topCustomers      = [];
            $allInvoices       = SalesBillMapper::getItemBills($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $regionSales       = SalesBillMapper::getItemRegionSales($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $countrySales      = SalesBillMapper::getItemCountrySales($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'), 5);
            $monthlySalesCosts = SalesBillMapper::getItemMonthlySalesCosts($item->getId(), (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));
        } else {
            $ytd               = new Money();
            $mtd               = new Money();
            $avg               = new Money();
            $lastOrder         = null;
            $newestInvoices    = [];
            $allInvoices       = [];
            $topCustomers      = [];
            $regionSales       = [];
            $countrySales      = [];
            $monthlySalesCosts = [];
        }

        $view->addData('ytd', $ytd);
        $view->addData('mtd', $mtd);
        $view->addData('avg', $avg);
        $view->addData('lastOrder', $lastOrder);
        $view->addData('newestInvoices', $newestInvoices);
        $view->addData('allInvoices', $allInvoices);
        $view->addData('topCustomers', $topCustomers);
        $view->addData('regionSales', $regionSales);
        $view->addData('countrySales', $countrySales);
        $view->addData('monthlySalesCosts', $monthlySalesCosts);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseItem(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = $this->viewItemManagementSalesItem($request, $response, $data);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->setData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehouseItem(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = $this->viewItemManagementSalesItem($request, $response, $data);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->setData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemSalesAnalysis(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/ClientManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-analysis');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1001602001, $request, $response));

        $monthlySalesCosts = [];
        for ($i = 1; $i < 13; ++$i) {
            $monthlySalesCosts[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000),
                'net_costs' => (int) ($sales * \mt_rand(25, 55) / 100),
                'year'      => 2020,
                'month'     => $i,
            ];
        }

        $view->addData('monthlySalesCosts', $monthlySalesCosts);

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

        $view->addData('monthlySalesCustomer', $monthlySalesCustomer);

        $annualSalesCustomer = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualSalesCustomer[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000) * 12,
                'customers' => \mt_rand(200, 400) * 6,
                'year'      => 2020 - 10 + $i,
            ];
        }

        $view->addData('annualSalesCustomer', $annualSalesCustomer);

        /////
        $monthlyCustomerRetention = [];
        for ($i = 1; $i < 10; ++$i) {
            $monthlyCustomerRetention[] = [
                'customers' => \mt_rand(200, 400),
                'year'      => \date('y') - 9 + $i,
            ];
        }

        $view->addData('monthlyCustomerRetention', $monthlyCustomerRetention);

        /////
        $currentCustomerRegion = [
            'Europe'  => (int) (\mt_rand(200, 400) / 4),
            'America' => (int) (\mt_rand(200, 400) / 4),
            'Asia'    => (int) (\mt_rand(200, 400) / 4),
            'Africa'  => (int) (\mt_rand(200, 400) / 4),
            'CIS'     => (int) (\mt_rand(200, 400) / 4),
            'Other'   => (int) (\mt_rand(200, 400) / 4),
        ];

        $view->addData('currentCustomerRegion', $currentCustomerRegion);

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

        $view->addData('annualCustomerRegion', $annualCustomerRegion);

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

        $view->addData('currentCustomersRep', $currentCustomersRep);

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

        $view->addData('annualCustomersRep', $annualCustomersRep);

        /////
        $currentCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $country                                           = ISO3166NameEnum::getRandom();
            $currentCustomersCountry[\substr($country, 0, 20)] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersCountry, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->addData('currentCustomersCountry', $currentCustomersCountry);

        $annualCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $countryCode                                          = ISO3166CharEnum::getRandom();
            $countryName                                          = ISO3166NameEnum::getByName('_' . $countryCode);
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

        $view->addData('annualCustomersCountry', $annualCustomersCountry);

        /////
        $customerGroups = [];
        for ($i = 1; $i < 7; ++$i) {
            $customerGroups['Group ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        $view->addData('customerGroups', $customerGroups);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemPurchaseAnalysis(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/ClientManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-analysis');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1001602001, $request, $response));

        $monthlySalesCosts = [];
        for ($i = 1; $i < 13; ++$i) {
            $monthlySalesCosts[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000),
                'net_costs' => (int) ($sales * \mt_rand(25, 55) / 100),
                'year'      => 2020,
                'month'     => $i,
            ];
        }

        $view->addData('monthlySalesCosts', $monthlySalesCosts);

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

        $view->addData('monthlySalesCustomer', $monthlySalesCustomer);

        $annualSalesCustomer = [];
        for ($i = 1; $i < 11; ++$i) {
            $annualSalesCustomer[] = [
                'net_sales' => $sales = \mt_rand(1200000000, 2000000000) * 12,
                'customers' => \mt_rand(200, 400) * 6,
                'year'      => 2020 - 10 + $i,
            ];
        }

        $view->addData('annualSalesCustomer', $annualSalesCustomer);

        /////
        $monthlyCustomerRetention = [];
        for ($i = 1; $i < 10; ++$i) {
            $monthlyCustomerRetention[] = [
                'customers' => \mt_rand(200, 400),
                'year'      => \date('y') - 9 + $i,
            ];
        }

        $view->addData('monthlyCustomerRetention', $monthlyCustomerRetention);

        /////
        $currentCustomerRegion = [
            'Europe'  => (int) (\mt_rand(200, 400) / 4),
            'America' => (int) (\mt_rand(200, 400) / 4),
            'Asia'    => (int) (\mt_rand(200, 400) / 4),
            'Africa'  => (int) (\mt_rand(200, 400) / 4),
            'CIS'     => (int) (\mt_rand(200, 400) / 4),
            'Other'   => (int) (\mt_rand(200, 400) / 4),
        ];

        $view->addData('currentCustomerRegion', $currentCustomerRegion);

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

        $view->addData('annualCustomerRegion', $annualCustomerRegion);

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

        $view->addData('currentCustomersRep', $currentCustomersRep);

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

        $view->addData('annualCustomersRep', $annualCustomersRep);

        /////
        $currentCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $country                                           = ISO3166NameEnum::getRandom();
            $currentCustomersCountry[\substr($country, 0, 20)] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        \uasort($currentCustomersCountry, function($a, $b) {
            return $b['customers'] <=> $a['customers'];
        });

        $view->addData('currentCustomersCountry', $currentCustomersCountry);

        $annualCustomersCountry = [];
        for ($i = 1; $i < 51; ++$i) {
            $countryCode                                          = ISO3166CharEnum::getRandom();
            $countryName                                          = ISO3166NameEnum::getByName('_' . $countryCode);
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

        $view->addData('annualCustomersCountry', $annualCustomersCountry);

        /////
        $customerGroups = [];
        for ($i = 1; $i < 7; ++$i) {
            $customerGroups['Group ' . $i] = [
                'customers' => (int) (\mt_rand(200, 400) / 12),
            ];
        }

        $view->addData('customerGroups', $customerGroups);

        return $view;
    }
}
