<?php
/**
 * Server-side rendering of the `core/navigation-menu` block.
 *
 * @package gutenberg
 */

/**
 * Renders the `core/navigation-menu` block on server.
 *
 * @param array $attributes The block attributes.
 * @param array $content The saved content.
 * @param array $block The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function render_block_navigation_menu( $attributes, $content, $block ) {
	// Add CSS classes and inline styles.
	$bg_color_css_classes = '';
	if ( array_key_exists('backgroundColor', $attributes ) ) {
		$bg_color_css_classes .= ' has-background-color';
	}
	if ( array_key_exists('backgroundColorCSSClass', $attributes ) ) {
		$bg_color_css_classes .= " {$attributes['backgroundColorCSSClass']}";
	}
	$bg_color_css_classes = trim( $bg_color_css_classes );

	$text_color_css_classes = '';
	if ( array_key_exists('textColor', $attributes ) ) {
		$text_color_css_classes .= ' has-text-color';
	}

	if ( array_key_exists('textColorCSSClass', $attributes ) ) {
		$text_color_css_classes .= " ${attributes['textColorCSSClass']}";
	}
	$text_color_css_classes = trim( $text_color_css_classes );


	$bg_inline_styles = '';
	if ( array_key_exists('customBackgroundColor', $attributes ) ) {
		$bg_inline_styles = "background-color: ${attributes['customBackgroundColor']};";
	} elseif ( array_key_exists('backgroundColorValue', $attributes ) ) {
		$bg_inline_styles = "background-color: ${attributes['backgroundColorValue']};";
	}

	$text_inline_styles = '';
	if ( array_key_exists('textColorValue', $attributes ) ) {
		$text_inline_styles = " color: ${attributes['textColorValue']};";
	} elseif ( array_key_exists('customTextColor', $attributes ) ) {
		$text_inline_styles = " color: ${attributes['customTextColor']};";
	}

	return
		'<nav class="wp-block-navigation-menu">' .
			build_navigation_menu_html(
				$block,
				$bg_color_css_classes,
				$text_color_css_classes,
				$bg_inline_styles,
				$text_inline_styles
			) .
		'</nav>';
}

/**
 * Walks the inner block structure and returns an HTML list for it.
 *
 * @param {array}   $block          The block.
 * @param {string}  $bg_css         Background color CSS classes.
 * @param {string}  $text_css       Text color CSS classes.
 * @param {string}  $bg_styles      Background color inline styles.
 * @param {string}  $text_styles    Text color inline styles.
 *
 * @return string Returns  an HTML list from innerBlocks.
 */
function build_navigation_menu_html( $block, $bg_css, $text_css, $bg_styles, $text_styles ) {
	$html = '';
	foreach ( (array) $block['innerBlocks'] as $key => $menu_item ) {
		$html .= "<li style='$bg_styles $text_styles' class='wp-block-navigation-menu-item $bg_css'>" .
			"<a class='wp-block-navigation-menu-link $text_css'";
		if ( isset( $menu_item['attrs']['destination'] ) ) {
			$html .= ' href="' . $menu_item['attrs']['destination'] . '"';
		}
		if ( isset( $menu_item['attrs']['title'] ) ) {
			$html .= ' title="' . $menu_item['attrs']['title'] . '"';
		}
		$html .= '>';
		if ( isset( $menu_item['attrs']['label'] ) ) {
			$html .= $menu_item['attrs']['label'];
		}
		$html .= '</a>';

		if ( count( (array) $menu_item['innerBlocks'] ) > 0 ) {
			$html .= build_navigation_menu_html( $menu_item, $bg_css, $text_css, $bg_styles, $text_styles );
		}

		$html .= '</li>';
	}
	return '<ul>' . $html . '</ul>';
}

/**
 * Register the navigation menu block.
 *
 * @uses render_block_navigation_menu()
 */
function register_block_core_navigation_menu() {
	$block_content = file_get_contents ( dirname( __FILE__ ) . '/../../../packages/block-library/src/navigation-menu/block.json' );
	if ( ! $block_content ) {
		throw new Error(
			'block.json file not found'
		);
	}
	$block_definition = json_decode( $block_content, true );
	if( is_null( $block_definition ) ) {
		throw new Error(
			'Unable to parse block.json file'
		);
	}

	// Pick up block name and remove it from the block-definition object.
	$block_name = $block_definition['name'];
	unset( $block_definition['name'] );

	// Add render callback into block-definition object.
	$block_definition['render_callback'] = 'render_block_navigation_menu';

	register_block_type(
		$block_name,
		$block_definition
	);
}

add_action( 'init', 'register_block_core_navigation_menu' );
