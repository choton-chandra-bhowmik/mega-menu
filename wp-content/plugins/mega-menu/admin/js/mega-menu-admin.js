/* Mega Menu Admin JavaScript */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Check if megaMenuData is defined
		if (typeof megaMenuData === 'undefined') {
			console.error('megaMenuData not defined. Plugin may not be properly initialized.');
			return;
		}

		// Load existing menus on page load
		loadAllMenus();

		// Create Menu Button
		$(document).on('click', '#create-menu-btn', function() {
			createMenu();
		});

		// Add Row Button
		$(document).on('click', '.add-row-btn', function() {
			var menuId = $(this).data('menu-id');
			addMenuItem(menuId);
		});

		// Delete Menu Button
		$(document).on('click', '.delete-menu', function() {
			if (confirm('Are you sure you want to delete this menu?')) {
				deleteMenu($(this).data('menu-id'));
			}
		});

		// Save Item Button
		$(document).on('click', '.save-item', function() {
			var itemId = $(this).data('item-id');
			saveMenuItem(itemId);
		});

		// Delete Item Button
		$(document).on('click', '.delete-item', function() {
			if (confirm('Are you sure you want to delete this item?')) {
				deleteMenuItem($(this).data('item-id'));
			}
		});

		// Upload Images Button
		$(document).on('click', '.upload-images-btn', function(e) {
			e.preventDefault();
			var itemId = $(this).data('item-id');
			var $row = $(this).closest('.menu-item-row');
			uploadImages($row, itemId);
		});

		// Remove Image Button
		$(document).on('click', '.remove-image', function(e) {
			e.preventDefault();
			var imageId = $(this).data('image-id');
			var $row = $(this).closest('.menu-item-row');
			removeImage($row, imageId);
		});

		// Copy Shortcode
		$(document).on('click', '.copy-shortcode', function(e) {
			e.preventDefault();
			var code = $(this).siblings('code').text();
			copyToClipboard(code);
		});
	});

	/**
	 * Load all menus
	 */
	function loadAllMenus() {
		if (typeof megaMenuData === 'undefined' || !megaMenuData.ajaxUrl) {
			console.error('AJAX URL not configured');
			return;
		}

		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'get_all_menus',
				nonce: megaMenuData.nonce
			},
			success: function(response) {
				if (response.success && response.data && response.data.menus) {
					var $menusList = $('#menus-list');
					$menusList.html('');

					$.each(response.data.menus, function(index, menu) {
						var menuHtml = createMenuElement(menu);
						$menusList.append(menuHtml);
						
						// Load items for this menu
						loadMenuItems(menu.id);
					});

					if (response.data.menus.length === 0) {
						$menusList.html('<p>No menus created yet. Create a menu to get started.</p>');
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('Failed to load menus:', textStatus, errorThrown);
				// Don't show error to user, just keep default message
			}
		});
	}

	/**
	 * Create Menu
	 */
	function createMenu() {
		var title = $('#menu-title').val();
		var pageId = $('#menu-page').val();

		if (!title) {
			alert('Please enter a menu title');
			return;
		}

		if (!pageId) {
			alert('Please select a page');
			return;
		}

		if (typeof megaMenuData === 'undefined' || !megaMenuData.ajaxUrl) {
			alert('AJAX is not properly configured. Plugin may need to be reactivated.');
			return;
		}

		console.log('Creating menu with title:', title, 'page_id:', pageId);

		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'create_mega_menu',
				nonce: megaMenuData.nonce,
				title: title,
				page_id: pageId
			},
			success: function(response) {
				console.log('Menu creation response:', response);

				if (response.success && response.data) {
					$('#menu-title').val('');
					$('#menu-page').val('');
					
					var $menusList = $('#menus-list');
					if ($menusList.find('p').length > 0) {
						$menusList.html('');
					}

					$menusList.prepend(response.data.html);
					
					// Automatically add one empty row for user to start entering data
					var menuId = response.data.menu.id;
					addMenuItem(menuId, true);
					
					console.log('Menu created successfully!');
				} else {
					var errorMsg = 'Unknown error';
					if (response.data) {
						if (typeof response.data === 'string') {
							errorMsg = response.data;
						} else if (typeof response.data === 'object') {
							if (response.data.message) {
								errorMsg = response.data.message;
								if (response.data.db_error) {
									errorMsg += ' - DB: ' + response.data.db_error;
								}
								if (response.data.code) {
									errorMsg += ' (' + response.data.code + ')';
								}
							}
						}
					}
					console.error('Menu creation failed:', response.data);
					alert('Error: ' + errorMsg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				var errorMsg = 'Unknown error';
				try {
					var response = JSON.parse(jqXHR.responseText);
					if (response && response.data) {
						if (typeof response.data === 'string') {
							errorMsg = response.data;
						} else if (response.data.message) {
							errorMsg = response.data.message;
						}
					}
				} catch(e) {
					errorMsg = jqXHR.status + ' ' + jqXHR.statusText;
				}
				console.error('AJAX Error:', {
					status: jqXHR.status,
					statusText: jqXHR.statusText,
					responseText: jqXHR.responseText,
					textStatus: textStatus,
					errorThrown: errorThrown
				});
				alert('Failed to create menu: ' + errorMsg);
			}
		});
	}

	/**
	 * Load menu items
	 */
	function loadMenuItems(menuId) {
		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'get_menu_items',
				nonce: megaMenuData.nonce,
				menu_id: menuId
			},
			success: function(response) {
				if (response.success) {
					var $container = $('[data-menu-id="' + menuId + '"]').find('.menu-items-list');
					$container.html(response.data.html);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('Failed to load menu items:', textStatus, errorThrown);
			}
		});
	}

	/**
	 * Add Menu Item
	 */
	function addMenuItem(menuId, silent) {
		silent = silent || false;
		
		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'add_menu_item',
				nonce: megaMenuData.nonce,
				menu_id: menuId,
				heading: 'New Item',
				link: '',
				image_ids: '[]'
			},
			success: function(response) {
				if (response.success && response.data) {
					var $container = $('[data-menu-id="' + menuId + '"]').find('.menu-items-list');
					$container.append(response.data.html);
					
					if (!silent) {
						alert('Item added successfully!');
					}
				} else {
					var errorMsg = response.data || 'Unknown error';
					alert('Error: ' + errorMsg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('Failed to add menu item:', textStatus, errorThrown);
				if (!silent) {
					alert('An error occurred while adding the item.');
				}
			}
		});
	}

	/**
	 * Save Menu Item
	 */
	function saveMenuItem(itemId) {
		var $row = $('[data-item-id="' + itemId + '"]');
		var heading = $row.find('.item-heading').val();
		var link = $row.find('.item-link').val();
		var imageIds = $row.find('.item-image-ids').val();

		console.log('Saving item:', { itemId, heading, link, imageIds });

		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'update_menu_item',
				nonce: megaMenuData.nonce,
				item_id: itemId,
				heading: heading,
				link: link,
				image_ids: imageIds
			},
			success: function(response) {
				console.log('Save response:', response);
				if (response.success) {
					alert('Item saved successfully!');
				} else {
					var errorMsg = 'Unknown error';
					if (response.data) {
						if (typeof response.data === 'string') {
							errorMsg = response.data;
						} else if (response.data.message) {
							errorMsg = response.data.message;
							if (response.data.db_error) {
								errorMsg += ' - DB: ' + response.data.db_error;
							}
						}
					}
					console.error('Save failed:', response.data);
					alert('Error: ' + errorMsg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				var errorMsg = 'Unknown error';
				try {
					var response = JSON.parse(jqXHR.responseText);
					if (response && response.data) {
						if (typeof response.data === 'string') {
							errorMsg = response.data;
						} else if (response.data.message) {
							errorMsg = response.data.message;
						}
					}
				} catch(e) {
					errorMsg = jqXHR.status + ' ' + jqXHR.statusText;
				}
				console.error('Save AJAX Error:', {
					status: jqXHR.status,
					statusText: jqXHR.statusText,
					responseText: jqXHR.responseText,
					textStatus: textStatus,
					errorThrown: errorThrown
				});
				alert('Error saving item: ' + errorMsg);
			}
		});
	}

	/**
	 * Delete Menu Item
	 */
	function deleteMenuItem(itemId) {
		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'delete_menu_item',
				nonce: megaMenuData.nonce,
				item_id: itemId
			},
			success: function(response) {
				if (response.success) {
					$('[data-item-id="' + itemId + '"]').fadeOut(function() {
						$(this).remove();
					});
				} else {
					alert('Error: ' + (response.data ? response.data : 'Unknown error'));
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('Failed to delete menu item:', textStatus, errorThrown);
				alert('An error occurred while deleting the item.');
			}
		});
	}

	/**
	 * Delete Menu
	 */
	function deleteMenu(menuId) {
		$.ajax({
			url: megaMenuData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'delete_mega_menu',
				nonce: megaMenuData.nonce,
				menu_id: menuId
			},
			success: function(response) {
				if (response.success) {
					$('[data-menu-id="' + menuId + '"]').fadeOut(function() {
						$(this).remove();
						
						// Check if no menus left
						if ($('#menus-list').find('.mega-menu-item').length === 0) {
							$('#menus-list').html('<p>No menus created yet. Create a menu to get started.</p>');
						}
					});
				} else {
					alert('Error: ' + (response.data ? response.data : 'Unknown error'));
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('Failed to delete menu:', textStatus, errorThrown);
				alert('An error occurred while deleting the menu.');
			}
		});
	}

	/**
	 * Upload Images
	 */
	function uploadImages($row, itemId) {
		var frame = wp.media({
			title: 'Select Images',
			button: {
				text: 'Use Images'
			},
			multiple: true
		});

		frame.on('select', function() {
			var attachments = frame.state().get('selection').toJSON();
			var imageIds = [];

			$.each(attachments, function(index, attachment) {
				imageIds.push(attachment.id);
			});

			// Update the hidden field
			$row.find('.item-image-ids').val(JSON.stringify(imageIds));

			// Update preview
			updateImagesPreview($row, imageIds);
		});

		frame.open();
	}

	/**
	 * Update Images Preview
	 */
	function updateImagesPreview($row, imageIds) {
		var $preview = $row.find('.images-preview');
		$preview.html('');

		$.each(imageIds, function(index, imageId) {
			// Use WordPress attachment data
			var attachment = wp.media.attachment(imageId);
			if (attachment) {
				attachment.fetch();
				var imageSrc = attachment.get('url');
				if (imageSrc) {
					addImageThumbnail($preview, imageId, imageSrc);
				}
			}
		});
	}

	/**
	 * Add Image Thumbnail
	 */
	function addImageThumbnail($container, imageId, imageSrc) {
		var html = '<div class="image-thumbnail" data-image-id="' + imageId + '">' +
			'<img src="' + imageSrc + '" alt="Thumbnail" />' +
			'<button type="button" class="remove-image" data-image-id="' + imageId + '">×</button>' +
			'</div>';
		$container.append(html);
	}

	/**
	 * Remove Image
	 */
	function removeImage($row, imageId) {
		var imageIds = JSON.parse($row.find('.item-image-ids').val() || '[]');
		imageIds = imageIds.filter(function(id) {
			return id != imageId;
		});

		$row.find('.item-image-ids').val(JSON.stringify(imageIds));
		$row.find('[data-image-id="' + imageId + '"]').remove();
	}

	/**
	 * Create Menu Element HTML
	 */
	function createMenuElement(menu) {
		var html = '<div class="mega-menu-item" data-menu-id="' + menu.id + '">' +
			'<div class="menu-header">' +
			'<h3>' + menu.title + '</h3>' +
			'<div class="menu-actions">' +
			'<span class="shortcode-display">' +
			'<strong>Shortcode:</strong> ' +
			'<code>[mega_menu shortcode="' + menu.shortcode + '"]</code>' +
			'<button type="button" class="button button-small copy-shortcode" title="Copy to clipboard">Copy</button>' +
			'</span>' +
			'<button type="button" class="button button-small delete-menu" data-menu-id="' + menu.id + '">Delete Menu</button>' +
			'</div>' +
			'</div>' +
			'<div class="menu-items-container">' +
			'<div class="menu-items-list"></div>' +
			'<button type="button" class="button button-secondary add-row-btn" data-menu-id="' + menu.id + '">+ Add Row</button>' +
			'</div>' +
			'</div>';

		return html;
	}

	/**
	 * Copy to Clipboard
	 */
	function copyToClipboard(text) {
		var $temp = $('<textarea>');
		$('body').append($temp);
		$temp.val(text).select();
		document.execCommand('copy');
		$temp.remove();
		alert('Shortcode copied to clipboard!');
	}

})(jQuery);
