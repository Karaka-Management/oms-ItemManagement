<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Controller;

use Model\SettingsEnum;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Billing\Models\BillMapper;
use Modules\ItemManagement\Models\ItemMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Localization\Money;
use phpOMS\Asset\AssetType;

/**
 * ItemManagement controller class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
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
    public function viewItemManagementSalesList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        $items = ItemMapper::withConditional('language', $response->getLanguage())::getAfterPivot(0, null, 25);
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
    public function viewItemManagementPurchaseList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/purchase-item-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        $items = ItemMapper::withConditional('language', $response->getLanguage())::getAfterPivot(0, null, 25);
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
    public function viewItemManagementWarehousingList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/stock-list');
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
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
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
    public function viewItemManagementPurchaseCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
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
    public function viewItemManagementWarehousingCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
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
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/ItemManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        $item = ItemMapper::withConditional('language', $response->getLanguage())::get((int) $request->getData('id'));
        $view->addData('item', $item);

        $settings = $this->app->appSettings->get(null, [
            SettingsEnum::DEFAULT_LOCALIZATION,
        ]);

        $view->setData('defaultlocalization', LocalizationMapper::get((int) $settings['id']));

        if ($this->app->moduleManager->isActive('Billing')) {
            $ytd = BillMapper::getSalesByItemId($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $mtd = BillMapper::getSalesByItemId($item->getId(), new SmartDateTime('Y-m-01'), new SmartDateTime('now'));
            $avg = BillMapper::getAvgSalesPriceByItemId($item->getId(), (new SmartDateTime('now'))->smartModify(-1), new SmartDateTime('now'));
            $lastOrder = BillMapper::getLastOrderDateByItemId($item->getId());
            $newestInvoices = BillMapper::getNewestItemInvoices($item->getId(), 5);
            $topCustomers = BillMapper::getItemTopCustomers($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'), 5);
            $regionSales = BillMapper::getItemRegionSales($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $countrySales = BillMapper::getItemCountrySales($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'), 5);
            $monthlySalesCosts = BillMapper::getItemMonthlySalesCosts($item->getId(), (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));
        } else {
            $ytd = new Money();
            $mtd = new Money();
            $avg = new Money();
            $lastOrder = null;
            $newestInvoices = [];
            $topCustomers = [];
            $regionSales = [];
            $countrySales = [];
            $monthlySalesCosts = [];
        }

        $view->addData('ytd', $ytd);
        $view->addData('mtd', $mtd);
        $view->addData('avg', $avg);
        $view->addData('lastOrder', $lastOrder);
        $view->addData('newestInvoices', $newestInvoices);
        $view->addData('topCustomers', $topCustomers);
        $view->addData('regionSales', $regionSales);
        $view->addData('countrySales', $countrySales);
        $view->addData('monthlySalesCosts', $monthlySalesCosts);

        return $view;
    }
}
