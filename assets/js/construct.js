/*	-----------------------------------------------------------------------------------------------
	Namespace
--------------------------------------------------------------------------------------------------- */

var chaplin = chaplin || {},
    $ = jQuery;


/*	-----------------------------------------------------------------------------------------------
	Global variables
--------------------------------------------------------------------------------------------------- */

var $doc = $( document ),
    $win = $( window ),
    winHeight = $win.height(),
    winWidth = $win.width();

var viewport = {};
	viewport.top = $win.scrollTop();
	viewport.bottom = viewport.top + $win.height();


/*	-----------------------------------------------------------------------------------------------
	Helper functions
--------------------------------------------------------------------------------------------------- */

/* Output AJAX errors -------------------------------- */

function ajaxErrors( jqXHR, exception ) {
	var message = '';
	if ( jqXHR.status === 0 ) {
		message = 'Not connect.n Verify Network.';
	} else if ( jqXHR.status == 404 ) {
		message = 'Requested page not found. [404]';
	} else if ( jqXHR.status == 500 ) {
		message = 'Internal Server Error [500].';
	} else if ( exception === 'parsererror' ) {
		message = 'Requested JSON parse failed.';
	} else if ( exception === 'timeout' ) {
		message = 'Time out error.';
	} else if ( exception === 'abort' ) {
		message = 'Ajax request aborted.';
	} else {
		message = 'Uncaught Error.n' + jqXHR.responseText;
	}
	console.log( 'AJAX ERROR:' + message );
}


/*	-----------------------------------------------------------------------------------------------
	Interval Scroll
--------------------------------------------------------------------------------------------------- */

chaplin.intervalScroll = {

	init: function() {

		didScroll = false;

		// Check for the scroll event
		$win.on( 'scroll load', function() {
			didScroll = true;
		} );

		// Once every 250ms, check if we have scrolled, and if we have, do the intensive stuff
		setInterval( function() {
			if ( didScroll ) {
				didScroll = false;

				// When this triggers, we know that we have scrolled
				$win.triggerHandler( 'did-interval-scroll' );

			}

		}, 250 );

	},

} // chaplin.intervalScroll


/*	-----------------------------------------------------------------------------------------------
	Resize End Event
--------------------------------------------------------------------------------------------------- */

chaplin.resizeEnd = {

	init: function() {

		var resizeTimer;

		$win.on( 'resize', function(e) {

			clearTimeout( resizeTimer );
			
			resizeTimer = setTimeout( function() {

				// Trigger this at the end of screen resizing
				$win.triggerHandler( 'resize-end' );
						
			}, 250 );

		} );

	},

} // chaplin.resizeEnd


/*	-----------------------------------------------------------------------------------------------
	Is Scrolling
--------------------------------------------------------------------------------------------------- */

chaplin.isScrolling = {

	init: function() {

		scrollPos = 0;

		// Sensitivity for the scroll direction check (higher = more scroll required to reverse)
		directionBuffer = 50;

		var $body = $( 'body' );

		$win.on( 'did-interval-scroll', function() {

			var currentScrollPos = $win.scrollTop(),
				docHeight = $body.outerHeight();

			// Detect scrolling
			if ( currentScrollPos > 0 || $( 'html' ).css( 'position' ) == 'fixed' ) {
				$body.addClass( 'is-scrolling' );
			} else {
				$body.removeClass( 'is-scrolling' );
			}

			// Detect whether we're at the bottom
			if ( currentScrollPos + winHeight >= docHeight ) {
				$body.addClass( 'scrolled-to-bottom' );
			} else {
				$body.removeClass( 'scrolled-to-bottom' );
			}

			// Detect the scroll direction
			if ( currentScrollPos > ( $win.outerHeight() / 3 ) ) {
				
				if ( Math.abs( scrollPos - currentScrollPos ) >= directionBuffer ) {
				
					// Scrolling down
					if ( currentScrollPos > scrollPos ){
						$( 'body' ).addClass( 'scrolling-down' ).removeClass( 'scrolling-up' );

					// Scrolling up
					} else {
						$( 'body' ).addClass( 'scrolling-up' ).removeClass( 'scrolling-down' );
					}

				}

			} else {

				$( 'body' ).removeClass( 'scrolling-up scrolling-down' );

			}

			scrollPos = currentScrollPos;

		} );

	}

} // chaplin.isScrolling


/*	-----------------------------------------------------------------------------------------------
	Toggles
--------------------------------------------------------------------------------------------------- */

chaplin.toggles = {

	init: function() {

		// Do the toggle
		chaplin.toggles.toggle();

		// Check for toggle/untoggle on resize
		chaplin.toggles.resizeCheck();

		// Check for untoggle on escape key press
		chaplin.toggles.untoggleOnEscapeKeyPress();

	},

	// Do the toggle
	toggle: function() {

		$( '*[data-toggle-target]' ).live( 'click', function( e ) {

			// Get our targets
			var $toggle = $( this ),
				targetString = $( this ).data( 'toggle-target' );

			if ( targetString == 'next' ) {
				var $target = $toggle.next();
			} else {
				var $target = $( targetString );
			}

			// Get the class to toggle, if specified
			var classToToggle = $toggle.data( 'class-to-toggle' ) ? $toggle.data( 'class-to-toggle' ) : 'active';

			// Toggle the target of the clicked toggle
			if ( $toggle.data( 'toggle-type' ) == 'slidetoggle' ) {
				var duration = $toggle.data( 'toggle-duration' ) ? $toggle.data( 'toggle-duration' ) : 250;
				$target.slideToggle( duration );
			} else {
				$target.toggleClass( classToToggle );
			}

			// If the toggle target is 'next', only give the clicked toggle the active class
			if ( targetString == 'next' ) {
				$toggle.toggleClass( 'active' )

			// If not, toggle all toggles with this toggle target
			} else {
				$( '*[data-toggle-target="' + targetString + '"]' ).toggleClass( 'active' );
			}

			// Toggle body class
			if ( $toggle.data( 'toggle-body-class' ) ) {
				$( 'body' ).toggleClass( $toggle.data( 'toggle-body-class' ) );
			}

			// Check whether to lock the screen
			if ( $toggle.data( 'lock-screen' ) ) {
				chaplin.scrollLock.setTo( true );
			} else if ( $toggle.data( 'unlock-screen' ) ) {
				chaplin.scrollLock.setTo( false );
			} else if ( $toggle.data( 'toggle-screen-lock' ) ) {
				chaplin.scrollLock.setTo();
			}

			// Check whether to set focus
			if ( $toggle.data( 'set-focus' ) ) {
				var $focusElement = $( $toggle.data( 'set-focus' ) );
				if ( $focusElement.length ) {
					if ( $toggle.is( '.active' ) ) {
						$focusElement.focus();
					} else {
						$focusElement.blur();
					}
				}
			}

			// Trigger the toggled event on the toggle target
			$target.triggerHandler( 'toggled' );

			return false;

		} );
	},

	// Check for toggle/untoggle on screen resize
	resizeCheck: function() {

		if ( $( '*[data-untoggle-above], *[data-untoggle-below], *[data-toggle-above], *[data-toggle-below]' ).length ) {

			$win.on( 'resize', function() {

				var winWidth = $win.width(),
					$toggles = $( '.toggle' );

				$toggles.each( function() {

					$toggle = $( this );

					var unToggleAbove = $toggle.data( 'untoggle-above' ),
						unToggleBelow = $toggle.data( 'untoggle-below' ),
						toggleAbove = $toggle.data( 'toggle-above' ),
						toggleBelow = $toggle.data( 'toggle-below' );

					// If no width comparison is set, continue
					if ( ! unToggleAbove && ! unToggleBelow && ! toggleAbove && ! toggleBelow ) {
						return;
					}

					// If the toggle width comparison is true, toggle the toggle
					if ( 
						( ( ( unToggleAbove && winWidth > unToggleAbove ) ||
						( unToggleBelow && winWidth < unToggleBelow ) ) &&
						$toggle.hasClass( 'active' ) )
						||
						( ( ( toggleAbove && winWidth > toggleAbove ) ||
						( toggleBelow && winWidth < toggleBelow ) ) &&
						! $toggle.hasClass( 'active' ) )
					) {
						$toggle.trigger( 'click' );
					}

				} );

			} );

		}

	},

	// Close toggle on escape key press
	untoggleOnEscapeKeyPress: function() {

		$doc.keyup( function( e ) {
			if ( e.key === "Escape" ) {

				$( '*[data-untoggle-on-escape].active' ).each( function() {
					if ( $( this ).hasClass( 'active' ) ) {
						$( this ).trigger( 'click' );
					}
				} );
					
			}
		} );

	},

} // chaplin.toggles


/*	-----------------------------------------------------------------------------------------------
	Cover Modals
--------------------------------------------------------------------------------------------------- */

chaplin.coverModals = {

	init: function () {

		if ( $( '.cover-modal' ).length ) {

			// Handle cover modals when they're toggled
			chaplin.coverModals.onToggle();

			// When toggled, untoggle if visitor clicks on the wrapping element of the modal
			chaplin.coverModals.outsideUntoggle();

			// Close on escape key press
			chaplin.coverModals.closeOnEscape();

			// Show a cover modal on load, if the query string says so
			chaplin.coverModals.showOnLoadandClick();

		}

	},

	// Handle cover modals when they're toggled
	onToggle: function() {

		$( '.cover-modal' ).on( 'toggled', function() {

			var $modal = $( this ),
				$body = $( 'body' );

			if ( $modal.hasClass( 'active' ) ) {
				$body.addClass( 'showing-modal' );
			} else {
				$body.removeClass( 'showing-modal' ).addClass( 'hiding-modal' );

				// Remove the hiding class after a delay, when animations have been run
				setTimeout ( function() {
					$body.removeClass( 'hiding-modal' );
				}, 500 );
			}
		} );

	},

	// Close modal on outside click
	outsideUntoggle: function() {

		$doc.live( 'click', function( e ) {

			var $target = $( e.target ),
				modal = '.cover-modal.active';

			if ( $target.is( modal ) ) {

				chaplin.coverModals.untoggleModal( $target );

			}

		} );

	},

	// Close modal on escape key press
	closeOnEscape: function() {

		$doc.keyup( function( e ) {
			if ( e.key === "Escape" ) {
				$( '.cover-modal.active' ).each( function() {
					chaplin.coverModals.untoggleModal( $( this ) );
				} );
			}
		} );

	},

	// Show modals on load
	showOnLoadandClick: function() {

		var key = 'modal';

		// Load based on query string
		if ( window.location.search.indexOf( key ) !== -1 ) {
				
			var modalTargetString = getQueryStringValue( key ),
				$modalTarget = $( '#' + modalTargetString + '-modal' );

			if ( modalTargetString && $modalTarget.length ) {
				setTimeout( function() {
					$modalTarget.addClass( 'active' ).triggerHandler( 'toggled' );
					chaplin.scrollLock.setTo( true );
				}, 250 );
			}
		}

		// Check for modal matching querystring when clicking a link
		// Format: www.url.com?modal=modal-id
		$( 'a' ).live( 'click', function() {

			// Load based on query string
			if ( $( this ).attr( 'href' ).indexOf( key ) !== -1 ) {
					
				var modalTargetString = getQueryStringValue( key, $( this ).attr( 'href' ) ),
					$modalTarget = $( '#' + modalTargetString );

				if ( modalTargetString && $modalTarget.length ) {
					
					$modalTarget.addClass( 'active' ).triggerHandler( 'toggled' );
					chaplin.scrollLock.setTo( true );

					return false;

				}
			}

		} );

	},

	// Untoggle a modal
	untoggleModal: function( $modal ) {

		$modalToggle = false;

		// If the modal has specified the string (ID or class) used by toggles to target it, untoggle the toggles with that target string
		// The modal-target-string must match the string toggles use to target the modal
		if ( $modal.data( 'modal-target-string' ) ) {
			var modalTargetClass = $modal.data( 'modal-target-string' ),
				$modalToggle = $( '*[data-toggle-target="' + modalTargetClass + '"]' ).first();
		}

		// If a modal toggle exists, trigger it so all of the toggle options are included
		if ( $modalToggle && $modalToggle.length ) {
			$modalToggle.trigger( 'click' );

		// If one doesn't exist, just hide the modal
		} else {
			$modal.removeClass( 'active' );
		}

	}

} // chaplin.coverModals


/*	-----------------------------------------------------------------------------------------------
	Element In View
--------------------------------------------------------------------------------------------------- */

chaplin.elementInView = {

	init: function() {

		$targets = $( '.do-spot' );
		chaplin.elementInView.run( $targets );

		// Rerun on AJAX content loaded
		$win.on( 'ajax-content-loaded', function() {
			$targets = $( '.do-spot' );
			chaplin.elementInView.run( $targets );
		} );

	},

	run: function( $targets ) {

		if ( $targets.length ) {

			// Add class indicating the elements will be spotted
			$targets.each( function() {
				$( this ).addClass( 'will-be-spotted' );
			} );

			chaplin.elementInView.handleFocus( $targets );
		}

	},

	handleFocus: function( $targets ) {

		// Get dimensions of window outside of scroll for performance
		$win.on( 'load resize orientationchange', function() {
			winHeight = $win.height();
		} );

		$win.on( 'resize orientationchange did-interval-scroll', function() {

			var winTop 		= $win.scrollTop();
				winBottom 	= winTop + winHeight;

			// Check for our targets
			$targets.each( function() {

				var $this = $( this );

				if ( chaplin.elementInView.isVisible( $this, checkAbove = true ) ) {
					$this.addClass( 'spotted' ).triggerHandler( 'spotted' );
				}

			} );

		} );

	},

	// Determine whether the element is in view
	isVisible: function( $elem, checkAbove ) {

		if ( typeof checkAbove === 'undefined' ) {
			checkAbove = false;
		}

		var winHeight 				= $win.height();

		var docViewTop 				= $win.scrollTop(),
			docViewBottom			= docViewTop + winHeight,
			docViewLimit 			= docViewBottom - 50;

		var elemTop 				= $elem.offset().top,
			elemBottom 				= $elem.offset().top + $elem.outerHeight();

		// If checkAbove is set to true, which is default, return true if the browser has already scrolled past the element
		if ( checkAbove && ( elemBottom <= docViewBottom ) ) {
			return true;
		}

		// If not, check whether the scroll limit exceeds the element top
		return ( docViewLimit >= elemTop );

	}

} // chaplin.elementInView


/*	-----------------------------------------------------------------------------------------------
	Fade Blocks
--------------------------------------------------------------------------------------------------- */

chaplin.fadeBlocks = {

	init: function() {

		var scroll = window.requestAnimationFrame ||
					window.webkitRequestAnimationFrame ||
					window.mozRequestAnimationFrame ||
					window.msRequestAnimationFrame ||
					window.oRequestAnimationFrame ||
					// IE Fallback, you can even fallback to onscroll
					function( callback ) { window.setTimeout( callback, 1000/60 ) };
				 
		function loop() {
		
			var windowOffset = window.pageYOffset;
			
			if ( windowOffset < $win.outerHeight() ) {
				$( '.fade-block' ).css({ 
					'opacity': 1 - ( windowOffset * 0.002 )
				} );
			}
		
			scroll( loop )
		
		}
		loop();

	},

} // chaplin.fadeBlocks


/*	-----------------------------------------------------------------------------------------------
	Smooth Scroll
--------------------------------------------------------------------------------------------------- */

chaplin.smoothScroll = {

	init: function() {

		// Scroll to anchor
		chaplin.smoothScroll.scrollToAnchor();

		// Scroll to element
		chaplin.smoothScroll.scrollToElement();

	},

	// Scroll to anchor
	scrollToAnchor: function() {
		
		$( 'a[href*="#"]' )
		// Remove links that don't actually link to anything
		.not( '[href="#"]' )
		.not( '[href="#0"]' )
		.not( '.do-not-scroll' )
		.on( 'click', function( event ) {
			// On-page links
			if ( location.pathname.replace(/^\//, '' ) == this.pathname.replace(/^\//, '' ) && location.hostname == this.hostname ) {
				// Figure out element to scroll to
				var $target = $( this.hash );
				$target = $target.length ? $target : $( '[name=' + this.hash.slice(1) + ']' );
				// Does a scroll target exist?
				if ( $target.length ) {
					// Only prevent default if animation is actually gonna happen
					event.preventDefault();
					var scrollOffset = $target.offset().top;
					$( 'html, body' ).animate({
						scrollTop: scrollOffset,
					}, 500 );
				}
			}
		} );

	},

	// Scroll to element
	scrollToElement: function() {
		
		$( '*[data-scroll-to]' ).on( 'click', function( event ) {

			// Figure out element to scroll to
			var $target = $( $( this ).data( 'scroll-to' ) );

			// Make sure said element exists
			if ( $target.length ) {

				event.preventDefault();

				// Get options
				var additionalOffset 	= $( this ).data( 'additional-offset' ),
					scrollSpeed 		= $( this ).data( 'scroll-speed' ) ? $( this ).data( 'scroll-speed' ) : 500;

				// Determine offset
				var originalOffset = $target.offset().top,
					scrollOffset = additionalOffset ? originalOffset + additionalOffset : originalOffset;

				$( 'html, body' ).animate( {
					scrollTop: scrollOffset,
				}, scrollSpeed );

			}
			
		} );

	}

} // chaplin.smoothScroll


/*	-----------------------------------------------------------------------------------------------
	Stick Me
--------------------------------------------------------------------------------------------------- */
chaplin.stickMe = {

	init: function() {

		var $stickyElement = $( '.stick-me' );

		if ( $stickyElement.length ) {

			var stickyClass = 'is-sticky',
				stickyOffset = $stickyElement.scrollTop();

			// Our stand-in element for stickyElement while stickyElement is off on a scroll
			if ( ! $( '.sticky-adjuster' ).length ) {
				$stickyElement.before( '<div class="sticky-adjuster"></div>' );
			}

			// Stick it on resize, scroll and load
			$win.on( 'resize scroll load', function(){
				var stickyOffset = $( '.sticky-adjuster' ).offset().top;
				chaplin.stickMe.stickIt( $stickyElement, stickyClass, stickyOffset );
			} );

			chaplin.stickMe.stickIt( $stickyElement, stickyClass, stickyOffset );

		}

	},

	// Check whether to stick the element
	stickIt: function ( $stickyElement, stickyClass, stickyOffset ) {

		var winScroll = $win.scrollTop();

		if ( $stickyElement.css( 'display' ) != 'none' && winScroll > stickyOffset ) {

			// If a sticky edge element exists and we've scrolled past it, stick it
			if ( ! $stickyElement.hasClass( stickyClass ) ) {
				$stickyElement.addClass( stickyClass );
				$( '.sticky-adjuster' ).height( $stickyElement.outerHeight() ).css( 'margin-bottom', parseInt( $stickyElement.css( 'marginBottom' ) ) );
				if ( $stickyElement.is( '.header-inner' ) ) {
					$( 'body' ).addClass( 'header-is-sticky' );
				}
			}

		// If not, remove class and sticky-adjuster properties
		} else {
			chaplin.stickMe.unstickIt( $stickyElement, stickyClass );
		}

	},

	unstickIt: function( $stickyElement, stickyClass ) {

		$stickyElement.removeClass( stickyClass );
		$( '.sticky-adjuster' ).height( 0 ).css( 'margin-bottom', '0' );

		if ( $stickyElement.is( '.header-inner' ) ) {
			$( 'body' ).removeClass( 'header-is-sticky' );
		}

	}

} // Sticky Menu


/*	-----------------------------------------------------------------------------------------------
	Intrinsic Ratio Embeds
--------------------------------------------------------------------------------------------------- */

chaplin.instrinsicRatioVideos = {

	init: function() {

		chaplin.instrinsicRatioVideos.makeFit();

		$win.on( 'resize fit-videos', function() {

			chaplin.instrinsicRatioVideos.makeFit();

		} );

	},

	makeFit: function() {

		var vidSelector = "iframe, object, video";

		$( vidSelector ).each( function() {

			var $video = $( this ),
				$container = $video.parent(),
				iTargetWidth = $container.width();

			// Skip videos we want to ignore
			if ( $video.hasClass( 'intrinsic-ignore' ) || $video.parent().hasClass( 'intrinsic-ignore' ) ) {
				return true;
			}

			if ( ! $video.attr( 'data-origwidth' ) ) {

				// Get the video element proportions
				$video.attr( 'data-origwidth', $video.attr( 'width' ) );
				$video.attr( 'data-origheight', $video.attr( 'height' ) );

			}

			// Get ratio from proportions
			var ratio = iTargetWidth / $video.attr( 'data-origwidth' );

			// Scale based on ratio, thus retaining proportions
			$video.css( 'width', iTargetWidth + 'px' );
			$video.css( 'height', ( $video.attr( 'data-origheight' ) * ratio ) + 'px' );

		} );

	}

} // chaplin.instrinsicRatioVideos


/*	-----------------------------------------------------------------------------------------------
	Scroll Lock
--------------------------------------------------------------------------------------------------- */

chaplin.scrollLock = {

	init: function() {

		// Init variables
		window.scrollLocked = false,
		window.prevScroll = {
			scrollLeft : $win.scrollLeft(),
			scrollTop  : $win.scrollTop()
		},
		window.prevLockStyles = {},
		window.lockStyles = {
			'overflow-y' : 'scroll',
			'position'   : 'fixed',
			'width'      : '100%'
		};

		// Instantiate cache in case someone tries to unlock before locking
		chaplin.scrollLock.saveStyles();

	},

	// Save context's inline styles in cache
	saveStyles: function() {

		var styleAttr = $( 'html' ).attr( 'style' ),
			styleStrs = [],
			styleHash = {};

		if ( ! styleAttr ) {
			return;
		}

		styleStrs = styleAttr.split( /;\s/ );

		$.each( styleStrs, function serializeStyleProp( styleString ) {
			if ( ! styleString ) {
				return;
			}

			var keyValue = styleString.split( /\s:\s/ );

			if ( keyValue.length < 2 ) {
				return;
			}

			styleHash[ keyValue[ 0 ] ] = keyValue[ 1 ];
		} );

		$.extend( prevLockStyles, styleHash );
	},

	// Lock the scroll (do not call this directly)
	lock: function() {

		var appliedLock = {};

		if ( scrollLocked ) {
			return;
		}

		// Save scroll state and styles
		prevScroll = {
			scrollLeft : $win.scrollLeft(),
			scrollTop  : $win.scrollTop()
		};

		chaplin.scrollLock.saveStyles();

		// Compose our applied CSS, with scroll state as styles
		$.extend( appliedLock, lockStyles, {
			'left' : - prevScroll.scrollLeft + 'px',
			'top'  : - prevScroll.scrollTop + 'px'
		} );

		// Then lock styles and state
		$( 'html' ).css( appliedLock );
		$win.scrollLeft( 0 ).scrollTop( 0 );

		scrollLocked = true;
	},

	// Unlock the scroll (do not call this directly)
	unlock: function() {

		if ( ! scrollLocked ) {
			return;
		}

		// Revert styles and state
		$( 'html' ).attr( 'style', $( '<x>' ).css( prevLockStyles ).attr( 'style' ) || '' );
		$win.scrollLeft( prevScroll.scrollLeft ).scrollTop( prevScroll.scrollTop );

		scrollLocked = false;
	},

	// Call this to lock or unlock the scroll
	setTo: function( on ) {

		// If an argument is passed, lock or unlock accordingly
		if ( arguments.length ) {
			if ( on ) {
				chaplin.scrollLock.lock();
			} else {
				chaplin.scrollLock.unlock();
			}
			// If not, toggle to the inverse state
		} else {
			if ( scrollLocked ) {
				chaplin.scrollLock.unlock();
			} else {
				chaplin.scrollLock.lock();
			}
		}

	},

} // chaplin.scrollLock


/*	-----------------------------------------------------------------------------------------------
	Load More
--------------------------------------------------------------------------------------------------- */

chaplin.loadMore = {

	init: function() {

		var $pagination = $( '#pagination' );

		// First, check that there's a pagination
		if ( $pagination.length ) {

			// Default values for variables
			window.loading = false;
			window.lastPage = false;

			chaplin.loadMore.prepare( $pagination );

		}

	},

	prepare: function( $pagination ) {

		// Get the query arguments from the pagination element
		var query_args = JSON.parse( $pagination.attr( 'data-query-args' ) );

		// If we're already at the last page, exit out here
		if ( query_args.paged == query_args.max_num_pages ) {
			$pagination.addClass( 'last-page' );
		} else {
			$pagination.removeClass( 'last-page' );
		}

		// Get the load more type (button or scroll)
		var loadMoreType = $pagination.data( 'pagination-type' );

		if ( ! loadMoreType ) {
			var loadMoreType = 'links';
		}

		// Do the appropriate load more detection, depending on the type
		if ( loadMoreType == 'scroll' ) {
			chaplin.loadMore.detectScroll( $pagination, query_args );
		} else if ( loadMoreType == 'button' ) {
			chaplin.loadMore.detectButtonClick( $pagination, query_args );
		}

	},

	// Load more on scroll
	detectScroll: function( $pagination, query_args ) {

		$win.on( 'did-interval-scroll', function() {

			// If it's the last page, or we're already loading, we're done here
			if ( lastPage || loading ) {
				return;
			}

			var paginationOffset 	= $pagination.offset().top,
				winOffset 			= $win.scrollTop() + $win.outerHeight();

			// If the bottom of the window is below the top of the pagination, start loading
			if ( ( winOffset > paginationOffset ) ) {
				chaplin.loadMore.loadPosts( $pagination, query_args );
			}

		} );

	},

	// Load more on click
	detectButtonClick: function( $pagination, query_args ) {

		// Load on click
		$( '#load-more' ).on( 'click', function() {

			// Make sure we aren't already loading
			if ( loading ) {
				return;
			}

			chaplin.loadMore.loadPosts( $pagination, query_args );
			return false;
		} );

	},

	// Load the posts
	loadPosts: function( $pagination, query_args ) {

		// We're now loading
		loading = true;
		$pagination.addClass( 'loading' ).removeClass( 'last-page' );

		// Increment paged to indicate another page has been loaded
		query_args.paged++;

		// Prepare the query args for submission
		var json_query_args = JSON.stringify( query_args );

		$.ajax({
			url: chaplin_ajax_load_more.ajaxurl,
			type: 'post',
			data: {
				action: 'chaplin_ajax_load_more',
				json_data: json_query_args
			},
			success: function( result ) {

				// Get the results
				var $result = $( result ),
					$articleWrapper = $( $pagination.data( 'load-more-target' ) );

				// If there are no results, we're at the last page
				if ( ! $result.length ) {
					loading = false;
					$articleWrapper.addClass( 'no-results' );
					$pagination.addClass( 'last-page' ).removeClass( 'loading' );
				}

				if ( $result.length ) {

					$articleWrapper.removeClass( 'no-results' );

					// Wait for the images to load
					$result.imagesLoaded( function() {

						// Append the results
						$articleWrapper.append( $result );

						$win.triggerHandler( 'ajax-content-loaded' );
						$win.triggerHandler( 'did-interval-scroll' );

						// Update history
						chaplin.loadMore.updateHistory( query_args.paged );

						// We're now finished with the loading
						loading = false;
						$pagination.removeClass( 'loading' );

						// If that was the last page, make sure we don't check for any more
						if ( query_args.paged == query_args.max_num_pages ) {
							$pagination.addClass( 'last-page' );
							lastPage = true;
							return;
						} else {
							$pagination.removeClass( 'last-page' );
							lastPage = false;
						}

					} );

				}

			},

			error: function( jqXHR, exception ) {
				ajaxErrors( jqXHR, exception );
			}
		} );

	},

	// Update browser history
    updateHistory: function( paged ) {

		var newUrl,
			currentUrl = document.location.href;

		var hasPaginationRegexp = new RegExp( '^(.*/page)/[0-9]*/(.*$)' );

		if ( hasPaginationRegexp.test( currentUrl ) ) {
			newUrl = currentUrl.replace( hasPaginationRegexp, '$1/' + paged + '/$2' );
		} else {
			var beforeSearchReplaceRegexp = new RegExp( '^([^?]*)(\\??.*$)' );
			newUrl = currentUrl.replace( beforeSearchReplaceRegexp, '$1page/' + paged + '/$2' );
		}

		history.pushState( {}, '', newUrl );

	}

} // chaplin.loadMore


/*	-----------------------------------------------------------------------------------------------
	Function Calls
--------------------------------------------------------------------------------------------------- */

$doc.ready( function() {

	chaplin.intervalScroll.init();				// Check for scroll on an interval

	chaplin.resizeEnd.init();					// Trigger event at end of resize

	chaplin.isScrolling.init();					// Check for scroll direction

	chaplin.toggles.init();						// Handle toggles

	chaplin.coverModals.init();					// Handle cover modals

	chaplin.elementInView.init();				// Check if elements are in view

	chaplin.fadeBlocks.init();					// Fade elements on scroll

	chaplin.instrinsicRatioVideos.init();		// Retain aspect ratio of videos on window resize

	chaplin.smoothScroll.init();				// Smooth scroll to anchor link or a specific element

	chaplin.stickMe.init();						// Stick elements on scroll

	chaplin.scrollLock.init();					// Scroll Lock

	chaplin.loadMore.init();					// Load More

} );