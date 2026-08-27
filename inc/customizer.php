<?php
/**
 * Customizer: colors, typography, header, footer, homepage, ad zones.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Font pair presets. Keys are stored in the `sw_font_pair` theme_mod.
 */
function snailworld_font_pairs() {
	return array(
		'garden'  => array(
			'label'          => __( 'Fraunces + Public Sans', 'snailworld' ),
			'heading_family' => '"Fraunces", "Noto Serif", serif',
			'body_family'    => '"Public Sans", "Noto Sans", sans-serif',
			'sample'         => __( 'Aa — warm, editorial headings', 'snailworld' ),
			'google'         => 'family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Public+Sans:wght@400;500;600;700&display=swap',
		),
		'meadow'  => array(
			'label'          => __( 'Literata + Inter', 'snailworld' ),
			'heading_family' => '"Literata", serif',
			'body_family'    => '"Inter", sans-serif',
			'sample'         => __( 'Aa — calm, book-like reading', 'snailworld' ),
			'google'         => 'family=Literata:wght@400;500;600;700&family=Inter:wght@400;500;700&display=swap',
		),
		'orchard' => array(
			'label'          => __( 'DM Serif Display + Karla', 'snailworld' ),
			'heading_family' => '"DM Serif Display", serif',
			'body_family'    => '"Karla", sans-serif',
			'sample'         => __( 'Aa — bold, playful contrast', 'snailworld' ),
			'google'         => 'family=DM+Serif+Display:wght@400&family=Karla:wght@400;500;700&display=swap',
		),
	);
}

/**
 * Build the Google Fonts stylesheet URL for the active pair.
 */
function snailworld_get_font_pair_url() {
	$pairs = snailworld_font_pairs();
	$key   = get_theme_mod( 'sw_font_pair', 'garden' );
	$pair  = isset( $pairs[ $key ] ) ? $pairs[ $key ] : $pairs['garden'];

	return 'https://fonts.googleapis.com/css2?' . $pair['google'];
}

/**
 * Central place for every color setting: [ mod_id, default, label ].
 */
function snailworld_color_settings() {
	return array(
		'sw_color_base'      => array( '#F7F3E7', __( 'Base / Background (garden cream)', 'snailworld' ) ),
		'sw_color_primary'   => array( '#52734D', __( 'Primary (moss / shell green)', 'snailworld' ) ),
		'sw_color_secondary' => array( '#8A5E3C', __( 'Secondary (soil / shell brown)', 'snailworld' ) ),
		'sw_color_accent'    => array( '#E08966', __( 'Accent (soft coral)', 'snailworld' ) ),
		'sw_color_text'      => array( '#2A2823', __( 'Text (deep charcoal ink)', 'snailworld' ) ),
		'sw_color_highlight' => array( '#D9AE5D', __( 'Highlight / CTA (warm sand gold)', 'snailworld' ) ),
	);
}

function snailworld_color_settings_dark() {
	return array(
		'sw_color_base_dark'      => array( '#1D2119', __( 'Base / Background — dark', 'snailworld' ) ),
		'sw_color_primary_dark'   => array( '#8FB284', __( 'Primary — dark', 'snailworld' ) ),
		'sw_color_secondary_dark' => array( '#C79A6E', __( 'Secondary — dark', 'snailworld' ) ),
		'sw_color_accent_dark'    => array( '#EDA07E', __( 'Accent — dark', 'snailworld' ) ),
		'sw_color_text_dark'      => array( '#ECE7D9', __( 'Text — dark', 'snailworld' ) ),
		'sw_color_highlight_dark' => array( '#E8C57E', __( 'Highlight / CTA — dark', 'snailworld' ) ),
	);
}

/**
 * Register everything.
 */
function snailworld_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport       = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	/* -----------------------------------------------------------
	 * 1. Colors — light
	 * --------------------------------------------------------- */
	$wp_customize->add_panel( 'sw_design_panel', array(
		'title'    => __( 'Garden Design', 'snailworld' ),
		'priority' => 25,
	) );

	$wp_customize->add_section( 'sw_colors_light', array(
		'title' => __( 'Colors — Light Mode', 'snailworld' ),
		'panel' => 'sw_design_panel',
	) );

	foreach ( snailworld_color_settings() as $id => $conf ) {
		list( $default, $label ) = $conf;
		$wp_customize->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
			'label'   => $label,
			'section' => 'sw_colors_light',
		) ) );
	}

	/* -----------------------------------------------------------
	 * 2. Colors — dark
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_colors_dark', array(
		'title' => __( 'Colors — Dark Mode', 'snailworld' ),
		'panel' => 'sw_design_panel',
	) );

	foreach ( snailworld_color_settings_dark() as $id => $conf ) {
		list( $default, $label ) = $conf;
		$wp_customize->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => 'sanitize_hex_color',
			// Refresh (not postMessage): the preview iframe is usually in
			// light mode, so there's no live element to patch — a refresh
			// still shows the change accurately whenever dark mode is active.
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
			'label'   => $label,
			'section' => 'sw_colors_dark',
		) ) );
	}

	$wp_customize->add_setting( 'sw_dark_mode_default', array(
		'default'           => 'auto',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'sw_dark_mode_default', array(
		'type'    => 'select',
		'section' => 'sw_colors_dark',
		'label'   => __( 'Default appearance', 'snailworld' ),
		'choices' => array(
			'auto'  => __( 'Match visitor system setting', 'snailworld' ),
			'light' => __( 'Always light', 'snailworld' ),
			'dark'  => __( 'Always dark', 'snailworld' ),
		),
	) );

	/* -----------------------------------------------------------
	 * 3. Typography
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_typography', array(
		'title' => __( 'Typography', 'snailworld' ),
		'panel' => 'sw_design_panel',
	) );

	$font_choices = array();
	foreach ( snailworld_font_pairs() as $key => $pair ) {
		$font_choices[ $key ] = $pair;
	}
	$wp_customize->add_setting( 'sw_font_pair', array(
		'default'           => 'garden',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( new SnailWorld_Radio_Card_Control( $wp_customize, 'sw_font_pair', array(
		'label'   => __( 'Font pair', 'snailworld' ),
		'section' => 'sw_typography',
		'choices' => $font_choices,
	) ) );

	$wp_customize->add_setting( 'sw_font_size_base', array(
		'default'           => 100,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new SnailWorld_Range_Control( $wp_customize, 'sw_font_size_base', array(
		'label'       => __( 'Base font size', 'snailworld' ),
		'description' => __( '% of 1rem (16px). Scales the whole site.', 'snailworld' ),
		'section'     => 'sw_typography',
		'input_attrs' => array( 'min' => 85, 'max' => 120, 'step' => 1 ),
	) ) );

	/* -----------------------------------------------------------
	 * 4. Texture
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_texture', array(
		'title' => __( 'Garden Texture', 'snailworld' ),
		'panel' => 'sw_design_panel',
	) );
	$wp_customize->add_setting( 'sw_enable_texture', array(
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_enable_texture', array(
		'type'    => 'checkbox',
		'section' => 'sw_texture',
		'label'   => __( 'Enable subtle dew/texture background overlay', 'snailworld' ),
	) );

	/* -----------------------------------------------------------
	 * 5. Header
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_header', array(
		'title'    => __( 'Header', 'snailworld' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'sw_header_logo_position', array(
		'default'           => 'left',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'sw_header_logo_position', array(
		'type'    => 'radio',
		'section' => 'sw_header',
		'label'   => __( 'Logo position', 'snailworld' ),
		'choices' => array(
			'left'   => __( 'Left', 'snailworld' ),
			'center' => __( 'Center', 'snailworld' ),
		),
	) );

	$wp_customize->add_setting( 'sw_header_sticky', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_header_sticky', array(
		'type'    => 'checkbox',
		'section' => 'sw_header',
		'label'   => __( 'Sticky header on scroll', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_header_cta_enable', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_header_cta_enable', array(
		'type'    => 'checkbox',
		'section' => 'sw_header',
		'label'   => __( 'Show header CTA button', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_header_cta_text', array(
		'default'           => __( 'Explore Garden', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_header_cta_text', array(
		'type'    => 'text',
		'section' => 'sw_header',
		'label'   => __( 'CTA button text', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_header_cta_url', array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sw_header_cta_url', array(
		'type'    => 'url',
		'section' => 'sw_header',
		'label'   => __( 'CTA button URL', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_enable_dark_toggle', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_enable_dark_toggle', array(
		'type'    => 'checkbox',
		'section' => 'sw_header',
		'label'   => __( 'Show dark-mode toggle in header', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_enable_live_search', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_enable_live_search', array(
		'type'    => 'checkbox',
		'section' => 'sw_header',
		'label'   => __( 'Show AJAX live search icon in header', 'snailworld' ),
	) );

	/* -----------------------------------------------------------
	 * 6. Footer
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_footer', array(
		'title'    => __( 'Footer', 'snailworld' ),
		'priority' => 35,
	) );

	$wp_customize->add_setting( 'sw_footer_copyright', array(
		'default'           => sprintf( __( '© %s SnailWorld. All rights reserved.', 'snailworld' ), '[year]' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_footer_copyright', array(
		'type'        => 'text',
		'section'     => 'sw_footer',
		'label'       => __( 'Copyright text', 'snailworld' ),
		'description' => __( 'Use [year] to auto-insert the current year.', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_footer_about_heading', array(
		'default'           => __( 'About', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_footer_about_heading', array(
		'type'    => 'text',
		'section' => 'sw_footer',
		'label'   => __( 'Footer "About" column heading', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_footer_about_text', array(
		'default'           => __( 'SnailWorld started as one small backyard obsession with garden snails and grew into a home for patient, slow-paced gardening — species profiles, feeding guides, and honest care tips for anyone tending a garden (or a terrarium) one quiet day at a time.', 'snailworld' ),
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'sw_footer_about_text', array(
		'type'    => 'textarea',
		'section' => 'sw_footer',
		'label'   => __( 'Footer "About" column text', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_footer_links_enable', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_footer_links_enable', array(
		'type'        => 'checkbox',
		'section'     => 'sw_footer',
		'label'       => __( 'Show a "Quick Links" footer column listing every published page', 'snailworld' ),
		'description' => __( 'Automatic — no menu setup needed. Turn off if you\'d rather add your own Navigation Menu widget instead.', 'snailworld' ),
	) );

	$socials = array(
		'facebook'  => __( 'Facebook URL', 'snailworld' ),
		'instagram' => __( 'Instagram URL', 'snailworld' ),
		'twitter'   => __( 'X / Twitter URL', 'snailworld' ),
		'youtube'   => __( 'YouTube URL', 'snailworld' ),
		'pinterest' => __( 'Pinterest URL', 'snailworld' ),
	);
	foreach ( $socials as $key => $label ) {
		$id = 'sw_social_' . $key;
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( $id, array(
			'type'    => 'url',
			'section' => 'sw_footer',
			'label'   => $label,
		) );
	}

	/* -----------------------------------------------------------
	 * 6b. Newsletter (footer)
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_newsletter', array(
		'title'       => __( 'Newsletter (Footer)', 'snailworld' ),
		'description' => __( 'Works out of the box — signups are stored in wp-admin under "Subscribers" and you get an email for each one. To use Mailchimp/Brevo/etc instead, paste that service\'s signup-form embed code below and it takes over automatically.', 'snailworld' ),
		'priority'    => 36,
	) );

	$wp_customize->add_setting( 'sw_newsletter_enable', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_newsletter_enable', array(
		'type'    => 'checkbox',
		'section' => 'sw_newsletter',
		'label'   => __( 'Show newsletter signup in the footer', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_newsletter_heading', array(
		'default'           => __( 'Join the Garden Newsletter', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_newsletter_heading', array(
		'type'    => 'text',
		'section' => 'sw_newsletter',
		'label'   => __( 'Heading', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_newsletter_subtext', array(
		'default'           => __( 'Slow-living garden tips, once in a while — no spam.', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_newsletter_subtext', array(
		'type'    => 'text',
		'section' => 'sw_newsletter',
		'label'   => __( 'Subtext', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_newsletter_embed_code', array(
		'default'           => '',
		'sanitize_callback' => 'snailworld_sanitize_ad_code',
	) );
	$wp_customize->add_control( new SnailWorld_Textarea_Control( $wp_customize, 'sw_newsletter_embed_code', array(
		'label'       => __( 'Signup form embed code', 'snailworld' ),
		'description' => __( 'Raw HTML/JS embed code from Mailchimp, Brevo, ConvertKit, etc.', 'snailworld' ),
		'section'     => 'sw_newsletter',
	) ) );

	/* -----------------------------------------------------------
	 * 7. Homepage
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'sw_homepage', array(
		'title'    => __( 'Homepage Sections', 'snailworld' ),
		'priority' => 40,
	) );

	$wp_customize->add_setting( 'sw_hero_enable', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_hero_enable', array(
		'type'    => 'checkbox',
		'section' => 'sw_homepage',
		'label'   => __( 'Show hero section', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_hero_eyebrow', array(
		'default'           => __( 'Slow Living, Small Gardens', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_hero_eyebrow', array(
		'type' => 'text', 'section' => 'sw_homepage', 'label' => __( 'Hero eyebrow text', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_hero_heading', array(
		'default'           => __( 'Take it slow. Let the garden lead.', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_hero_heading', array(
		'type' => 'text', 'section' => 'sw_homepage', 'label' => __( 'Hero heading', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_hero_subheading', array(
		'default'           => __( 'Guides, garden creatures, and grow-at-your-own-pace tips for anyone tending a small patch of green.', 'snailworld' ),
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'sw_hero_subheading', array(
		'type' => 'textarea', 'section' => 'sw_homepage', 'label' => __( 'Hero subheading', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_hero_cta_text', array(
		'default'           => __( 'Start Exploring', 'snailworld' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_hero_cta_text', array(
		'type' => 'text', 'section' => 'sw_homepage', 'label' => __( 'Hero primary button text', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_hero_cta_url', array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sw_hero_cta_url', array(
		'type' => 'url', 'section' => 'sw_homepage', 'label' => __( 'Hero primary button URL', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_hero_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sw_hero_image', array(
		'label'   => __( 'Hero image', 'snailworld' ),
		'section' => 'sw_homepage',
	) ) );

	$wp_customize->add_setting( 'sw_featured_count', array(
		'default'           => 6,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( new SnailWorld_Range_Control( $wp_customize, 'sw_featured_count', array(
		'label'       => __( 'Featured / latest posts count', 'snailworld' ),
		'section'     => 'sw_homepage',
		'input_attrs' => array( 'min' => 3, 'max' => 12, 'step' => 1 ),
	) ) );

	$wp_customize->add_setting( 'sw_category_grid_enable', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_category_grid_enable', array(
		'type'    => 'checkbox',
		'section' => 'sw_homepage',
		'label'   => __( 'Show category icon grid', 'snailworld' ),
	) );

	$wp_customize->add_setting( 'sw_enable_toc', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sw_enable_toc', array(
		'type'    => 'checkbox',
		'section' => 'sw_homepage',
		'label'   => __( 'Enable auto table of contents on posts', 'snailworld' ),
	) );

	/* -----------------------------------------------------------
	 * 8. Ad Zones
	 * --------------------------------------------------------- */
	$wp_customize->add_panel( 'sw_ads_panel', array(
		'title'       => __( 'AdSense Zones', 'snailworld' ),
		'description' => __( 'Toggle ad zones and paste your AdSense (or any) ad unit code. Remember to add your ads.txt Publisher ID below.', 'snailworld' ),
		'priority'    => 45,
	) );

	$zones = array(
		'header'       => __( 'Header banner (below menu)', 'snailworld' ),
		'in_content_1' => __( 'In-content — after 3rd paragraph', 'snailworld' ),
		'in_content_2' => __( 'In-content — after 8th paragraph', 'snailworld' ),
		'sidebar'      => __( 'Sidebar', 'snailworld' ),
		'footer'       => __( 'Footer (above widgets)', 'snailworld' ),
	);

	$wp_customize->add_section( 'sw_ads_general', array(
		'title' => __( 'Ads.txt / Publisher', 'snailworld' ),
		'panel' => 'sw_ads_panel',
	) );
	$wp_customize->add_setting( 'sw_adsense_publisher_id', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sw_adsense_publisher_id', array(
		'type'        => 'text',
		'section'     => 'sw_ads_general',
		'label'       => __( 'AdSense Publisher ID (pub-XXXXXXXXXXXXXXXX)', 'snailworld' ),
		'description' => __( 'When set, the theme automatically serves a valid /ads.txt at your domain root.', 'snailworld' ),
	) );

	foreach ( $zones as $zone_key => $zone_label ) {
		$section_id = 'sw_ad_zone_' . $zone_key;
		$wp_customize->add_section( $section_id, array(
			'title' => $zone_label,
			'panel' => 'sw_ads_panel',
		) );

		$enable_id = 'sw_ad_enable_' . $zone_key;
		$wp_customize->add_setting( $enable_id, array(
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		) );
		$wp_customize->add_control( $enable_id, array(
			'type'    => 'checkbox',
			'section' => $section_id,
			/* translators: %s: ad zone name. */
			'label'   => sprintf( __( 'Enable %s ad zone', 'snailworld' ), $zone_label ),
		) );

		$code_id = 'sw_ad_code_' . $zone_key;
		$wp_customize->add_setting( $code_id, array(
			'default'           => '',
			'sanitize_callback' => 'snailworld_sanitize_ad_code',
		) );
		$wp_customize->add_control( new SnailWorld_Textarea_Control( $wp_customize, $code_id, array(
			'label'       => __( 'Ad unit code', 'snailworld' ),
			'description' => __( 'Paste your AdSense <script>/<ins> ad unit code here.', 'snailworld' ),
			'section'     => $section_id,
		) ) );
	}
}
add_action( 'customize_register', 'snailworld_customize_register' );

/**
 * Ad code is trusted admin-entered markup (AdSense snippets require raw
 * <script>/<ins> tags), so we only strip disallowed protocols and keep the
 * rest — same trust model as the WP core "Additional CSS"/custom HTML block
 * for logged-in `unfiltered_html` users. Non-privileged users can't reach
 * this setting since it's admin/Customizer only.
 */
function snailworld_sanitize_ad_code( $code ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		return $code;
	}
	return wp_kses_post( $code );
}

/**
 * Live-preview support (postMessage) for a handful of fast-changing settings.
 */
function snailworld_customize_preview_js() {
	wp_enqueue_script( 'snailworld-customizer-preview', SNAILWORLD_URI . '/assets/js/customizer-preview.js', array( 'customize-preview' ), SNAILWORLD_VERSION, true );
}
add_action( 'customize_preview_init', 'snailworld_customize_preview_js' );
