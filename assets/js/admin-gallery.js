/**
 * Property gallery meta box: pick and order images in the core media modal.
 */
( function ( $ ) {
	'use strict';

	var settings = window.estateinGallery || {};

	$( function () {
		var $input = $( '[data-estatein-gallery-input]' );
		var $list = $( '[data-estatein-gallery-list]' );
		var frame;

		// Block editor: store the IDs as post meta through the editor store, so
		// "Update" becomes active and the gallery saves with the post. The hidden
		// input still covers the Classic Editor.
		function store( ids ) {
			$input.val( ids.join( ',' ) );
			try {
				var editor = window.wp && wp.data && wp.data.dispatch( 'core/editor' );
				if ( editor && editor.editPost ) {
					editor.editPost( { meta: { gallery: ids.map( Number ) } } );
				}
			} catch ( e ) {
				// Classic Editor: no editor store; the hidden input is saved with the form.
			}
		}

		function render( attachments ) {
			$list.empty();
			attachments.forEach( function ( a ) {
				var sizes = a.sizes || {};
				var src = ( sizes.thumbnail || sizes.full || {} ).url || a.url;
				$( '<li>' ).append( $( '<img>', { src: src, alt: '', width: 80, height: 80, css: { objectFit: 'cover' } } ) ).appendTo( $list );
			} );
		}

		$( '[data-estatein-gallery-pick]' ).on( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( {
					title: settings.title,
					button: { text: settings.button },
					library: { type: 'image' },
					multiple: 'add',
				} );

				// Pre-select the current images when the modal opens.
				frame.on( 'open', function () {
					var selection = frame.state().get( 'selection' );
					selection.reset();
					( $input.val() || '' ).split( ',' ).filter( Boolean ).forEach( function ( id ) {
						var attachment = wp.media.attachment( id );
						attachment.fetch();
						selection.add( attachment );
					} );
				} );

				frame.on( 'select', function () {
					var attachments = frame.state().get( 'selection' ).toJSON();
					store( attachments.map( function ( a ) {
						return a.id;
					} ) );
					render( attachments );
				} );
			}
			frame.open();
		} );

		$( '[data-estatein-gallery-clear]' ).on( 'click', function () {
			store( [] );
			$list.empty();
		} );
	} );
}( jQuery ) );
