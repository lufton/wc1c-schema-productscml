<?php namespace Wc1c\Main\Schemas\Productscml;

defined('ABSPATH') || exit;

use Wc1c\Main\Traits\CoreTrait;
use Wc1c\Main\Traits\SingletonTrait;
use Wc1c\Main\Traits\UtilityTrait;

/**
 * Admin
 *
 * @package Wc1c\Main\Schemas\Productscml
 */
class Admin
{
	use SingletonTrait;
	use UtilityTrait;
	use CoreTrait;

	/**
	 * @return void
	 */
	public function initConfigurationsFields()
	{
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsReceiver'], 10, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProducts'], 20, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsSync'], 30, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsSku'], 60, 1);
        add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsCategories'], 60, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsNames'], 60, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsDescriptions'], 60, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsImages'], 60, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsAttributes'], 60, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsPrices'], 70, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsInventories'], 71, 1);
        add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsTaxes'], 73, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsDimensions'], 74, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsOther'], 79, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsWithCharacteristics'], 80, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsCategories'], 80, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsAttributes'], 90, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsMediaLibrary'], 100, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsLogs'], 110, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsOther'], 120, 1);
	}

	/**
	 * Configurations fields: receiver
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsReceiver(array $fields): array
	{
		$fields['title_receiver'] =
		[
			'title' => __('Receiving requests from 1C', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Authorization of requests and regulation of algorithms for receiving requests for the Receiver from the 1C programs by CommerceML protocol.', 'wc1c-main'),
		];

		$lazy_sign = $this->core()->configuration()->getMeta('receiver_lazy_sign');

		if(empty($lazy_sign))
		{
			$lazy_sign = md5($this->core()->configuration()->getId() . time());
			$this->core()->configuration()->addMetaData('receiver_lazy_sign', $lazy_sign, true);
			$this->core()->configuration()->saveMetaData();
		}

		$url_raw = trim(get_site_url(null, '/?wc1c-receiver=' . $this->core()->configuration()->getId() . '&lazysign=' . $lazy_sign . '&get_param'));
		$url_raw = '<span class="d-block input-text mt-0 p-2 bg-light regular-input wc1c_urls">' . esc_url($url_raw) . '</span>';

		$fields['url_requests'] =
		[
			'title' => __('Website address', 'wc1c-main'),
			'type' => 'raw',
			'raw' => $url_raw,
			'description' => sprintf(
				'%s<hr>%s',
				__('Specified in the exchange settings on the 1C side. The Recipient is located at this address, which will receive requests from 1C.', 'wc1c-main'),
				__('When copying, you need to get rid of whitespace characters, if they are present.', 'wc1c-main')
			)
		];

		$fields['user_login'] =
		[
			'title' => __('Username', 'wc1c-main'),
			'type' => 'text',
			'description' => sprintf(
				'%s<hr>%s',
				__('Specified when setting up an exchange with a site on the 1C side. Any name can be specified, except for an empty value.', 'wc1c-main'),
				__('Work with data on the site is performed on behalf of the configuration owner, and not on behalf of the specified username.', 'wc1c-main')
			),
			'default' => '',
			'css' => 'min-width: 377px;',
		];

		$fields['user_password'] =
		[
			'title' => __('User password', 'wc1c-main'),
			'type' => 'password',
			'description' => __('Specified in pair with the username when setting up on the 1C side. It is advisable not to specify a password for the current WordPress user.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 377px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: other
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsOther(array $fields): array
	{
		$fields['title_other'] =
		[
			'title' => __('Other parameters', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Change of data processing behavior for environment compatibility and so on.', 'wc1c-main'),
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum size of accepted requests', 'wc1c-main'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s<br />%s <b>%s</b><hr>%s',
				__('Enter the maximum size of accepted requests from 1C at a time in bytes. May be specified with a dimension suffix, such as 7M, where M = megabyte, K = kilobyte, G - gigabyte.', 'wc1c-main'),
				__('Current WC1C limit:', 'wc1c-main'),
				wc1c()->settings()->get('php_post_max_size', wc1c()->environment()->get('php_post_max_size')),
				__('Can only decrease the value, because it must not exceed the limits from the WC1C settings.', 'wc1c-main')
			),
			'default' => wc1c()->settings()->get('php_post_max_size', wc1c()->environment()->get('php_post_max_size')),
			'css' => 'min-width: 100px;',
		];

		$fields['php_max_execution_time'] =
		[
			'title' => __('Maximum time for execution PHP', 'wc1c-main'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s <br /> %s <b>%s</b> <br /> %s',
				__('Value is seconds. Algorithms of current configuration will run until a time limit is end.', 'wc1c-main'),
				__('Current WC1C limit:', 'wc1c-main'),
				wc1c()->settings()->get('php_max_execution_time', wc1c()->environment()->get('php_max_execution_time')),
				__('If specify 0, the time limit will be disabled. Specifying 0 is not recommended, it is recommended not to exceed the WC1C limit.', 'wc1c-main')
			),
			'default' => wc1c()->settings()->get('php_max_execution_time', wc1c()->environment()->get('php_max_execution_time')),
			'css' => 'min-width: 100px;',
		];

		$fields['browser_debug'] =
		[
			'title' => __('Browser debug mode', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('The setting is required only for debugging activities and must be turned off when such activities are completed.', 'wc1c-main'),
				__('Only used in debug mode.', 'wc1c-main')
			),
			'default' => 'no'
		];

		$response_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'standard' => __('Standard', 'wc1c-main'),
		];

		$fields['directory_clean_mode'] =
		[
			'title' => __('Cleaning up a directory', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			('<b>%s</b> - %s<br /><b>%s</b> - %s<br /><hr>%s',
			 __('Do not use', 'wc1c-main'),
			 __('File deletion steps will be skipped.', 'wc1c-main'),
			 __('Standard', 'wc1c-main'),
			 __('The standard cleaning algorithm will be used. Suitable for most users.', 'wc1c-main'),
			 __('The choice of deletion mode must be taken very seriously.', 'wc1c-main')
			),
			'default' => 'standard',
			'css' => 'min-width:100%',
			'options' => $response_options
		];

		$fields['ob_end_clean'] =
		[
			'title' => __('Clearing the buffer before the receiver', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('When enabled, buffering will be disabled in the request receiver and the output buffer will be cleared.', 'wc1c-main'),
				__('Used for sites that have viruses.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$response_options =
		[
			'failure' => __('Failure', 'wc1c-main'),
			'success' => __('Success', 'wc1c-main'),
		];

		$fields['response_unknown_action'] =
		[
			'title' => __('Response on unknown action', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			('<b>%s</b> - %s<br /><b>%s</b> - %s<br /><hr>%s',
			 __('Failure', 'wc1c-main'),
			 __('Error information will be sent.', 'wc1c-main'),
			 __('Success', 'wc1c-main'),
			 __('Information about the successful processing of the request will be sent.', 'wc1c-main'),
			 __('You need to understand what this setting is for. The default is to always throw an error.', 'wc1c-main')
			),
			'default' => 'failure',
			'css' => 'min-width:100%',
			'options' => $response_options
		];

		return $fields;
	}

	/**
	 * Configuration fields: categories
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsCategories(array $fields): array
	{
		$fields['categories'] =
		[
			'title' => __('Categories', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Categorization of product positions on the WooCommerce side according to data from 1C.', 'wc1c-main'),
		];

		$merge_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'yes' => __('Name matching', 'wc1c-main'),
			'yes_parent' => __('Name matching, with the match of the parent category', 'wc1c-main'),
		];

		$fields['categories_merge'] =
		[
			'title' => __('Using existing categories', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			('%s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><hr>%s',
			 __('In the event that the categories were created manually or from another configuration, you must enable the merge. Merging will avoid duplication of categories.', 'wc1c-main'),
			 __('Name matching', 'wc1c-main'),
			 __('The categories will be linked when the names match without any other data matching.', 'wc1c-main'),
			 __('Name matching, with the match of the parent category', 'wc1c-main'),
			 __('The categories will be linked only if they have the same name and parent category.', 'wc1c-main'),
			 __('When using existing categories, the creation of found categories will be skipped and the update settings will be applied for them.', 'wc1c-main')
			),
			'default' => 'no',
			'css' => 'min-width:100%',
			'options' => $merge_options
		];

		$fields['categories_create'] =
		[
			'title' => __('Creating categories', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Categories are only created if they are recognized as new. New categories are those that are not related according to 1C data and are not in an identical hierarchy.', 'wc1c-main'),
				__('To create categories, you must also set up category sources. The current setting is just a global flag to allow creation.', 'wc1c-main')
			),
			'default' => 'no'
		];

        $fields['categories_classifier_groups_create'] =
        [
            'title' => __('Creating categories from classifier groups', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('Categories are only created if they have not been created before. Also, if access to work with categories is allowed from the global settings.', 'wc1c-main'),
            'default' => 'no'
        ];

        $fields['categories_classifier_groups_create_assign_parent'] =
        [
            'title' => __('Assign parent categories on creating', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('If there is a parent category in 1C, it will also be assigned in WooCommerce. The setting is triggered when a category is created.', 'wc1c-main'),
            'default' => 'yes'
        ];

        $fields['categories_classifier_groups_create_assign_description'] =
        [
            'title' => __('Assign categories description on creating', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('When creating categories, descriptions will be filled in if category descriptions are present in 1C.', 'wc1c-main'),
            'default' => 'no'
        ];

		$fields['categories_classifier_groups_create_assign_image'] =
		[
			'title' => __('Assign image on creating', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('If there is a image in 1C, it will also be assigned in WooCommerce. The setting is triggered when a category is created.', 'wc1c-main'),
			'default' => 'yes'
		];

		$fields['categories_update'] =
		[
			'title' => __('Updating categories', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('If the category created earlier was linked to 1C data, then when you change any category data in 1C, the data will also change in WooCommerce.', 'wc1c-main'),
				__('To update categories, you must also configure category sources. The current setting is just a global checkbox that allows updates.', 'wc1c-main')
			),
			'default' => 'no'
		];

        $fields['categories_classifier_groups_update'] =
        [
            'title' => __('Updating categories from classifier groups', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('If the category created earlier was linked to 1C data, then when you change any category data in 1C, the data will also change in WooCommerce.', 'wc1c-main'),
            'default' => 'no'
        ];

        $fields['categories_classifier_groups_update_parent'] =
        [
            'title' => __('Update parent categories on updating', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('When enabled, parent categories will be updated when they are updated in 1C. The setting is triggered when a category is updated.', 'wc1c-main'),
            'default' => 'yes'
        ];

        $fields['categories_classifier_groups_update_name'] =
        [
            'title' => __('Updating categories name', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('If the category was previously linked to 1C data, then when changing the name in 1C, the name will also change in WooCommerce.', 'wc1c-main'),
            'default' => 'no'
        ];

        $fields['categories_classifier_groups_update_description'] =
        [
            'title' => __('Updating categories description', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => __('If the category was previously linked to 1C data, then when you change the description in 1C, the description will also change in WooCommerce. 
            It should be borne in mind that descriptions in 1C are not always stored. Therefore, you should not enable this function if the descriptions were filled out on the site.', 'wc1c-main'),
            'default' => 'no'
        ];

		$fields['categories_classifier_groups_update_image'] =
		[
			'title' => __('Updating categories image', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('If the category was previously linked to 1C data, then when changing the image in 1C, the image will also change in WooCommerce.', 'wc1c-main'),
			'default' => 'no'
		];

		$fields['categories_update_only_configuration'] =
		[
			'title' => __('Consider configuration when updating categories', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('When updating category data, the update will only occur if the category was created through the current configuration.', 'wc1c-main'),
			'default' => 'no'
		];

		$fields['categories_update_only_schema'] =
		[
			'title' => __('Consider schema when updating categories', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('When updating category data, the update will only occur if the category was created through the current schema.', 'wc1c-main'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products with characteristics
	 *
	 * @param array $fields Прежний массив настроек
	 *
	 * @return array Новый массив настроек
	 */
	public function configurationsFieldsProductsWithCharacteristics(array $fields): array
	{
		$fields['title_products_with_characteristics'] =
		[
			'title' => __('Products (goods): with characteristics', 'wc1c-main'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s %s',
				__('The same product (product) can have various kinds of differences, such as color, size, etc.', 'wc1c-main'),
				__('In 1C programs, these differences can be presented in the form of characteristics.', 'wc1c-main'),
				__('This section of the settings regulates the behavior of the processing of such characteristics on the Woocommerce side.', 'wc1c-main')
			)
		];

		$fields['products_with_characteristics'] =
		[
			'title' => __('Using characteristics', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<br/>%s %s<br /><hr>%s',
				__('When turning on, products with characteristics will processing on the basis of settings for products.', 'wc1c-main'),
				__('At the same time, products are divided into simple and variable. Work with simple products will occur when the parent is not found.', 'wc1c-main'),
				__('The search for the parent product takes place according to a unique identifier of 1C. Search for simple products is carried out in all available settings for synchronization.', 'wc1c-main'),
				__('With the option disconnected, all the data of products with characteristics will be simply missed. Neither the creation, nor update and no other processing will be.', 'wc1c-main')
			),
			'default' => 'no'
		];

        $fields['products_with_characteristics_simple'] =
        [
            'title' => __('Create simple products from characteristics', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => sprintf
            (
                '%s %s<br /><hr>%s',
                __('Sometimes 1C does not provide complete information for creating variable products in WooCommerce.', 'wc1c-main'),
                __('The option allows you to enable the creation of simple products according to the characteristics of the item from 1C.', 'wc1c-main'),
                __('As a result, it turns out like this: a characteristic in 1C is equal to a simple product in WooCommerce.', 'wc1c-main')
            ),
            'default' => 'no'
        ];

		return $fields;
	}

	/**
	 * Configuration fields: attributes
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsAttributes(array $fields): array
	{
		$fields['attributes'] =
		[
			'title' => __('Attributes', 'wc1c-main'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s %s',
				__('General (global) attributes are used for all products.', 'wc1c-main'),
				__('Work with individual product attributes is configured at the product level.', 'wc1c-main'),
				__('These settings only affect the global attributes. As a rule, there is no deletion of global attributes and their values. Removal operations are performed manually or through a cleaner.', 'wc1c-main')
			)
		];

		$fields['attributes_create'] =
		[
			'title' => __('Creating attributes', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('It will be allowed to add common attributes for products based on characteristics, properties and other data according to the other setting sections.', 'wc1c-main'),
				__('Creation will only occur if the attribute has not been previously created. Verification is possible by: name, identifier from 1C, etc. The default is to match by name.', 'wc1c-main')
			),
			'default' => 'no'
		];

		$fields['attributes_create_by_classifier_properties'] =
		[
			'title' => __('Creating attributes from classifier properties', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('The creation will occur when processing the properties of the classifier. Creation occurs only if there is no attribute with the specified name or associated identifier.', 'wc1c-main'),
				__('If disable the creation of attributes and create some attributes manually, it is possible to adding values to them.', 'wc1c-main')
			),
			'default' => 'yes'
		];

        $fields['attributes_create_by_product_characteristics'] =
        [
            'title' => __('Creating attributes from product characteristics', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => sprintf
            (
                '%s %s<hr>%s',
                __('Products may contain characteristics that are not in the properties of the classifier.', 'wc1c-main'),
                __('Due to this setting, when processing such products, missing global attributes will be created.', 'wc1c-main'),
                __('If disable the creation of attributes and create some attributes manually, it is possible to adding values to them.', 'wc1c-main')
            ),
            'default' => 'yes'
        ];

		$fields['attributes_values_by_classifier_properties'] =
		[
			'title' => __('Adding values to attributes from classifier properties', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('Adding product attribute values based on classifier property values.', 'wc1c-main'),
				__('The value is added only if it is absent: by name.', 'wc1c-main'),
                __('The values of the classifier properties are not always filled in by the reference book. It is also recommended to enable adding values based on product properties.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$fields['attributes_values_by_product_properties'] =
		[
			'title' => __('Adding values to attributes from product properties', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('Classifier properties do not always contain values in the reference. When the setting is enabled, values will be added based on the values of the product properties.', 'wc1c-main'),
				__('The value is added only if it is absent: by name.', 'wc1c-main'),
				__('The value is added only if it is missing.', 'wc1c-main')
			),
			'default' => 'yes'
		];

        $fields['attributes_values_by_product_characteristics'] =
        [
            'title' => __('Adding values to attributes from product characteristics', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => sprintf
            (
                '%s %s<hr>%s %s',
                __('The characteristics of products may contain data on properties that are not presented anywhere.', 'wc1c-main'),
                __('Based on this data, in the process of processing products, new values will be added.', 'wc1c-main'),
                __('The value is added only if it is absent: by name.', 'wc1c-main'),
                __('For correct operation, you must either enable the setting for creating global attributes based on product characteristics, or add the attribute manually.', 'wc1c-main')
            ),
            'default' => 'yes'
        ];

        $fields['attributes_update'] =
        [
            'title' => __('Updating attributes', 'wc1c-main'),
            'type' => 'checkbox',
            'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
            'description' => sprintf
            (
                '%s<hr>%s',
                __('It will be allowed to update common attributes for products based on characteristics, properties and other data according to the other setting sections.', 'wc1c-main'),
                __('Attribute updating refers to adding product attribute values based on product characteristics, classifier properties, and other data specified in the settings. If you disable this feature, work will only occur with existing attribute values without updating attribute data. In some cases, updating refers to sorting and renaming the attributes themselves.', 'wc1c-main')
            ),
            'default' => 'no'
        ];

		return $fields;
	}

	/**
	 * Configuration fields: products categories
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsCategories(array $fields): array
	{
		$fields['products_categories'] =
		[
			'title' => __('Products (goods): categories', 'wc1c-main'),
			'type' => 'title',
			'description' => sprintf
			('%s',
			    __('Algorithms for product categories when creating and updating.', 'wc1c-main')
			),
		];

        $products_categories_source_options =
        [
            'no' => __('Do not use', 'wc1c-main'),
            'classifier_groups' => __('From classifier groups', 'wc1c-main'),
        ];

        $fields['products_categories_source'] =
        [
            'title' => __('Source for categories', 'wc1c-main'),
            'type' => 'select',
            'description' => sprintf
            (
                '%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s %s',
                __('The setting works when creating and updating products (goods).', 'wc1c-main'),
                __('Do not use', 'wc1c-main'),
                __('Populating the categories data from CommerceML data will be skipped. If a product is updating, then its current categories will not be updated.', 'wc1c-main'),
                __('From classifier groups', 'wc1c-main'),
                __('The categories data will be filled in based on the classifier groups of the products (goods).', 'wc1c-main'),
                __('To use this mode, need to configure the creation of categories based on classifier groups.', 'wc1c-main')
            ),
            'default' => 'classifier_groups',
            'options' => $products_categories_source_options
        ];

		$fields['products_create_adding_category'] =
		[
			'title' => __('Assigning categories of the creating products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Products in 1C have their own hierarchy. Thanks to this hierarchy, it is possible to automatically assign categories to products on the site.', 'wc1c-main'),
				__('For the correct operation of filling in categories, you must first configure them in a separate settings block.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$fields['products_create_adding_category_fill_parent'] =
		[
			'title' => __('Filling the parent categories of the created product', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('If the category assigned to a product is a child category of other categories, then the parent categories will also be assigned to the product being created.', 'wc1c-main'),
				__('It is recommended to enable this setting.', 'wc1c-main')
			),
			'default' => 'yes'
		];

        $products_update_categories_options =
        [
            'no' => __('Do not update', 'wc1c-main'),
            'yes' => __('Update in any case', 'wc1c-main'),
            'add' => __('Add if not on the site and available in 1C', 'wc1c-main'),
            'yes_yes' => __('Update if present on the site and in 1C', 'wc1c-main'),
        ];

        $fields['products_update_categories'] =
        [
            'title' => __('Categories updating when products updates', 'wc1c-main'),
            'default' => 'no',
            'type' => 'select',
            'description' => sprintf
            (
                '<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
                __('Do not update', 'wc1c-main'),
                __('Categories updates will be skipped in any case.', 'wc1c-main'),
                __('Update in any case', 'wc1c-main'),
                __('Categories will be updated in any case. The same value will always be on the site and in 1C.', 'wc1c-main'),
                __('Add if not on the site and available in 1C', 'wc1c-main'),
                __('Existing categories will not be affected. There will be a filling of those missing on the site if they are available in 1C.', 'wc1c-main'),
                __('Update if present on the site and in 1C', 'wc1c-main'),
                __('Categories will be updated only if they are filled in 1C and on the site at the same time.', 'wc1c-main'),
                __('The setting works when updating products (goods).', 'wc1c-main')
            ),
            'options' => $products_update_categories_options
        ];

		$fields['products_update_categories_fill_parent'] =
		[
			'title' => __('Filling the parent categories when requesting product updates', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => __('Fill in the categories that are higher in level for the product? It is recommended to enable this setting.', 'wc1c-main'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products sync
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsSync(array $fields): array
	{
		$fields['product_sync'] =
		[
			'title' => __('Products (goods): synchronization', 'wc1c-main'),
			'type' => 'title',
			'description' => sprintf
			('%s <br /> %s',
			    __('Dispute resolution between existing products (goods) on the 1C side and in WooCommerce. For extended matching (example by SKU), must use the extension.', 'wc1c-main'),
				__('Products not found by sync keys will be treated as new. Accordingly, the rules for creating products will apply to them.', 'wc1c-main')
			),
		];

		$fields['product_sync_by_id'] =
		[
			'title' => __('By external ID from 1C', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr> %s',
				__('When creating new products based on data from 1C, a universal global identifier from 1C is filled in for them. Can also fill in global identifiers manually for manually created products.', 'wc1c-main'),
				__('Enabling the option allows you to use the filled External ID to mark products (goods) as existing, and thereby run algorithms to update them.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProducts(array $fields): array
	{
		$fields['title_products'] =
		[
			'title' => __('Products (goods)', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for products. Operations on products are based on data from product catalogs and offer packages described in CommerceML.', 'wc1c-main'),
		];

		$fields['products_create'] =
		[
			'title' => __('Creation of products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable the creation of new products upon request from 1C. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<br />%s<br /><hr>%s',
				__('The products is only created if it is not found in WooCommerce when searching by criteria for synchronization.', 'wc1c-main'),
				__('To create, the products parameters from the current configuration are used.', 'wc1c-main'),
				__('The option works only with automatic creation of products. When disabled, it is still possible to manually create products through ManualCML and similar extensions.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$fields['products_create_delete_mark'] =
		[
			'title' => __('Creation of products: marked for deletion in 1C', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('If the product is marked in 1C for deletion, then when you enable the setting, it will still be created on the site and filled with data.', 'wc1c-main'),
				__('At the same time, it is possible to place such products directly in the trash. There is a separate setting for this.', 'wc1c-main')
			),
			'default' => 'no'
		];

		$fields['products_create_delete_mark_trash'] =
		[
			'title' => __('Creation of products: placement of products from 1C marked for deletion to the trash', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('If the product is marked in 1C for deletion, then when the setting is enabled, it will be placed in the trash.', 'wc1c-main'),
				__('It is possible to restore the products placed in the basket both manually and using the settings for updating products.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$fields['products_update'] =
		[
			'title' => __('Update of products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable product updates on demand from 1C. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<br />%s<br /><hr>%s',
				__('Products are updated only if they were found using the product synchronization keys.', 'wc1c-main'),
				__('To update, the products parameters from the current configuration are used.', 'wc1c-main'),
				__('The option works only with automatic updating of products. When disabled, it is still possible to manually update products through ManualCML and similar extensions.', 'wc1c-main')
			),
			'default' => 'no'
		];

		$fields['products_update_use_delete_mark'] =
		[
			'title' => __('Update of products: restoring from the trash removed from deletion in 1C', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('If the product is not marked in 1C for deletion, and it is in the basket on the site, then when the setting is enabled, it will be returned from the basket and filled with data according to the update settings.', 'wc1c-main'),
				__('If the setting is disabled, all products placed in the basket will be there permanently. It will be impossible to create new products of the same kind.', 'wc1c-main')
			),
			'default' => 'no'
		];

		$fields['products_update_delete_mark_trash'] =
		[
			'title' => __('Update of products: placement of products marked for deletion in 1C to the trash', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('If the product is marked in 1C for deletion, then when the setting is enabled, it will be placed in the trash.', 'wc1c-main'),
				__('It is possible to restore the products placed in the trash both manually and using the settings for updating products.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$fields['products_update_only_configuration'] =
		[
			'title' => __('Consider configuration when requesting product updates', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('When updating products data, the update will only occur if the product was created through the current configuration.', 'wc1c-main'),
			'default' => 'yes'
		];

		$fields['products_update_only_schema'] =
		[
			'title' => __('Consider schema when requesting product updates', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('When updating products data, the update will only occur if the product was created through the current schema.', 'wc1c-main'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products prices
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsPrices(array $fields): array
	{
		$fields['title_products_prices'] =
		[
			'title' => __('Products (goods): prices', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Comprehensive settings for updating prices.', 'wc1c-main'),
		];

		$products_prices_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'yes_primary' => __('From first found', 'wc1c-main'),
			'yes_name' => __('From specified name', 'wc1c-main'),
		];

		$fields['products_prices_regular_by_cml'] =
		[
			'title' => __('Prices based on CommerceML data: regular', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods). The found price after use will not be available for selection as a sale price.', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Populating the regular prices data from CommerceML data will be skipped.', 'wc1c-main'),
				__('From first found', 'wc1c-main'),
				__('The first available price of all available prices for the product will be used as the regular price.', 'wc1c-main'),
				__('From specified name', 'wc1c-main'),
				__('The price with the specified name will be used as the regular price. If the price is not found by name, no value will be assigned.', 'wc1c-main')
			),
			'default' => 'yes_primary',
			'options' => $products_prices_by_cml_options
		];

		$fields['products_prices_regular_by_cml_from_name'] =
		[
			'title' => __('Prices based on CommerceML data: regular - name in 1C', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the name of the base price in 1C, which is used for filling to WooCommerce as the base price.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		$fields['products_prices_sale_by_cml'] =
		[
			'title' => __('Prices based on CommerceML data: sale', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods). The sale price must be less than the regular price. Otherwise, it simply wont apply.', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Populating the sale prices data from CommerceML data will be skipped.', 'wc1c-main'),
				__('From first found', 'wc1c-main'),
				__('The first available price of all available prices for the product will be used as the sale price.', 'wc1c-main'),
				__('From specified name', 'wc1c-main'),
				__('The price with the specified name will be used as the sale price. If the price is not found by name, no value will be assigned.', 'wc1c-main')
			),
			'default' => 'no',
			'options' => $products_prices_by_cml_options
		];

		$fields['products_prices_sale_by_cml_from_name'] =
		[
			'title' => __('Prices based on CommerceML data: sale - name in 1C', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the name of the sale price in 1C, which is used for filling to WooCommerce as the sale price.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products sku
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsSku(array $fields): array
	{
		$fields['title_products_sku'] =
		[
			'title' => __('Products (goods): SKU', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Sources and algorithms for filling out products SKU.', 'wc1c-main'),
		];

		$fields['products_create_adding_sku'] =
		[
			'title' => __('Filling the SKU of the created product', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
            (
                '%s<hr>%s',
                __('The product SKU will be added according to data from 1C. It is recommended to enable this feature.', 'wc1c-main'),
                __('The setting works when creating products (goods).', 'wc1c-main')
            ),
            'default' => 'yes'
		];

		$products_update_sku_options =
		[
			'no' => __('Do not update', 'wc1c-main'),
			'yes' => __('Update in any case', 'wc1c-main'),
			'add' => __('Add if not on the site and available in 1C', 'wc1c-main'),
			'yes_yes' => __('Update if present on the site and in 1C', 'wc1c-main'),
		];

		$fields['products_update_sku'] =
		[
			'title' => __('SKU update when products updates', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => sprintf
			(
				'<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
				__('Do not update', 'wc1c-main'),
				__('SKUs updates will be skipped in any case.', 'wc1c-main'),
				__('Update in any case', 'wc1c-main'),
				__('SKUs will be updated in any case. The same value will always be on the site and in 1C.', 'wc1c-main'),
				__('Add if not on the site and available in 1C', 'wc1c-main'),
				__('Existing SKUs will not be affected. There will be a filling of those missing on the site if they are available in 1C.', 'wc1c-main'),
				__('Update if present on the site and in 1C', 'wc1c-main'),
				__('SKUs will be updated only if they are filled in 1C and on the site at the same time.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'options' => $products_update_sku_options
		];

		$products_sku_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'sku' => __('From the standard SKU', 'wc1c-main'),
			'code' => __('From the code', 'wc1c-main'),
            'barcode' => __('From the barcode', 'wc1c-main'),
			'yes_requisites' => __('From requisite with the specified name', 'wc1c-main'),
		];

		$fields['products_sku_by_cml'] =
		[
			'title' => __('Source for SKUs', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Populating the SKU data from CommerceML data will be skipped. If a product is updating, then its current SKU will not be updated.', 'wc1c-main'),
				__('From the standard SKU', 'wc1c-main'),
				__('This SKU is contained in the standard SKU of 1C products. It is located in the conditional tag - sku.', 'wc1c-main'),
				__('From the code', 'wc1c-main'),
				__('In 1C it is presented in the form of the code of the nomenclature. Unloaded as a requisite with the appropriate name.', 'wc1c-main'),
                __('From the barcode', 'wc1c-main'),
                __('The SKUs will be filled in from the product barcode.', 'wc1c-main'),
                __('From requisite with the specified name', 'wc1c-main'),
				__('The SKU data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c-main')
			),
			'default' => 'sku',
			'options' => $products_sku_by_cml_options
		];

		$fields['products_sku_from_requisites_name'] =
		[
			'title' => __('Source for SKUs: name for requisite', 'wc1c-main'),
			'type' => 'text',
			'description' => __('The name of the requisite of the products (goods) which contains a SKU of the product.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products names
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsNames(array $fields): array
	{
		$fields['title_products_names'] =
		[
			'title' => __('Products (goods): names', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Sources and algorithms for filling out products name.', 'wc1c-main'),
		];

		$fields['products_update_name'] =
		[
			'title' => __('Product name update when update products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
            (
                '%s<hr>%s %s',
                __('When changing the products names in 1C, the data will be changed on the site.', 'wc1c-main'),
                __('It is only possible to enable or disable, because. name is always required.', 'wc1c-main'),
                __('It is possible to disable the update of names for specific products (via additional extensions).', 'wc1c-main')
            ),
			'default' => 'no'
		];

		$products_names_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'name' => __('From the standard name', 'wc1c-main'),
			'full_name' => __('From the full name', 'wc1c-main'),
			'yes_requisites' => __('From requisite with the specified name', 'wc1c-main'),
		];

		$fields['products_names_by_cml'] =
		[
			'title' => __('Source for product names', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Populating the name data from CommerceML data will be skipped. If a product is updating, then its current name will not be updated.', 'wc1c-main'),
				__('From the standard name', 'wc1c-main'),
				__('This name is contained in the standard name of 1C products. It is located in the conditional tag - name.', 'wc1c-main'),
				__('From the full name', 'wc1c-main'),
				__('In 1C it is presented in the form of the Full name of the nomenclature. Unloaded as a requisite with the appropriate name.', 'wc1c-main'),
				__('From requisite with the specified name', 'wc1c-main'),
				__('The name data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c-main')
			),
			'default' => 'name',
			'options' => $products_names_by_cml_options
		];

		$fields['products_names_from_requisites_name'] =
		[
			'title' => __('Source for product names: name for requisite', 'wc1c-main'),
			'type' => 'text',
			'description' => __('The name of the requisite of the products (goods) which contains a name of the product.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products descriptions
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsDescriptions(array $fields): array
	{
		$fields['title_products_descriptions'] =
		[
			'title' => __('Products (goods): descriptions', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Sources and algorithms for filling out product descriptions, both short descriptions and full descriptions.', 'wc1c-main'),
		];

		$fields['products_create_adding_description'] =
		[
			'title' => __('Filling the description of the created products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('In the data that came from 1C, there may be descriptions of products that will be placed in a short description.', 'wc1c-main'),
				__('If there are no brief descriptions in 1C, you can turn off the filling and edit the data directly on the site.', 'wc1c-main'),
				__('The choice of a source for a brief description in 1C is in a separate settings block - Products (goods): descriptions.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$products_update_description_options =
		[
			'no' => __('Do not update', 'wc1c-main'),
			'yes' => __('Update in any case', 'wc1c-main'),
			'add' => __('Add if not on the site and available in 1C', 'wc1c-main'),
			'yes_yes' => __('Update if present on the site and in 1C', 'wc1c-main'),
		];

		$fields['products_update_description'] =
		[
			'title' => __('Description update when update products', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => sprintf
			(
				'<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
				__('Do not update', 'wc1c-main'),
				__('Description updates will be skipped in any case.', 'wc1c-main'),
				__('Update in any case', 'wc1c-main'),
				__('Description will be updated in any case. The same value will always be on the site and in 1C.', 'wc1c-main'),
				__('Add if not on the site and available in 1C', 'wc1c-main'),
				__('Existing description will not be affected. There will be a filling of those missing on the site if they are available in 1C.', 'wc1c-main'),
				__('Update if present on the site and in 1C', 'wc1c-main'),
				__('Description will be updated only if they are filled in 1C and on the site at the same time.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'options' => $products_update_description_options
		];

		$products_descriptions_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'yes' => __('From the standard description', 'wc1c-main'),
			'yes_html' => __('From the HTML description', 'wc1c-main'),
			'yes_requisites' => __('From requisite with the specified name', 'wc1c-main'),
			'yes_specification' => __('From the specification', 'wc1c-main'),
		];

		$fields['products_descriptions_short_by_cml'] =
		[
			'title' => __('Source for descriptions', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Populating the short description data from CommerceML data will be skipped. If a product is updating, then its current short description will not be updated.', 'wc1c-main'),
				__('From the standard description', 'wc1c-main'),
				__('This description is contained in the standard description of 1C products. It is located in the conditional tag - description.', 'wc1c-main'),
				__('From the HTML description', 'wc1c-main'),
				__('Standard description, in HTML format only. Unloaded in a short description if there is a checkmark in 1C - Description in HTML format.', 'wc1c-main'),
				__('From requisite with the specified name', 'wc1c-main'),
				__('The short description data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c-main'),
				__('From the specification', 'wc1c-main'),
				__('The short description will be filled in from the item specification in 1C.', 'wc1c-main')
			),
			'default' => 'yes',
			'options' => $products_descriptions_by_cml_options
		];

		$fields['products_descriptions_short_from_requisites_name'] =
		[
			'title' => __('Source for descriptions: name for requisite', 'wc1c-main'),
			'type' => 'text',
			'description' => __('The name of the requisite of the product (goods) which contains a short description of the product.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		$fields['products_create_adding_description_full'] =
		[
			'title' => __('Filling a full description of the created products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('The data received from 1C may contain full descriptions of products that will be placed in the full description.', 'wc1c-main'),
				__('If there are no brief full descriptions in 1C, you can turn off the filling and edit the data directly on the site.', 'wc1c-main'),
				__('The choice of a source for a brief full description in 1C is in a separate settings block - Products (goods): descriptions.', 'wc1c-main')
			),
			'default' => 'no'
		];

		$fields['products_update_description_full'] =
		[
			'title' => __('Full description update when update products', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => sprintf
			(
				'<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
				__('Do not update', 'wc1c-main'),
				__('Description updates will be skipped in any case.', 'wc1c-main'),
				__('Update in any case', 'wc1c-main'),
				__('Description will be updated in any case. The same value will always be on the site and in 1C.', 'wc1c-main'),
				__('Add if not on the site and available in 1C', 'wc1c-main'),
				__('Existing description will not be affected. There will be a filling of those missing on the site if they are available in 1C.', 'wc1c-main'),
				__('Update if present on the site and in 1C', 'wc1c-main'),
				__('Description will be updated only if they are filled in 1C and on the site at the same time.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'options' => $products_update_description_options
		];

		$fields['products_descriptions_by_cml'] =
		[
			'title' => __('Source for full descriptions', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Filling in full description data from CommerceML data will be skipped. If a product is updating, then its current full description will not be updated.', 'wc1c-main'),
				__('From the standard description', 'wc1c-main'),
				__('This description is contained in the standard description of 1C products. It is located in the conditional tag - description.', 'wc1c-main'),
				__('From the HTML description', 'wc1c-main'),
				__('Standard description, in HTML format only. It is unloaded when there is a checkmark in 1C - Description in HTML format.', 'wc1c-main'),
				__('From requisite with the specified name', 'wc1c-main'),
				__('The full description data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c-main'),
				__('From the specification', 'wc1c-main'),
				__('The full description will be filled in from the item specification in 1C.', 'wc1c-main')
			),
			'default' => 'yes_html',
			'options' => $products_descriptions_by_cml_options
		];

		$fields['products_descriptions_from_requisites_name'] =
		[
			'title' => __('Source for full descriptions: name for requisite', 'wc1c-main'),
			'type' => 'text',
			'description' => __('The name of the requisite of the product (goods) which contains a full description of the product.', 'wc1c-main'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products attributes
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsAttributes(array $fields): array
	{
		$fields['title_products_attributes'] =
		[
			'title' => __('Products (goods): attributes', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for working with attributes of products (goods).', 'wc1c-main'),
		];

		$fields['products_create_adding_attributes'] =
		[
			'title' => __('Assigning attributes of the created product', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => __('Newly created products will have attributes added based on the attribute settings. Attribute settings in a separate block.', 'wc1c-main'),
			'default' => 'yes'
		];

		$fields['products_update_attributes'] =
		[
			'title' => __('Product attributes update when requesting product updates', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('Existing synced products will have their attributes updated based on their attribute settings. Attribute settings in a separate block.', 'wc1c-main'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products taxes
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsTaxes(array $fields): array
	{
		$fields['title_products_taxes'] =
		[
			'title' => __('Products (goods): taxes', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for working with taxes of products (goods).', 'wc1c-main'),
		];

		$taxes_status =
		[
			'taxable' => __( 'Taxable', 'wc1c-main' ),
			'shipping' => __( 'Shipping only', 'wc1c-main' ),
			'none' => _x( 'None', 'Tax status', 'wc1c-main'),
		];

		$products_default_options =
		[
			'no' => __('Do not update', 'wc1c-main'),
		];

		$taxes_status_update = array_merge($products_default_options, $taxes_status);

		$fields['products_create_taxes_status'] =
		[
			'title' => __('Tax status of created products', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Define whether or not the entire product is taxable, or just the cost of shipping it.', 'wc1c-main'),
				__('The setting works when creating products (goods).', 'wc1c-main')
			),
			'default' => 'taxable',
			'options' => $taxes_status
		];

		$fields['products_update_taxes_status'] =
		[
			'title' => __('Tax status for updated products', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Define whether or not the entire product is taxable, or just the cost of shipping it.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'default' => 'no',
			'options' => $taxes_status_update
		];

		$fields['products_create_taxes_class'] =
		[
			'title' => __('Tax class for created products', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'wc1c-main'),
				__('The setting works when creating products (goods).', 'wc1c-main')
			),
			'default' => 'standard',
			'options' => wc_get_product_tax_class_options()
		];

		$products_taxes_class_options = array_merge($products_default_options, wc_get_product_tax_class_options());

		$fields['products_update_taxes_class'] =
		[
			'title' => __('Tax class for updated products', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'default' => 'standard',
			'options' => $products_taxes_class_options
		];

		return $fields;
	}

	/**
	 * Configuration fields: products images
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsImages(array $fields): array
	{
		$fields['title_products_images'] =
		[
			'title' => __('Products (goods): images', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for working with images of products (goods).', 'wc1c-main'),
		];

		$fields['products_create_adding_images'] =
		[
			'title' => __('Adding the images of the created products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s',
				__('Products in 1C can have images. When this setting is enabled, they will be added to newly created products on the site.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$products_update_images_options =
		[
			'no' => __('Do not update', 'wc1c-main'),
			'yes' => __('Update in any case', 'wc1c-main'),
			'add' => __('Add if not on the site, but available in 1C', 'wc1c-main'),
			'yes_yes' => __('Update if present on the site and in 1C', 'wc1c-main'),
		];

		$fields['products_update_images'] =
		[
			'title' => __('Update images when update products', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => sprintf
			(
				'<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
				__('Do not update', 'wc1c-main'),
				__('Update images will be skipped in any case.', 'wc1c-main'),
				__('Update in any case', 'wc1c-main'),
				__('Images will be updated in any case. The same value will always be on the site and in 1C.', 'wc1c-main'),
				__('Add if not on the site, but available in 1C', 'wc1c-main'),
				__('Existing images will not be affected. There will be a filling of those missing on the site if they are available in 1C.', 'wc1c-main'),
				__('Update if present on the site and in 1C', 'wc1c-main'),
				__('Images will be updated only if they are filled in 1C and on the site at the same time.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'options' => $products_update_images_options
		];

		$fields['products_images_by_cml'] =
		[
			'title' => __('Images based on CommerceML data', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('When enabled, work with images based on CommerceML files will be allowed.', 'wc1c-main'),
				__('Available images in CommerceML data for products will be populated for future use.', 'wc1c-main'),
				__('In this case, the image files themselves must first be added to the WordPress media library. If they are not included, their use will be skipped.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$fields['products_images_by_cml_max'] =
		[
			'title' => __('Images based on CommerceML data: maximum quantity', 'wc1c-main'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('The maximum number of images to be processed. The excess number will be ignored. To remove the limit, specify - 0. The limit is necessary for weak systems.', 'wc1c-main'),
				__('If you specify one image, it will be uploaded as the main one without adding the rest to the product gallery.', 'wc1c-main')
			),
			'default' => '10',
			'css' => 'min-width: 60px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products other
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsOther(array $fields): array
	{
		$fields['title_products_other'] =
		[
			'title' => __('Products (goods): other', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Regulation of other algorithms for working with products (goods).', 'wc1c-main'),
		];

		$product_statuses = get_post_statuses();
		unset($product_statuses['private']);

		$fields['products_create_status'] =
		[
			'title' => __('Status of the created product', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('Newly created products will have selected status. It is recommended to select the status: Draft.', 'wc1c-main'),
				__('The product catalog comes without prices and balances. Publication is best done at the stage of filling in this data.', 'wc1c-main'),
				__('If a product is marked for deletion in 1C, it will be placed in the trash, regardless of the current setting.', 'wc1c-main')
			),
			'default' => 'draft',
			'options' => $product_statuses
		];

		$default_statuses =
		[
			'' => __('Do not update', 'wc1c-main')
		];

		$statuses = array_merge($default_statuses, $product_statuses);

		$fields['products_update_status'] =
		[
			'title' => __('Product status update when requesting product updates', 'wc1c-main'),
			'type' => 'select',
			'description' => __('The selected status will be assigned to all upgraded products when requesting product upgrades.', 'wc1c-main'),
			'default' => '',
			'options' => $statuses
		];

		$products_update_options =
		[
			'no' => __('Do not update', 'wc1c-main'),
			'yes' => __('Check', 'wc1c-main'),
			'none' => __('Uncheck', 'wc1c-main'),
		];

		$products_update_options_description = sprintf
		(
			'<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
			__('Do not update', 'wc1c-main'),
			__('The current value of the option will remain.', 'wc1c-main'),
			__('Check', 'wc1c-main'),
			__('The mark will be set.', 'wc1c-main'),
			__('Uncheck', 'wc1c-main'),
			__('The mark will be removed.', 'wc1c-main'),
			__('The setting works when updating products (goods).', 'wc1c-main')
		);

		$fields['products_create_set_featured'] =
		[
			'title' => __('Featured on create products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('The created product will be marked as featured.', 'wc1c-main'),
			'default' => 'no'
		];

		$fields['products_update_set_featured'] =
		[
			'title' => __('Featured on update products', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => $products_update_options_description,
			'options' => $products_update_options
		];

		$fields['products_create_set_sold_individually'] =
		[
			'title' => __('Individual sale on create products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('Enable to have the product sold individually in one order. Two units of a product in one order will be impossible to order.', 'wc1c-main'),
			'default' => 'no'
		];

		$fields['products_update_set_sold_individually'] =
		[
			'title' => __('Individual sale on update products', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => $products_update_options_description,
			'options' => $products_update_options
		];

		$visibility_options = wc_get_product_visibility_options();

		$fields['products_create_set_catalog_visibility'] =
		[
			'title' => __('Product visibility on create products', 'wc1c-main'),
			'type' => 'select',
			'description' => __('This setting determines which pages products will be displayed on.', 'wc1c-main'),
			'default' => 'visible',
			'options' => $visibility_options
		];

		$fields['products_update_set_catalog_visibility'] =
		[
			'title' => __('Product visibility on update products', 'wc1c-main'),
			'type' => 'select',
			'description' => __('This setting determines which pages products will be displayed on.', 'wc1c-main'),
			'default' => '',
			'options' => array_merge($default_statuses, $visibility_options)
		];

		$fields['products_create_set_reviews_allowed'] =
		[
			'title' => __('Allow reviews on create products', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('It will be allowed to leave reviews for created products.', 'wc1c-main'),
			'default' => 'no'
		];

		$fields['products_update_set_reviews_allowed'] =
		[
			'title' => __('Allow reviews on update products', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => $products_update_options_description,
			'options' => $products_update_options
		];

		return $fields;
	}

	/**
	 * Configuration fields: media library
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsMediaLibrary(array $fields): array
	{
		$fields['title_media_library'] =
		[
			'title' => __('Media library', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Regulation of algorithms for working with WordPress media library.', 'wc1c-main'),
		];

		$fields['media_library'] =
		[
			'title' => __('Using the media library', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('All file handling capabilities available to the library will be enabled. If disabled, no actions will be performed on files in the library through the schema.', 'wc1c-main'),
			'default' => 'yes'
		];

		$fields['media_library_images_by_receiver'] =
		[
			'title' => __('Images based on Receiver', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s %s',
				__('All image files sent to Receiver files will be added to the WordPress media library.', 'wc1c-main'),
				__('These images can later be used to populate product images.', 'wc1c-main'),
				__('When adding an image, it will be assigned the identifier of the current configuration, as well as the identifier of the scheme and the path of being in 1C.', 'wc1c-main')
			),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Configuration fields: products inventories
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsInventories(array $fields): array
	{
		$fields['title_products_inventories'] =
		[
			'title' => __('Products (goods): inventories', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Comprehensive settings for updating inventories based on data from the offer package.', 'wc1c-main'),
		];

		$fields['products_create_stock_status'] =
		[
			'title' => __('The stock status of product created', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr>%s',
				__('Newly created products will have the selected stock status. It is recommended to select the status: Out of stock.', 'wc1c-main'),
				__('The product catalog comes without quantities. When creating new products, it is better not to expose their availability.', 'wc1c-main')
			),
			'default' => 'outofstock',
			'options' => wc_get_product_stock_status_options()
		];

		$stock_statuses =
		[
			'' => __('Do not update', 'wc1c-main')
		];

		$stock_statuses = array_merge($stock_statuses, wc_get_product_stock_status_options());

		$fields['products_update_stock_status'] =
		[
			'title' => __('Product stock status update when requesting product updates', 'wc1c-main'),
			'type' => 'select',
			'description' => __('Upgradable products will change the balance status to the selected option from the list. It is recommended to select the status: Out of stock. Residues in this case will be restored with further processing of the residues.', 'wc1c-main'),
			'default' => '',
			'options' => $stock_statuses
		];

		$fields['products_inventories_by_offers_quantity'] =
		[
			'title' => __('Filling inventories based on quantity from offers', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c-main'),
			'description' => __('It will be allowed to fill in the quantity of product stocks in WooCommerce based on the quantity received in 1C offers.', 'wc1c-main'),
			'default' => 'yes'
		];

		$fields['products_inventories_quantities_min'] =
		[
			'title' => __('Minimum quantity for availability on the site', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the minimum quantity of goods in 1C to calculate the availability on the site. If there are fewer balances in 1C, then the product will be on the site with a balance of 0, i.e. with the status not available.', 'wc1c-main'),
			'default' => '1',
			'css' => 'min-width: 70px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products dimensions
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsDimensions(array $fields): array
	{
		$fields['title_products_dimensions'] =
		[
			'title' => __('Products (goods): dimensions', 'wc1c-main'),
			'type' => 'title',
			'description' => __('The main settings for filling in the dimensions of products (goods) according to data from 1C. Dimensions include: weight, length, width, height.', 'wc1c-main'),
		];

		$fields['products_create_adding_dimensions'] =
		[
			'title' => __('Filling the dimensions of the created product', 'wc1c-main'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Enabled by default.', 'wc1c-main'),
			'description' => sprintf
			(
				'%s<hr>%s',
				__('The product dimensions will be added according to data from 1C. It is recommended to enable this feature.', 'wc1c-main'),
				__('The setting works when creating products (goods).', 'wc1c-main')
			),
			'default' => 'yes'
		];

		$products_update_dimensions_options =
		[
			'no' => __('Do not update', 'wc1c-main'),
			'yes' => __('Update in any case', 'wc1c-main'),
			'add' => __('Add if not on the site and available in 1C', 'wc1c-main'),
			'yes_yes' => __('Update if present on the site and in 1C', 'wc1c-main'),
		];

		$fields['products_update_dimensions'] =
		[
			'title' => __('Dimensions update when products updates', 'wc1c-main'),
			'default' => 'no',
			'type' => 'select',
			'description' => sprintf
			(
				'<b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s<hr>%s',
				__('Do not update', 'wc1c-main'),
				__('Dimensions updates will be skipped in any case.', 'wc1c-main'),
				__('Update in any case', 'wc1c-main'),
				__('Dimensions will be updated in any case. The same value will always be on the site and in 1C.', 'wc1c-main'),
				__('Add if not on the site, but available in 1C', 'wc1c-main'),
				__('Existing dimensions will not be affected. There will be a filling of those missing on the site if they are available in 1C.', 'wc1c-main'),
				__('Update if present on the site and in 1C', 'wc1c-main'),
				__('Dimensions will be updated only if they are filled in 1C and on the site at the same time.', 'wc1c-main'),
				__('The setting works when updating products (goods).', 'wc1c-main')
			),
			'options' => $products_update_dimensions_options
		];

		$products_dimensions_source_options =
		[
			'no' => __('Do not use', 'wc1c-main'),
			'yes_requisites' => __('From requisite with the specified name', 'wc1c-main'),
		];

		$fields['products_dimensions_source'] =
		[
			'title' => __('Source for dimensions', 'wc1c-main'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods).', 'wc1c-main'),
				__('Do not use', 'wc1c-main'),
				__('Populating the dimensions data from CommerceML data will be skipped. If a product is updating, then its current dimensions will not be updated.', 'wc1c-main'),
				__('From requisite with the specified name', 'wc1c-main'),
				__('The dimensions data will be filled in based on the completed name of the requisite of the products (goods).', 'wc1c-main')
			),
			'default' => 'yes_requisites',
			'options' => $products_dimensions_source_options
		];

		$fields['products_dimensions_by_requisites_weight_from_name'] =
		[
			'title' => __('Dimensions based on requisites: weight', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the weight in 1C, which is used for filling to WooCommerce as the weight.', 'wc1c-main'),
			'default' => __('Weight', 'wc1c-main'),
			'css' => 'min-width: 370px;',
		];

		$fields['products_dimensions_by_requisites_length_from_name'] =
		[
			'title' => __('Dimensions based on requisites: length', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the length in 1C, which is used for filling to WooCommerce as the length.', 'wc1c-main'),
			'default' => __('Length', 'wc1c-main'),
			'css' => 'min-width: 370px;',
		];

		$fields['products_dimensions_by_requisites_width_from_name'] =
		[
			'title' => __('Dimensions based on requisites: width', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the width in 1C, which is used for filling to WooCommerce as the width.', 'wc1c-main'),
			'default' => __('Width', 'wc1c-main'),
			'css' => 'min-width: 370px;',
		];

		$fields['products_dimensions_by_requisites_height_from_name'] =
		[
			'title' => __('Dimensions based on requisites: height', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Specify the requisite name of the height in 1C, which is used for filling to WooCommerce as the height.', 'wc1c-main'),
			'default' => __('Height', 'wc1c-main'),
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: logs
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsLogs(array $fields): array
	{
		$fields['title_logger'] =
		[
			'title' => __('Event logs', 'wc1c-main'),
			'type' => 'title',
			'description' => __('Maintaining event logs for the current configuration. You can view the logs through the extension or via FTP.', 'wc1c-main'),
		];

		$fields['logger_level'] =
		[
			'title' => __('Level for events', 'wc1c-main'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wc1c-main'),
			'default' => '300',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c-main'),
				'100' => __('DEBUG (100)', 'wc1c-main'),
				'200' => __('INFO (200)', 'wc1c-main'),
				'250' => __('NOTICE (250)', 'wc1c-main'),
				'300' => __('WARNING (300)', 'wc1c-main'),
				'400' => __('ERROR (400)', 'wc1c-main'),
			],
		];

		$fields['logger_files_max'] =
		[
			'title' => __('Maximum files', 'wc1c-main'),
			'type' => 'text',
			'description' => __('Log files created daily. This option on the maximum number of stored files. By default saved of the logs are for the last 10 days.', 'wc1c-main'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		return $fields;
	}
}