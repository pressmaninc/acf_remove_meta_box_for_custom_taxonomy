<?php
/*
Plugin Name: ACF Remove Meta Box for Taxonomy
Description: ACFのタクソノミーフィールドに元の設定パネル／メタボックスを削除するオプションを追加する
Version: 1.1
*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Acf_Remove_Meta_Box_for_Custom_Taxonomy {
	protected static $_instance = null;

	public $remove_meta_boxes = [];
	public $remove_option_key = 'remove_meta_box_for_taxonomy';

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'acf/render_field_settings/type=taxonomy', array( $this, 'add_remove_meta_box_option' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor' ) );
		add_action( 'acf/render_fields', array( $this, 'remove_meta_boxes_for_block_editor' ) );
		add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes_for_legacy_editor' ) );
	}

	public function add_remove_meta_box_option( $field ) {
		$setting = [
			'label' => __('元のカスタムタクソノミー用のメタボックスを表示しない'),
			'name' => $this->remove_option_key,
			'type' => 'true_false',
			'ui' => 1,
		];
		acf_render_field_setting( $field, $setting );
	}

	public function enqueue_block_editor() {
		wp_enqueue_script( __CLASS__, plugin_dir_url(__FILE__) . "assets/js/editor.js", [ 'wp-blocks' ], "1.0", true );
	}

	public function remove_meta_boxes_for_block_editor( $fields ) {
		$remove_meta_boxes = [];
		foreach( $fields as $field ) {
			if ( isset( $field[ $this->remove_option_key ] ) && $field[ $this->remove_option_key ] ) {
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

	public function remove_meta_boxes_for_legacy_editor() {
		global $post_type, $hook_suffix, $current_screen;

		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) return;

		if ( $this->is_editor( $hook_suffix ) ) return;

		$screen = $post_type === 'post' ? 'post' : 'page';

		$field_groups = acf_get_field_groups( [ 'post_type' => $post_type ] );
		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group['ID'] );
			foreach ( $fields as $field ) {
				if ( isset( $field[ $this->remove_option_key ] ) && $field[ $this->remove_option_key ] ) {
					remove_meta_box( "tagsdiv-{$field['taxonomy']}", $screen, 'side' );
				}
			}
		}
	}

	public function is_editor( $hook_suffix ) {
		return $hook_suffix === 'post.php' || $hook_suffix === 'post-new.php';
	}
}
Acf_Remove_Meta_Box_for_Custom_Taxonomy::instance();
