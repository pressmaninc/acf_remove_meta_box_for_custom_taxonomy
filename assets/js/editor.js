
class Acf_Remove_Meta_Box_for_Custom_Taxonomy {
	constructor() {
		this.removeMetaBox();
	}

	removeMetaBox = () => {
		wp.data.dispatch( 'core/edit-post' ).removeEditorPanel( 'taxonomy-panel-hackathon2022' );
		// if ( acf_remove_meta_box_for_custom_taxonomy === undefined ) return;

		// acf_remove_meta_box_for_custom_taxonomy.forEach( taxonomy => {
		// 	wp.data.dispatch( 'core/edit-post' ).removeEditorPanel( 'taxonomy-panel-' + taxonomy );
		// } );
	}
}
new Acf_Remove_Meta_Box_for_Custom_Taxonomy();
