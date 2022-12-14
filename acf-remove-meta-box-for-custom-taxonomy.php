<?php
/*
Plugin Name: ACF Remove Meta Box for Taxonomy
Description: 
Version: 1.0
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Acf_Remove_Meta_Box_for_Custom_Taxonomy {
	protected static $_instance = null;

	public $remove_meta_boxes = [];

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		// add_action( 'admin_menu', array( $this, 'test' ) );
		add_action( 'acf/render_field_settings/type=taxonomy', array( $this, '_render_field_settings' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
		add_action( 'acf/render_fields', array( $this, 'render_field' ) );
	}

	// クラシックエディタ用
	// public function test() {
	// 	remove_meta_box( 'tagsdiv-hackathon2022', 'page', 'side' );
	// }

	public function _render_field_settings( $field ) {
		$setting = [
			'label' => __('元のカスタムタクソノミー用のメタボックスを表示しない', 'domain'),
			'name' => 'remove_meta_box_for_taxonomy',
			'type' => 'true_false',
			'ui' => 1,
		];
		acf_render_field_setting( $field, $setting );
	}

	public function enqueue() {
		wp_enqueue_script( __CLASS__, plugin_dir_url(__FILE__) . "assets/js/editor.js", [ 'wp-blocks', 'wp-edit-post' ], "1.0", true );
	}

	public function render_field( $fields ) {
		$remove_meta_boxes = [];
		foreach( $fields as $field ) {
			if ( isset( $field['remove_meta_box_for_taxonomy'] ) && $field['remove_meta_box_for_taxonomy'] ) {
				$remove_meta_boxes[] = $field['taxonomy'];
			}
		}

		if ( empty( $remove_meta_boxes ) ) return;

		$script = '<script>
		const acf_remove_meta_box_for_custom_taxonomy = ["';
		$script .= implode('","', $remove_meta_boxes);
		$script .= '"];</script>';
		wp_add_inline_script( __CLASS__, $script );
	}


}
Acf_Remove_Meta_Box_for_Custom_Taxonomy::instance();
