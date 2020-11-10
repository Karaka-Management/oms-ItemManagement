<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Controller;

use Modules\ItemManagement\Models\AttributeValueType;
use Modules\ItemManagement\Models\ItemMapper;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use Modules\ItemManagement\Models\Item;
use Modules\ItemManagement\Models\ItemL11n;
use Modules\ItemManagement\Models\ItemAttribute;
use Modules\ItemManagement\Models\ItemAttributeTypeL11n;
use Modules\ItemManagement\Models\ItemAttributeValue;
use Modules\ItemManagement\Models\ItemL11nType;
use Modules\ItemManagement\Models\ItemAttributeType;

/**
 * ItemManagement class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 1.0
 * @link    https://orange-management.org
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
    public function apiItemCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemCreate($request))) {
            $response->set('item_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $item = $this->createItemFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $item, ItemMapper::class, 'item', $request->getOrigin());
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
        $item = new Item();
        $item->setNumber($request->getData('number') ?? '');

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
    public function apiItemAttributeCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeCreate($request))) {
            $response->set('attribute_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $attribute = $this->createItemAttributeFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $attribute, ItemAttributeMapper::class, 'attribute', $request->getOrigin());
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
        $attribute = new ItemAttribute();
        $attribute->setItem((int) $request->getData('item'));
        $attribute->setType((int) $request->getData('type'));
        $attribute->setValue((int) $request->getData('value'));

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
            || ($val['value'] = empty($request->getData('value')))
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
    public function apiItemAttributeTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeTypeL11nCreate($request))) {
            $response->set('attr_type_l11n_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $attrL11n = $this->createItemAttributeTypeL11nFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $attrL11n, ItemAttributeTypeL11nMapper::class, 'attr_type_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type localization', 'Attribute type localization successfully created', $attrL11n);
    }

    /**
     * Method to create item attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return ItemAttributeTypeL11n
     *
     * @since 1.0.0
     */
    private function createItemAttributeTypeL11nFromRequest(RequestAbstract $request) : ItemAttributeTypeL11n
    {
        $attrL11n = new ItemAttributeTypeL11n();
        $attrL11n->setType((int) ($request->getData('type') ?? 0));
        $attrL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $attrL11n->setTitle((string) ($request->getData('title') ?? ''));

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
    public function apiItemAttributeTypeCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeTypeCreate($request))) {
            $response->set('attr_type_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $attrType = $this->createItemAttributeTypeFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $attrType, ItemAttributeTypeMapper::class, 'attr_type', $request->getOrigin());

        $l11nRequest = new HttpRequest($request->getUri());
        $l11nRequest->setData('type', $attrType->getId());
        $l11nRequest->setData('title', $request->getData('title'));
        $l11nRequest->setData('language', $request->getData('language'));

        $l11nAttributeType = $this->createItemAttributeTypeL11nFromRequest($l11nRequest);
        $this->createModel($request->getHeader()->getAccount(), $l11nAttributeType, TagL11nMapper::class, 'attr_type_l11n_create', $request->getOrigin());

        $attrType->setL11n($l11nAttributeType);

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
        $attrType = new ItemAttributeType();
        $attrType->setName((string) ($request->getData('name') ?? ''));
        $attrType->setFields((int) ($request->getData('fields') ?? 0));
        $attrType->setCustom((bool) ($request->getData('custom') ?? false));

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
        if (($val['name'] = empty($request->getData('name')))
            || ($val['l11n'] = empty($request->getData('l11n')))
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
    public function apiItemAttributeValueCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemAttributeValueCreate($request))) {
            $response->set('attr_value_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $attrValue = $this->createItemAttributeValueFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $attrValue, ItemAttributeValueMapper::class, 'attr_value', $request->getOrigin());

        if ($attrValue->isDefault()) {
            $this->createModelRelation(
                $request->getHeader()->getAccount(),
                (int) $request->getData('attributetype'),
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
        $attrValue = new ItemAttributeValue();

        $type = $request->getData('type') ?? 0;
        if ($type === AttributeValueType::_INT) {
            $attrValue->setValueInt((int) $request->getData('value'));
        } elseif ($type === AttributeValueType::_STRING) {
            $attrValue->setValueString((string) $request->getData('value'));
        } elseif ($type === AttributeValueType::_FLOAT) {
            $attrValue->setValueDecimal((float) $request->getData('value'));
        } elseif ($type === AttributeValueType::_DATETIME) {
            $attrValue->setValueDat(new \DateTime($request->getData('value') ?? ''));
        }

        $attrValue->setDefault((bool) ($request->getData('default') ?? false));

        if ($request->hasData('language')) {
            $attrValue->setLanguage((string) ($request->getData('language') ?? $request->getLanguage()));
        }

        if ($request->hasData('country')) {
            $attrValue->setCountry((string) ($request->getData('country') ?? $request->getHeader()->getL11n()->getCountry()));
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
    public function apiItemL11nTypeCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemL11nTypeCreate($request))) {
            $response->set('item_l11n_type_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $itemL11nType = $this->createItemL11nTypeFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $itemL11nType, ItemL11nTypeMapper::class, 'item_l11n_type', $request->getOrigin());
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
        $itemL11nType = new ItemL11nType();
        $itemL11nType->setTitle((string) ($request->getData('title') ?? ''));

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
    public function apiItemL11nCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateItemL11nCreate($request))) {
            $response->set('item_l11n_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $itemL11n = $this->createItemL11nFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $itemL11n, ItemL11nMapper::class, 'item_l11n', $request->getOrigin());
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
        $itemL11n = new ItemL11n();
        $itemL11n->setItem((int) ($request->getData('item') ?? 0));
        $itemL11n->setType((int) ($request->getData('type') ?? 0));
        $itemL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $itemL11n->setDescription((string) ($request->getData('description') ?? ''));

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
    public function apiFileCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $uploadedFiles = $request->getFiles() ?? [];

        if (empty($uploadedFiles)) {
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Item', 'Invalid item image', $uploadedFiles);
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
            $request->getData('name') ?? '',
            $uploadedFiles,
            $request->getHeader()->getAccount(),
            'Modules/Media/Files/Modules/ItemManagement/' . ($request->getData('item') ?? '0'),
            '/Modules/ItemManagement/' . ($request->getData('item') ?? '0'),
            $request->getData('type') ?? '',
            '',
            '',
            PathSettings::FILE_PATH
        );

        $this->createModelRelation(
            $request->getHeader()->getAccount(),
            (int) $request->getData('item'),
            \reset($uploaded)->getId(),
            ItemMapper::class, 'files', '', $request->getOrigin()
        );

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Image', 'Image successfully updated', $uploaded);
    }
}