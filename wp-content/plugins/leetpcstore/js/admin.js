
var LEETPCStoreAdmin = {

	comInputEl: null,

	init: function() {

		this.comInputEl = jQuery( 'input.components-list-input' );

		jQuery( '#product_metabox .components-list .component' ).on( 'click', jQuery.proxy( this.calculatePrices, this ) );
		jQuery( '#product_metabox .components-list .group .title' ).on( 'click', this.toggleGroup );

		if ( this.comInputEl.length > 0 ) {
			this.importComponents();
		}

		this.finishedLoading();

		jQuery( '#product_metabox .components-list .group .title' ).trigger( 'click' );

	},

	toggleGroup: function() {
		jQuery( this ).parent().children( 'ul' ).first().toggle();
	},

	finishedLoading: function() {
		jQuery( '#product_metabox .loading-overlay' ).remove();
	},

	importComponents: function() {

		var c = this.comInputEl.val().split( ',' );

		if ( c.length > 0 ) {
	
			for ( var i = 0; i < c.length; i++ ) {
	
				var id = c[i];
				var def = id.match( /\*$/ );
	
				id = def ? id.substr( 0, id.length-1 ) : id;
				
				jQuery( 'li.' + id ).find( 'input[type=checkbox]' ).attr( 'checked', 'checked' );
	
				if ( def ) {
					jQuery( 'li.' + id ).find( 'input[type=radio]' ).attr( 'checked', 'checked' );
				}
	
			}
	
			this.calculatePrices();

		}

	},

	calculatePrices: function() {

		var that = this;
		var selectedComponents = this.getSelectedComponents();

		this.comInputEl.val( selectedComponents.toString() );
		jQuery( 'input.internal-cost' ).val( this.getTotalFor( selectedComponents, true ) + '.00' );
		jQuery( 'input.calculated-price' ).val( this.getTotalFor( selectedComponents, false ) + '.00' );

		jQuery( '#product_metabox .components-list' ).find( 'li.group > div.title' ).each( function() {

			jQuery( this ).find( '.values' ).html( '' );

			var group = jQuery( this ).text().toLowerCase();
			jQuery( this ).find( '.values' ).html( that.getSelectedComponents( group ).join( ', ' ) );

		} );

	},

	getSelectedComponents: function( groupName ) {

		var selectedComponents = [];

		jQuery( '#product_metabox .components-list li' + ( groupName ? '.group-' + groupName : '' ) + ' input[type=checkbox]' ).each( function( e ) {

			if ( jQuery( this ).hasClass( 'clear-value' ) ) return;

			if ( jQuery( this ).attr( 'checked' ) ) {
				var def = jQuery( this ).parents( 'li' ).first().find( 'input[type=radio]' ).attr( 'checked' );
				selectedComponents[selectedComponents.length] = jQuery( this ).attr( 'name' ) + ( def ? '*' : '' );
			}

		} );

		return selectedComponents;

	},

	getTotalFor: function( ids, returnMin ) {

		var total = 0;

		for ( var i = 0; i < ids.length; i++ ) {

			var id = ids[i];
			var def = ids[i].match( /\*$/ );

			id = def ? id.substr( 0, id.length-1 ) : id;
			var listItemEl = jQuery( 'li.' + id );
			var costEl = listItemEl.find( '.cost' );

			if ( def && costEl ) {

				var cost = costEl.text().replace( /\$/g, '' ).split( ' / ' );
				total += parseFloat( cost[returnMin?0:cost.length-1] );

			}

		}

		return total;

	}

};

jQuery( document ).ready( function() {

	LEETPCStoreAdmin.init();

} );