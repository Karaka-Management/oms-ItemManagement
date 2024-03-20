<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Controller;

use Modules\Admin\Models\NullAccount;
use Modules\Billing\Models\Price\PriceType;
use Modules\ItemManagement\Models\Attribute\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\Container;
use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemL11nMapper;
use Modules\ItemManagement\Models\ItemL11nTypeMapper;
use Modules\ItemManagement\Models\ItemMapper;
use Modules\ItemManagement\Models\ItemRelationType;
use Modules\ItemManagement\Models\ItemRelationTypeMapper;
use Modules\ItemManagement\Models\ItemStatus;
use Modules\ItemManagement\Models\MaterialTypeL11nMapper;
use Modules\ItemManagement\Models\MaterialTypeMapper;
use Modules\ItemManagement\Models\PermissionCategory;
use Modules\ItemManagement\Models\SettingsEnum as ItemSettingsEnum;
use Modules\ItemManagement\Models\StockIdentifierType;
use Modules\Media\Models\Collection;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\MediaTypeMapper;
use Modules\Media\Models\PathSettings;
use phpOMS\Account\PermissionType;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\BaseStringL11nType;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Localization\NullBaseStringL11nType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Stdlib\Base\FloatInt;
use phpOMS\System\MimeType;

/**
 * ItemManagement class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @todo Import item prices from csv/excel sheet
 *      https://github.com/Karaka-Management/oms-ItemManagement/issues/15
 *
 * @todo Perform inflation increase on all items
 *      https://github.com/Karaka-Management/oms-ItemManagement/issues/16
 */
final class ApiController extends Controller
{
    /**
     * Api method to find items
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemFind(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        // @question How to handle empty search?
        //      1. Return empty
        //      2. Return normal item list with default limit

        /** @var BaseStringL11n[] $l11n */
        $l11n = ItemL11nMapper::getAll()
            ->with('type')
            ->where('type/title', ['internal_matchcodes'], 'IN')
            ->where('language', $response->header->l11n->language)
            ->where('content', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->limit($request->getDataInt('limit') ?? 50)
            ->execute();

        if (empty($l11n)) {
            /** @var BaseStringL11n[] $l11n */
            $l11n = ItemL11nMapper::getAll()
                ->with('type')
                ->where('type/title',  ['name1', 'name2'], 'IN')
                ->where('language', $response->header->l11n->language)
                ->where('content', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
                ->limit($request->getDataInt('limit') ?? 50)
                ->execute();
        }

        if (empty($l11n)) {
            $searches = \explode(' ', $request->getDataString('search') ?? '');
            foreach ($searches as $search) {
                /** @var BaseStringL11n[] $l11n */
                $l11n = ItemL11nMapper::getAll()
                    ->with('type')
                    ->where('type/title', ['internal_matchcodes'], 'IN')
                    ->where('language', $response->header->l11n->language)
                    ->where('content', '%' . $search . '%', 'LIKE')
                    ->limit($request->getDataInt('limit') ?? 50)
                    ->execute();

                if (!empty($l11n)) {
                    break;
                }
            }

            if (empty($l11n)) {
                foreach ($searches as $search) {
                    /** @var BaseStringL11n[] $l11n */
                    $l11n = ItemL11nMapper::getAll()
                        ->with('type')
                        ->where('type/title', ['name1', 'name2'], 'IN')
                        ->where('language', $response->header->l11n->language)
                        ->where('content', '%' . $search . '%', 'LIKE')
                        ->limit($request->getDataInt('limit') ?? 50)
                        ->execute();

                    if (!empty($l11n)) {
                        break;
                    }
                }
            }
        }

        $itemList = [];
        if (!empty($l11n)) {
            $itemIds = \array_map(function (BaseStringL11n $l) : int {
                return $l->ref;
            }, $l11n);

            /** @var Item[] $itemList */
            $itemList = ItemMapper::getAll()
                ->with('l11n')
                ->with('l11n/type')
                ->where('l11n/type/title', ['name1', 'name2'], 'IN')
                ->where('l11n/language', $request->header->l11n->language)
                ->where('id', $itemIds, 'IN')
                ->execute();
        }

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set($request->uri->__toString(), \array_values($itemList));

        /*
        @todo BIG TODO.
        This is the query I want to be used internally:

        select itemmgmt_item.itemmgmt_item_no, itemmgmt_item_l11n.itemmgmt_item_l11n_description
        from itemmgmt_item
        left join itemmgmt_item_l11n on itemmgmt_item.itemmgmt_item_id = itemmgmt_item_l11n.itemmgmt_item_l11n_item
        left join itemmgmt_item_l11n_type on itemmgmt_item_l11n.itemmgmt_item_l11n_typeref = itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_id
        where
            itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_title IN ("name1", "name2")
            AND itemmgmt_item_l11n.itemmgmt_item_l11n_lang = "en"
            AND itemmgmt_item_l11n.itemmgmt_item_l11n_description LIKE "%Doc%"

        It is not used because they are defined as has many relations and therefore queried as a loop internally. I as the person making the request know its a 1 to 1 result despite the 1 to many db relation.
        */
    }

    /**
     * Create media directory path
     *
     * @param Item $item Item
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function createItemDir(Item $item) : string
    {
        return '/Modules/ItemManagement/Items/'
            . (empty($item->number) ? $item->id : $item->number);
    }

    /**
     * Api method to export items items
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemListExport(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $items = [];

        /** @var Item $item */
        foreach (ItemMapper::yield()->executeYield() as $item) {
            $items[] = [
                'id'    => $item->id,
                'name1' => $item->getL11n('name1')->content,
                'name2' => $item->getL11n('name2')->content,
            ];
        }

        $report       = new \Modules\Exchange\Models\Report();
        $report->data = $items;

        $this->app->moduleManager->get('Exchange', 'Api')
            ->apiExportReport($request, $response, $report, $request->getDataString('type') ?? 'csv');
    }

    /**
     * Api method to create item
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateItemCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $item = $this->createItemFromRequest($request);

        $this->app->dbPool->get()->con->beginTransaction();
        $this->createModel($request->header->account, $item, ItemMapper::class, 'item', $request->getOrigin());
        $this->app->dbPool->get()->con->commit();

        // Define default item containers
        /** @var \Modules\Attribute\Models\AttributeType[] $types */
        $types = ItemAttributeTypeMapper::getAll()
            ->where('name', ['default_sales_container', 'default_purchase_container'], 'IN')
            ->execute();

        $primaryContainer = \reset($item->container);
        if ($primaryContainer !== false) {
            foreach ($types as $type) {
                $internalResponse = clone $response;
                $internalRequest  = new HttpRequest();

                $internalRequest->header->account = $request->header->account;
                $internalRequest->setData('ref', $item->id);
                $internalRequest->setData('type', $type->id);
                $internalRequest->setData('value', $primaryContainer->id);

                $this->app->moduleManager->get('ItemManagement', 'ApiAttribute')->apiItemAttributeCreate($internalRequest, $internalResponse);
            }
        }

        if ($this->app->moduleManager->isActive('Billing')) {
            $billing = $this->app->moduleManager->get('Billing', 'ApiPrice');

            // Sales price
            $internalRequest  = new HttpRequest();
            $internalResponse = new HttpResponse();

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('name', 'default');
            $internalRequest->setData('type', PriceType::SALES);
            $internalRequest->setData('item', $item->id);
            $internalRequest->setData('price_new', $request->getDataString('salesprice') ?? 0);

            $billing->apiPriceCreate($internalRequest, $internalResponse);

            // Purchase price
            $internalRequest  = new HttpRequest();
            $internalResponse = new HttpResponse();

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('name', 'default');
            $internalRequest->setData('type', PriceType::PURCHASE);
            $internalRequest->setData('item', $item->id);
            $internalRequest->setData('supplier', $request->getDataInt('supplier'));
            $internalRequest->setData('price_new', $request->getDataString('purchaseprice') ?? 0);

            $billing->apiPriceCreate($internalRequest, $internalResponse);
        }

        $this->createMediaDirForItem($item->number, $request->header->account);
        $path = $this->createItemDir($item);

        $uploadedFiles = $request->files['item_profile_image'] ?? [];
        if (!empty($uploadedFiles)) {
            // upload image
            $uploaded = $this->app->moduleManager->get('Media', 'Api')->uploadFiles(
                names: [],
                fileNames: [],
                files: $uploadedFiles,
                account: $request->header->account,
                basePath: __DIR__ . '/../../../Modules/Media/Files' . $path,
                virtualPath: $path,
                pathSettings: PathSettings::FILE_PATH
            );

            // create type / media relation
            /** @var \Modules\Media\Models\MediaType $profileImageType */
            $profileImageType = MediaTypeMapper::get()
                ->where('name', 'item_profile_image')
                ->execute();

            $this->createModelRelation(
                $request->header->account,
                $uploaded[0]->id,
                $profileImageType->id,
                MediaMapper::class,
                'types',
                '',
                $request->getOrigin()
            );

            // create item relation
            $this->createModelRelation(
                $request->header->account,
                $item->id,
                $uploaded[0]->id,
                ItemMapper::class,
                'files',
                '',
                $request->getOrigin()
            );
        }

        $this->createItemSegmentation($request, $response, $item);

        $this->createStandardCreateResponse($request, $response, $item);
    }

    /**
     * Create item segmentation.
     *
     * Default: segment->section->sales_group->product_group and to the side product_type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param Item             $item     Item
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function createItemSegmentation(RequestAbstract $request, ResponseAbstract $response, Item $item) : void
    {
        /** @var \Model\Setting $settings */
        $settings = $this->app->appSettings->get(null, ItemSettingsEnum::DEFAULT_SEGMENTATION);

        /** @var array $segmentation */
        $segmentation = \json_decode($settings->content, true);
        if ($segmentation === false || $segmentation === null) {
            return;
        }

        /** @var \Modules\Attribute\Models\AttributeType[] $types */
        $types = ItemAttributeTypeMapper::getAll()
            ->where('name', \array_keys($segmentation), 'IN')
            ->execute();

        foreach ($types as $type) {
            $internalResponse = clone $response;
            $internalRequest  = new HttpRequest();

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('ref', $item->id);
            $internalRequest->setData('type', $type->id);
            $internalRequest->setData('value_id', $segmentation[$type->name]);

            $this->app->moduleManager->get('ItemManagement', 'ApiAttribute')->apiItemAttributeCreate($internalRequest, $internalResponse);
        }
    }

    /**
     * Create directory for an account
     *
     * @param string $number    Item number
     * @param int    $createdBy Creator of the directory
     *
     * @return Collection
     *
     * @since 1.0.0
     */
    private function createMediaDirForItem(string $number, int $createdBy) : Collection
    {
        $collection       = new Collection();
        $collection->name = $number;
        $collection->setVirtualPath('/Modules/ItemManagement/Items');
        $collection->setPath('/Modules/Media/Files/Modules/ItemManagement/Items/' . $number);
        $collection->createdBy = new NullAccount($createdBy);

        CollectionMapper::create()->execute($collection);

        return $collection;
    }

    /**
     * Method to create item from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Item
     *
     * @since 1.0.0
     */
    private function createItemFromRequest(RequestAbstract $request) : Item
    {
        $item                  = new Item();
        $item->number          = $request->getDataString('number') ?? '';
        $item->stockIdentifier = $request->getDataInt('stockidentifier') ?? StockIdentifierType::NONE;
        $item->salesPrice      = new FloatInt($request->getDataString('salesprice') ?? 0);
        $item->purchasePrice   = new FloatInt($request->getDataString('purchaseprice') ?? 0);
        $item->info            = $request->getDataString('info') ?? '';
        $item->parent          = $request->getDataInt('parent');
        $item->unit            = $request->getDataInt('unit') ?? $this->app->unitId;
        $item->status          = ItemStatus::tryFromValue($request->getDataInt('status')) ?? ItemStatus::ACTIVE;

        $container           = new Container();
        $container->name     = 'default';
        $container->quantity = FloatInt::DIVISOR;

        $item->container[] = $container;

        return $item;
    }

    /**
     * Validate item create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['number'] = !$request->hasData('number'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item attribute
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateItemL11nUpdate($request))) {
            $response->data['l11n_update'] = new FormValidation($val);
            $response->header->status      = RequestStatusCode::R_400;

            return;
        }

        $old = ItemL11nMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $new = $this->updateItemL11nFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, ItemL11nMapper::class, 'l11n', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to create item l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function updateItemL11nFromRequest(RequestAbstract $request, BaseStringL11n $l11n) : BaseStringL11n
    {
        $l11n->content = $request->getDataString('content') ?? '';

        return $l11n;
    }

    /**
     * Validate item attribute create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemL11nUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))
            || ($val['content'] = !$request->hasData('content'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item l11n type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nTypeCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateItemL11nTypeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $itemL11nType = $this->createItemL11nTypeFromRequest($request);
        $this->createModel($request->header->account, $itemL11nType, ItemL11nTypeMapper::class, 'item_l11n_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $itemL11nType);
    }

    /**
     * Api method to create item l11n type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nTypeUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
    }

    /**
     * Method to create item l11n type from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11nType
     *
     * @since 1.0.0
     */
    private function createItemL11nTypeFromRequest(RequestAbstract $request) : BaseStringL11nType
    {
        $itemL11nType             = new BaseStringL11nType();
        $itemL11nType->title      = $request->getDataString('title') ?? '';
        $itemL11nType->isRequired = $request->getDataBool('is_required') ?? false;

        return $itemL11nType;
    }

    /**
     * Validate item l11n type create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemL11nTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item l11n type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemRelationTypeCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateItemRelationTypeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $itemRelationType = $this->createItemRelationTypeFromRequest($request);
        $this->createModel($request->header->account, $itemRelationType, ItemRelationTypeMapper::class, 'item_relation_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $itemRelationType);
    }

    /**
     * Method to create item l11n type from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemRelationType
     *
     * @since 1.0.0
     */
    private function createItemRelationTypeFromRequest(RequestAbstract $request) : ItemRelationType
    {
        $itemRelationType        = new ItemRelationType();
        $itemRelationType->title = $request->getDataString('title') ?? '';

        return $itemRelationType;
    }

    /**
     * Validate item l11n type create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemRelationTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateItemL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $itemL11n = $this->createItemL11nFromRequest($request);
        $this->createModel($request->header->account, $itemL11n, ItemL11nMapper::class, 'item_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $itemL11n);
    }

    /**
     * Method to create item l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createItemL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $itemL11n           = new BaseStringL11n();
        $itemL11n->ref      = $request->getDataInt('item') ?? 0;
        $itemL11n->type     = new NullBaseStringL11nType($request->getDataInt('type') ?? 0);
        $itemL11n->language = ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? $request->header->l11n->language;
        $itemL11n->content  = $request->getDataString('content') ?? '';

        return $itemL11n;
    }

    /**
     * Validate item l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['item'] = !$request->hasData('item'))
            || ($val['type'] = !$request->hasData('type'))
            || ($val['content'] = !$request->hasData('content'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item files
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiFileCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateFileCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $uploadedFiles = $request->files;

        if (empty($uploadedFiles)) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $uploadedFiles);

            return;
        }

        /** @var \Modules\ItemManagement\Models\Item $item */
        $item = ItemMapper::get()
            ->where('id', (int) $request->getData('item'))
            ->execute();

        $path = $this->createItemDir($item);

        $uploaded = $this->app->moduleManager->get('Media', 'Api')->uploadFiles(
            names: $request->getDataList('names'),
            fileNames: $request->getDataList('filenames'),
            files: $uploadedFiles,
            account: $request->header->account,
            basePath: __DIR__ . '/../../../Modules/Media/Files' . $path,
            virtualPath: $path,
            pathSettings: PathSettings::FILE_PATH
        );

        if ($request->hasData('type')) {
            foreach ($uploaded as $file) {
                $this->createModelRelation(
                    $request->header->account,
                    $file->id,
                    $request->getDataInt('type'),
                    MediaMapper::class,
                    'types',
                    '',
                    $request->getOrigin()
                );
            }
        }

        if (empty($uploaded)) {
            $this->createInvalidAddResponse($request, $response, []);

            return;
        }

        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('item'),
            \reset($uploaded)->id,
            ItemMapper::class, 'files', '', $request->getOrigin()
        );

        $this->createStandardUpdateResponse($request, $response, $uploaded);
    }

    /**
     * Validate item note create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateFileCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['item'] = !$request->hasData('item'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item files
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiNoteCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateNoteCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $request->setData('virtualpath', '/Modules/ItemManagement/Items/' . $request->getData('id'), true);
        $this->app->moduleManager->get('Editor', 'Api')->apiEditorCreate($request, $response, $data);

        if ($response->header->status !== RequestStatusCode::R_200) {
            return;
        }

        $responseData = $response->getDataArray($request->uri->__toString());
        if (!\is_array($responseData)) {
            return;
        }

        $model = $responseData['response'];
        $this->createModelRelation($request->header->account, (int) $request->getData('id'), $model->id, ItemMapper::class, 'notes', '', $request->getOrigin());
    }

    /**
     * Validate item note create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateNoteCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update Note
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiNoteUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $accountId = $request->header->account;
        if (!$this->app->accountManager->get($accountId)->hasPermission(
            PermissionType::MODIFY, $this->app->unitId, $this->app->appId, self::NAME, PermissionCategory::NOTE, $request->getDataInt('id'))
        ) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        $this->app->moduleManager->get('Editor', 'Api')->apiEditorUpdate($request, $response, $data);
    }

    /**
     * Api method to delete Note
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiNoteDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $accountId = $request->header->account;
        if (!$this->app->accountManager->get($accountId)->hasPermission(
            PermissionType::DELETE, $this->app->unitId, $this->app->appId, self::NAME, PermissionCategory::NOTE, $request->getDataInt('id'))
        ) {
            $this->fillJsonResponse($request, $response, NotificationLevel::HIDDEN, '', '', []);
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        $this->app->moduleManager->get('Editor', 'Api')->apiEditorDelete($request, $response, $data);
    }

    /**
     * Api method to create item material type
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemMaterialTypeCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateMaterialTypeCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $materialType = $this->createMaterialTypeFromRequest($request);
        $this->createModel($request->header->account, $materialType, MaterialTypeMapper::class, 'material_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $materialType);
    }

    /**
     * Validate material create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateMaterialTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['name'] = !$request->hasData('name'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create material from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11nType
     *
     * @since 1.0.0
     */
    private function createMaterialTypeFromRequest(RequestAbstract $request) : BaseStringL11nType
    {
        $materialType = new BaseStringL11nType($request->getDataString('name') ?? '');
        $materialType->setL11n(
            $request->getDataString('title') ?? '',
            ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? ISO639x1Enum::_EN
        );

        return $materialType;
    }

    /**
     * Api method to create item material l11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemMaterialTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateMaterialTypeL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $materialL11n = $this->createMaterialTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $materialL11n, MaterialTypeL11nMapper::class, 'material_type_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $materialL11n);
    }

    /**
     * Validate material l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateMaterialTypeL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['type'] = !$request->hasData('type'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create material l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createMaterialTypeL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $materialL11n           = new BaseStringL11n();
        $materialL11n->ref      = $request->getDataInt('type') ?? 0;
        $materialL11n->language = ISO639x1Enum::tryFromValue($request->getDataString('language')) ?? $request->header->l11n->language;
        $materialL11n->content  = $request->getDataString('title') ?? '';

        return $materialL11n;
    }
}
