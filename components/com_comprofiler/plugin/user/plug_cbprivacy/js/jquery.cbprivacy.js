(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbprivacy = $( $this ).data( 'cbprivacy' );

				if ( cbprivacy ) {
					return; // cbprivacy is already bound; so no need to rebind below
				}

				cbprivacy = {};
				cbprivacy.options = options;
				cbprivacy.defaults = $.fn.cbprivacy.defaults;
				cbprivacy.settings = $.extend( true, {}, cbprivacy.defaults, cbprivacy.options );
				cbprivacy.element = $( $this );

				if ( cbprivacy.settings.useData ) {
					$.each( cbprivacy.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbprivacy.element.data( 'cbprivacy' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbprivacy.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbprivacy.element.data( 'cbprivacy' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbprivacy.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbprivacy.element.trigger( 'cbprivacy.init.before', [cbprivacy] );

				if ( ! cbprivacy.settings.init ) {
					return;
				}

				cbprivacy.dropdownTemplate = function( option ) {
					if ( typeof option.id != 'undefined' ) {
						var icon = $( option.element ).data( 'cbprivacy-option-icon' );

						if ( typeof icon != 'undefined' ) {
							return $( '<span><span class="cb_template cbPrivacySelectOptionIcon">' + icon + '</span><span class="cbPrivacySelectOptionText">' + option.text + '</span></span>' );
						} else {
							return $( '<span><span class="cb_template cbPrivacySelectOptionNoIcon"></span><span class="cbPrivacySelectOptionText">' + option.text + '</span></span>' );
						}
					} else {
						return option.text;
					}
				};

				cbprivacy.selectionTemplate = function( option ) {
					if ( typeof option.id != 'undefined' ) {
						var icon = $( option.element ).data( 'cbprivacy-option-icon' );

						if ( cbprivacy.settings.layout == 'tags' ) {
							return option.text;
						} else if ( cbprivacy.settings.layout == 'icon' ) {
							if ( typeof icon != 'undefined' ) {
								return $( '<span><span class="cb_template cbPrivacySelectOptionIcon">' + icon + '</span></span>' );
							} else {
								return $( '<span><span class="cb_template cbPrivacySelectOptionNoIcon"><span class="cbPrivacySelectOptionIconCustom fa fa-cog"></span></span></span>' );
							}
						} else {
							var value = cbprivacy.element.cbselect( 'get' );

							if ( value && ( value.length > 1 ) ) {
								return $( '<span><span class="cb_template cbPrivacySelectOptionIcon">' + icon + '</span><span class="cbPrivacySelectOptionText">' + cbprivacy.settings.custom + '</span><span class="cbPrivacySelectOptionIconCaret fa fa-caret-down"></span></span>' );
							} else {
								if ( typeof icon != 'undefined' ) {
									return $( '<span><span class="cb_template cbPrivacySelectOptionIcon">' + icon + '</span><span class="cbPrivacySelectOptionText">' + option.text + '</span><span class="cbPrivacySelectOptionIconCaret fa fa-caret-down"></span></span>' );
								} else {
									return $( '<span><span class="cb_template cbPrivacySelectOptionNoIcon"><span class="cbPrivacySelectOptionIconCustom fa fa-cog"></span></span><span class="cbPrivacySelectOptionText">' + option.text + '</span><span class="cbPrivacySelectOptionIconCaret fa fa-caret-down"></span></span>' );
								}
							}
						}
					} else {
						return option.text;
					}
				};

				cbprivacy.selectingHandler = function( e, cbselect, value ) {
					var selected = cbprivacy.element.cbselect( 'get' );
					var unset = [];

					if ( value == 0 ) {
						cbprivacy.element.cbselect( 'set', '0' );
						cbprivacy.element.cbselect( 'close' );
					} else if ( value == 1 ) {
						cbprivacy.element.cbselect( 'set', '1' );
						cbprivacy.element.cbselect( 'close' );
					} else if ( value == 99 ) {
						cbprivacy.element.cbselect( 'set', '99' );
						cbprivacy.element.cbselect( 'close' );
					} else if ( value == 2 ) {
						unset = ['0', '1', '99'];

						$.each( selected, function( i, v ) {
							if ( v.indexOf( 'CONN-' ) > -1 ) {
								unset.push( v );
							}
						});

						if ( unset.length ) {
							cbprivacy.element.cbselect( 'unset', unset );
							cbprivacy.element.cbselect( 'close' );
						}
					} else if ( value == 3 ) {
						$.each( selected, function( i, v ) {
							if ( v.indexOf( 'CONN-' ) > -1 ) {
								unset.push( v );
							}
						});

						if ( unset.length ) {
							cbprivacy.element.cbselect( 'unset', unset );
							cbprivacy.element.cbselect( 'close' );
						}
					} else {
						if ( selected.indexOf( '0' ) > -1 ) {
							cbprivacy.element.cbselect( 'unset', '0' );
						} else if ( selected.indexOf( '1' ) > -1 ) {
							cbprivacy.element.cbselect( 'unset', '1' );
						} else if ( selected.indexOf( '99' ) > -1 ) {
							cbprivacy.element.cbselect( 'unset', '99' );
						} else if ( value.indexOf( 'CONN-' ) > -1 ) {
							if ( selected.indexOf( '2' ) > -1 ) {
								cbprivacy.element.cbselect( 'unset', '2' );
							}

							if ( selected.indexOf( '3' ) > -1 ) {
								cbprivacy.element.cbselect( 'unset', '3' );
							}
						}
					}
				};

				cbprivacy.ajaxHandler = function() {
					if ( ( ! cbprivacy.settings.ajax ) || cbprivacy.element.hasClass( 'privacySaving' ) ) {
						return;
					}

					var value = cbprivacy.element.cbselect( 'get' );

					if ( ( value == null ) || ( value.length == 0 ) ) {
						return;
					}

					if ( $.isArray( value ) && $.isArray( cbprivacy.selected ) && ( value.join( ',' ) == cbprivacy.selected.join( ',' ) ) ) {
						return;
					}

					cbprivacy.selected = value;

					var id = cbprivacy.element.attr( 'name' );
					var post = {};

					post[id] = value;

					$.ajax({
						url: cbprivacy.settings.ajax,
						type: 'POST',
						data: post,
						dataType: 'html',
						beforeSend: function( jqXHR, textStatus, errorThrown ) {
							cbprivacy.element.addClass( 'privacySaving' );
							cbprivacy.element.prop( 'disabled', true );
							cbprivacy.element.cbselect( 'container' ).addClass( 'privacySaving' );

							cbprivacy.element.triggerHandler( 'cbprivacy.ajax.send', [cbprivacy, jqXHR, textStatus, errorThrown] );
						}
					}).fail( function( jqXHR, textStatus, errorThrown ) {
						cbprivacy.element.triggerHandler( 'cbprivacy.ajax.error', [cbprivacy, jqXHR, textStatus, errorThrown] );
					}).done( function( data, textStatus, jqXHR ) {
						cbprivacy.element.removeClass( 'privacySaving' );
						cbprivacy.element.prop( 'disabled', false );
						cbprivacy.element.cbselect( 'container' ).removeClass( 'privacySaving' );

						cbprivacy.element.triggerHandler( 'cbprivacy.ajax.success', [cbprivacy, data, textStatus, jqXHR] );
					});
				};

				cbprivacy.changeHandler = function() {
					var value = cbprivacy.element.cbselect( 'get' );

					if ( ( value == null ) || ( value.length == 0 ) ) {
						cbprivacy.element.cbselect( 'set', cbprivacy.element.children( 'option[value!=""]:first' ).val() );
					}
				};

				cbprivacy.cbselect = cbprivacy.element.cbselect({
					width: 'auto',
					closeOnSelect: false,
					minimumResultsForSearch: Infinity,
					templateSelection: cbprivacy.selectionTemplate,
					templateResult: cbprivacy.dropdownTemplate
				});

				cbprivacy.element.on( 'cbselect.selecting', cbprivacy.selectingHandler );

				if ( cbprivacy.settings.ajax ) {
					cbprivacy.element.on( 'cbselect.close', cbprivacy.ajaxHandler );
				}

				cbprivacy.element.on( 'change', cbprivacy.changeHandler ).change();

				cbprivacy.selected = cbprivacy.element.cbselect( 'get' );

				// Destroy the cbprivacy element:
				cbprivacy.element.on( 'remove destroy.cbprivacy', function() {
					cbprivacy.element.cbprivacy( 'destroy' );
				});

				// Rebind the cbprivacy element to pick up any data attribute modifications:
				cbprivacy.element.on( 'rebind.cbprivacy', function() {
					cbprivacy.element.cbprivacy( 'rebind' );
				});

				// If the cbprivacy element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbprivacy.element.on( 'modified.cbprivacy', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbprivacy.element.cbprivacy( 'rebind' );
					}
				});

				// If the cbprivacy is cloned we need to rebind it back:
				cbprivacy.element.on( 'cloned.cbprivacy', function( e, oldId ) {
					destroyPlugin.call( this, cbprivacy );

					$( this ).cbprivacy( cbprivacy.options );
				});

				cbprivacy.element.trigger( 'cbprivacy.init.after', [cbprivacy] );

				// Bind the cbprivacy to the element so it's reusable and chainable:
				cbprivacy.element.data( 'cbprivacy', cbprivacy );

				// Add this instance to our instance array so we can keep track of our cbprivacy instances:
				instances.push( cbprivacy );
			});
		},
		rebind: function() {
			var cbprivacy = $( this ).data( 'cbprivacy' );

			if ( ! cbprivacy ) {
				return this;
			}

			cbprivacy.element.cbprivacy( 'destroy' );
			cbprivacy.element.cbprivacy( cbprivacy.options );

			return this;
		},
		destroy: function() {
			var cbprivacy = $( this ).data( 'cbprivacy' );

			if ( ! cbprivacy ) {
				return false;
			}

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbprivacy.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			destroyPlugin.call( cbprivacy.element, cbprivacy );

			cbprivacy.element.trigger( 'cbprivacy.destroyed', [cbprivacy] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function destroyPlugin( cbprivacy ) {
		var element = ( this.jquery ? this : $( this ) );

		element.off( 'destroy.cbprivacy' );
		element.off( 'rebind.cbprivacy' );
		element.off( 'cloned.cbprivacy' );
		element.off( 'modified.cbprivacy' );

		element.on( 'cbselect.selecting', cbprivacy.selectingHandler );

		if ( cbprivacy.settings.ajax ) {
			element.on( 'cbselect.close', cbprivacy.ajaxHandler );
		}

		element.on( 'change', cbprivacy.changeHandler );
		element.cbselect( 'destroy' );

		element.removeData( 'cbprivacy' );
	}

	$.fn.cbprivacy = function( options ) {
		if ( ! $.fn.cbselect ) {
			return this; // this plugin entirely depends on cbselect and if it's not available then just give up
		}

		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbprivacy.defaults = {
		init: true,
		useData: true,
		layout: 'tags',
		custom: 'Custom',
		ajax: null
	};
})(jQuery);