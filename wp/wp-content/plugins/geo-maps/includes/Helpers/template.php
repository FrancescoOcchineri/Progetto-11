<?php
if (!function_exists('geo_maps_load_admin_template')) {

	function geo_maps_load_admin_template($template = null, $variables = array(), $include_once = false)
	{
		$variables = (array)$variables;

		$variables = apply_filters('geo_maps_load_admin_template_variables', $variables);

		extract($variables);

		$isLoad = apply_filters('should_geo_maps_load_admin_template', true, $template, $variables);

		if (!$isLoad) {

			return;
		}

		do_action('geo_maps_load_admin_template_before', $template, $variables);

		if ($include_once) {

			include_once geo_maps_get_admin_template($template);

		} else {

			include geo_maps_get_admin_template($template);
		}
		do_action('geo_maps_load_admin_template_after', $template, $variables);
	}
}
if (!function_exists('geo_maps_get_admin_template')) {

	function geo_maps_get_admin_template($template = null)
	{
		if (!$template) {
			return false;
		}
		$template = str_replace('.', DIRECTORY_SEPARATOR, $template);

		$template_location = GEO_MAPS_ABSPATH . "includes/Admin/Templates/{$template}.php";

		if (!file_exists($template_location)) {

			echo '<div class="geo_maps-notice-warning"> ' . __(sprintf('The file you are trying to load is not exists in your theme or geo_maps plugins location, if you are a developer and extending geo_maps plugin, please create a php file at location %s ', "<code>{$template_location}</code>"), 'geo-maps') . ' </div>';
		}


		return apply_filters('geo_maps_get_admin_template_path', $template_location, $template);
	}
}
