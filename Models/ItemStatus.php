<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Item status enum.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class ItemStatus extends Enum
{
    public const ACTIVE = 1;

    public const INACTIVE = 2;

    public const BANNED = 4;
}
