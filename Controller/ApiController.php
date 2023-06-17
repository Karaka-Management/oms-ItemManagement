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
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeType;
use Modules\Attribute\Models\AttributeValue;
use Modules\Attribute\Models\NullAttribute;
use Modules\Attribute\Models\NullAttributeType;
use Modules\Attribute\Models\NullAttributeValue;
use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemAttributeMapper;
use Modules\ItemManagement\Models\ItemAttributeTypeL11nMapper;
use Modules\ItemManagement\Models\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\ItemAttributeValueL11nMapper;
use Modules\ItemManagement\Models\ItemAttributeValueMapper;
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
use phpOMS\Localization\ISO639x1Enum;
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
            $response->data['item_create'] = new FormValidation($val);
            $response->header->status      = RequestStatusCode::R_400;

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
            $internalRequest->setData('name', 'base_price');
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
            $response->data['item_create'] = new FormValidation($val);
            $response->header->status      = RequestStatusCode::R_400;

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
    public function apiItemAttributeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeCreate($request))) {
            $response->data['attribute_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

            return;
        }

        $attribute = $this->createItemAttributeFromRequest($request);
        $this->createModel($request->header->account, $attribute, ItemAttributeMapper::class, 'attribute', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attribute);
    }

    /**
     * Method to create item attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Attribute
     *
     * @since 1.0.0
     */
    private function createItemAttributeFromRequest(RequestAbstract $request) : Attribute
    {
        $attribute       = new Attribute();
        $attribute->ref  = (int) $request->getData('item');
        $attribute->type = new NullAttributeType((int) $request->getData('type'));

        if ($request->hasData('value')) {
            $attribute->value = new NullAttributeValue((int) $request->getData('value'));
        } else {
            $newRequest = clone $request;
            $newRequest->setData('value', $request->getData('custom'), true);

            $value = $this->createAttributeValueFromRequest($newRequest);

            $attribute->value = $value;
        }

        return $attribute;
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
    private function validateItemAttributeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['type'] = !$request->hasData('type'))
            || ($val['value'] = (!$request->hasData('value') && !$request->hasData('custom')))
            || ($val['item'] = !$request->hasData('item'))
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
    public function apiItemAttributeUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeUpdate($request))) {
            $response->data['attribute_update'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

            return;
        }

        $old = ItemAttributeMapper::get()
            ->with('type')
            ->with('type/defaults')
            ->with('value')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $new = $this->updateItemAttributeFromRequest($request, $old->deepClone());

        if ($new->id === 0) {
            // Set response header to invalid request because of invalid data
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $new);

            return;
        }

        $this->updateModel($request->header->account, $old, $new, ItemAttributeMapper::class, 'attribute', $request->getOrigin());

        if ($new->value->getValue() !== $old->value->getValue()) {
            $this->updateModel($request->header->account, $old->value, $new->value, ItemAttributeValueMapper::class, 'attribute_value', $request->getOrigin());
        }

        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to create item attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Attribute
     *
     * @since 1.0.0
     */
    private function updateItemAttributeFromRequest(RequestAbstract $request, Attribute $attribute) : Attribute
    {
        if ($attribute->type->custom) {
            // @question: we are overwriting the old value, could there be a use case where we want to create a new value and keep the old one?
            $attribute->value->setValue($request->getData('value'), $attribute->type->datatype);
        } else {
            // @todo: fix by only accepting the value id to be used
            // this is a workaround for now because the front end doesn't allow to dynamically show default values.
            $value = $attribute->type->getDefaultByValue($request->getData('value'));

            // Couldn't find matching default value
            if ($value->id === 0) {
                return new NullAttribute();
            }

            $attribute->value = $value;
        }

        return $attribute;
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
    private function validateItemAttributeUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item attribute l11n
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
    public function apiItemAttributeTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeTypeL11nCreate($request))) {
            $response->data['attr_type_l11n_create'] = new FormValidation($val);
            $response->header->status                = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createItemAttributeTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, ItemAttributeTypeL11nMapper::class, 'attr_type_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrL11n);
    }

    /**
     * Method to create item attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createItemAttributeTypeL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n      = new BaseStringL11n();
        $attrL11n->ref = $request->getDataInt('type') ?? 0;
        $attrL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $attrL11n->content = $request->getDataString('title') ?? '';

        return $attrL11n;
    }

    /**
     * Validate item attribute l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemAttributeTypeL11nCreate(RequestAbstract $request) : array
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
     * Api method to create item attribute type
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
    public function apiItemAttributeTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeTypeCreate($request))) {
            $response->data['attr_type_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

            return;
        }

        $attrType = $this->createAttributeTypeFromRequest($request);
        $this->createModel($request->header->account, $attrType, ItemAttributeTypeMapper::class, 'attr_type', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrType);
    }

    /**
     * Method to create item attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AttributeType
     *
     * @since 1.0.0
     */
    private function createAttributeTypeFromRequest(RequestAbstract $request) : AttributeType
    {
        $attrType                    = new AttributeType($request->getDataString('name') ?? '');
        $attrType->datatype          = $request->getDataInt('datatype') ?? 0;
        $attrType->custom            = $request->getDataBool('custom') ?? false;
        $attrType->isRequired        = (bool) ($request->getData('is_required') ?? false);
        $attrType->validationPattern = $request->getDataString('validation_pattern') ?? '';
        $attrType->setL11n($request->getDataString('title') ?? '', $request->getDataString('language') ?? ISO639x1Enum::_EN);
        $attrType->setFields($request->getDataInt('fields') ?? 0);

        return $attrType;
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
    private function validateItemAttributeTypeCreate(RequestAbstract $request) : array
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
     * Api method to create item attribute value
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
    public function apiItemAttributeValueCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeValueCreate($request))) {
            $response->data['attr_value_create'] = new FormValidation($val);
            $response->header->status            = RequestStatusCode::R_400;

            return;
        }

        $attrValue = $this->createAttributeValueFromRequest($request);
        $this->createModel($request->header->account, $attrValue, ItemAttributeValueMapper::class, 'attr_value', $request->getOrigin());

        if ($attrValue->isDefault) {
            $this->createModelRelation(
                $request->header->account,
                (int) $request->getData('type'),
                $attrValue->id,
                ItemAttributeTypeMapper::class, 'defaults', '', $request->getOrigin()
            );
        }

        $this->createStandardCreateResponse($request, $response, $attrValue);
    }

    /**
     * Method to create item attribute value from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AttributeValue
     *
     * @since 1.0.0
     */
    private function createAttributeValueFromRequest(RequestAbstract $request) : AttributeValue
    {
        /** @var AttributeType $type */
        $type = ItemAttributeTypeMapper::get()
            ->where('id', $request->getDataInt('type') ?? 0)
            ->execute();

        $attrValue            = new AttributeValue();
        $attrValue->isDefault = $request->getDataBool('default') ?? false;
        $attrValue->setValue($request->getData('value'), $type->datatype);

        if ($request->hasData('title')) {
            $attrValue->setL11n($request->getDataString('title') ?? '', $request->getDataString('language') ?? ISO639x1Enum::_EN);
        }

        return $attrValue;
    }

    /**
     * Validate item attribute value create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemAttributeValueCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['type'] = !$request->hasData('type'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create item attribute l11n
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
    public function apiItemAttributeValueL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeValueL11nCreate($request))) {
            $response->data['attr_value_l11n_create'] = new FormValidation($val);
            $response->header->status                 = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createAttributeValueL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, ItemAttributeValueL11nMapper::class, 'attr_value_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrL11n);
    }

    /**
     * Method to create item attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createAttributeValueL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n      = new BaseStringL11n();
        $attrL11n->ref = $request->getDataInt('value') ?? 0;
        $attrL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $attrL11n->content = $request->getDataString('title') ?? '';

        return $attrL11n;
    }

    /**
     * Validate item attribute l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateItemAttributeValueL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to handle api item attributes
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
    public function apiItemAttribute(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeValueL11nCreate($request))) {
            $response->data['attr_value_l11n_create'] = new FormValidation($val);
            $response->header->status                 = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createAttributeValueL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, ItemAttributeValueL11nMapper::class, 'attr_value_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $attrL11n);
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
            $response->data['item_l11n_type_create'] = new FormValidation($val);
            $response->header->status                = RequestStatusCode::R_400;

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
        $itemL11nType->isRequired = (bool) ($request->getData('is_required') ?? false);

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
            $response->data['item_relation_type_create'] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

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
            $response->data['item_l11n_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

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
        $itemL11n->content = $request->getDataString('description') ?? '';

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
            || ($val['description'] = !$request->hasData('description'))
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
            $response->data['item_file_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

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
            $response->data['item_note_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

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
