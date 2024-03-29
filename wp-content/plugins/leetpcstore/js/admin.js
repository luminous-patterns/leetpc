
var LEETPCStoreAdmin = {

	comInputEl: null,

	init: function() {

		this.comInputEl = jQuery( 'input.components-list-input' );
		this.invoiceMetaEl = jQuery( '#invoice_metabox' );

		jQuery( '#product_metabox .components-list .component' ).on( 'click', jQuery.proxy( this.calculatePrices, this ) );
		jQuery( '#product_metabox .components-list .group .title' ).on( 'click', this.toggleGroup );

		if ( this.comInputEl.length > 0 && this.comInputEl.val() ) {
			this.importComponents();
		}

		if ( this.invoiceMetaEl.length > 0 ) {
			this.processInvoiceMeta();
		}

		if ( jQuery( '#order_details_metabox' ).length ) {
			var orderDetailsMetaEl = this.orderDetailsMetaEl = jQuery( '#order_details_metabox' );
			var orderDetailsMetaTabEls = this.orderDetailsMetaTabEls = orderDetailsMetaEl.find( '.order-details-tabs li' );
			var orderDetailsMetaContentEls = this.orderDetailsMetaContentEls = orderDetailsMetaEl.find( '.tabs .tab-content' );
			orderDetailsMetaTabEls.each( function() {
				jQuery( this ).on( 'click', function() {
					orderDetailsMetaTabEls.removeClass( 'selected' );
					jQuery( this ).addClass( 'selected' );
					orderDetailsMetaContentEls.addClass( 'hidden' );
					orderDetailsMetaContentEls.filter( '.' + jQuery( this ).attr( 'data-tab-name' ) ).removeClass( 'hidden' );
				} );
			} );
		}

		this.finishedLoading();

		jQuery( '#product_metabox .components-list .group .title' ).trigger( 'click' );

	},

	processInvoiceMeta: function() {

		this.invoiceMetaEl.find( '.form-table td' ).each( function() {

			if ( !jQuery( this ).html().trim() ) return;

			var obj = jQuery.parseJSON( jQuery( this ).html().trim().replace( /\//g, '\\/' ) );

			var rLoop = function( i ) {
				var o = '';
				jQuery.each( i, function( key, value ) {
					v = typeof value == 'object' ? rLoop( value ) : value;
					o = o + '<div class="group"><h4>' + key + '</h4><div class="value">' + v + '</div></div>';
				} );
				return o;
			};

			jQuery( this ).html( rLoop( obj ) );

		} ); 

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
		jQuery( 'input.internal-cost' ).val( this.getTotalFor( selectedComponents, true ) );
		jQuery( 'input.calculated-price' ).val( this.getTotalFor( selectedComponents, false ) );

		jQuery( '#product_metabox .components-list' ).find( 'li.group > div.title' ).each( function() {

			var valuesEl = jQuery( this ).find( '.values' );

			valuesEl.html( '' );

			var group = jQuery( this ).text().toLowerCase();
			var components = _.sortBy( that.getSelectedComponents( group ), function( i ) { return i.substr( -1 ) != '*'; } );
			var componentNames = [];

			for ( var i = 0; i < components.length; i++ ) {
				
				var comName = components[i];
				var def = comName.substr( -1 ) == '*';

				// comName = jQuery( '.components-list li.' + comName.replace( '*', '' ) ).clone().find( 'label .cost' ).empty().parent().parent().text().trim();
				comName = jQuery( '.components-list li.' + comName.replace( '*', '' ) ).attr( 'data-component-title' );

				if ( def ) valuesEl.prepend( jQuery( '<strong>' + comName + '</strong>' ) );
				else valuesEl.append( comName + ( i < components.length - 1 ? ', ' : '' ) );

			}

		} );

	},

	getSelectedComponents: function( groupName ) {

		var selectedComponents = [];

		jQuery( '#product_metabox .components-list li' + ( groupName ? '.group-' + groupName : '' ) + ' input[type=checkbox]' ).each( function( e ) {

			if ( jQuery( this ).hasClass( 'clear-value' ) ) return;

			if ( jQuery( this ).attr( 'checked' ) ) {
				var liEl = jQuery( this ).parents( 'li' ).first();
				var fixed = liEl.parents( 'li' ).first().parents( 'li' ).first().find( 'input[type=checkbox]:checked' ).length == 1;
				var def = !fixed ? liEl.find( 'input[type=radio]' ).attr( 'checked' ) : true;
				selectedComponents[selectedComponents.length] = jQuery( this ).attr( 'name' ) + ( def ? '*' : '' ) + ( fixed ? '*' : '' );
			}

		} );

		return selectedComponents;

	},

	getTotalFor: function( ids, returnMin ) {

		var total = 0;

		for ( var i = 0; i < ids.length; i++ ) {

			var id = ids[i];
			var def = false;
			var fixed = false;

			if ( ids[i].match( /\*\*$/ ) ) {
				def = true;
				fixed = true;
				id = id.substr( 0, id.length-2 );
			}

			if ( !fixed && ids[i].match( /\*$/ ) ) {
				def = true;
				id = id.substr( 0, id.length-1 );
			}

			var listItemEl = jQuery( 'li.' + id );
			var costEl = listItemEl.find( '.cost' );

			if ( def && costEl ) {
				var cost = costEl.text().replace( /\$/g, '' ).split( ' / ' );
				total += parseFloat( cost[0] );
			}

		}

		if ( !returnMin ) {
			total += Math.max( total * .1, 100 ) + 100;
		}

		return Number( total ).toFixed( 2 );

	}

};

jQuery( document ).ready( function() {

	LEETPCStoreAdmin.init();

} );