( function( $, elementorFrontend ) {

	"use strict";

	var JetWooBuilder = {

		init: function() {

			var widgets = {
				'jet-single-images.default' : JetWooBuilder.productImages,
				'jet-single-add-to-cart.default' : JetWooBuilder.addToCart,
				'jet-single-tabs.default' : JetWooBuilder.productTabs,
				'jet-woo-products.default' : JetWooBuilder.widgetProducts,
				'jet-woo-categories.default' : JetWooBuilder.widgetCategories,
			};

			$.each( widgets, function( widget, callback ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});

			$( document ).on( 'jet-filter-content-rendered', JetWooBuilder.reInitCarousel );
			elementorFrontend.hooks.addFilter( 'jet-popup/widget-extensions/popup-data', JetWooBuilder.prepareJetPopup );
			$( window ).on( 'jet-popup/render-content/ajax/success', JetWooBuilder.jetPopupLoaded );
			$( document ).on( 'wc_update_cart added_to_cart', JetWooBuilder.jetCartPopupOpen);

		},

		jetPopupLoaded : function( event, popupData ){
			setTimeout( function(){
				$( window ).trigger('resize');

				$( '.jet-popup .woocommerce-product-gallery.images' ).each( function(e) {
					$( this ).wc_product_gallery();
				} );
			}, 500);
		},

		prepareJetPopup: function( popupData, widgetData, $scope, event ) {

			if ( widgetData['is-jet-woo-builder'] ) {
				var $product;
				popupData['isJetWooBuilder'] = true;
				popupData['templateId'] = widgetData['jet-woo-builder-qv-template'];

				if( $scope.hasClass( 'elementor-widget-jet-woo-products' ) || $scope.hasClass( 'elementor-widget-jet-woo-products-list' ) ){
					$product     = $( event.target ).parents( '.jet-woo-builder-product' );
				} else {
					$product     = $scope.parents( '.jet-woo-builder-product' );
				}

				if( $product.length ){
					popupData['productId'] = $product.data( 'product-id' );
				}
			}

			return popupData;

		},

		productImages: function( $scope ) {
			$scope.find( '.jet-single-images__loading' ).remove();

			if ( $('body').hasClass( 'single-product' ) ) {
				return;
			}

			$scope.find( '.woocommerce-product-gallery' ).each( function() {
				$( this ).wc_product_gallery();
			} );

		},

		addToCart: function( $scope ) {

			if ( $('body').hasClass( 'single-product' ) ) {
				return;
			}

			if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
				$scope.find( '.variations_form' ).each( function() {
					$( this ).wc_variation_form();
				});
			}

		},

		productTabs: function( $scope ) {

			$scope.find( '.jet-single-tabs__loading' ).remove();

			if ( $('body').hasClass( 'single-product' ) ) {
				return;
			}

			var hash  = window.location.hash;
			var url   = window.location.href;
			var $tabs = $scope.find( '.wc-tabs, ul.tabs' ).first();

			$tabs.find( 'a' ).addClass( 'elementor-clickable' );

			$scope.find( '.wc-tab, .woocommerce-tabs .panel:not(.panel .panel)' ).hide();

			if ( hash.toLowerCase().indexOf( 'comment-' ) >= 0 || hash === '#reviews' || hash === '#tab-reviews' ) {
				$tabs.find( 'li.reviews_tab a' ).click();
			} else if ( url.indexOf( 'comment-page-' ) > 0 || url.indexOf( 'cpage=' ) > 0 ) {
				$tabs.find( 'li.reviews_tab a' ).click();
			} else if ( hash === '#tab-additional_information' ) {
				$tabs.find( 'li.additional_information_tab a' ).click();
			} else {
				$tabs.find( 'li:first a' ).click();
			}

		},

		widgetProducts: function ( $scope ) {

			var $target = $scope.find( '.jet-woo-carousel' );

			if ( ! $target.length ) {
				return;
			}

			JetWooBuilder.initCarousel( $target, $target.data( 'slider_options' ) );

		},

		widgetCategories: function ( $scope ) {

			var $target = $scope.find( '.jet-woo-carousel' );

			if ( ! $target.length ) {
				return;
			}

			JetWooBuilder.initCarousel( $target, $target.data( 'slider_options' ) );

		},

		reInitCarousel: function( event, $scope ) {
			JetWooBuilder.widgetProducts( $scope );
		},

		initCarousel: function( $target, options ) {

			var mobileSlides, tabletSlides, desktopSlides, defaultOptions, visibleSlides,
				$slidesCount = $target.find('.swiper-slide').length;
			
			if ( options.slidesToShow.mobile ) {
				mobileSlides = options.slidesToShow.mobile;
			} else {
				mobileSlides = 1;
			}

			if ( options.slidesToShow.tablet ) {
				tabletSlides = options.slidesToShow.tablet;
			} else {
				tabletSlides = 1 === options.slidesToShow.desktop ? 1 : 2;
			}
			
			desktopSlides = options.slidesToShow.desktop;
		
			if( $( window ).width() < 768 ) {
				visibleSlides = mobileSlides;
			} else if ( $( window ).width() < 1025 ) {
				visibleSlides = tabletSlides;
			} else {
				visibleSlides = desktopSlides;
			}

			defaultOptions = {
				slidesPerView: desktopSlides,
				handleElementorBreakpoints: true,
				breakpoints: {
					768: {
						slidesPerView: mobileSlides,
						slidesPerGroup: 1,
					},
					1025: {
						slidesPerView: tabletSlides,
						slidesPerGroup: 1,
					},
				},
				pagination: {
					el: '.swiper-pagination',
					clickable: true,
				},
				navigation: {
					nextEl: '.jet-swiper-button-next',
					prevEl: '.jet-swiper-button-prev',
				},
			};
			
			if ( $slidesCount > visibleSlides ) {
				new Swiper($target, $.extend( {}, defaultOptions, options ) );
				$target.find( '.jet-arrow' ).show();
			} else if ( options.direction === 'vertical' ) {
				$target.addClass( 'swiper-container-vertical' );
				$target.find( '.jet-arrow' ).hide();
			} else {
				$target.find( '.jet-arrow' ).hide();
			}
		},
		
		jetCartPopupOpen: function ( event ) {
			var $target_enable = $( event.currentTarget.activeElement ).parents('.jet-woo-products, .jet-woo-products-list, .jet-woo-builder-archive-add-to-cart, .jet-woo-builder-single-ajax-add-to-cart').data('cart-popup-enable'),
				$target_id     = $( event.currentTarget.activeElement ).parents('.jet-woo-products, .jet-woo-products-list, .jet-woo-builder-archive-add-to-cart, .jet-woo-builder-single-ajax-add-to-cart').data('cart-popup-id');
			
			$target_id = $($target_id)[0];
			
			if ( $target_enable ) {
				$( window ).trigger( {
					type: 'jet-popup-open-trigger',
					popupData: {
						popupId: 'jet-popup-' + $target_id
					}
				} );
			}
		}

	};

	$( window ).on( 'elementor/frontend/init', JetWooBuilder.init );

}( jQuery, window.elementorFrontend ) );