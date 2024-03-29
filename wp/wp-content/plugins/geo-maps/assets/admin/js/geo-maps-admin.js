// @var geoMapsAdminParams
(function ($) {
	var GeoMapsAdmin = {
		init: function () {
			this.settings = (geoMapsAdminParams.options.settings);
			this.bindEvents();
			this.renderPreviewMap();
			this.image_upload_frame = '';
			this.initMediaUploader();
		},
		bindEvents: function () {
			var _that = this;
			$('body').on('change', '#geo_maps_map_type', function () {
				_that.settings.map_type = _that.getMapType();
				_that.renderPreviewMap();
			});

			$('body').on('change', '#geo_maps_osm_map_provider[name="geo_maps_osm_map_provider"]', function () {
				_that.settings.osm_provider = $(this).val();
				if (_that.getMapType() === "open_street_map") {
					_that.renderPreviewMap();
				}
			});
			$('body').on('click', '.matrixaddons-tab-nav-item', function (e) {
				e.preventDefault();
				var id = $(this).attr('id');
				$(this).closest('.matrixaddons-tabs').find('.matrixaddons-tab-nav .matrixaddons-tab-nav-item').removeClass('item-active');
				$(this).addClass('item-active');
				$(this).closest('.matrixaddons-tabs').find('.matrixaddons-tab-section').addClass('matrixaddons-hide');
				$(this).closest('.matrixaddons-tabs').find('.matrixaddons-tab-section.' + id + '_content').removeClass('matrixaddons-hide');
				var tab = $(this).attr('data-tab');
				$('[name="geo_maps_meta_active_tab"]').val(tab);

			});
			$('body').on('click', '.matrixaddons-repeater-add', function (e) {
				e.preventDefault();
				var parent = $(this).closest('.matrixaddons-field-group');
				var id = parent.attr('id');
				var totalLength = parent.find('.matrixaddons-repeater-wrapper').find('.matrixaddons-repeater-item').length
				var item_id = ((totalLength + 1) - 1);
				var replace_to = '___' + id + '[0]';
				var replace_with = id + '[' + item_id + ']';
				var tmpl = parent.find('.matrixaddons-repeater-item.matrixaddons-repeater-hidden').html();
				var replacedTemplate = _that._replaceAll(tmpl, replace_to, replace_with);
				var newTemplate = $('<div class="matrixaddons-repeater-item" data-item-id="' + item_id + '">').append(replacedTemplate);
				parent.find('.matrixaddons-repeater-wrapper').append(newTemplate);
				_that.loadMapItem(item_id);
			})
			$('body').on('click', '.matrixaddons-repeater-title', function (e) {
				if (!$(e.target).hasClass('matrixaddons-repeater-remove')) {
					var el = $(this).closest('.matrixaddons-repeater-item').find('.matrixaddons-repeater-content');
					if (el.hasClass('matrixaddons-hide')) {
						$(this).closest('.matrixaddons-repeater-item').find('.matrixaddons-repeater-header-icon').removeClass('dashicons dashicons-arrow-up-alt2').addClass('dashicons dashicons-arrow-down-alt2');

						el.removeClass('matrixaddons-hide');
						var marker_index = $(this).closest('.matrixaddons-repeater-item').attr('data-item-id');
						_that.loadMapItem(marker_index);
					} else {
						$(this).closest('.matrixaddons-repeater-item').find('.matrixaddons-repeater-header-icon').removeClass('dashicons dashicons-arrow-down-alt2').addClass('dashicons dashicons-arrow-up-alt2');

						el.addClass('matrixaddons-hide');

					}
				}
			})
			$('body').on('keyup', 'input.geo-maps-marker-title', function () {
				var val = $(this).val();
				$(this).closest('.matrixaddons-repeater-item').find('.matrixaddons-repeater-text').text(val);
			});
			$('body').on('click', '.matrixaddons-repeater-remove', function () {
				var min_item = parseInt($(this).attr('data-min-item'));
				var min_item_message = $(this).attr('data-min-item-message');
				var item_length = $(this).closest('.matrixaddons-repeater-wrapper').find('.matrixaddons-repeater-item').length;
				if (item_length <= min_item) {
					alert(min_item_message);
					return;
				}
				var confirm = $(this).attr('data-confirm');
				if (window.confirm((confirm))) {
					var wrap = $(this).closest('.matrixaddons-repeater-wrapper');
					$(this).closest('.matrixaddons-repeater-item').remove();
					_that.reindexRepeaterItems(wrap);
					_that.reCalculateMarkerContent();
				}
			});

			$('body').on('click', '.geo-maps-location-search-button', function (e) {
				e.preventDefault();
				_that.mapLocationHtml($(this));
			});

			var locationKeyUpTimer = null;
			$('body').on('keyup', '.geo-maps-marker-location', function (e) {
				var keyUp = $(this);
				clearTimeout(locationKeyUpTimer);
				locationKeyUpTimer = setTimeout(function () {
					_that.mapLocationHtml(keyUp);
				}, 1000);
			});

			$('body').on('click', '.geo-maps-location-list-item', function () {
				var lat = $(this).attr('data-lat');
				var lng = $(this).attr('data-lng');
				var title = $(this).text();
				var wrap = $(this).closest('.matrixaddons-repeater-item');

				$(this).closest('ul').remove();

				wrap.find('input.geo-maps-marker-title').val(title).trigger('change');
				wrap.find('input.geo-maps-marker-location').val(title);
				wrap.find('input.geo-maps-marker-latitude').val(lat).trigger('change');
				wrap.find('input.geo-maps-marker-longitude').val(lng).trigger('change');

			});
			$('body').on('change', '.geo-maps-marker-latitude, .geo-maps-marker-longitude, .geo-maps-marker-title, .geo-maps-marker-content', function () {
				var item_id = $(this).closest('.matrixaddons-repeater-item').attr('data-item-id');
				_that.loadMapItem(item_id, true);
			})

			$('body').on('input', '.geo-maps-marker-latitude', function () {
				_that.validateLatLong($(this));
			});
			$('body').on('input', '.geo-maps-marker-longitude', function () {
				_that.validateLatLong($(this));
			});
			$('body').on('click', '.geo-maps-marker-center-position', function () {
				var isChecked = $(this).is(':checked');
				if (isChecked) {
					var wrap = $(this).closest('.matrixaddons-repeater-wrapper');
					wrap.find('.geo-maps-marker-center-position').prop('checked', false);
					$(this).prop('checked', true);
					var index = $(this).closest('.matrixaddons-repeater-item').attr('data-item-id');
					_that.settings.center_index = index;
					_that.renderPreviewMap();
				}
			});
			$('body').on('change', '.geo-maps-marker-image-id, .geo-maps-marker-image-height, .geo-maps-marker-image-width', function (e) {
				e.preventDefault();
				_that.reCalculateMarkerContent();
			});
			$('body').on('click', '.geo-maps-marker-scroll-wheel-zoom', function () {
				_that.settings.scroll_wheel_zoom = false;
				var isChecked = $(this).is(':checked');
				if (isChecked) {
					_that.settings.scroll_wheel_zoom = true;
				}
				_that.renderPreviewMap();
			});

			$('body').on('click', '.geo-maps-map-control-position', function () {
				var position = $(this).val();
				_that.settings.control_position = position;
				_that.settings.show_control = position !== 'hide';
				_that.renderPreviewMap();
			});
			$('body').on('change', '.geo-maps-popup-show-on', function (e) {
				e.preventDefault();
				_that.settings.popup_show_on = $(this).val();
				_that.renderPreviewMap();
			});
		},
		validateLatLong: function (el) {
			var validNumber = new RegExp(/^\d*\.?\d*$/);
			if (!validNumber.test($(el).val())) {
				$(el).val(0);
			}

		},
		getMapType: function () {

			var map_type = $('#geo_maps_map_type option:selected').val();

			if (map_type == '' || map_type == null) {

				$('#geo-maps-map-osm-provider.postbox').removeClass('matrixaddons-hide');

				return 'google_map';
			}
			if (map_type === "open_street_map") {

				$('#geo-maps-map-osm-provider.postbox').removeClass('matrixaddons-hide');

			} else {
				$('#geo-maps-map-osm-provider.postbox').addClass('matrixaddons-hide');
			}
			return map_type;

		},
		reindexRepeaterItems: function (wrap) {
			var _that = this;
			var items = $(wrap).find('.matrixaddons-repeater-item');
			var index_id = 0;
			$.each(items, function () {

				var old_index = $(this).attr('data-item-id');


				if (old_index != index_id) {

					var elements = $(this).find('[name*="[' + old_index + ']"], [id*="[' + old_index + ']"]');


					$.each(elements, function () {
						var element = $(this);

						if ($(this).attr("name")) {
							var name = element.attr('name');
							var new_name = _that._replaceAll(name, old_index, index_id);
							$(this).attr('name', new_name);
						}
						if ($(this).attr("id")) {
							var id = element.attr('id');
							var new_id = _that._replaceAll(id, old_index, index_id);
							$(this).attr('id', new_id);
						}
					})

				}
				$(this).attr('data-item-id', index_id);
				index_id++;
			});
		},
		mapLocationHtml: function (el) {

			var fieldset = $(el).closest('.matrixaddons-fieldset');
			var value = fieldset.find(".geo-maps-marker-location").val();
			if (value === '') {
				fieldset.find('.geo-maps-location-lists').remove();
				return;
			}
			this.callLocationAPI(value, fieldset);


		},
		callLocationAPI: function (value, fieldset) {
			var location_search_url = 'https://nominatim.openstreetmap.org/search?q=' + value + '&format=json';

			fetch(location_search_url).then(function (response) {
				return response.json();
			}).then(function (response_data) {
				if (response_data.length > 0) {
					var el = $('<ul class="geo-maps-location-lists wp-map-block-modal-place-search__results"/>');

					response_data.forEach(function (item, index) {
						var title = item.display_name;
						var lat = item.lat;
						var lng = item.lon;
						var li = $('<li class="geo-maps-location-list-item" data-lng="' + lng + '" data-lat="' + lat + '"/>');
						li.text(title);
						el.append(li);
					});
					fieldset.find('.geo-maps-location-lists').remove();
					fieldset.append(el);
				}
			});
		},
		_replaceAll: function (str, toReplace, replaceWith) {
			return str ? str.split(toReplace).join(replaceWith) : '';
		},
		renderPreviewMap: function () {
			var _that = this;
			console.log(JSON.stringify(_that.settings));
			_that.settings.map_type = _that.getMapType();
			$(".geo_maps_map_render_element").each((index, element) => {
				const Element = jQuery(element);
				window.Geo_Maps_Render(
					Element.attr("ID"),
					_that.settings,
				);
			});
		},
		loadMapItem: function (marker_index, force_remap = false) {

			var _that = this;

			var item = $('.matrixaddons-repeater-item[data-item-id="' + marker_index + '"]');
			if (item.length < 1) {
				return;
			}
			var element = item.find('.geo_maps_marker_item_position ');

			if ($(element).hasClass('geo-map-added') && !force_remap) {
				return;
			}
			const Element = jQuery(element).closest('.geo-maps-marker-content-wrap');
			$(element).addClass('geo-map-added');
			let mapSetting = Object.assign({}, _that.settings);
			var default_lat = geoMapsAdminParams.default_marker.lat;
			var default_lng = geoMapsAdminParams.default_marker.lng;
			if (item.find('.geo-maps-marker-latitude').val() !== "" && item.find('.geo-maps-marker-longitude').val() !== "") {
				default_lat = item.find('.geo-maps-marker-latitude').val();
				default_lng = item.find('.geo-maps-marker-longitude').val();
			}

			var title = item.find('.geo-maps-marker-title').val()
			var content = item.find('.geo-maps-marker-content').val();
			mapSetting.scroll_wheel_zoom = true;
			mapSetting.popup_show_on = 'click';
			mapSetting.control_position = 'topright';
			mapSetting.show_control = true;
			mapSetting.map_marker = [{
				title: title,
				content: content,
				draggable: 'true',
				lat: default_lat,
				lng: default_lng,
				dragendCallback: function (event) {
					_that.markerDragendCallback(event);
				}
			}];
			mapSetting.center_index = 0;
			mapSetting.map_type = _that.getMapType();
			window.Geo_Maps_Render(
				Element.attr("ID"),
				mapSetting,
			);


			_that.reCalculateMarkerContent();


		},
		reCalculateMarkerContent: function () {
			var _that = this;
			var items = $('#geo_maps_markers').find('.matrixaddons-repeater-wrapper').find('.matrixaddons-repeater-item');
			var mapMarkers = [];
			if (items.length > 0) {
				$.each(items, function () {
					var item = $(this);
					var markerIndex = item.attr('data-item-id');
					var title = item.find('input.geo-maps-marker-title').val();
					var latitude = item.find('input.geo-maps-marker-latitude').val();
					var longitude = item.find('input.geo-maps-marker-longitude').val();
					var content = item.find('.geo-maps-marker-content').val();
					var custom_marker = {
						lat: latitude,
						lng: longitude,
						title: title,
						content: content
					};
					mapMarkers[markerIndex] = _that.getItemMarkerImage(markerIndex, custom_marker);

				});
			} else {
				var default_marker = geoMapsAdminParams.default_marker;
				var main_marker_image = $('#geo_maps_marker_image').find('.geo-maps-marker-image-id');
				if (main_marker_image.length > 0) {
					if (parseInt(main_marker_image.val()) > 0) {
						var image_url = $('#geo_maps_marker_image').find('.image-wrapper').attr('data-url');
						if (image_url !== '') {
							default_marker.customIconUrl = image_url;
							default_marker.iconType = 'custom';
						}
					}
				}
				mapMarkers[0] = default_marker;
			}
			_that.settings.map_marker = mapMarkers;

			_that.renderPreviewMap();
		},
		markerDragendCallback: function (event) {
			var marker = event.target;
			var position = marker.getLatLng();
			var wrap = $(event.target.getElement()).closest('.matrixaddons-fieldset-content');
			wrap.find('.geo-maps-marker-latitude').val(position.lat).trigger('change');
			wrap.find('.geo-maps-marker-longitude').val(position.lng).trigger('change');

		},
		initMediaUploader: function () {
			var _this = this;
			$('body').on('click', '.matrixaddons-image-field-add', function (event) {
				event.preventDefault();
				_this.uploadWindow($(this), $(this).closest('.matrixaddons-image-field-wrap'));
			});
			$('body').on('click', '.matrixaddons-image-delete', function (event) {
				event.preventDefault();
				var imageField = $(this).closest('.matrixaddons-field-image');
				imageField.find('.image-wrapper').attr('data-url', '');
				imageField.find('.image-container, .field-container').addClass('matrixaddons-hide');
				imageField.find('.matrixaddons-image-field-add').removeClass('matrixaddons-hide');
				imageField.find('.geo-maps-marker-image-id').val(0).trigger('change');

			});
		},
		uploadWindow: function (uploadBtn, wrapper) {

			var _this = this;
			if (this.image_upload_frame) this.image_upload_frame.close();

			this.image_upload_frame = wp.media.frames.file_frame = wp.media({
				title: uploadBtn.data('uploader-title'),
				button: {
					text: uploadBtn.data('uploader-button-text'),
				},
				multiple: false
			});

			this.image_upload_frame.on('select', function () {

				var selection = _this.image_upload_frame.state().get('selection');
				var selected_list_node = wrapper.find('.image-container');
				var imageHtml = '';
				var attachment_id = 0;
				selection.map(function (attachment_object, i) {
					var attachment = attachment_object.toJSON();
					attachment_id = attachment.id;

					var attachment_url = attachment.sizes.full.url;
					imageHtml = _this.getImageElement(attachment_url);

				});

				if (attachment_id > 0) {
					wrapper.find('.image-container, .field-container').removeClass('matrixaddons-hide');
					wrapper.find('.matrixaddons-image-field-add').addClass('matrixaddons-hide');
					selected_list_node.find('.image-wrapper').remove();
					selected_list_node.append(imageHtml);
					wrapper.find('.field-container').find('.geo-maps-marker-image-id').val(attachment_id).trigger('change');
				}
			});


			this.image_upload_frame.open();
		},
		getImageElement: function (src) {
			return '<div data-url="' + src + '" class="image-wrapper"><div class="image-content"><img src="' + src + '" alt=""><div class="image-overlay"><a class="matrixaddons-image-delete remove dashicons dashicons-trash"></a></div></div></div>';
		},
		getItemMarkerImage: function (item_index, new_item) {
			var wrap = $('.matrixaddons-repeater-wrapper').find('.matrixaddons-repeater-item[data-item-id="' + item_index + '"]').find('#geo_maps_marker_item_image');
			var _that = this;
			var height = parseInt(wrap.find('.geo-maps-marker-image-height').val());
			var width = parseInt(wrap.find('.geo-maps-marker-image-width').val());
			var image_id = wrap.find('.geo-maps-marker-image-id').val();
			if (image_id === '' || parseInt(image_id) < 1) {

				new_item.iconType = 'default';

				return _that.getMainMarker(new_item);

			}
			new_item.iconType = 'custom';
			new_item.customIconUrl = wrap.find('.image-wrapper').attr('data-url');
			new_item.customIconWidth = width < 1 ? 25 : width;
			new_item.customIconHeight = height < 1 ? 40 : height;

			return new_item;

		},
		getMainMarker: function (new_item) {
			var wrap = $('#geo_maps_marker_image');
			var image_id = wrap.find('.geo-maps-marker-image-id').val();

			if (image_id === '' || parseInt(image_id) < 1) {
				return new_item;

			}
			var height = parseInt(wrap.find('.geo-maps-marker-image-height').val());
			var width = parseInt(wrap.find('.geo-maps-marker-image-width').val());

			new_item.iconType = 'custom';
			new_item.customIconUrl = wrap.find('.image-wrapper').attr('data-url');
			new_item.customIconWidth = width < 1 ? 25 : width;
			new_item.customIconHeight = height < 1 ? 40 : height;

			return new_item;

		}

	};

	$(document).ready(function () {
		GeoMapsAdmin.init();
	});
}(jQuery));
