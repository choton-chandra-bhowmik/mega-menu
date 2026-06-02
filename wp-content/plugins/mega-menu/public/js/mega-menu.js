/* Mega Menu Frontend JavaScript */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Add lightbox functionality to images
		initImageLightbox();
		
		// Smooth scroll for links
		initSmoothScroll();
	});

	/**
	 * Initialize image lightbox
	 */
	function initImageLightbox() {
		$(document).on('click', '.mega-menu-item-image', function() {
			var imageSrc = $(this).attr('src');
			var imageAlt = $(this).attr('alt');
			
			// Create lightbox
			var lightbox = $('<div class="mega-menu-lightbox">' +
				'<div class="mega-menu-lightbox-content">' +
				'<img src="' + imageSrc + '" alt="' + imageAlt + '" />' +
				'<button class="mega-menu-lightbox-close">×</button>' +
				'</div>' +
				'</div>');
			
			$('body').append(lightbox);
			
			// Close on click
			lightbox.on('click', function(e) {
				if (e.target === this) {
					lightbox.fadeOut(function() {
						$(this).remove();
					});
				}
			});
			
			// Close button
			lightbox.find('.mega-menu-lightbox-close').on('click', function() {
				lightbox.fadeOut(function() {
					$(this).remove();
				});
			});
			
			// Keyboard support
			$(document).on('keyup.megamenu', function(e) {
				if (e.keyCode === 27) { // Escape key
					lightbox.fadeOut(function() {
						$(this).remove();
					});
					$(document).off('keyup.megamenu');
				}
			});
		});
	}

	/**
	 * Initialize smooth scroll
	 */
	function initSmoothScroll() {
		$(document).on('click', '.mega-menu-row-heading a', function(e) {
			var href = $(this).attr('href');
			
			// Check if it's an internal anchor link
			if (href && href.startsWith('#')) {
				e.preventDefault();
				var target = $(href);
				
				if (target.length) {
					$('html, body').animate({
						scrollTop: target.offset().top - 50
					}, 800);
				}
			}
		});
	}

	// Add dynamic content loading if needed
	window.megaMenu = {
		init: function() {
			initImageLightbox();
			initSmoothScroll();
		}
	};

})(jQuery);
