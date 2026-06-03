<?php
/**
 * Public Mega Menu class for shortcodes and frontend
 */

class Mega_Menu {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct() {
		add_shortcode( 'mega_menu', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
	}
	
	/**
	 * Enqueue public CSS and JS
	 */
	public function enqueue_public_assets() {
		wp_enqueue_style(
			'mega-menu-public',
			MEGA_MENU_PLUGIN_URL . 'public/css/mega-menu.css',
			array(),
			MEGA_MENU_VERSION
		);
		
		wp_enqueue_script(
			'mega-menu-public',
			MEGA_MENU_PLUGIN_URL . 'public/js/mega-menu.js',
			array( 'jquery' ),
			MEGA_MENU_VERSION,
			true
		);
	}
	
	/**
	 * Render mega menu shortcode
	 * Usage: [mega_menu shortcode="sports_xyz123"]
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'shortcode' => '',
			),
			$atts,
			'mega_menu'
		);
		
		if ( empty( $atts['shortcode'] ) ) {
			return '<p>' . esc_html__( 'Mega menu shortcode not specified.', 'mega-menu' ) . '</p>';
		}
		
		$menu = Mega_Menu_DB::get_menu_by_shortcode( $atts['shortcode'] );
		
		if ( ! $menu ) {
			return '<p>' . esc_html__( 'Mega menu not found.', 'mega-menu' ) . '</p>';
		}
		
		$menu_items = Mega_Menu_DB::get_menu_items( $menu->id );
		
		if ( empty( $menu_items ) ) {
			return '<p>' . esc_html__( 'No items in this mega menu.', 'mega-menu' ) . '</p>';
		}
		
		return $this->render_menu_html( $menu, $menu_items );
	}
	
	private function render_menu_html( $menu, $menu_items ) {

	ob_start();
	?>

	<div class="mega-menu-container">

		<div class="mega-menu-left">

			<ul class="mega-menu-title-list">

				<?php foreach ( $menu_items as $index => $item ) : ?>

					<li class="mega-menu-title-item"
						data-target="image-<?php echo esc_attr( $index ); ?>">

						<?php if ( ! empty( $item->link ) ) : ?>

							<a href="<?php echo esc_url( $item->link ); ?>">
								<?php echo esc_html( $item->heading ); ?>
							</a>

						<?php else : ?>

							<?php echo esc_html( $item->heading ); ?>

						<?php endif; ?>

					</li>

				<?php endforeach; ?>

			</ul>

		</div>

		<div class="mega-menu-right">

			<?php foreach ( $menu_items as $index => $item ) : ?>

				<?php

				// Get image metadata from new table
				$image_metadata = Mega_Menu_DB::get_image_metadata( $item->id );

				if ( empty( $image_metadata ) ) {
					continue;
				}

				?>

				<div class="mega-menu-image-wrapper <?php echo ( $index === 0 ) ? 'active' : ''; ?>"
					id="image-<?php echo esc_attr( $index ); ?>">

					<div class="mega-menu-image-grid">

						<?php foreach ( $image_metadata as $img_meta ) : ?>

							<?php
							$image_id = $img_meta->image_id;
							$image_text = $img_meta->image_text;
							$image_url = $img_meta->image_url;
							
							$attachment_url = wp_get_attachment_image_url(
								$image_id,
								'large'
							);

							if ( ! $attachment_url ) {
								continue;
							}
							?>

							<div class="mega-menu-image-item">
								<?php if ( ! empty( $image_url ) ) : ?>
									<a href="<?php echo esc_url( $image_url ); ?>" class="mega-menu-image-link">
										<img
											src="<?php echo esc_url( $attachment_url ); ?>"
											class="mega-menu-preview-image"
											alt="<?php echo esc_attr( $image_text ); ?>"
										>
										<?php if ( ! empty( $image_text ) ) : ?>
											<span class="mega-menu-image-text"><?php echo esc_html( $image_text ); ?></span>
										<?php endif; ?>
									</a>
								<?php else : ?>
									<img
										src="<?php echo esc_url( $attachment_url ); ?>"
										class="mega-menu-preview-image"
										alt="<?php echo esc_attr( $image_text ); ?>"
									>
									<?php if ( ! empty( $image_text ) ) : ?>
										<span class="mega-menu-image-text"><?php echo esc_html( $image_text ); ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</div>

						<?php endforeach; ?>

					</div>

				</div>

			<?php endforeach; ?>

		</div>

	</div>

	<script>
	jQuery(function($){

		$('.mega-menu-title-item').on('mouseenter', function(){

			var target = $(this).data('target');

			$('.mega-menu-image-wrapper').removeClass('active');

			$('#' + target).addClass('active');

		});

	});
	</script>

	<?php

	return ob_get_clean();
}
}
?>
