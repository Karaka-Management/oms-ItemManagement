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

use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Uri\HttpUri;

trait ApiControllerL11nTrait
{
    /**
     * @covers Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemL11nTypeCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('title', 'TestItemL11nType');
        $request->setData('name', 'test_name');
        $request->setData('language', ISO639x1Enum::_EN);

        $this->module->apiItemL11nTypeCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->id);
    }

    /**
     * @covers Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemL11nCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('item', '1');
        $request->setData('type', '1');
        $request->setData('description', 'Description');

        $this->module->apiItemL11nCreate($request, $response);
        self::assertGreaterThan(0, $response->get('')['response']->id);
    }

    /**
     * @covers Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemL11nTypeCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiItemL11nTypeCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\ItemManagement\Controller\ApiController
     * @group module
     */
    public function testApiItemL11nCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiItemL11nCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
