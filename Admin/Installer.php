<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Admin;

use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;
use phpOMS\Uri\HttpUri;

/**
 * Installer class.
 *
 * @package Modules\ItemManagement\Admin
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($app, $info, $cfgHandler);

        /* Attributes */
        $fileContent = \file_get_contents(__DIR__ . '/Install/attributes.json');
        if ($fileContent === false) {
            return;
        }

        $attributes = \json_decode($fileContent, true);
        $attrTypes  = self::createItemAttributeTypes($app, $attributes);
        $attrValues = self::createItemAttributeValues($app, $attrTypes, $attributes);

        /* Localizations */
        $fileContent = \file_get_contents(__DIR__ . '/Install/localizations.json');
        if ($fileContent === false) {
            return;
        }

        $localizations = \json_decode($fileContent, true);
        $l11nTypes     = self::createItemL11nTypes($app, $localizations);

        /* Relations */
        $fileContent = \file_get_contents(__DIR__ . '/Install/relations.json');
        if ($fileContent === false) {
            return;
        }

        $relations = \json_decode($fileContent, true);
        $l11nTypes = self::createItemRelationTypes($app, $relations);
    }

    /**
     * Install default l11n types
     *
     * @param ApplicationAbstract $app   Application
     * @param array               $l11ns Attribute definition
     *
     * @return array<array>
     *
     * @since 1.0.0
     */
    private static function createItemL11nTypes(ApplicationAbstract $app, array $l11ns) : array
    {
        /** @var array<string, array> $l11nTypes */
        $l11nTypes = [];

        /** @var \Modules\ItemManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('ItemManagement');

        foreach ($l11ns as $l11n) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('title', $l11n['name']);
            $request->setData('is_required', $l11n['is_required'] ?? false);

            $module->apiItemL11nTypeCreate($request, $response);

            $responseData = $response->get('');
            if (!\is_array($responseData)) {
                continue;
            }

            $l11nTypes[] = !\is_array($responseData['response'])
                ? $responseData['response']->toArray()
                : $responseData['response'];
        }

        return $l11nTypes;
    }

    /**
     * Install default relation types
     *
     * @param ApplicationAbstract $app       Application
     * @param array               $relations Attribute definition
     *
     * @return array<array>
     *
     * @since 1.0.0
     */
    private static function createItemRelationTypes(ApplicationAbstract $app, array $rels) : array
    {
        /** @var array<string, array> $relations */
        $relations = [];

        /** @var \Modules\ItemManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('ItemManagement');

        foreach ($rels as $rel) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('title', $rel['name']);

            $module->apiItemRelationTypeCreate($request, $response);

            $responseData = $response->get('');
            if (!\is_array($responseData)) {
                continue;
            }

            $relations[] = !\is_array($responseData['response'])
                ? $responseData['response']->toArray()
                : $responseData['response'];
        }

        return $relations;
    }

    /**
     * Install default attribute types
     *
     * @param ApplicationAbstract                                                                                                                                                              $app        Application
     * @param array<array{name:string, l11n?:array<string, string>, is_required?:bool, is_custom_allowed?:bool, validation_pattern?:string, value_type?:string, values?:array<string, mixed>}> $attributes Attribute definition
     *
     * @return array<string, array>
     *
     * @since 1.0.0
     */
    private static function createItemAttributeTypes(ApplicationAbstract $app, array $attributes) : array
    {
        /** @var array<string, array> $itemAttrType */
        $itemAttrType = [];

        /** @var \Modules\ItemManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('ItemManagement');

        /** @var array $attribute */
        foreach ($attributes as $attribute) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('name', $attribute['name'] ?? '');
            $request->setData('title', \reset($attribute['l11n']));
            $request->setData('language', \array_keys($attribute['l11n'])[0] ?? 'en');
            $request->setData('is_required', $attribute['is_required'] ?? false);
            $request->setData('is_custom_allowed', $attribute['is_custom_allowed'] ?? false);
            $request->setData('validation_pattern', $attribute['validation_pattern'] ?? '');
            $request->setData('datatype', (int) $attribute['value_type']);

            $module->apiItemAttributeTypeCreate($request, $response);

            $responseData = $response->get('');
            if (!\is_array($responseData)) {
                continue;
            }

            $itemAttrType[$attribute['name']] = !\is_array($responseData['response'])
                ? $responseData['response']->toArray()
                : $responseData['response'];

            $isFirst = true;
            foreach ($attribute['l11n'] as $language => $l11n) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $request->header->account = 1;
                $request->setData('title', $l11n);
                $request->setData('language', $language);
                $request->setData('type', $itemAttrType[$attribute['name']]['id']);

                $module->apiItemAttributeTypeL11nCreate($request, $response);
            }
        }

        return $itemAttrType;
    }

    /**
     * Create default attribute values for types
     *
     * @param ApplicationAbstract                                                                                                                                                              $app          Application
     * @param array                                                                                                                                                                            $itemAttrType Attribute types
     * @param array<array{name:string, l11n?:array<string, string>, is_required?:bool, is_custom_allowed?:bool, validation_pattern?:string, value_type?:string, values?:array<string, mixed>}> $attributes   Attribute definition
     *
     * @return array<string, array>
     *
     * @since 1.0.0
     */
    private static function createItemAttributeValues(ApplicationAbstract $app, array $itemAttrType, array $attributes) : array
    {
        /** @var array<string, array> $itemAttrValue */
        $itemAttrValue = [];

        /** @var \Modules\ItemManagement\Controller\ApiController $module */
        $module = $app->moduleManager->getModuleInstance('ItemManagement');

        foreach ($attributes as $attribute) {
            $itemAttrValue[$attribute['name']] = [];

            /** @var array $value */
            foreach ($attribute['values'] as $value) {
                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $request->header->account = 1;
                $request->setData('value', $value['value'] ?? '');
                $request->setData('unit', $value['unit'] ?? '');
                $request->setData('default', isset($attribute['values']) && !empty($attribute['values']));
                $request->setData('attributetype', $itemAttrType[$attribute['name']]['id']);

                if (isset($value['l11n']) && !empty($value['l11n'])) {
                    $request->setData('title', \reset($value['l11n']));
                    $request->setData('language', \array_keys($value['l11n'])[0] ?? 'en');
                }

                $module->apiItemAttributeValueCreate($request, $response);

                $responseData = $response->get('');
                if (!\is_array($responseData)) {
                    continue;
                }

                $attrValue = !\is_array($responseData['response'])
                    ? $responseData['response']->toArray()
                    : $responseData['response'];

                $itemAttrValue[$attribute['name']][] = $attrValue;

                $isFirst = true;
                foreach (($value['l11n'] ?? []) as $language => $l11n) {
                    if ($isFirst) {
                        $isFirst = false;
                        continue;
                    }

                    $response = new HttpResponse();
                    $request  = new HttpRequest(new HttpUri(''));

                    $request->header->account = 1;
                    $request->setData('title', $l11n);
                    $request->setData('language', $language);
                    $request->setData('value', $attrValue['id']);

                    $module->apiItemAttributeValueL11nCreate($request, $response);
                }
            }
        }

        return $itemAttrValue;
    }
}
