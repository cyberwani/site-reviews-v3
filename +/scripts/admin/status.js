/** global: GLSR, jQuery */
;(function( x ) {

	'use strict';

	var Status = function( selector ) {
		var elements = document.querySelectorAll( selector );
		if( !elements.length )return;
		elements.forEach( function( el ) {
			el.addEventListener( 'click', this.onClick_ );
		}.bind( this ));
	};

	Status.prototype = {
		/** @return void */
		onClick_: function( ev ) { // MouseEvent
			var post_id = ev.target.href.match( /post=([0-9]+)/ );
			var status = ev.target.href.match( /action=([a-z]+)/ );
			if( post_id === null || status === null )return;
			var request = {
				action: 'change-review-status',
				nonce: GLSR.nonce['change-review-status'],
				post_id: post_id[1],
				status: status[1],
			};
			(new GLSR.Ajax( request, ev )).post( function( response ) {
				if( !response.class )return;
				var el = x( ev.target );
				el.closest( 'tr' ).removeClass( 'status-pending status-publish' ).addClass( response.class );
				el.closest( 'td.column-title' ).find( 'strong' ).html( response.link );
			});
		},
	};

	GLSR.Status = Status;
})( jQuery );
