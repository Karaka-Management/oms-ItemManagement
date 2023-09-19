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

use Modules\Admin\Models\NullAccount;
use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemL11nMapper;
use Modules\ItemManagement\Models\ItemL11nTypeMapper;
use Modules\ItemManagement\Models\ItemMapper;
use Modules\ItemManagement\Models\ItemPrice;
use Modules\ItemManagement\Models\ItemPriceStatus;
use Modules\ItemManagement\Models\ItemRelationType;
use Modules\ItemManagement\Models\ItemRelationTypeMapper;
use Modules\ItemManagement\Models\ItemStatus;
use Modules\Media\Models\Collection;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\MediaTypeMapper;
use Modules\Media\Models\PathSettings;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\BaseStringL11nType;
use phpOMS\Localization\ISO4217CharEnum;
use phpOMS\Localization\NullBaseStringL11nType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Stdlib\Base\FloatInt;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;

/**
 * ItemManagement class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Api method to find items
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var BaseStringL11n[] $l11n */
        $l11n = ItemL11nMapper::getAll()
            ->with('type')
            ->where('type/title', ['name1', 'name2', 'name3'], 'IN')
            ->where('language', $request->header->l11n->language)
            ->where('description', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->execute();

        $items = [];
        foreach ($l11n as $item) {
            $items[] = $item->ref;
        }

        /** @var \Modules\ItemManagement\Models\Item[] $itemList */
        $itemList = ItemMapper::getAll()
            ->with('l11n')
            ->with('l11n/type')
            ->where('id', $items, 'IN')
            ->where('l11n/type/title', ['name1', 'name2', 'name3'], 'IN')
            ->where('l11n/language', $request->header->l11n->language)
            ->execute();

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values($itemList)
        );

        /*
        @todo: BIG TODO.
        This is the query I want to be used internally:

        select itemmgmt_item.itemmgmt_item_no, itemmgmt_item_l11n.itemmgmt_item_l11n_description
        from itemmgmt_item
        left join itemmgmt_item_l11n on itemmgmt_item.itemmgmt_item_id = itemmgmt_item_l11n.itemmgmt_item_l11n_item
        left join itemmgmt_item_l11n_type on itemmgmt_item_l11n.itemmgmt_item_l11n_typeref = itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_id
        where
            itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_title IN ("name1", "name2", "name3")
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
        return '/Modules/ItemManagement/Item/'
            . $item->id;
    }

    /**
     * Api method to create item
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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

        if ($this->app->moduleManager->isActive('Billing')) {
            $billing = $this->app->moduleManager->get('Billing');

            $internalRequest  = new HttpRequest(new HttpUri(''));
            $internalResponse = new HttpResponse();

            $internalRequest->header->account = $request->header->account;
            $internalRequest->setData('name', 'base');
            $internalRequest->setData('item', $item->id);
            $internalRequest->setData('price', $request->getDataInt('salesprice') ?? 0);

            $billing->apiPriceCreate($internalRequest, $internalResponse);
        }

        $this->createMediaDirForItem($item->number, $request->header->account);

        $path = $this->createItemDir($item);

        $uploadedFiles = $request->files['item_profile_image'] ?? [];
        if (!empty($uploadedFiles)) {
            // upload image
            $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
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

        $this->createStandardCreateResponse($request, $response, $item);
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
        $item                = new Item();
        $item->number        = $request->getDataString('number') ?? '';
        $item->salesPrice    = new FloatInt($request->getDataInt('salesprice') ?? 0);
        $item->purchasePrice = new FloatInt($request->getDataInt('purchaseprice') ?? 0);
        $item->info          = $request->getDataString('info') ?? '';
        $item->parent        = $request->getDataInt('parent');
        $item->unit          = $request->getDataInt('unit');
        $item->setStatus($request->getDataInt('status') ?? ItemStatus::ACTIVE);

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
     * Api method to create item
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemPriceCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemPriceCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $item = $this->createItemPriceFromRequest($request);
        $this->createModel($request->header->account, $item, ItemMapper::class, 'item', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $item);
    }

    /**
     * Method to create item from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemPrice
     *
     * @since 1.0.0
     */
    private function createItemPriceFromRequest(RequestAbstract $request) : ItemPrice
    {
        $item                       = new ItemPrice();
        $item->currency             = $request->getDataString('currency') ?? '';
        $item->price                = new FloatInt($request->getDataInt('price') ?? 0);
        $item->minQuantity          = $request->getDataInt('minquantity') ?? 0;
        $item->relativeDiscount     = $request->getDataInt('relativediscount') ?? 0;
        $item->absoluteDiscount     = $request->getDataInt('absolutediscount') ?? 0;
        $item->relativeUnitDiscount = $request->getDataInt('relativeunitdiscount') ?? 0;
        $item->absoluteUnitDiscount = $request->getDataInt('absoluteunitdiscount') ?? 0;
        $item->promocode            = $request->getDataString('promocode') ?? '';
        $item->start                = $request->getDataDateTime('start');
        $item->end                  = $request->getDataDateTime('end');
        $item->setStatus($request->getDataInt('status') ?? ItemPriceStatus::ACTIVE);

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
    private function validateItemPriceCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['price'] = !$request->hasData('price'))
            || ($val['currency'] = !ISO4217CharEnum::isValidValue($request->getData('currency')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item attribute
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemRelationTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiItemL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
        $itemL11n       = new BaseStringL11n();
        $itemL11n->ref  = $request->getDataInt('item') ?? 0;
        $itemL11n->type = new NullBaseStringL11nType($request->getDataInt('type') ?? 0);
        $itemL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $itemL11n->content = $request->getDataString('content') ?? '';

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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiFileCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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

        $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiNoteCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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

        $responseData = $response->get($request->uri->__toString());
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
}
