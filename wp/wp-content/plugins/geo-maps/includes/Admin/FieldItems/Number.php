<?php

namespace MatrixAddons\GeoMaps\Admin\FieldItems;


class Number
{
	public static function render($field, $field_id, $value, $group_id = null)
	{
		$class = $field['class'] ?? '';

		$after = $field['after'] ?? '';

		$field_name = !(is_null($group_id)) ? $group_id . '[' . $field_id . ']' : $field_id;

		echo '
					<div class="matrixaddons-fieldset">
					<input type="number" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" class="' . esc_attr($class) . '" />
					' . wp_kses($after, array(
				'a' => array('href' => array(), 'class' => array(), 'target' => array()),
				'h2' => array('class' => array()),
				'div' => array('class' => array())
			)) . '
					</div>

				';
	}

	public static function sanitize($field, $raw_value, $field_id)
	{

		return absint($raw_value);
	}

}
