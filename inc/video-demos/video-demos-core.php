<?php
/**
 * Video Production Demo Gallery — fully isolated module.
 *
 * Adds a "Video Demos" custom post type (admin-managed, own meta box,
 * own DB table via postmeta — no existing table touched) and appends a
 * demo gallery section to the live Video Production page's own content
 * via the `the_content` filter, scoped strictly to that one page. Does
 * not modify page-production content file, any existing CPT, or any
 * other page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function womensfight_register_video_demo_cpt() {
	register_post_type(
		'wf_video_demo',
		array(
			'labels'          => array(
				'name'          => 'Video Demos',
				'singular_name' => 'Video Demo',
				'menu_name'     => 'Video Demos',
				'all_items'     => 'All Video Demos',
				'add_new_item'  => 'Add New Video Demo',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-video-alt3',
			'menu_position'   => 30,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_video_demo_cpt' );

function womensfight_video_demo_categories() {
	return array( 'TVC', 'OVC', 'Corporate', 'Social Media Reel', 'Product Promo', 'Event Coverage' );
}

function womensfight_register_video_demo_detail_box() {
	add_meta_box( 'womensfight_video_demo_details', 'Video Demo Details', 'womensfight_render_video_demo_detail_box', 'wf_video_demo', 'normal', 'high' );
}
add_action( 'add_meta_boxes_wf_video_demo', 'womensfight_register_video_demo_detail_box' );

function womensfight_video_demo_enqueue_media( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
	if ( 'post.php' === $hook && isset( $_GET['post'] ) ) {
		$post_type = get_post_type( absint( $_GET['post'] ) );
	}
	if ( 'wf_video_demo' === $post_type ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'womensfight_video_demo_enqueue_media' );

function womensfight_render_video_demo_detail_box( $post ) {
	wp_nonce_field( 'womensfight_save_video_demo', 'womensfight_video_demo_nonce' );

	$category    = get_post_meta( $post->ID, 'wf_vd_category', true );
	$description = get_post_meta( $post->ID, 'wf_vd_description', true );
	$video_url   = get_post_meta( $post->ID, 'wf_vd_video_url', true );
	$thumbnail   = get_post_meta( $post->ID, 'wf_vd_thumbnail_id', true );
	?>
	<table class="widefat" style="margin-bottom:14px;">
		<tbody>
			<tr>
				<th style="width:180px;">Category</th>
				<td>
					<select name="wf_vd_category">
						<?php foreach ( womensfight_video_demo_categories() as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat ); ?>" <?php selected( $category, $cat ); ?>><?php echo esc_html( $cat ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Video URL</th>
				<td><input type="url" name="wf_vd_video_url" value="<?php echo esc_attr( $video_url ); ?>" class="regular-text" placeholder="YouTube বা Vimeo লিংক"></td>
			</tr>
			<tr>
				<th>Short Description</th>
				<td><textarea name="wf_vd_description" rows="3" class="large-text"><?php echo esc_textarea( $description ); ?></textarea></td>
			</tr>
		</tbody>
	</table>

	<p><strong>Thumbnail</strong></p>
	<div id="wf_vd_thumb_preview" style="margin-bottom:8px;">
		<?php if ( $thumbnail ) : ?>
			<?php echo wp_get_attachment_image( $thumbnail, 'medium' ); ?>
		<?php endif; ?>
	</div>
	<input type="hidden" name="wf_vd_thumbnail_id" id="wf_vd_thumbnail_id" value="<?php echo esc_attr( $thumbnail ); ?>">
	<button type="button" class="button" id="wf_vd_thumb_pick">ছবি সিলেক্ট করুন</button>
	<button type="button" class="button" id="wf_vd_thumb_remove" <?php echo $thumbnail ? '' : 'style="display:none;"'; ?>>Remove</button>
	<p class="description">Media Library থেকে সিলেক্ট বা নতুন আপলোড করুন (ভিডিওর থাম্বনেইল/কভার ছবি)।</p>
	<script>
	(function($){
		var frame;
		$('#wf_vd_thumb_pick').on('click', function(e){
			e.preventDefault();
			if (frame) { frame.open(); return; }
			frame = wp.media({ title: 'থাম্বনেইল সিলেক্ট করুন', multiple: false, library: { type: 'image' } });
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$('#wf_vd_thumbnail_id').val(att.id);
				$('#wf_vd_thumb_preview').html('<img src="' + (att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url) + '" style="max-width:300px; height:auto;">');
				$('#wf_vd_thumb_remove').show();
			});
			frame.open();
		});
		$('#wf_vd_thumb_remove').on('click', function(e){
			e.preventDefault();
			$('#wf_vd_thumbnail_id').val('');
			$('#wf_vd_thumb_preview').html('');
			$(this).hide();
		});
	})(jQuery);
	</script>
	<?php
}

function womensfight_save_video_demo_meta( $post_id ) {
	if ( ! isset( $_POST['womensfight_video_demo_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['womensfight_video_demo_nonce'] ), 'womensfight_save_video_demo' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['wf_vd_category'] ) && in_array( wp_unslash( $_POST['wf_vd_category'] ), womensfight_video_demo_categories(), true ) ) {
		update_post_meta( $post_id, 'wf_vd_category', sanitize_text_field( wp_unslash( $_POST['wf_vd_category'] ) ) );
	}
	if ( isset( $_POST['wf_vd_video_url'] ) ) {
		update_post_meta( $post_id, 'wf_vd_video_url', esc_url_raw( wp_unslash( $_POST['wf_vd_video_url'] ) ) );
	}
	if ( isset( $_POST['wf_vd_description'] ) ) {
		update_post_meta( $post_id, 'wf_vd_description', sanitize_textarea_field( wp_unslash( $_POST['wf_vd_description'] ) ) );
	}

	if ( isset( $_POST['wf_vd_thumbnail_id'] ) ) {
		$attachment_id = absint( $_POST['wf_vd_thumbnail_id'] );
		if ( $attachment_id && 'attachment' === get_post_type( $attachment_id ) ) {
			update_post_meta( $post_id, 'wf_vd_thumbnail_id', $attachment_id );
		} else {
			delete_post_meta( $post_id, 'wf_vd_thumbnail_id' );
		}
	}
}
add_action( 'save_post_wf_video_demo', 'womensfight_save_video_demo_meta' );

function womensfight_video_demo_columns( $columns ) {
	return array(
		'cb'          => $columns['cb'],
		'title'       => 'Video Name',
		'wf_vd_cat'   => 'Category',
		'date'        => $columns['date'],
	);
}
add_filter( 'manage_wf_video_demo_posts_columns', 'womensfight_video_demo_columns' );

function womensfight_video_demo_column_content( $column, $post_id ) {
	if ( 'wf_vd_cat' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'wf_vd_category', true ) );
	}
}
add_action( 'manage_wf_video_demo_posts_custom_column', 'womensfight_video_demo_column_content', 10, 2 );

function womensfight_get_video_demo_list() {
	return get_posts(
		array(
			'post_type'      => 'wf_video_demo',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

/**
 * Turn a YouTube/Vimeo watch URL into its embeddable iframe src.
 * Returns '' if the URL isn't a recognized YouTube/Vimeo link, so the
 * caller can simply skip rendering an iframe for that card.
 */
function womensfight_video_embed_url( $url ) {
	if ( preg_match( '~youtu\.be/([A-Za-z0-9_-]+)~', $url, $m ) || preg_match( '~youtube\.com/watch\?v=([A-Za-z0-9_-]+)~', $url, $m ) || preg_match( '~youtube\.com/shorts/([A-Za-z0-9_-]+)~', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}
	if ( preg_match( '~vimeo\.com/(\d+)~', $url, $m ) ) {
		return 'https://player.vimeo.com/video/' . $m[1];
	}
	return '';
}

/**
 * Insert the video demo gallery into the Video Production page's own
 * content, right after its page-header block — scoped strictly to that
 * one page slug, so every other page (including the ones Elementor has
 * fully rebuilt) is completely untouched.
 */
function womensfight_append_video_demo_gallery( $content ) {
	if ( ! is_page( 'video-production' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$demos = womensfight_get_video_demo_list();
	if ( ! $demos ) {
		return $content;
	}

	ob_start();
	?>
	<section class="tight wrap" id="wf-video-demo-gallery">
		<div class="section-head"><span class="eyebrow">আমাদের কাজ</span><h2>ভিডিও প্রোডাকশন ডেমো</h2><p>আগের কিছু কাজ দেখুন — প্লে চাপলেই ভিডিও চলবে।</p></div>
		<div class="wf-demo-grid">
			<?php foreach ( $demos as $demo ) :
				$cat         = get_post_meta( $demo->ID, 'wf_vd_category', true );
				$description = get_post_meta( $demo->ID, 'wf_vd_description', true );
				$video_url   = get_post_meta( $demo->ID, 'wf_vd_video_url', true );
				$thumb_id    = get_post_meta( $demo->ID, 'wf_vd_thumbnail_id', true );
				$embed_url   = $video_url ? womensfight_video_embed_url( $video_url ) : '';
				$name        = get_the_title( $demo->ID );
				?>
				<div class="wf-demo-card">
					<div class="wf-demo-shot">
						<?php if ( $thumb_id ) : ?>
							<?php echo wp_get_attachment_image( $thumb_id, 'medium_large' ); ?>
						<?php else : ?>
							<div class="wf-demo-shot-placeholder"><svg><use href="#i-film"/></svg></div>
						<?php endif; ?>
						<?php if ( $cat ) : ?><span class="wf-demo-tag"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
					</div>
					<div class="wf-demo-body">
						<h3><?php echo esc_html( $name ); ?></h3>
						<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
						<div class="wf-demo-actions">
							<?php if ( $embed_url ) : ?>
								<button type="button" class="btn btn-primary" onclick="wfOpenVideoDemo('<?php echo esc_js( $embed_url ); ?>');">ভিডিও দেখুন</button>
							<?php elseif ( $video_url ) : ?>
								<a class="btn btn-primary" href="<?php echo esc_url( $video_url ); ?>" target="_blank" rel="noopener">ভিডিও দেখুন</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	$gallery_html = ob_get_clean();

	ob_start();
	?>
	<div class="wf-modal-overlay" id="wfVideoDemoOverlay" onclick="if(event.target===this){wfCloseVideoDemo();}">
		<div class="wf-modal" style="max-width:860px; padding:0; background:transparent; box-shadow:none;">
			<button type="button" class="wf-modal-close" onclick="wfCloseVideoDemo();" aria-label="Close">&times;</button>
			<div style="aspect-ratio:16/9;">
				<iframe id="wfVideoDemoFrame" src="" style="width:100%; height:100%; border:0; border-radius:16px;" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
	</div>
	<script>
	function wfOpenVideoDemo(src) {
		document.getElementById('wfVideoDemoFrame').src = src + (src.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1';
		document.getElementById('wfVideoDemoOverlay').classList.add('open');
		document.body.style.overflow = 'hidden';
	}
	function wfCloseVideoDemo() {
		document.getElementById('wfVideoDemoFrame').src = '';
		document.getElementById('wfVideoDemoOverlay').classList.remove('open');
		document.body.style.overflow = '';
	}
	</script>
	<?php
	$modal_html = ob_get_clean();

	/* Drop the gallery right after the page-header block (the first
	   "</div></div>" closes it), so it shows near the top of the page
	   instead of after every other section. Falls back to a plain
	   append if that marker isn't found, so nothing ever disappears. */
	$marker_pos = strpos( $content, '</div></div>' );
	if ( false !== $marker_pos ) {
		$insert_at = $marker_pos + strlen( '</div></div>' );
		$content   = substr( $content, 0, $insert_at ) . $gallery_html . substr( $content, $insert_at );
	} else {
		$content .= $gallery_html;
	}

	return $content . $modal_html;
}
add_filter( 'the_content', 'womensfight_append_video_demo_gallery' );
