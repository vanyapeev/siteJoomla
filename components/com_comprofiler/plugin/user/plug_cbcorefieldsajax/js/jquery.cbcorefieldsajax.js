(function($) {
	var instances = [];
	var valueCache = {};
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbajaxfield = $( $this ).data( 'cbajaxfield' );

				if ( cbajaxfield ) {
					return; // cbajaxfield is already bound; so no need to rebind below
				}

				cbajaxfield = {};
				cbajaxfield.options = options;
				cbajaxfield.defaults = $.fn.cbajaxfield.defaults;
				cbajaxfield.settings = $.extend( true, {}, cbajaxfield.defaults, cbajaxfield.options );
				cbajaxfield.element = $( $this );

				if ( cbajaxfield.settings.useData ) {
					$.each( cbajaxfield.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbajaxfield.element.data( 'cbajaxfield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbajaxfield.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbajaxfield.element.data( 'cbajaxfield' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbajaxfield.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbajaxfield.element.trigger( 'cbajaxfield.init.before', [cbajaxfield] );

				if ( ! cbajaxfield.settings.init ) {
					return;
				}

				$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
					options.async = true;
				});

				if ( cbajaxfield.settings.mode == 'update' ) {
					if ( ! ( cbajaxfield.settings.selectors || $( cbajaxfield.settings.selectors ).length ) ) {
						return;
					}

					if ( ( cbajaxfield.settings.post === null ) || ( ( typeof cbajaxfield.settings.post != 'object' ) && ( ! $.isArray( cbajaxfield.settings.post ) ) ) ) {
						cbajaxfield.settings.post = {};
					}

					cbajaxfield.timer = null;

					cbajaxfield.changeHandler = function( event ) {
						if ( typeof event.data.ajaxUpdateTarget == 'undefined' ) {
							return;
						}

						var post = cbajaxfield.settings.post;
						var name = $( this ).attr( 'name' );
						var value = getValue.call( event.data.ajaxUpdateTarget );

						if ( ! name.length ) {
							return;
						}

						post[name] = value;

						var target = findTarget.call( cbajaxfield.element );

						if ( target.length ) {
							var targetName = target.attr( 'name' );
							var targetValue = getValue.call( target );

							if ( targetName.length ) {
								post[targetName] = targetValue;
							}
						}

						var field = cbajaxfield.element.find( '.cb_field > div:first,.cb_field > span:first,.fieldCell' );

						if ( ! field.length ) {
							field = cbajaxfield.element;
						}

						var timerDelay = 400;

						if ( event.type == 'keyup' ) {
							timerDelay = 800;
						}

						if ( cbajaxfield.timer ) {
							clearTimeout( cbajaxfield.timer );
						}

						cbajaxfield.timer = setTimeout( function() {
							if ( cbajaxfield.element.hasClass( 'cbAjaxUpdating' ) ) {
								return;
							}

							if ( typeof valueCache[name] != 'undefined' ) {
								var oldValue = ( $.isArray( valueCache[name] ) ? valueCache[name].toString() : valueCache[name] );
								var newValue = ( $.isArray( value ) ? value.toString() : value );

								if ( oldValue === newValue ) {
									return;
								}
							}

							valueCache[name] = value;

							$.ajax({
								url: cbajaxfield.settings.url,
								type: 'POST',
								dataType: 'html',
								cache: false,
								data: post,
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									cbajaxfield.element.addClass( 'cbAjaxUpdating' );
									cbajaxfield.element.find( '.cbAjaxUpdate' ).remove();

									if ( ! target.length ) {
										field.append( '<span class="cbFieldIcons cbAjaxUpdate"><span class="cbSpinner fa fa-spinner fa-spin-fast"></span></span>' );
									} else {
										target.after( '<span class="cbFieldIcons cbAjaxUpdate"><span class="cbSpinner fa fa-spinner fa-spin-fast"></span></span>' );
									}

									cbajaxfield.element.triggerHandler( 'cbajaxfield.update.send', [cbajaxfield, jqXHR, textStatus, errorThrown] );
								},
								error: function( jqXHR, textStatus, errorThrown ) {
									cbajaxfield.element.removeClass( 'cbAjaxUpdating' );
									cbajaxfield.element.find( '.cbAjaxUpdate' ).remove();

									cbajaxfield.element.triggerHandler( 'cbajaxfield.update.error', [cbajaxfield, jqXHR, textStatus, errorThrown] );
								},
								success: function( data, textStatus, jqXHR ) {
									cbajaxfield.element.removeClass( 'cbAjaxUpdating' );
									cbajaxfield.element.find( '.cbAjaxUpdate' ).remove();

									var editHtml = $( '<div />' ).html( data );
									var loadScripts = parseHeaders.call( editHtml );

									cbajaxfield.element.removeClass( 'cbValidationError has-error' );
									cbajaxfield.element.find( '.cbValidationMessage' ).remove();

									field.html( editHtml.html() );

									cbajaxfield.element.triggerHandler( 'rebind' );
									cbajaxfield.element.find( 'select,input,textarea' ).removeClass( '.cbValidationError' ).triggerHandler( 'change' );

									parseScripts.call( cbajaxfield.element, loadScripts );

									if ( $.fn.cbtooltip ) {
										cbajaxfield.element.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();
									}

									cbajaxfield.element.triggerHandler( 'cbajaxfield.update.success', [cbajaxfield, data, textStatus, jqXHR] );
								}
							});
						}, timerDelay );
					};

					$( cbajaxfield.settings.selectors ).each( function() {
						var target = findTarget.call( this );

						if ( ! target.length ) {
							return true;
						}

						var name = target.attr( 'name' );

						if ( name.length ) {
							valueCache[name] = getValue.call( target );
						}

						target.on( 'keyup change', {ajaxUpdateTarget: target}, cbajaxfield.changeHandler );

						// The target element is being told to rebind so lets rebind its change handler:
						$( this ).on( 'rebind.cbajaxfield', function() {
							// clear any existing bindings just encase to avoid double binding:
							target.off( 'keyup change', {ajaxUpdateTarget: target}, cbajaxfield.changeHandler );

							// redo the binding:
							target = findTarget.call( this );

							if ( ! target.length ) {
								return;
							}

							target.on( 'keyup change', {ajaxUpdateTarget: target}, cbajaxfield.changeHandler );
						});
					});
				} else {
					cbajaxfield.editHandler = function( e ) {
						if ( ( ! cbajaxfield.element.hasClass( 'cbAjaxEditing' ) ) && ( ( ! cbajaxfield.settings.ignore ) || ( cbajaxfield.settings.ignore && ( ! $( e.target ).is( cbajaxfield.settings.ignore ) ) && ( ! $( e.target ).parents().is( cbajaxfield.settings.ignore ) ) ) ) ) {
							cbajaxfield.element.addClass( 'cbAjaxEditing' );

							var url = cbajaxfield.settings.url;

							if ( ! url ) {
								url = $( this ).attr( 'href' );
							}

							var width = cbajaxfield.element.innerWidth();

							if ( width < 300 ) {
								width = 400;
							}

							var mode = ( ( cbajaxfield.settings.mode != 'tooltip' ) && ( cbajaxfield.settings.mode != 'modal' ) || ( ! $.fn.cbtooltip ) ? 'inline' : cbajaxfield.settings.mode );

							$.ajax({
								url: url,
								type: 'GET',
								dataType: 'html',
								cache: false,
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									$( document ).find( '.cbAjaxCancel' ).click();
									cbajaxfield.element.append( '<span class="cbSpinner fa fa-spinner fa-spin-fast"></span>' );

									if ( mode == 'inline' ) {
										cbajaxfield.element.find( '.cbAjaxValue' ).addClass( 'hidden' );
									}

									cbajaxfield.element.triggerHandler( 'cbajaxfield.edit.send', [cbajaxfield, jqXHR, textStatus, errorThrown] );
								},
								error: function( jqXHR, textStatus, errorThrown ) {
									cbajaxfield.element.removeClass( 'cbAjaxEditing' );
									cbajaxfield.element.find( '.cbAjaxForm,.cbSpinner' ).remove();

									if ( mode == 'inline' ) {
										cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );
									}

									cbajaxfield.element.triggerHandler( 'cbajaxfield.edit.error', [cbajaxfield, jqXHR, textStatus, errorThrown] );
								},
								success: function( data, textStatus, jqXHR ) {
									cbajaxfield.element.find( '.cbSpinner' ).remove();

									if ( mode == 'inline' ) {
										cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );
									}

									var editHtml = $( data );
									var loadScripts = parseHeaders.call( editHtml );
									var tooltip = null;

									if ( mode == 'inline' ) {
										cbajaxfield.element.addClass( 'hidden' );
										cbajaxfield.element.after( editHtml );
									} else if ( mode == 'tooltip' ) {
										tooltip = $( '<div />' ).cbtooltip({
											tooltip: editHtml,
											openReady: true,
											positionMy: 'top left',
											positionAt: 'top left',
											positionTarget: cbajaxfield.element,
											adjustMethod: 'none',
											openEvent: 'none',
											closeEvent: 'none',
											dialog: true,
											buttonClose: false,
											width: width,
											height: 'auto',
											classes: ( $.fn.cbtooltip.defaults.classes ? $.fn.cbtooltip.defaults.classes : '' ) + ( cbajaxfield.settings.classes ? ' ' + cbajaxfield.settings.classes : '' ) + ' cbAjaxTooltip'
										});

										editHtml = tooltip.qtip( 'api' ).elements.content;
									} else if ( mode == 'modal' ) {
										tooltip = $( '<div />' ).cbtooltip({
											tooltip: editHtml,
											openReady: true,
											openEvent: 'none',
											closeEvent: 'none',
											modal: true,
											dialog: true,
											buttonClose: false,
											width: width,
											height: 'auto',
											classes: ( $.fn.cbtooltip.defaults.classes ? $.fn.cbtooltip.defaults.classes : '' ) + ( cbajaxfield.settings.classes ? ' ' + cbajaxfield.settings.classes : '' ) + ' cbAjaxModal'
										});

										editHtml = tooltip.qtip( 'api' ).elements.content;

										editHtml.find( 'input,select,textarea' ).on( 'change', function() {
											if ( tooltip ) {
												tooltip.cbtooltip( 'reposition' );

												// account for fade usages:
												if ( tooltip ) {
													setTimeout( function() {
														if ( tooltip ) {
															tooltip.cbtooltip( 'reposition' );

															setTimeout( function() {
																if ( tooltip ) {
																	tooltip.cbtooltip( 'reposition' );
																}
															}, 200 );
														}
													}, 600 );
												}
											}
										});
									}

									parseScripts.call( editHtml, loadScripts );

									if ( $.fn.cbtooltip ) {
										editHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();
									}

									if ( tooltip ) {
										tooltip.on( 'cbtooltip.hidden', function() {
											cbajaxfield.element.removeClass( 'cbAjaxEditing' );

											tooltip = null;

											cbajaxfield.element.trigger( 'cbajaxfield.cancel', [cbajaxfield, e] );
										});
									} else {
										editHtml.find( '.cbAjaxCancel' ).on( 'click', function( e ) {
											cbajaxfield.element.removeClass( 'cbAjaxEditing' );

											if ( mode == 'inline' ) {
												editHtml.remove();
												cbajaxfield.element.find( '.cbSpinner' ).remove();
												cbajaxfield.element.removeClass( 'hidden' );
											} else if ( tooltip ) {
												tooltip.qtip( 'api' ).toggle( false );

												tooltip = null;
											}

											cbajaxfield.element.trigger( 'cbajaxfield.cancel', [cbajaxfield, e] );
										});
									}

									editHtml.find( '.cbAjaxForm' ).on( 'submit', function( e ) {
										e.preventDefault();

										$( this ).ajaxSubmit({
											type: 'POST',
											dataType: 'html',
											beforeSerialize: function( form, options ) {
												cbajaxfield.element.trigger( 'cbajaxfield.save.serialize', [cbajaxfield, form, options] );
											},
											beforeSubmit: function( formData, form, options ) {
												var validator = editHtml.find( '.cbAjaxForm' ).data( 'cbvalidate' );

												if ( validator ) {
													if ( ! validator.element.cbvalidate( 'validate' ) ) {
														return false;
													}
												}

												cbajaxfield.element.append( '<span class="cbSpinner fa fa-spinner fa-spin-fast"></span>' );

												if ( mode == 'inline' ) {
													editHtml.addClass( 'hidden' );
													cbajaxfield.element.removeClass( 'hidden' );
													cbajaxfield.element.find( '.cbAjaxValue' ).addClass( 'hidden' );
												} else if ( tooltip ) {
													tooltip.qtip( 'api' ).toggle( false );

													tooltip = null;
												}

												cbajaxfield.element.trigger( 'cbajaxfield.save.submit', [cbajaxfield, formData, form, options] );
											},
											error: function( jqXHR, textStatus, errorThrown ) {
												cbajaxfield.element.removeClass( 'cbAjaxEditing' );
												cbajaxfield.element.find( '.cbSpinner' ).remove();

												if ( mode == 'inline' ) {
													editHtml.remove();
													cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );
												} else if ( tooltip ) {
													tooltip.qtip( 'api' ).toggle( false );

													tooltip = null;
												}

												cbajaxfield.element.trigger( 'cbajaxfield.save.error', [cbajaxfield, jqXHR, textStatus, errorThrown] );
											},
											success: function( data, textStatus, jqXHR ) {
												cbajaxfield.element.removeClass( 'cbAjaxEditing' );
												cbajaxfield.element.find( '.cbSpinner' ).remove();

												if ( mode == 'inline' ) {
													editHtml.remove();
													cbajaxfield.element.find( '.cbAjaxValue' ).removeClass( 'hidden' );
												} else if ( tooltip ) {
													tooltip.qtip( 'api' ).toggle( false );

													tooltip = null;
												}

												var saveHtml = $( '<div />' ).html( data );
												var loadScripts = parseHeaders.call( saveHtml );

												cbajaxfield.element.find( '.cbAjaxValue' ).html( saveHtml.html() );

												parseScripts.call( saveHtml, loadScripts );

												if ( $.fn.cbtooltip ) {
													saveHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();
												}

												cbajaxfield.element.trigger( 'cbajaxfield.save.success', [cbajaxfield, data, textStatus, jqXHR] );
											}
										});

										return false;
									});

									cbajaxfield.element.triggerHandler( 'cbajaxfield.edit.success', [cbajaxfield, data, textStatus, jqXHR] );
								}
							});
						}

						cbajaxfield.element.trigger( 'cbajaxfield.edit', [cbajaxfield, e] );
					};

					cbajaxfield.element.on( 'click', '.cbAjaxToggle', cbajaxfield.editHandler );
				}

				// Destroy the cbajaxfield element:
				cbajaxfield.element.on( 'remove destroy.cbajaxfield', function() {
					cbajaxfield.element.cbajaxfield( 'destroy' );
				});

				// Rebind the cbajaxfield element to pick up any data attribute modifications:
				cbajaxfield.element.on( 'rebind.cbajaxfield', function() {
					cbajaxfield.element.cbajaxfield( 'rebind' );
				});

				// If the cbajaxfield element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbajaxfield.element.on( 'modified.cbajaxfield', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbajaxfield.element.cbajaxfield( 'rebind' );
					}
				});

				// If the cbajaxfield is cloned we need to rebind it back:
				cbajaxfield.element.on( 'cloned.cbajaxfield', function( e, oldId ) {
					destroyPlugin.call( this, cbajaxfield );

					$( this ).cbajaxfield( cbajaxfield.options );
				});

				cbajaxfield.element.trigger( 'cbajaxfield.init.after', [cbajaxfield] );

				// Bind the cbajaxfield to the element so it's reusable and chainable:
				cbajaxfield.element.data( 'cbajaxfield', cbajaxfield );

				// Add this instance to our instance array so we can keep track of our cbajaxfield instances:
				instances.push( cbajaxfield );
			});
		},
		rebind: function() {
			var cbajaxfield = $( this ).data( 'cbajaxfield' );

			if ( ! cbajaxfield ) {
				return this;
			}

			cbajaxfield.element.cbajaxfield( 'destroy' );
			cbajaxfield.element.cbajaxfield( cbajaxfield.options );

			return this;
		},
		destroy: function() {
			var cbajaxfield = $( this ).data( 'cbajaxfield' );

			if ( ! cbajaxfield ) {
				return false;
			}

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbajaxfield.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			destroyPlugin.call( cbajaxfield.element, cbajaxfield );

			cbajaxfield.element.trigger( 'cbajaxfield.destroyed', [cbajaxfield] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	function destroyPlugin( cbajaxfield ) {
		var element = ( this.jquery ? this : $( this ) );

		element.off( 'destroy.cbajaxfield' );
		element.off( 'rebind.cbajaxfield' );
		element.off( 'cloned.cbajaxfield' );
		element.off( 'modified.cbajaxfield' );

		if ( cbajaxfield.settings.mode == 'update' ) {
			element.removeClass( 'cbAjaxUpdating' );
			element.find( '.cbAjaxUpdate' ).remove();

			if ( cbajaxfield.timer ) {
				clearTimeout( cbajaxfield.timer );
			}

			$( cbajaxfield.settings.selectors ).each( function() {
				findTarget.call( this ).off( 'keyup change', cbajaxfield.changeHandler );
			});
		} else {
			$( document ).find( '.cbAjaxCancel' ).click();

			element.find( '.cbAjaxValue' ).removeClass( 'hidden' );
			element.find( '.cbSpinner' ).remove();
			element.off( 'click', '.cbAjaxValue', cbajaxfield.editHandler );
		}

		element.removeData( 'cbajaxfield' );
	}

	function findTarget() {
		var element = ( this.jquery ? this : $( this ) );
		var target = null;

		if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
			target = element;
		} else {
			target = element.find( 'input,select,textarea' ).first();

			if ( target.is( ':checkbox' ) || target.is( ':radio' ) ) {
				target = element.find( 'input[name="' + target.attr( 'name' ) + '"]' );
			}
		}

		return target;
	}

	function getValue() {
		var element = ( this.jquery ? this : $( this ) );
		var value = null;

		if ( element.is( 'input' ) || element.is( 'select' ) || element.is( 'textarea' ) ) {
			if ( element.is( 'input[type="checkbox"]' ) || element.is( 'input[type="radio"]' ) ) {
				value = [];

				element.each( function() {
					if ( $( this ).is( ':checked' ) ) {
						value.push( $( this ).val() );
					}
				});
			} else if ( element.is( 'select[multiple]' ) ) {
				value = element.val();

				if ( value && ( ! $.isArray( value ) ) ) {
					value = value.split( ',' );
				}
			} else {
				value = element.val();
			}
		}

		return value;
	}

	function parseHeaders() {
		var element = ( this.jquery ? this : $( this ) );
		var headers = element.find( '.cbAjaxHeaders' );

		if ( ! headers.length ) {
			headers = element.filter( '.cbAjaxHeaders' );
		}

		if ( ! headers.length ) {
			return [];
		}

		var head = $( 'head' );
		var loadedCSS = [];
		var loadedScripts = [];

		head.find( 'link' ).each( function() {
			var cssUrl = $( this ).attr( 'href' );

			if ( typeof cssUrl != 'undefined' ) {
				loadedCSS.push( cssUrl )
			}
		});

		head.find( 'script' ).each( function() {
			var scriptUrl = $( this ).attr( 'src' );

			if ( typeof scriptUrl != 'undefined' ) {
				loadedScripts.push( scriptUrl )
			}
		});

		headers.children( 'link' ).each( function() {
			var cssUrl = $( this ).attr( 'href' );

			if ( ( typeof cssUrl != 'undefined' ) && ( loadedCSS.indexOf( cssUrl ) !== -1 ) ) {
				$( this ).remove();
			}
		});

		var loadScripts = [];

		headers.children( 'script' ).each( function() {
			var scriptUrl = $( this ).attr( 'src' );

			if ( typeof scriptUrl == 'undefined' ) {
				loadScripts.push( this );
			} else {
				if ( loadedScripts.indexOf( scriptUrl ) === -1 ) {
					loadScripts.push( this );
				}
			}

			$( this ).remove();
		});

		return loadScripts;
	}

	function parseScripts( loadScripts ) {
		if ( ! loadScripts.length ) {
			return;
		}

		var element = ( this.jquery ? this : $( this ) );
		var scripts = $( '<div class="cbAjaxHeadersScripts" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;" />' );

		var loadScript = function( i ) {
			var nextScript = ( i + 1 );
			var scriptUrl = $( this ).attr( 'src' );

			if ( scriptUrl ) {
				$.ajax({
					url: scriptUrl,
					dataType: 'script'
				}).always( function() {
					scripts.append( '<script type="text/javascript" src="' + scriptUrl + '"></script>' );

					if ( typeof loadScripts[nextScript] != 'undefined' ) {
						loadScript.call( loadScripts[nextScript], nextScript );
					}
				});
			} else {
				scripts.append( '<script type="text/javascript">' + $( this ).text() + '</script>' );

				if ( typeof loadScripts[nextScript] != 'undefined' ) {
					loadScript.call( loadScripts[nextScript], nextScript );
				}
			}
		};

		loadScript.call( loadScripts[0], 0 );

		var headers = element.find( '.cbAjaxHeaders' );

		if ( ! headers.length ) {
			headers = element.filter( '.cbAjaxHeaders' );
		}

		headers.append( scripts );
	}

	$.fn.cbajaxfield = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbajaxfield.defaults = {
		init: true,
		useData: true,
		mode: 'inline',
		selectors: null,
		ignore: 'a,video,audio,iframe',
		classes: null,
		url: null,
		post: null
	};
})(jQuery);