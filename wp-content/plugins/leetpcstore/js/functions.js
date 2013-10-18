
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

		jQuery( document ).keyup( jQuery.proxy( this.onKeyUp, this ) );

		jQuery( 'button.checkout' ).bind( 'click', jQuery.proxy( this.checkout, this ) );

		jQuery( '.toggle-nav' ).bind( 'click', jQuery.proxy( this.toggleNav, this ) );
		jQuery( '.toggle-details' ).bind( 'click', jQuery.proxy( this.toggleDetailsEl, this ) );

		jQuery( '.sidebar-toggle-container' ).bind( 'click', jQuery.proxy( this.toggleSidebarEl, this ) );

		if ( this.bodyClasses.indexOf( 'single-product' ) > -1 ) {
			this.pageType = 'single-product';
		}

		if ( this.bodyClasses.indexOf( 'post-type-archive-product' ) > -1 ) {
			this.pageType = 'product-list';
		}

		if ( this.bodyClasses.indexOf( 'my-cart' ) > -1 ) {
			this.pageType = 'my-cart';
		}

		if ( this.pageType == 'single-product' ) {
			this.postID = jQuery( 'article.product' ).attr( 'id' ).split( '-' )[1];
			jQuery( 'article.product button.customize' ).bind( 'click', jQuery.proxy( this.customizeCurrentProduct, this ) );
		}

		if ( this.pageType == 'product-list' ) {
			jQuery( 'article.product button.customize' ).bind( 'click', jQuery.proxy( this.customizeCurrentProduct, this ) );
		}

		if ( this.pageType == 'my-cart' ) {

			this.couponEntryEl = jQuery( '.promo-code .promo-entry' );
			this.couponEntryToggleEl = jQuery( '.promo-code .promo-entry-toggle' );
			this.couponEntryInputEl = this.couponEntryEl.find( 'input[name=promo-code]' );
			this.couponEntryApplyEl = this.couponEntryEl.find( 'button.apply-promo-code' );

			this.lineItemEls = jQuery( 'table.line-items .line-item' );

			this.couponEntryToggleEl.bind( 'click', jQuery.proxy( this.toggleCouponEntryEl, this ) );
			this.couponEntryApplyEl.bind( 'click', jQuery.proxy( this.applyCouponCode, this ) );
			
			this.lineItemEls.find( '.remove-line-item' ).bind( 'click', jQuery.proxy( this.removeLineItem, this ) );
			this.lineItemEls.find( '.item-qty' ).bind( 'change', jQuery.proxy( this.updateLineItemQty, this ) );

		}

	},

	toggleSidebarEl: function( ev ) {
		if ( jQuery( 'body' ).hasClass( 'toggle-sidebar-on' ) ) {
			jQuery( 'body' ).removeClass( 'toggle-sidebar-on' );
			jQuery( '.sidebar-toggle-container' ).addClass( 'secondary' );
		}
		else {
			jQuery( 'body' ).addClass( 'toggle-sidebar-on' );
			jQuery( '.sidebar-toggle-container' ).removeClass( 'secondary' );
		}
	},

	error: function( message ) {



	},

	closeError: function() {

	},

	applyCouponCode: function() {

		if ( !this.couponEntryInputEl.val() ) {
			this.couponEntryInputEl.focus();
			return;
		}

		var data = { 
			action: 'apply_coupon_code', 
			coupon_code: this.couponEntryInputEl.val() 
		};

		this.adminAjax( data, this.refreshCart, this.onApplyCouponCodeError );

	},

	onApplyCouponCodeError: function( xhr ) {
		var data = xhr.responseJSON.error;
		alert( data.message );
		this.couponEntryInputEl.focus();
	},

	toggleCouponEntryEl: function( ev ) {
		if ( jQuery( ev.target ).has( 'href' ) ) {
			ev.preventDefault();
		}
		this.couponEntryEl.toggleClass( 'hidden' );
		this.couponEntryToggleEl.toggleClass( 'hidden' );
		if ( !this.couponEntryEl.hasClass( 'hidden' ) ) {
			this.couponEntryInputEl.attr( 'value', '' ).focus();
		}
	},

	toggleDetailsEl: function( ev ) {
		ev.preventDefault();
		jQuery( ev.target ).parents( '.line-item' ).find( '.details' ).toggleClass( 'hidden' );
	},

	onKeyUp: function( ev ) {

		if ( ev.keyCode == 27 ) { //escape
			this.closeModal();
		}

	},

	toggleNav: function() {
		jQuery( '.nav' ).toggle();
	},

	refreshCart: function( r ) {
		
		this.updateCart( r.cart.items_count );

		if ( this.pageType != 'my-cart' ) {
			window.location = '/my-cart/';
		}
		else {
			window.location.reload();
		}

	},

	refreshPage: function( r ) {
		window.location.reload();
	},

	removeLineItem: function( ev ) {
		ev.preventDefault();
		var that = this;
		var lineItemEl = jQuery( ev.target ).parents( '.line-item' );
		lineItemEl.addClass( 'removing' );
		this.adminAjax( { action: 'remove_from_cart', line_item_id: lineItemEl.attr( 'data-line-item-id' ) }, this.refreshPage );
	},

	updateLineItemQty: function( ev ) {
		ev.preventDefault();
		var that = this;
		var lineItemEl = jQuery( ev.target ).parents( '.line-item' );
		this.adminAjax( { action: 'update_line_item_qty', line_item_id: lineItemEl.attr( 'data-line-item-id' ), qty: parseInt( lineItemEl.find( 'select.item-qty' ).val() ) }, this.refreshPage );
	},

	emptyCart: function() {
		var that = this;
		this.adminAjax( { action: 'empty_cart' }, this.refreshCart );
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

		this.adminAjax( data, this.refreshCart );

	},

	onCheckoutError: function( xhr ) {
		console.log(xhr);
		var data = xhr.responseJSON.error;
		var fields = data.fields;
		this.currentModalEl.find( '.modal-header .loading-anim' ).remove();
		this.currentModalEl.find( '.field-error div.error-message' ).remove();
		this.currentModalEl.find( '.field-error' ).removeClass( 'field-error' );
		for ( var i = 0; i < fields.length; i++ ) {
			if ( i == 0 ) {
				this.currentModalEl.find( '.' + fields[i].name + ' input' ).focus();
			}
			if ( fields[i].message ) {
				this.currentModalEl.find( '.' + fields[i].name ).append( jQuery( '<div class="error-message">' + fields[i].message + '</div>' ) );
			}
			this.currentModalEl.find( '.' + fields[i].name ).addClass( 'field-error' );
		}
		console.log(xhr);
		//alert(data.message);
	},

	adminAjax: function( data, onSuccess ) {

		var that = this;
		var onError = arguments.length > 2 ? arguments[2] : this.onCheckoutError;
		var url = '/wp-admin/admin-ajax.php';
		var opts = { 
			method: 'post',
			data: data, 
			success: jQuery.proxy( onSuccess, that ),
			error: jQuery.proxy( onError, that )
		};

		if ( this.currentModalEl ) {
			this.currentModalEl.find( '.modal-header' ).prepend( this.makeLoadingAnim() );
		}

		jQuery.ajax( url, opts );

	},

	removeFromCart: function( lineitem_id ) {

		var that = this;
		var data = { 
			action: 'remove_from_cart',
			lineitem_id: lineitem_id
		};

		this.adminAjax( data, this.refreshCart );

	},

	checkout: function() {

		var that = this;
		var data = { 
			action: 'get_checkout_step',
			step: 1
		};

		this.adminAjax( data, this.openCheckoutForm );

	},

	onClickNextStep: function( ev ) {

		var that = this;
		var data = {
			action: 'get_checkout_step',
			step: parseInt( jQuery( ev.target ).parents( '.checkout-modal' ).find( 'input[name=current_step]' ).val() ) + 1,
			direction: 1,
			submitted: {}
		};

		jQuery( jQuery( ev.target ).parents( '.checkout-modal' ).find( ':input' ).serializeArray() ).each( function( i, m ) {
			data.submitted[m.name] = m.value;
		} );

		this.adminAjax( data, this.openCheckoutForm );

	},

	onClickPrevStep: function( ev ) {

		var that = this;
		var data = {
			action: 'get_checkout_step',
			step: parseInt( jQuery( ev.target ).parents( '.checkout-modal' ).find( 'input[name=current_step]' ).val() ) - 1,
			direction: -1,
			submitted: {}
		};

		this.adminAjax( data, this.openCheckoutForm );

	},

	openCheckoutForm: function( r ) {

		this.closeModal();

		this.currentModalEl = jQuery( r );

		this.currentModalEl.find( '.close-modal' ).bind( 'click', jQuery.proxy( this.closeModal, this ) );
		this.currentModalEl.find( '.previous-step' ).bind( 'click', jQuery.proxy( this.onClickPrevStep, this ) );
		this.currentModalEl.find( '.next-step' ).bind( 'click', jQuery.proxy( this.onClickNextStep, this ) );
		this.currentModalEl.find( 'input[name=user-registered]' ).bind( 'change', jQuery.proxy( this.onClickRegisteredToggle, this ) );
		this.currentModalEl.find( 'input[name=payment-method]' ).bind( 'change', jQuery.proxy( this.onClickPayMethodToggle, this ) );
		this.currentModalEl.find( 'input[name=delivery-use_different_address]' ).bind( 'change', jQuery.proxy( this.onClickDeliveryUseAddr, this ) );

		jQuery( 'body' ).css( 'overflow', 'hidden' ).append( this.currentModalEl );

	},

	onClickDeliveryUseAddr: function() {

		if ( this.currentModalEl.find( 'input[name=delivery-use_different_address]:checked' ).val() == '1' ) {
			this.currentModalEl.find( '.delivery-address' ).removeClass( 'hidden' );
		}
		else {
			this.currentModalEl.find( '.delivery-address' ).addClass( 'hidden' );
		}

	},

	onClickPayMethodToggle: function() {

		var paymentMethod = this.currentModalEl.find( 'input[name=payment-method]:checked' ).val();

		this.currentModalEl.find( '.payment-section' ).removeClass( 'hidden' );
		this.currentModalEl.find( '.payment-section' ).not( '.payment-method-' + paymentMethod ).addClass( 'hidden' );

	},

	onClickRegisteredToggle: function( ev ) {

		if ( this.currentModalEl.find( 'input[name=user-registered]:checked' ).val() == '1' ) {
			this.currentModalEl.find( 'div.row.user-conf_email' ).addClass( 'hidden' );
			this.currentModalEl.find( 'div.row.user-password' ).removeClass( 'hidden' );
		}
		else {
			this.currentModalEl.find( 'div.row.user-password' ).addClass( 'hidden' );
			this.currentModalEl.find( 'div.row.user-conf_email' ).removeClass( 'hidden' );
		}

	},

	customizeCurrentProduct: function( e ) {

		switch ( this.pageType ) {

			case 'single-product':
				this.customizeProduct( this.postID );
				break;

			case 'product-list':
				this.customizeProduct( jQuery( e.target ).parents( 'article' ).attr( 'id' ).replace( 'post-', '' ) );
				break;

		}

	},

	customizeProduct: function( product_id ) {

		var data = { 
			action: 'get_customize_form',
			product_id: product_id 
		};

		this.adminAjax( data, this.openCustomizeForm );

	},

	openCustomizeForm: function( r ) {

		this.currentModalEl = jQuery( r );

		this.currentModalEl.find( '.close-modal' ).bind( 'click', jQuery.proxy( this.closeModal, this ) );
		this.currentModalEl.find( '.add-to-cart' ).bind( 'click', jQuery.proxy( this.onClickAddToCart, this ) );
		this.currentModalEl.find( 'input[type=radio]' ).bind( 'click', jQuery.proxy( this.onClickCustomizeOption, this ) );
		this.currentModalEl.find( '.change-selection' ).bind( 'click', jQuery.proxy( this.onClickChangeSelection, this ) );

		jQuery( 'body' ).css( 'overflow', 'hidden' ).append( this.currentModalEl );

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

	closeModal: function( e ) {
		if ( e ) { e.preventDefault(); }
		if ( this.currentModalEl ) {
			jQuery( 'body' ).css( 'overflow', 'auto' );
			this.currentModalEl.remove();
			this.currentModalEl = null;
		}
	},

	updateCart: function( n ) {
		var newString = '| ';
		newString += n < 1 ? 'No' : n;
		newString += n == 0 || n > 1 ? ' items' : ' item';
		jQuery( '.cart span' ).html( newString );
	},

	makeLoadingAnim: function() {
		var loadingAnimEl = jQuery( '<img class="loading-anim" src="/wp-content/plugins/leetpcstore/img/ajax-loader.gif" />' );
		return loadingAnimEl;
	}

};

jQuery( document ).ready( function() {

	LEETPCStore.init();

	jQuery( '.product-sampler' ).lpcSlider();

} );

( function ( $ ) {

	var o = null;
 
    $.fn.lpcSlider = function() {
        o = new lpcFeaturedProductSlider( this ).init();
        window.lpcslider = o;
        return this;
    };

	function lpcFeaturedProductSlider( sliderEl ) {

		return  {
 
 			el: null,
			els: null,
			currentItemIndex: 1,

			mouseOver: false,

			timeout: null,

			use: {
				csstransitions: false
			},

			// Functions
			init: function() {

				this.checkCompatibility();

				var that = this;
				var el = this.el = sliderEl;
				var els = this.els = el.find( '.featured-product.hidden' );

				this.containerEl = el.find( '.featured-product .inside-container' );

				if ( this.use.csstransitions ) {
					this.containerEl.css( 'transition', 'opacity 200ms' );
				}

				this.el.on( 'mouseover', function() { that.mouseOver = true; } );
				this.el.on( 'mouseout', function() { that.mouseOver = false; } );

				this.tick();

				return this;

			},

			tick: function() {
				if ( !this.mouseOver ) this.change();
				return this.timeout = setTimeout( jQuery.proxy( this.tick, this ), 11000 );
			},

			checkCompatibility: function() {

				if ( !window.Modernizr ) return;

				this.use.csstransitions = Modernizr.csstransitions;

			},

			getCurrentItemIndex: function() {
				return this.currentItemIndex;
			},

			getNextItemIndex: function() {
				return this.currentItemIndex == this.els.length ? 1 : this.currentItemIndex + 1;
			},

			getCurrentItemEl: function() {
				return this.getItemEl( this.getCurrentItemIndex() );
			},

			getNextItemEl: function() {
				return this.getItemEl( this.getNextItemIndex() );
			},

			getItemEl: function( index ) {
				return this.el.find( '.featured-product.display-order-' + index );
			},

			change: function() {

				this.doChain( [
					[ this.containerEl, { opacity: 0 }, this.nextSlide ],
					[ this.containerEl, { opacity: 1 } ]
				] );

			},

			nextSlide: function() {
				return this.setSlide( this.getNextItemIndex() );
			},

			setSlide: function( itemIndex ) {
				this.currentItemIndex = itemIndex;
				this.setSliderContents( this.getItemEl( itemIndex ).html() );
			},

			setSliderContents: function( html ) {
				this.containerEl.html( html );
				return true;
			},

			doChain: function( chain ) {

				var that = this;
				var s = arguments.length > 1 ? arguments[1] : null;
				var step = s ? chain[s] : chain[0];

				if ( !step ) return true;

				var targetEl = step[0];
				var cssRules = step[1];
				var onTransitionEnd = step[2];

				var doNextInChain = function() {
					targetEl.off( 'transitionend', doNextInChain );
					if ( onTransitionEnd && typeof onTransitionEnd == 'function' ) {
						onTransitionEnd.call( that );
					}
					that.doChain( chain, s + 1 );
				};

				if ( this.use.csstransitions )
					targetEl.on( 'transitionend', doNextInChain );

				targetEl.css( cssRules );

				if ( !this.use.csstransitions ) 
					doNextInChain();

				return true;

			}

		};

	}
 
}( jQuery ) );