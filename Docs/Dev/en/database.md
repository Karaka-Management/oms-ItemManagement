# Database

## itemmgmt_item

Items

## itemmgmt_item_l11n

Item localizations

## itemmgmt_item_l11n_type

Item localization types (e.g. nam1, name2, description, ...)

## itemmgmt_attr_type

Available attribute types (e.g. size, color, weight, etc.)

## itemmgmt_attr_type_l11n

Attribute localizations

## itemmgmt_attr_value

Attribute values belonging to the attribute types (already localized). The localization can be omitted for globally valid values. Detailled numeric specifications should not be stored in here (e.g. size, length, weight etc.) since there are too many variations.

## itemmgmt_item_attr

Attribute types which a item has / should have. This references the selected default value from `itemmgmt_attr_value` or a custom value from `itemmgmt_item_attr_value`

## itemmgmt_item_attr_value

Custom attribute values of an item, in case globally defined attributes make no sense (e.g. color code, weight, length, size, ... usually numeric). These values are already localized. The localization can be omitted for globally valid values.