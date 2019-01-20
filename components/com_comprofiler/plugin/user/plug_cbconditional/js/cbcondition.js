(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbcondition = {};

				cbcondition.options = options;
				cbcondition.defaults = $.fn.cbcondition.defaults;
				cbcondition.settings = $.extend( true, {}, cbcondition.defaults, cbcondition.options );
				cbcondition.element = $( $this );

				if ( ( $this != window ) && cbcondition.settings.useData ) {
					$.each( cbcondition.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbcondition.element.data( 'cbcondition' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbcondition.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbcondition.element.data( 'cbcondition' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbcondition.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbcondition.element.triggerHandler( 'cbcondition.init.before', [cbcondition] );

				if ( ! cbcondition.settings.init ) {
					return;
				}

				if ( ! $.isArray( cbcondition.settings.conditions ) ) {
					cbcondition.settings.conditions = [];
				}

				if ( ! $.isArray( cbcondition.settings.show ) ) {
					cbcondition.settings.show = [];
				}

				if ( ! $.isArray( cbcondition.settings.hide ) ) {
					cbcondition.settings.hide = [];
				}

				cbcondition.changeHandler = function() {
					conditionElement.call( this, cbcondition );
				};

				cbcondition.element.each( function() {
					if ( this != window ) {
						var target = null;

						if ( $( this ).is( 'input' ) || $( this ).is( 'select' ) || $( this ).is( 'textarea' ) ) {
							target = $( this );
						} else {
							target = $( this ).find( 'input,select,textarea' ).first();

							if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
								target = $( this ).find( 'input[name="' + target.attr( 'name' ) + '"]' );
							}
						}

						target.on( 'keyup change condition', cbcondition.changeHandler );
					}
				});

				conditionElement.call( this, cbcondition );

				if ( $this != window ) {
					// Destroy the cbcondition element:
					cbcondition.element.on( 'remove destroy.cbcondition', function() {
						cbcondition.element.cbcondition( 'destroy' );
					});

					// Rebind the cbcondition element to pick up any data attribute modifications:
					cbcondition.element.on( 'rebind.cbcondition', function() {
						cbcondition.element.cbcondition( 'rebind' );
					});

					// If the cbcondition element is modified we need to rebuild it to ensure all our bindings are still ok:
					cbcondition.element.on( 'modified.cbcondition', function( e, oldId, newId, index ) {
						if ( oldId != newId ) {
							cbcondition.element.cbcondition( 'destroy' );
							cbcondition.element.cbcondition( cbcondition.options );
						}
					});

					// If the cbcondition is cloned we need to rebind it back:
					cbcondition.element.on( 'cloned.cbcondition', function( e, oldId ) {
						$( this ).off( 'destroy.cbcondition' );
						$( this ).off( 'rebind.cbcondition' );
						$( this ).off( 'cloned.cbcondition' );
						$( this ).off( 'modified.cbcondition' );
						$( this ).removeData( 'cbconditions' );

						var target = null;

						if ( $( this ).is( 'input' ) || $( this ).is( 'select' ) || $( this ).is( 'textarea' ) ) {
							target = $( this );
						} else {
							target = $( this ).find( 'input,select,textarea' ).first();

							if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
								target = $( this ).find( 'input[name="' + target.attr( 'name' ) + '"]' );
							}
						}

						target.off( 'keyup change condition', cbcondition.changeHandler );

						$( this ).cbcondition( cbcondition.options );
					});
				}

				cbcondition.element.triggerHandler( 'cbcondition.init.after', [cbcondition] );

				if ( $this != window ) {
					// Bind the cbcondition to the element so it's reusable and chainable:
					var cbconditions = cbcondition.element.data( 'cbconditions' );

					if ( ! $.isArray( cbconditions ) ) {
						cbconditions = [];
					}

					cbconditions.push( cbcondition );

					cbcondition.element.data( 'cbconditions', cbconditions );
				}

				// Add this instance to our instance array so we can keep track of our cbcondition instances:
				instances.push( cbcondition );
			});
		},
		rebind: function() {
			var cbconditions = $( this ).data( 'cbconditions' );

			if ( ! cbconditions ) {
				return this;
			}

			$.each( cbconditions, function() {
				var cbcondition = this;

				cbcondition.element.cbcondition( 'destroy' );
				cbcondition.element.cbcondition( cbcondition.options );
			});

			return this;
		},
		destroy: function() {
			var cbconditions = $( this ).data( 'cbconditions' );

			if ( ! cbconditions ) {
				return false;
			}

			$.each( cbconditions, function() {
				var cbcondition = this;

				cbcondition.element.off( 'destroy.cbcondition' );
				cbcondition.element.off( 'rebind.cbcondition' );
				cbcondition.element.off( 'cloned.cbcondition' );
				cbcondition.element.off( 'modified.cbcondition' );

				cbcondition.element.each( function() {
					if ( this == window ) {
						return;
					}

					var target = null;

					if ( $( this ).is( 'input' ) || $( this ).is( 'select' ) || $( this ).is( 'textarea' ) ) {
						target = $( this );
					} else {
						target = $( this ).find( 'input,select,textarea' ).first();

						if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
							target = $( this ).find( 'input[name="' + target.attr( 'name' ) + '"]' );
						}
					}

					target.off( 'keyup change condition', cbcondition.changeHandler );
				});

				$.each( instances, function( i, instance ) {
					if ( instance.element == cbcondition.element ) {
						instances.splice( i, 1 );

						return false;
					}

					return true;
				});

				revertCondition.call( cbcondition, cbcondition );

				cbcondition.element.removeData( 'cbcondition' );
				cbcondition.element.triggerHandler( 'cbcondition.destroyed', [cbcondition] );
			});

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function revertCondition( cbcondition ) {
		var $this = ( this.jquery ? this : $( this ) );
		var show = cbcondition.settings.show;
		var hide = cbcondition.settings.hide;

		if ( ! $.isArray( show ) ) {
			show = [];
		}

		if ( ! $.isArray( hide ) ) {
			hide = [];
		}

		$.each( show, function( i, selector ) {
			showElement.call( $this, cbcondition, $( selector ) );
		});

		$.each( hide, function( i, selector ) {
			showElement.call( $this, cbcondition, $( selector ) );
		});
	}

	function conditionElement( cbcondition, match ) {
		var $this = ( this.jquery ? this : $( this ) );
		var conditions = cbcondition.settings.conditions;

		if ( ! $.isArray( conditions ) ) {
			conditions = [];
		}

		var matched = false;

		if ( typeof match == 'undefined' ) {
			$.each( conditions, function( orIndex, orCondition ) {
				if ( ! $.isArray( orCondition ) ) {
					orCondition = [];
				}

				var andMatched = true;

				$.each( orCondition, function( andIndex, andCondition ) {
					var element = andCondition.element;
					var input = andCondition.input;
					var target = $this;

					if ( typeof element != 'undefined' ) {
						element = ( element.jquery ? element : $( element ) );

						if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
							target = element;
						} else {
							target = element.find( 'input,select,textarea' ).first();

							if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
								target = element.find( 'input[name="' + target.attr( 'name' ) + '"]' );
							}
						}
					}

					if ( target.is( 'input' ) || target.is( 'select' ) || target.is( 'textarea' ) ) {
						if ( target.is( 'input[type="checkbox"]' ) || target.is( 'input[type="radio"]' ) ) {
							input = [];

							target.each( function() {
								if ( $( this ).is( ':checked' ) ) {
									input.push( $( this ).val() );
								}
							});
						} else if ( target.is( 'select[multiple]' ) ) {
							input = target.val();

							if ( input && ( ! $.isArray( input ) ) ) {
								input = input.split( ',' );
							}
						} else {
							input = target.val();
						}
					}

					var isMatched = matchCondition.call( target, cbcondition, input, andCondition.operator, andCondition.value, andCondition.delimiter );

					if ( andMatched ) {
						andMatched = isMatched;
					}

					if ( cbcondition.settings.debug ) {
						console.log({
							orIndex: ( orIndex + 1 ),
							andIndex: ( andIndex + 1 ),
							element: element,
							target: target,
							input: input,
							operator: andCondition.operator,
							value: andCondition.value,
							delimiter: andCondition.delimiter,
							matched: isMatched
						});
					}
				});

				if ( ( ! matched  ) && andMatched ) {
					matched = true;
				}
			});
		} else {
			matched = match;
		}

		if ( matched ) {
			$.each( cbcondition.settings.show, function( i, selector ) {
				showElement.call( $this, cbcondition, $( selector ) );
			});

			$.each( cbcondition.settings.hide, function( i, selector ) {
				hideElement.call( $this, cbcondition, $( selector ) );
			});

			cbcondition.element.triggerHandler( 'cbcondition.match.true', [cbcondition] );
		} else {
			$.each( cbcondition.settings.show, function( i, selector ) {
				hideElement.call( $this, cbcondition, $( selector ) );
			});

			$.each( cbcondition.settings.hide, function( i, selector ) {
				showElement.call( $this, cbcondition, $( selector ) );
			});

			cbcondition.element.triggerHandler( 'cbcondition.match.false', [cbcondition] );
		}

		// Trigger conditions for elements that might have been shown or hidden:
		$.each( $.extend( cbcondition.settings.show, cbcondition.settings.hide ), function( i, selector ) {
			var element = $( selector );

			if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) || element.is( 'option' ) ) {
				element.triggerHandler( 'condition' );
			} else {
				element.find( 'input,select,textarea' ).triggerHandler( 'condition' );
			}
		});
	}

	function showElement( cbcondition, element ) {
		if ( element.length && element.hasClass( 'cbDisplayDisabled' ) && ( ! element.parents( '.cbDisplayDisabled' ).length ) ) {
			if ( element.hasClass( 'cbTabPane' ) ) {
				var cbtabs = element.closest( '.cbTabs' );

				if ( cbtabs.data( 'cbtabs' ) ) {
					cbtabs.cbtabs( 'show', element.attr( 'id' ) );
				}
			} else if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
				if ( element.closest( '.cbSingleCntrl' ).length ) {
					element.closest( '.cbSingleCntrl' ).removeClass( 'cbDisplayDisabled hidden' );
				} else if ( element.closest( '.cbSnglCtrlLbl' ).length ) {
					element.closest( '.cbSnglCtrlLbl' ).removeClass( 'cbDisplayDisabled hidden' );
				} else if ( element.parent( 'label' ).length ) {
					element.parent( 'label' ).removeClass( 'cbDisplayDisabled hidden' );
				}
			}

			if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
				element.removeClass( 'cbValidationDisabled' );
			} else if ( element.is( 'option' ) ) {
				element.prop( 'disabled', false );
			} else {
				element.find( 'input,select,textarea' ).removeClass( 'cbValidationDisabled' );
			}

			element.removeClass( 'cbDisplayDisabled hidden' );

			cbcondition.element.triggerHandler( 'cbcondition.show', [cbcondition, element] );
		}
	}

	function hideElement( cbcondition, element ) {
		if ( element.length && ( ! element.hasClass( 'cbDisplayDisabled' ) ) && ( ! element.parents( '.cbDisplayDisabled' ).length ) ) {
			if ( element.hasClass( 'cbTabPane' ) ) {
				var cbtabs = element.closest( '.cbTabs' );

				if ( cbtabs.data( 'cbtabs' ) ) {
					cbtabs.cbtabs( 'hide', element.attr( 'id' ) );
				}
			} else if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
				if ( element.closest( '.cbSingleCntrl' ).length ) {
					element.closest( '.cbSingleCntrl' ).addClass( 'cbDisplayDisabled hidden' );
				} else if ( element.closest( '.cbSnglCtrlLbl' ).length ) {
					element.closest( '.cbSnglCtrlLbl' ).addClass( 'cbDisplayDisabled hidden' );
				} else if ( element.parent( 'label' ).length ) {
					element.parent( 'label' ).addClass( 'cbDisplayDisabled hidden' );
				}
			}

			if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
				if ( cbcondition.settings.reset ) {
					if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
						element.prop( 'checked', false );
					} else if ( ! element.is( 'input[type="hidden"]' ) ) {
						element.val( '' );
					}
				}

				element.addClass( 'cbValidationDisabled' );
			} else if ( element.is( 'option' ) ) {
				if ( cbcondition.settings.reset ) {
					element.prop( 'selected', false );
				}

				element.prop( 'disabled', true );
			} else {
				var elements = element.find( 'input,select,textarea' );

				if ( cbcondition.settings.reset ) {
					elements.each( function() {
						if ( $( this ).is( 'input[type="checkbox"]' ) || $( this ).is( 'input[type="radio"]' ) ) {
							$( this ).prop( 'checked', false );
						} else if ( ! $( this ).is( 'input[type="hidden"]' ) ) {
							$( this ).val( '' );
						}
					});
				}

				elements.addClass( 'cbValidationDisabled' );
			}

			element.addClass( 'cbDisplayDisabled hidden' );

			cbcondition.element.triggerHandler( 'cbcondition.hide', [cbcondition, element] );
		}
	}

	function matchCondition( cbcondition, input, operator, value, delimiter ) {
		if ( $.isArray( value ) ) {
			value = value.join( '|*|' );
		}

		if ( $.isArray( input ) ) {
			input = input.join( '|*|' );
		}

		input				=	$.trim( input );
		value				=	$.trim( value );

		var match			=	false;

		switch ( operator ) {
			case '!=':
			case '<>':
			case 1:
				match		=	( input != value );
				break;
			case '>':
			case 2:
				match		=	( input > value );
				break;
			case '<':
			case 3:
				match		=	( input < value );
				break;
			case '>=':
			case 4:
				match		=	( input >= value );
				break;
			case '<=':
			case 5:
				match		=	( input <= value );
				break;
			case 'empty':
			case 6:
				match		=	( ! input.length );
				break;
			case '!empty':
			case 7:
				match		=	( input.length );
				break;
			case 'contain':
			case 8:
				if ( delimiter ) {
					match	=	( input.split( delimiter ).indexOf( value ) != -1 );
				} else {
					if ( input === '' ) {
						// Can't have an empty needle so fallback to simple equal to check:
						return matchCondition( cbcondition, input, 0, value, delimiter );
					}

					match	=	( input.indexOf( value ) != -1 );
				}
				break;
			case '!contain':
			case 9:
				if ( delimiter ) {
					match	=	( input.split( delimiter ).indexOf( value ) == -1 );
				} else {
					if ( input === '' ) {
						// Can't have an empty needle so fallback to simple not equal to check:
						return matchCondition( cbcondition, input, 1, value, delimiter );
					}

					match	=	( input.indexOf( value ) == -1 );
				}
				break;
			case 'regexp':
			case 10:
				if ( value === '' ) {
					// Can't have an empty regexp so fallback to simple equal to check:
					return matchCondition( cbcondition, input, 0, value, delimiter );
				}

				match		=	( input.match( eval( value ) ) );
				break;
			case '!regexp':
			case 11:
				if ( value === '' ) {
					// Can't have an empty regexp so fallback to simple not equal to check:
					return matchCondition( cbcondition, input, 1, value, delimiter );
				}

				match		=	( ! input.match( eval( value ) ) );
				break;
			case 'in':
			case 12:
				if ( delimiter ) {
					match	=	( value.split( delimiter ).indexOf( input ) != -1 );
				} else {
					if ( value === '' ) {
						// Can't have an empty needle so fallback to simple equal to check:
						return matchCondition( cbcondition, input, 0, value, delimiter );
					}

					match	=	( value.indexOf( input ) != -1 );
				}
				break;
			case '!in':
			case 13:
				if ( delimiter ) {
					match	=	( value.split( delimiter ).indexOf( input ) == -1 );
				} else {
					if ( value === '' ) {
						// Can't have an empty needle so fallback to simple not equal to check:
						return matchCondition( cbcondition, input, 1, value, delimiter );
					}

					match	=	( value.indexOf( input ) == -1 );
				}
				break;
			case '=':
			case 0:
			default:
				match		=	( input == value );
				break;
		}

		cbcondition.element.triggerHandler( 'cbcondition.match', [cbcondition, input, operator, value, match] );

		return match;
	}

	$.fn.cbcondition = function( options ) {
		var $this = this;

		if ( ! $( this ).length ) {
			// Looks like condition was called on an element that doesn't exist so lets just fallback to an unbound condition:
			return $( window ).cbcondition( options );
		}

		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbcondition.defaults = {
		init: true,
		useData: true,
		target: null,
		conditions: [],
		show: [],
		hide: [],
		reset: true,
		debug: false
	};
})(jQuery);