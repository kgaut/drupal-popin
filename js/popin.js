(function($) { 'use strict';

	// Class creation
	var Popin = function(el) { this.init(el); }
	var P = Popin.prototype;

	// Parameters
	P.popinOffset = 60;

	// ======================================
	// @function init(el)
	// __ constructor
	// ======================================
	P.init = function(el) {
		this.$container = $(el);
		this.$triggers = $('[data-popin="'+el.id+'"]');
		this.$actions = this.$container.find('[data-action]');

		this.$container.addClass('initialised');

		// Event Listeners
		this.$triggers.on('click', this.open.bind(this));
		$(window).on('keydown', this.onKeyDown.bind(this));
		this.$actions.on('click', this.onClickAction.bind(this));
	}

	// ======================================
	// @function onKeyDown()
	//
	// ======================================
	P.onKeyDown = function(e) {
		switch (e.keyCode) {
			case 27: // echap
				this.close();
			break;
		}
	}

	// ======================================
	// @function onClickAction()
	//
	// ======================================
	P.onClickAction = function(e) {
		var action = e.currentTarget.attributes['data-action'].value;
			e.preventDefault();

		switch (action) {
			case 'close':
				this.close();
			break;
		}
	}

	// ======================================
	// @function open()
	//
	// ======================================
	P.open = function(e) {
		this.$container.addClass('active');
	}

	// ======================================
	// @function close()
	//
	// ======================================
	P.close = function(e) {
		this.$container.removeClass('active');
	}

	// set the Class to global scope
	window.Popin = Popin;


	// Instanciation
	var nbCheckPopinMax = 5,
		checkPopinInterval = 0,
		nbCheckPopin = 0;

	var createPopins = function() {
			$('.popin').each(function() {
			if (!$(this).hasClass('initialised')) {
				new Popin(this);
			}
		});

		if (++nbCheckPopin >= nbCheckPopinMax) {
			clearInterval(checkPopinInterval);
		}
	}

	var checkPopinsJs = function() {
		checkPopinInterval = setInterval(createPopins, 1000);
	}
	createPopins();
	checkPopinsJs();

})(jQuery);
