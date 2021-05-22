<?php

$cover_header_style = '';
$cover_header_classes = '';

$color_overlay_style = '';
$color_overlay_classes = '';

$section_inner_classes = '';

$image_url = ! post_password_required() ? get_the_post_thumbnail_url( $post->ID, 'chaplin_fullscreen' ) : '';

if ( $image_url ) {
	$cover_header_style 	= ' style="background-image: url( \'' . esc_url( $image_url ) . '\' );"';
	$cover_header_classes 	= ' bg-image';
}

// Get the color used for the color overlay
$color_overlay_color = get_theme_mod( 'chaplin_cover_template_overlay_background_color' );
if ( $color_overlay_color ) {
	$color_overlay_style = ' style="color: ' . esc_attr( $color_overlay_color ) . ';"';
} else {
	$color_overlay_style = '';
}

// Note: The text color is applied by chaplin_get_customizer_css(), in functions.php.

// Get the fixed background attachment option.
if ( get_theme_mod( 'chaplin_cover_template_fixed_background', true ) ) {
	$cover_header_classes .= ' bg-attachment-fixed';
}

// Get the opacity of the color overlay.
$color_overlay_opacity = get_theme_mod( 'chaplin_cover_template_overlay_opacity' );
$color_overlay_opacity = ( $color_overlay_opacity === false ) ? 80 : $color_overlay_opacity;
$color_overlay_classes .= ' opacity-' . $color_overlay_opacity;

// Get the blend mode of the color overlay (default = multiply).
$color_overlay_opacity = get_theme_mod( 'chaplin_cover_template_overlay_blend_mode', 'multiply' );
$color_overlay_classes .= ' blend-mode-' . $color_overlay_opacity;

// Check whether we're fading the text.
$overlay_fade_text = get_theme_mod( 'chaplin_cover_template_fade_text', true );
$section_inner_classes = $overlay_fade_text ? ' fade-block' : '';

?>

<div class="cover-header screen-height screen-width<?php echo esc_attr( $cover_header_classes ); ?>"<?php echo $cover_header_style; ?>>
	<div class="cover-header-inner-wrapper">
		<div class="cover-header-inner">
			<div class="cover-color-overlay color-accent<?php echo esc_attr( $color_overlay_classes ); ?>"<?php echo $color_overlay_style; ?>></div>
			<?php if ( $image_url ) the_post_thumbnail( 'chaplin_fullscreen' ); ?>
			<div class="section-inner<?php echo esc_attr( $section_inner_classes ); ?>">
				<?php get_template_part( 'parts/page-header' ); ?>
			</div><!-- .section-inner -->
		</div><!-- .cover-header-inner -->
	</div><!-- .cover-header-inner-wrapper -->
</div><!-- .cover-header -->

<?php
