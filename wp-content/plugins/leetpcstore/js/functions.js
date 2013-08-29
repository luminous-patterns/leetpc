
var LEETPCStore = {

	// Statics
	postID: null,
	bodyClasses: null,
	pageType: 'unknown',

	// Vars
	customizing: false,

	// Functions
	init: function() {

		var that = this;

		this.bodyClasses = jQuery( 'body' ).attr( 'class' ).split( ' ' );

		if ( this.bodyClasses.indexOf( 'single-product' ) > -1 ) {
			this.pageType = 'single-product';
		}

		if ( this.pageType == 'single-product' ) {
			this.postID = jQuery( 'article.product' ).attr( 'id' ).split( '-' )[1];
			jQuery( 'article.product button.customize' ).bind( 'click', jQuery.proxy( this.customizeProduct, this ) );
		}

	},

	customizeProduct: function() {

		// alert( '/customize/' + window.location.pathname.split( '/' )[2] );

		var that = this;
		var product_id = this.postID;

		var url = '/wp-admin/admin-ajax.php';
		var opts = { 
			method: 'post',
			data: { 
				action: 'get_customize_form',
				product_id: product_id 
			}, 
			success: jQuery.proxy( that.openCustomizeForm, that )
		};

		jQuery.ajax( url, opts );

	},

	openCustomizeForm: function( r ) {

		this.customizeFormEl = jQuery( r );

		this.customizeFormEl.find( 'button.secondary' ).bind( 'click', jQuery.proxy( this.closeCustomizeForm, this ) );

		jQuery( 'body' ).append( this.customizeFormEl );

	},

	closeCustomizeForm: function() {
		this.customizeFormEl.remove();
	},

	updateCart: function( n ) {
		var newString = '| ';
		newString += n < 1 ? 'No' : n;
		newString += n == 0 || n > 1 ? ' items' : ' item';
		jQuery( '.cart span' ).html( newString );
	}

};

jQuery( document ).ready( function() {

	LEETPCStore.init();

} );

