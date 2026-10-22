/**
 * Estatein front-end behaviour.
 *
 * Vanilla JS, no dependencies, loaded with `defer`. Every feature is a
 * progressive enhancement: the page is fully usable without JavaScript.
 */
( function () {
	'use strict';

	var i18n = window.estateinI18n || { of: 'of', readMore: 'Read More', readLess: 'Read Less' };
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/**
	 * Announcement banner: dismiss and remember the choice.
	 */
	function initBanner() {
		var close = document.querySelector( '[data-banner-close]' );
		if ( ! close ) {
			return;
		}

		close.addEventListener( 'click', function () {
			document.documentElement.classList.add( 'banner-dismissed' );
			try {
				window.localStorage.setItem( 'estateinBannerClosed', '1' );
			} catch ( e ) {
				// Storage unavailable (private mode): dismissal lasts for this page only.
			}
			var brand = document.querySelector( '.site-header .logo' );
			if ( brand ) {
				brand.focus();
			}
		} );
	}

	/**
	 * Mobile navigation toggle with Escape and outside-click to close.
	 */
	function initNav() {
		var toggle = document.querySelector( '[data-nav-toggle]' );
		var nav = document.querySelector( '[data-nav]' );
		if ( ! toggle || ! nav ) {
			return;
		}

		function setOpen( open ) {
			toggle.setAttribute( 'aria-expanded', String( open ) );
			nav.classList.toggle( 'is-open', open );
		}

		toggle.addEventListener( 'click', function () {
			setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' && nav.classList.contains( 'is-open' ) ) {
				setOpen( false );
				toggle.focus();
			}
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( nav.classList.contains( 'is-open' ) && ! nav.contains( event.target ) && ! toggle.contains( event.target ) ) {
				setOpen( false );
			}
		} );

		// Reset when resizing back to desktop.
		window.matchMedia( '(min-width: 1024px)' ).addEventListener( 'change', function ( mq ) {
			if ( mq.matches ) {
				setOpen( false );
			}
		} );
	}

	/**
	 * Carousels: native scroll-snap tracks with prev/next buttons and an
	 * "01 of 10" counter. Swipe, trackpad and keyboard scrolling all work
	 * because the track is a real scroll container.
	 */
	function initCarousels() {
		document.querySelectorAll( '[data-carousel]' ).forEach( function ( carousel ) {
			var track = carousel.querySelector( '[data-carousel-track]' );
			var prev = carousel.querySelector( '[data-carousel-prev]' );
			var next = carousel.querySelector( '[data-carousel-next]' );
			var current = carousel.querySelector( '[data-carousel-current]' );
			var slides = track ? track.children : [];
			if ( ! track || ! slides.length ) {
				return;
			}

			function slideStep() {
				var gap = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
				return slides[ 0 ].getBoundingClientRect().width + gap;
			}

			function pad( n ) {
				return n < 10 ? '0' + n : String( n );
			}

			function update() {
				var maxScroll = track.scrollWidth - track.clientWidth;
				var index = Math.round( track.scrollLeft / slideStep() );
				var atEnd = track.scrollLeft >= maxScroll - 2;

				// At the end, count the last slide so the counter reaches "10 of 10".
				var shown = atEnd ? slides.length : Math.min( index + 1, slides.length );

				if ( current ) {
					current.textContent = pad( shown );
				}
				if ( prev ) {
					prev.disabled = track.scrollLeft <= 2;
				}
				if ( next ) {
					next.disabled = atEnd;
				}
			}

			function go( direction ) {
				track.scrollBy( {
					left: direction * slideStep(),
					behavior: reduceMotion ? 'auto' : 'smooth',
				} );
			}

			if ( prev ) {
				prev.addEventListener( 'click', function () {
					go( -1 );
				} );
			}
			if ( next ) {
				next.addEventListener( 'click', function () {
					go( 1 );
				} );
			}

			track.addEventListener( 'keydown', function ( event ) {
				if ( event.target !== track ) {
					return;
				}
				if ( event.key === 'ArrowRight' ) {
					event.preventDefault();
					go( 1 );
				} else if ( event.key === 'ArrowLeft' ) {
					event.preventDefault();
					go( -1 );
				}
			} );

			var ticking = false;
			track.addEventListener( 'scroll', function () {
				if ( ! ticking ) {
					window.requestAnimationFrame( function () {
						update();
						ticking = false;
					} );
					ticking = true;
				}
			}, { passive: true } );

			window.addEventListener( 'resize', update );
			update();
		} );
	}

	/**
	 * FAQ cards: "Read More" reveals the full answer in place.
	 */
	function initFaqs() {
		document.querySelectorAll( '[data-faq-toggle]' ).forEach( function ( button ) {
			var answer = document.getElementById( button.getAttribute( 'aria-controls' ) );
			if ( ! answer ) {
				return;
			}

			button.addEventListener( 'click', function () {
				var open = button.getAttribute( 'aria-expanded' ) !== 'true';
				button.setAttribute( 'aria-expanded', String( open ) );
				button.textContent = open ? i18n.readLess : i18n.readMore;
				answer.hidden = ! open;
			} );
		} );
	}

	/**
	 * Fade sections and cards up as they scroll into view.
	 */
	function initReveal() {
		if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		var targets = document.querySelectorAll( '.section-head, .carousel, .card-grid > li, .cta__inner, .features' );
		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		}, { rootMargin: '0px 0px -10% 0px' } );

		targets.forEach( function ( el ) {
			// Elements already on screen stay visible (no flash on load).
			if ( el.getBoundingClientRect().top < window.innerHeight ) {
				return;
			}
			el.classList.add( 'reveal' );
			observer.observe( el );
		} );
	}

	/**
	 * Hero stats count up from zero once ("200+", "10k+", "16+").
	 */
	function initCountUp() {
		if ( reduceMotion ) {
			return;
		}

		document.querySelectorAll( '[data-count-up]' ).forEach( function ( el ) {
			var match = el.textContent.trim().match( /^(\D*)(\d+(?:\.\d+)?)(.*)$/ );
			if ( ! match ) {
				return;
			}

			var prefix = match[ 1 ];
			var target = parseFloat( match[ 2 ] );
			var suffix = match[ 3 ];
			var decimals = ( match[ 2 ].split( '.' )[ 1 ] || '' ).length;
			var duration = 1200;
			var start = null;

			// Keep the final width so the layout does not shift while counting.
			el.style.minWidth = el.offsetWidth + 'px';

			function frame( time ) {
				if ( start === null ) {
					start = time;
				}
				var progress = Math.min( ( time - start ) / duration, 1 );
				var eased = 1 - Math.pow( 1 - progress, 3 );
				el.textContent = prefix + ( target * eased ).toFixed( decimals ) + suffix;
				if ( progress < 1 ) {
					window.requestAnimationFrame( frame );
				}
			}

			window.requestAnimationFrame( frame );
		} );
	}

	/**
	 * Submit the property-type filter as soon as it changes.
	 */
	function initAutoSubmit() {
		document.querySelectorAll( '[data-autosubmit]' ).forEach( function ( select ) {
			select.addEventListener( 'change', function () {
				select.form.submit();
			} );
		} );
	}

	/**
	 * Accessible inline validation for forms marked data-validate.
	 * The server validates again; this only gives faster feedback.
	 */
	function initValidation() {
		document.querySelectorAll( 'form[data-validate]' ).forEach( function ( form ) {
			function check( field ) {
				var errorId = field.id + '-error';
				var error = document.getElementById( errorId );
				var valid = field.checkValidity();

				field.setAttribute( 'aria-invalid', String( ! valid ) );

				if ( ! valid ) {
					if ( ! error ) {
						error = document.createElement( 'p' );
						error.className = 'field__error';
						error.id = errorId;
						( field.closest( '.field' ) || field.parentNode ).appendChild( error );
						field.setAttribute( 'aria-describedby', errorId );
					}
					error.textContent = field.validationMessage;
				} else if ( error ) {
					error.remove();
					field.removeAttribute( 'aria-describedby' );
				}
				return valid;
			}

			form.querySelectorAll( '[required]' ).forEach( function ( field ) {
				if ( ! field.id ) {
					field.id = 'field-' + Math.random().toString( 36 ).slice( 2 );
				}
				field.addEventListener( 'blur', function () {
					check( field );
				} );
			} );

			form.addEventListener( 'submit', function ( event ) {
				var firstInvalid = null;
				form.querySelectorAll( '[required]' ).forEach( function ( field ) {
					if ( ! check( field ) && ! firstInvalid ) {
						firstInvalid = field;
					}
				} );
				if ( firstInvalid ) {
					event.preventDefault();
					firstInvalid.focus();
				}
			} );
		} );

		// After a redirect, move focus to the status message so it is announced.
		var message = document.querySelector( '[data-focus-on-load]' );
		if ( message ) {
			message.focus();
		}
	}

	initBanner();
	initNav();
	initCarousels();
	initFaqs();
	initReveal();
	initCountUp();
	initAutoSubmit();
	initValidation();
}() );
