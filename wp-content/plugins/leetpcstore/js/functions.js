
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

		jQuery( 'button.checkout' ).bind( 'click', jQuery.proxy( this.checkout, this ) );

		if ( this.bodyClasses.indexOf( 'single-product' ) > -1 ) {
			this.pageType = 'single-product';
		}

		if ( this.bodyClasses.indexOf( 'my-cart' ) > -1 ) {
			this.pageType = 'my-cart';
		}

		if ( this.pageType == 'single-product' ) {
			this.postID = jQuery( 'article.product' ).attr( 'id' ).split( '-' )[1];
			jQuery( 'article.product button.customize' ).bind( 'click', jQuery.proxy( this.customizeProduct, this ) );
		}

	},

	refreshCart: function( r ) {

		this.updateCart( r.cart.items_count );

		if ( this.pageType != 'my-cart' ) {
			window.location = '/my-cart/';
		}

	},

	emptyCart: function() {

		var that = this;
		var url = '/wp-admin/admin-ajax.php';
		var opts = { 
			method: 'post',
			data: { 
				action: 'empty_cart'
			}, 
			success: jQuery.proxy( that.refreshCart, that )
		};

		jQuery.ajax( url, opts );

	},

	onClickChangeSelection: function( ev ) {

		ev.preventDefault();

		var el = jQuery( ev.target );
		var cEl = el.parents( '.component-options' );

		cEl.find( '.selected' ).hide();
		cEl.find( '.options' ).show();

	},

	onClickCustomizeOption: function( ev ) {

		var el = jQuery( ev.target );
		var cEl = el.parents( '.component-options' );

		var optionHtmlEl = jQuery( '<div>' + el.parent().html() + '</div>' );
		optionHtmlEl.find( 'input' ).remove();

		cEl.find( '.selected label' ).html( optionHtmlEl.html() );

		this.refreshCustomize();

		cEl.find( '.selected' ).show();
		cEl.find( '.options' ).hide();

	},

	refreshCustomize: function() {

		this.refreshCustomizePrice();
		this.refreshFinalComponents();

	},

	addToCart: function( product_id ) {

		var that = this;
		var data = { 
			action: 'add_to_cart',
			product_id: product_id
		};

		if ( arguments.length > 1 ) {
			data.component_ids = arguments[1];
		}

		var url = '/wp-admin/admin-ajax.php';
		var opts = { 
			method: 'post',
			data: data, 
			success: jQuery.proxy( that.refreshCart, that )
		};

		jQuery.ajax( url, opts );

	},

	checkout: function() {

		var that = this;

		var url = '/wp-admin/admin-ajax.php';
		var opts = { 
			method: 'post',
			data: { 
				action: 'get_checkout_step',
				step: 1
			}, 
			success: jQuery.proxy( that.openCheckoutForm, that )
		};

		jQuery.ajax( url, opts );

	},

	openCheckoutForm: function( r ) {

		this.currentModalEl = jQuery( r );

		this.currentModalEl.find( 'button.close-modal' ).bind( 'click', jQuery.proxy( this.closeModal, this ) );
		this.currentModalEl.find( 'button.add-to-cart' ).bind( 'click', jQuery.proxy( this.onClickAddToCart, this ) );

		jQuery( 'body' ).css( 'overflow-y', 'hidden' ).append( this.currentModalEl );

	},

	customizeProduct: function() {

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

		this.currentModalEl = jQuery( r );

		this.currentModalEl.find( 'button.close-modal' ).bind( 'click', jQuery.proxy( this.closeModal, this ) );
		this.currentModalEl.find( 'button.add-to-cart' ).bind( 'click', jQuery.proxy( this.onClickAddToCart, this ) );
		this.currentModalEl.find( 'input[type=radio]' ).bind( 'click', jQuery.proxy( this.onClickCustomizeOption, this ) );
		this.currentModalEl.find( '.change-selection' ).bind( 'click', jQuery.proxy( this.onClickChangeSelection, this ) );

		jQuery( 'body' ).css( 'overflow-y', 'hidden' ).append( this.currentModalEl );

		this.refreshCustomize();

	},

	getCustomizeSelections: function() {

		var selections = [];

		jQuery( '.component-list input:checked' ).each( function() {
			selections[selections.length] = jQuery( this ).val();
		} );

		return selections;

	},

	calcCustomizePrice: function() {

		var sub_total = parseFloat( this.currentModalEl.find( 'input[name=product_base_price]' ).val() );

		jQuery( '.component-list input:checked' ).each( function() {
			sub_total += parseFloat( jQuery( this ).attr('data-price-diff') );
		} );

		return sub_total;

	},

	refreshCustomizePrice: function() {

		jQuery( '.customize-form .sub-total .amount' ).html( '&dollar;' + this.calcCustomizePrice().toLocaleString() );

	},

	refreshFinalComponents: function() {

		var componentInputs = jQuery( '.customize-form .component-list input' );
		var components = componentInputs.serializeArray();

		var final_components = [];

		jQuery( components ).each( function( i, c ) {
			final_components.push( c.value );
		} );

		jQuery( '.customize-form input[name=final_selection]' ).val( final_components.join( ',' ) );

	},

	onClickAddToCart: function( e ) {

		var attrsEl = jQuery( e.target ).parents( '.product-attrs' );
		var product_id = attrsEl.find( 'input[name=product_id]' ).val();
		var component_ids = attrsEl.find( 'input[name=final_selection]' ).val();

		this.addToCart( product_id, component_ids );

	},

	closeModal: function() {
		jQuery( 'body' ).css( 'overflow-y', 'auto' );
		this.currentModalEl.remove();
		this.currentModalEl = null;
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

