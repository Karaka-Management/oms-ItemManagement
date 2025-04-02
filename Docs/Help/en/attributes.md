# Attributes

## Default

The module automatically installs the following default attributes which can be set in the attribute tab in the respective item.

### General

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| default_sales_container | Default sales container to be used | First container created for the item |
| default_purchase_container | Default purchase container to be used | First container created for the item |
| internal_item | Is the item only meant for internal purposes (e.g. cleaning utilities)? |  |
| bill_fees | Is special item on invoices (e.g. shipping cost, insurance fees, ...) | |
| upc | Universal product code | |
| hs_code | Harmonized system code (HS code) | |
| hazmat | Hazardous material type | |
| color | Item color | |
| length | Item length | |
| weight | Item weight | |
| height | Item height | |
| volume | Item volume | |
| release_date | Date of first release | |
| license | License type | |
| it_platform | Is it platform | |
| brand | Brand name | |
| model | Model description | |
| os | Operating system | |
| os_version | Operating system version | |
| dual_use | Is dual use item | |
| contract | Contract id related to this item | |
| subscription | Is item with subscription model | |
| subscription_interval_types | Interval types of the subscription (e.g. monthly) | |
| subscription_interval_value | Interval of the subscription | |
| subscription_renewal_type | How does the subscription renew | |
| subscription_interval_end | When does the subscription end? | |
| one_click_pay_cc | One click payment link for credit card payment | |
| one_click_pay_cc_id | One click id for credit card payment | |
| one_click_pay_paypal | One click payment link for paypal payment | |
| iso_keep_dry | Does the item need to be kept dry? | |
| iso_temparature_lower_limit | Lowest temperature for storage/transportation | |
| iso_temparature_upper_limit | Highest temperature for storage/transportation | |
| iso_shelf_life | What is the shelf life in days? | |
| country_of_origin | Country of origin | |
| country_of_assembly | Country of assembly | |
| country_of_last_processing | Country of last processing | |
| consumablefor | Is consumable for other item? Id of item required | |
| successorof | Is successor of other item? Id of item required | |
| variantof | Is variant of other item? Id of item required | |
| accessoryfor | Is accessory for other item? Id of item required | |
| sparepartfor | Is sparepart for other item? Id of item required | |
| isfamilyfriendly | Is familyfriendly item? | |
| item_condition | Condition of the item (e.g. used) | |
| gtin | GTIN | |
| eu_medical_device_class | EU medical device class | |
| fda_medical_regulatory_class | FDA medical regulatory class | |

### Categories

Items can be put in categories for horizontal and vertical grouping. By default the system uses segment->section->sales_group->product_group as categories as well as product_type. These categories also get used by other modules. Additional groups can be defined but are not used by other modules by default.

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| segment | Level 1 | 1 |
| section | Level 2 | 1 |
| sales_group | Level 3 | 1 |
| product_group | Level 4 | 1 |
| product_type | **NOT** hierarchically but to the side (e.g. machine, consumable, spare part, ...) | 1 |

The following table shows an example item segmentation for the hierarchically categories:

| Level | >                     | >                     | >                     | >                     | >                     | >                     | Sample                |
| :---: | :-------------------: | :-------------------: | :-------------------: | :-------------------: | :-------------------: | :-------------------: | :-------------------: |
| 1     | >                     | >                     | >                     | >                     | Segment 1             | >                     | Segment 2             |
| 2     | >                     | >                     | Section 1.1           | >                     | Section 1.2           | >                     | Section 2.1           |
| 3     | Sales Group 1.1.1     | >                     | Sales Group 1.1.2     | >                     | Sales Group 1.2.1     | Sales Group 2.1.1     | Sales Group 2.1.2     |
| 4     | Product Group 1.1.1.1 | Product Group 1.1.2.1 | Product Group 1.1.2.2 | Product Group 1.2.1.1 | Product Group 1.2.1.2 | Product Group 2.1.1.1 | Product Group 2.1.2.1 |

> You could consider the item (number) itself `Level 5`.

### Purchase & Stock

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| lead_time | Lead time in days for the procurement. Important for automatic order suggestions. | 3 days |
| admin_time | Internal administration time in seconds needed **per quantity** until a delivered item becomes ready for sales (e.g. quality control). Important for automatic order suggestions. | 10 seconds |
| maximum_stock_quantity | Maximum stock quantity to limit the automatic order suggestion. Only needed for certain algorithms. |  |
| minimum_stock_quantity | Minimum stock quantity that should always be in stock. Important for automatic order suggestions. Alternatively, see **minimum_stock_range**. |  |
| minimum_stock_range | Minimum stock range in days that should always be in stock. Important for automatic order suggestions. Alternatively, see **minimum_stock_quantity**. | 1 day |
| minimum_order_quantity | Minimum order quantity when ordering. Important for automatic order suggestions. |  |
| order_quantity_steps | Minimum quantity increments above the minimum_order_quantity. This allows item purchases in increments of 10, 100 etc. Important for automatic order suggestions. | 1 |
| order_suggestion_type | Coming soon |  |
| order_suggestion_optimization_type | Coming soon |  |
| order_suggestion_history_duration | Coming soon |  |
| order_suggestion_averaging_method | Coming soon |  |
| order_suggestion_comparison_duration_type | Coming soon |  |
| stock_evaluation_category | How should the item value for the stock evaluation be determined? | Average |

### Shop

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| shop_item | Is the item used in a shop? |  |
| shop_featured | Is the item a featured item? |  |
| shop_front | Should the item be shown on the front page of the shop? |  |
| shop_external_link | Link for the item to an external website (e.g. manufacturer website) |  |

### Accounting

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| sales_tax_code | Tax code for sales | |
| purchase_tax_code | Tax code for purchase | |
| costcenter | Cost center for accounting | |
| costobject | Cost object for accounting | |

