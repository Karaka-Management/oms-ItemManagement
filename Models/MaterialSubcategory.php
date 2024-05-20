<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Default settings enum.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class MaterialSubcategory extends Enum
{
    // packaging
    public const PRIMARY = 0; // Verkaufsverpackung

    public const SECONDARY = 1;

    public const TERTIARY = 2; // Transportverpackung

    public const RETAIL = 3; // Umverpackung

    // product
    // RHB
    public const RAW_MATERIAL = 100;

    public const AUXILIARY_MATERIAL = 101;

    public const SUPPLIES = 102;
}
