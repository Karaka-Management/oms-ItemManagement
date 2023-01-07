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

use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemAttribute;
use Modules\ItemManagement\Models\ItemAttributeMapper;
use Modules\ItemManagement\Models\ItemAttributeType;
use phpOMS\Localization\BaseStringL11n;
use Modules\ItemManagement\Models\ItemAttributeTypeL11nMapper;
use Modules\ItemManagement\Models\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\ItemAttributeValue;
use Modules\ItemManagement\Models\ItemAttributeValueL11nMapper;
use Modules\ItemManagement\Models\ItemAttributeValueMapper;
use Modules\ItemManagement\Models\ItemL11n;
use Modules\ItemManagement\Models\ItemL11nMapper;
use Modules\ItemManagement\Models\ItemL11nType;
use Modules\ItemManagement\Models\ItemL11nTypeMapper;
use Modules\ItemManagement\Models\ItemRelationType;
use Modules\ItemManagement\Models\ItemRelationTypeMapper;
use Modules\ItemManagement\Models\ItemMapper;
use Modules\ItemManagement\Models\ItemPrice;
use Modules\ItemManagement\Models\ItemPriceStatus;
use Modules\ItemManagement\Models\NullItemAttributeType;
use Modules\ItemManagement\Models\NullItemAttributeValue;
use Modules\ItemManagement\Models\NullItemL11nType;
use Modules\Media\Models\PathSettings;
use phpOMS\Localization\ISO4217CharEnum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Localization\Money;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;

/**
 * ItemManagement class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
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
            $response->set('item_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $item = $this->createItemFromRequest($request);

        $this->app->dbPool->get()->con->beginTransaction();
        $this->createModel($request->header->account, $item, ItemMapper::class, 'item', $request->getOrigin());
        $this->app->dbPool->get()->con->commit();

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Item', 'Item successfully created', $item);
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
        $item->number        = (string) ($request->getData('number') ?? '');
        $item->salesPrice    = new Money($request->getData('salesprice', 'int') ?? 0);
        $item->purchasePrice = new Money($request->getData('purchaseprice', 'int') ?? 0);
        $item->info          = (string) ($request->getData('info') ?? '');
        $item->parent        = ($request->getData('parent') !== null) ? (int) $request->getData('parent') : null;
        $item->unit        = ($request->getData('unit') !== null) ? (int) $request->getData('unit') : null;

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
        if (($val['number'] = empty($request->getData('number')))) {
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
            $response->set('item_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $item = $this->createItemPriceFromRequest($request);
        $this->createModel($request->header->account, $item, ItemMapper::class, 'item', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Item', 'Item successfully created', $item);
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
        $item->currency             = (string) ($request->getData('currency') ?? '');
        $item->price                = new Money($request->getData('price', 'int') ?? 0);
        $item->minQuantity          = (int) ($request->getData('minquantity') ?? 0);
        $item->relativeDiscount     = (int) ($request->getData('relativediscount') ?? 0);
        $item->absoluteDiscount     = (int) ($request->getData('absolutediscount') ?? 0);
        $item->relativeUnitDiscount = (int) ($request->getData('relativeunitdiscount') ?? 0);
        $item->absoluteUnitDiscount = (int) ($request->getData('absoluteunitdiscount') ?? 0);
        $item->promocode            = (string) ($request->getData('promocode') ?? '');

        $item->setStatus((int) ($request->getData('status') ?? ItemPriceStatus::ACTIVE));

        $item->start = ($request->getData('start') === null)
            ? null
            : new \DateTime($request->getData('start'));

        $item->end = ($request->getData('end') === null)
            ? null
            : new \DateTime($request->getData('end'));

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
        if (($val['price'] = empty($request->getData('price')))
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
            $response->set('attribute_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attribute = $this->createItemAttributeFromRequest($request);
        $this->createModel($request->header->account, $attribute, ItemAttributeMapper::class, 'attribute', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute', 'Attribute successfully created', $attribute);
    }

    /**
     * Method to create item attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemAttribute
     *
     * @since 1.0.0
     */
    private function createItemAttributeFromRequest(RequestAbstract $request) : ItemAttribute
    {
        $attribute       = new ItemAttribute();
        $attribute->item = (int) $request->getData('item');
        $attribute->type = new NullItemAttributeType((int) $request->getData('type'));

        if ($request->getData('value') !== null) {
            $attribute->value = new NullItemAttributeValue((int) $request->getData('value'));
        } else {
            $newRequest = clone $request;
            $newRequest->setData('value', $request->getData('custom'), true);

            $value = $this->createItemAttributeValueFromRequest($request);

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
        if (($val['type'] = empty($request->getData('type')))
            || ($val['value'] = (empty($request->getData('value')) && empty($request->getData('custom'))))
            || ($val['item'] = empty($request->getData('item')))
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
            $response->set('attr_type_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createItemAttributeTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, ItemAttributeTypeL11nMapper::class, 'attr_type_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type localization', 'Attribute type localization successfully created', $attrL11n);
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
        $attrL11n       = new BaseStringL11n();
        $attrL11n->ref = (int) ($request->getData('type') ?? 0);
        $attrL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $attrL11n->content = (string) ($request->getData('title') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))
            || ($val['type'] = empty($request->getData('type')))
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
            $response->set('attr_type_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrType = $this->createItemAttributeTypeFromRequest($request);
        $this->createModel($request->header->account, $attrType, ItemAttributeTypeMapper::class, 'attr_type', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type', 'Attribute type successfully created', $attrType);
    }

    /**
     * Method to create item attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemAttributeType
     *
     * @since 1.0.0
     */
    private function createItemAttributeTypeFromRequest(RequestAbstract $request) : ItemAttributeType
    {
        $attrType = new ItemAttributeType($request->getData('name') ?? '');
        $attrType->setL11n((string) ($request->getData('title') ?? ''), $request->getData('language') ?? ISO639x1Enum::_EN);
        $attrType->datatype            = (int) ($request->getData('datatype') ?? 0);
        $attrType->setFields((int) ($request->getData('fields') ?? 0));
        $attrType->custom            = (bool) ($request->getData('custom') ?? false);
        $attrType->isRequired        = (bool) ($request->getData('is_required') ?? false);
        $attrType->validationPattern = (string) ($request->getData('validation_pattern') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))
            || ($val['name'] = empty($request->getData('name')))
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
            $response->set('attr_value_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrValue = $this->createItemAttributeValueFromRequest($request);
        $this->createModel($request->header->account, $attrValue, ItemAttributeValueMapper::class, 'attr_value', $request->getOrigin());

        if ($attrValue->isDefault) {
            $this->createModelRelation(
                $request->header->account,
                (int) $request->getData('type'),
                $attrValue->getId(),
                ItemAttributeTypeMapper::class, 'defaults', '', $request->getOrigin()
            );
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute value', 'Attribute value successfully created', $attrValue);
    }

    /**
     * Method to create item attribute value from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemAttributeValue
     *
     * @since 1.0.0
     */
    private function createItemAttributeValueFromRequest(RequestAbstract $request) : ItemAttributeValue
    {
        $type = ItemAttributeTypeMapper::get()
            ->where('id', (int) ($request->getData('type') ?? 0))
            ->execute();

        $attrValue            = new ItemAttributeValue();
        $attrValue->isDefault = (bool) ($request->getData('default') ?? false);
        $attrValue->setValue($request->getData('value'), $type->datatype);

        if ($request->getData('title') !== null) {
            $attrValue->setL11n($request->getData('title'), $request->getData('language') ?? ISO639x1Enum::_EN);
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
        if (($val['type'] = empty($request->getData('type')))
            || ($val['value'] = empty($request->getData('value')))
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
            $response->set('attr_value_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createItemAttributeValueL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, ItemAttributeValueL11nMapper::class, 'attr_value_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type localization', 'Attribute type localization successfully created', $attrL11n);
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
    private function createItemAttributeValueL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n        = new BaseStringL11n();
        $attrL11n->ref = (int) ($request->getData('value') ?? 0);
        $attrL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $attrL11n->content = (string) ($request->getData('title') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))
            || ($val['value'] = empty($request->getData('value')))
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
            $response->set('item_l11n_type_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $itemL11nType = $this->createItemL11nTypeFromRequest($request);
        $this->createModel($request->header->account, $itemL11nType, ItemL11nTypeMapper::class, 'item_l11n_type', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Item localization type', 'Item localization type successfully created', $itemL11nType);
    }

    /**
     * Method to create item l11n type from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemL11nType
     *
     * @since 1.0.0
     */
    private function createItemL11nTypeFromRequest(RequestAbstract $request) : ItemL11nType
    {
        $itemL11nType             = new ItemL11nType();
        $itemL11nType->title      = (string) ($request->getData('title') ?? '');
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
        if (($val['title'] = empty($request->getData('title')))) {
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
            $response->set('item_relation_type_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $itemRelationType = $this->createItemRelationTypeFromRequest($request);
        $this->createModel($request->header->account, $itemRelationType, ItemRelationTypeMapper::class, 'item_relation_type', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Item relation type', 'Item relation type successfully created', $itemRelationType);
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
        $itemRelationType->title = (string) ($request->getData('title') ?? '');

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
        if (($val['title'] = empty($request->getData('title')))) {
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
            $response->set('item_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $itemL11n = $this->createItemL11nFromRequest($request);
        $this->createModel($request->header->account, $itemL11n, ItemL11nMapper::class, 'item_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Item localization', 'Item localization successfully created', $itemL11n);
    }

    /**
     * Method to create item l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemL11n
     *
     * @since 1.0.0
     */
    private function createItemL11nFromRequest(RequestAbstract $request) : ItemL11n
    {
        $itemL11n       = new ItemL11n();
        $itemL11n->item = (int) ($request->getData('item') ?? 0);
        $itemL11n->type = new NullItemL11nType((int) ($request->getData('type') ?? 0));
        $itemL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $itemL11n->description = (string) ($request->getData('description') ?? '');

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
        if (($val['item'] = empty($request->getData('item')))
            || ($val['type'] = empty($request->getData('type')))
            || ($val['description'] = empty($request->getData('description')))
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
            $response->set('item_file_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $uploadedFiles = $request->getFiles();

        if (empty($uploadedFiles)) {
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Item', 'Invalid item image', $uploadedFiles);
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
            $request->getDataList('names'),
            $request->getDataList('filenames'),
            $uploadedFiles,
            $request->header->account,
            __DIR__ . '/../../../Modules/Media/Files/Modules/ItemManagement/Articles/' . ($request->getData('item') ?? '0'),
            '/Modules/ItemManagement/Articles/' . ($request->getData('item') ?? '0'),
            $request->getData('type', 'int'),
            pathSettings: PathSettings::FILE_PATH
        );

        $this->createModelRelation(
            $request->header->account,
            (int) $request->getData('item'),
            \reset($uploaded)->getId(),
            ItemMapper::class, 'files', '', $request->getOrigin()
        );

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Image', 'Image successfully updated', $uploaded);
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
        if (($val['item'] = empty($request->getData('item')))
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
            $response->set('item_note_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $request->setData('virtualpath', '/Modules/ItemManagement/Articles/' . $request->getData('id'), true);
        $this->app->moduleManager->get('Editor')->apiEditorCreate($request, $response, $data);

        if ($response->header->status !== RequestStatusCode::R_200) {
            return;
        }

        $responseData = $response->get($request->uri->__toString());
        if (!\is_array($responseData)) {
            return;
        }

        $model = $responseData['response'];
        $this->createModelRelation($request->header->account, (int) $request->getData('id'), $model->getId(), ItemMapper::class, 'notes', '', $request->getOrigin());
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
        if (($val['id'] = empty($request->getData('id')))
        ) {
            return $val;
        }

        return [];
    }
}
