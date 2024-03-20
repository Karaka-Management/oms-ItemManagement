<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\tests\Controller\Api;

use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;

trait ApiControllerAttributeTrait
{
    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeTypeCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('name', 'test_attribute');
        $request->setData('title', 'EN:1');
        $request->setData('language', ISO639x1Enum::_EN);

        $this->attrModule->apiItemAttributeTypeCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeTypeL11nCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('title', 'DE:2');
        $request->setData('type', '1');
        $request->setData('language', ISO639x1Enum::_DE);

        $this->attrModule->apiItemAttributeTypeL11nCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeValueIntCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('default', '1');
        $request->setData('type', '1');
        $request->setData('value', '1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->attrModule->apiItemAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeValueStrCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('type', '1');
        $request->setData('value', '1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->attrModule->apiItemAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeValueFloatCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('type', '1');
        $request->setData('value', '1.1');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->attrModule->apiItemAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeValueDatCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('type', '1');
        $request->setData('value', '2020-08-02');
        $request->setData('language', ISO639x1Enum::_DE);
        $request->setData('country', ISO3166TwoEnum::_DEU);

        $this->attrModule->apiItemAttributeValueCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('ref', '1');
        $request->setData('value', '1');
        $request->setData('type', '1');

        $this->attrModule->apiItemAttributeCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeValueCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->attrModule->apiItemAttributeValueCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeTypeCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->attrModule->apiItemAttributeTypeCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeTypeL11nCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->attrModule->apiItemAttributeTypeL11nCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers \Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemAttributeCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->attrModule->apiItemAttributeCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
