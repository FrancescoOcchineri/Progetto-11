<?php

namespace MatrixAddons\GeoMaps\Admin\FieldItems;


class Content
{
	public static function render($field, $field_id, $value, $group_id = null)
	{
		$content = $field['content'] ?? '';

		echo '<div class="geo-maps-map-render-element-wrap">';
		echo "<div id='{$group_id}' class='geo-maps-marker-content-wrap'>";
		echo wp_kses($content, array(
			'a' => array('href' => array(), 'class' => array(), 'target' => array()),
			'h2' => array('class' => array()),
			'div' => array('class' => array())
		));
		echo '</div>';
		echo '</div>';
	}

	public static function sanitize($field, $raw_value, $field_id)
	{

		return '';
	}
}
