(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbrepeat = $( $this ).data( 'cbrepeat' );

				if ( cbrepeat ) {
					return; // cbtabs is already bound; so no need to rebind below
				}

				cbrepeat = {};
				cbrepeat.options = ( typeof options != 'undefined' ? options : {} );
				cbrepeat.defaults = $.fn.cbrepeat.defaults;
				cbrepeat.settings = $.extend( true, {}, cbrepeat.defaults, cbrepeat.options );
				cbrepeat.element = $( $this );

				if ( cbrepeat.settings.useData ) {
					$.each( cbrepeat.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbrepeat.element.data( 'cbrepeat' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbrepeat.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbrepeat.element.data( 'cbrepeat' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbrepeat.settings[key] = dataValue;
								}
							}
						}
					});
				}

				if ( ! cbrepeat.settings.min ) {
					cbrepeat.settings.min = 1;
				}

				cbrepeat.element.triggerHandler( 'cbrepeat.init.before', [cbrepeat] );

				if ( ! cbrepeat.settings.init ) {
					return;
				}

				if ( cbrepeat.settings.sortable ) {
					cbrepeat.element.sortable({
						placeholder: cbrepeat.element.children( '.cbRepeatRow' ).first().attr( 'class' ) + ' cbRepeatRowPlaceholder',
						forcePlaceholderSize: true,
						cursor: 'move',
						items: '.cbRepeatRow',
						containment: 'parent',
						animated: true,
						stop: function( event, ui ) {
							var checked = [];

							cbrepeat.element.children( '.cbRepeatRow' ).find( 'input:checked' ).each( function() {
								checked.push( $( this ) );
							});

							updateRepeat.call( $this, cbrepeat );

							$.each( checked, function( checkedElementId, checkedElement ) {
								checkedElement.prop( 'checked', true );
							});

							cbrepeat.element.triggerHandler( 'cbrepeat.move', [cbrepeat, event, ui] );
						},
						tolerance: 'pointer',
						handle: '.cbRepeatRowMove',
						opacity: 0.5
					});
				}

				cbrepeat.addHandler = function( e ) {
					e.preventDefault();

					var add = cbrepeat.element.find( '.cbRepeatRowAddCount' ).filter( function() {
									return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
								});
					var count = 1;

					if ( add.length && ( add.val() > 0 ) ) {
						count = add.val();
					}

					if ( cbrepeat.settings.limit && ( count > cbrepeat.settings.limit ) ) {
						count = cbrepeat.settings.limit;

						if ( add.length ) {
							add.val( count );
						}
					}

					for ( var i = 0; i < count; ++i ) {
						addRow.call( $this );
					}
				};

				if ( cbrepeat.settings.add ) {
					cbrepeat.element.find( '.cbRepeatRowAdd' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
					}).on( 'click', cbrepeat.addHandler );
				}

				cbrepeat.removeHandler = function( e ) {
					e.preventDefault();

					var row = $( this ).closest( '.cbRepeatRow' );

					removeRow.call( $this, row );
				};

				if ( cbrepeat.settings.remove ) {
					cbrepeat.element.find( '.cbRepeatRowRemove' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
					}).on( 'click', cbrepeat.removeHandler );
				}

				updateRepeat.call( $this, cbrepeat );

				cbrepeat.element.on( 'remove destroy.cbrepeat', function() {
					cbrepeat.element.cbrepeat( 'destroy' );
				});

				cbrepeat.element.on( 'rebind.cbrepeat', function() {
					cbrepeat.element.cbrepeat( 'rebind' );
				});

				cbrepeat.element.on( 'modified.cbrepeat', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbrepeat.element.cbrepeat( 'rebind' );
					}
				});

				cbrepeat.element.bind( 'cloned.cbrepeat', function() {
					$( this ).off( 'destroy.cbrepeat' );
					$( this ).off( 'rebind.cbrepeat' );
					$( this ).off( 'cloned.cbrepeat' );
					$( this ).off( 'modified.cbrepeat' );
					$( this ).removeData( 'cbrepeat' );
					$( this ).removeData( 'uiSortable' );
					$( this ).removeData( 'ui-sortable' );

					var container = $( this ).find( '.cbRepeatRow' ).first().parent();

					$( this ).find( '.cbRepeatRowAdd' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( container );
					}).off( 'click', cbrepeat.addHandler );

					$( this ).find( '.cbRepeatRowRemove' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( container );
					}).off( 'click', cbrepeat.removeHandler );

					$( this ).cbrepeat( cbrepeat.options );
				});

				cbrepeat.element.triggerHandler( 'cbrepeat.init.after', [cbrepeat] );

				// Bind the cbrepeat to the element so it's reusable and chainable:
				cbrepeat.element.data( 'cbrepeat', cbrepeat );

				// Add this instance to our instance array so we can keep track of our repeat instances:
				instances.push( cbrepeat );
			});
		},
		add: function() {
			addRow.call( this );

			return this;
		},
		remove: function( row ) {
			removeRow.call( this, row );

			return this;
		},
		update: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			updateRepeat.call( this, cbrepeat );

			return this;
		},
		reset: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			var row = cbrepeat.element.children( '.cbRepeatRow:not(:first)' );

			row.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).off( 'remove removeqtip' );
			row.remove();

			updateRepeat.call( this, cbrepeat );

			return this;
		},
		rebind: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			cbrepeat.element.cbrepeat( 'cbrepeat' );
			cbrepeat.element.cbrepeat( cbrepeat.options );

			return this;
		},
		destroy: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			if ( cbrepeat.settings.sortable ) {
				cbrepeat.element.sortable( 'destroy' );
				cbrepeat.element.removeData( 'uiSortable' );
				cbrepeat.element.removeData( 'ui-sortable' );
			}

			cbrepeat.element.find( '.cbRepeatRowAdd' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).off( 'click', cbrepeat.addHandler );

			cbrepeat.element.find( '.cbRepeatRowRemove' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).off( 'click', cbrepeat.removeHandler );

			cbrepeat.element.off( 'destroy.cbrepeat' );
			cbrepeat.element.off( 'rebind.cbrepeat' );
			cbrepeat.element.off( 'cloned.cbrepeat' );
			cbrepeat.element.off( 'modified.cbrepeat' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbrepeat.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbrepeat.element.removeData( 'cbrepeat' );
			cbrepeat.element.triggerHandler( 'cbrepeat.destroyed', [cbrepeat] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function addRow( count ) {
		var cbrepeat = $( this ).data( 'cbrepeat' );

		if ( ! cbrepeat ) {
			return false;
		}

		if ( cbrepeat.settings.max && ( cbrepeat.element.children( '.cbRepeatRow' ).length >= cbrepeat.settings.max ) ) {
			return false;
		}

		var row = cbrepeat.element.children( '.cbRepeatRow' ).last();
		var checked = [];

		row.find( 'input:checked' ).each( function() {
			checked.push( $( this ) );
		});

		var clone = row.clone( true );

		clone.insertAfter( row );

		var items = clone.find( '*' );

		if ( cbrepeat.settings.ignore ) {
			items = items.not( cbrepeat.settings.ignore );
		}

		items.each( function() {
			if ( $( this ).is( 'input,select,textarea' ) && ( ! $( this ).hasClass( 'cbRepeatNoReset' ) ) && ( ! $( this ).closest( '.cbRepeatNoReset' ).length ) ) {
				var type = $( this ).attr( 'type' );
				var defaultValue = $( this ).data( 'cbrepeat-default' );

				if ( typeof defaultValue != 'undefined' ) {
					defaultValue = $.trim( defaultValue );
				} else {
					defaultValue = null;
				}

				if ( ( type == 'checkbox' ) || ( type == 'radio' ) ) {
					if ( defaultValue ) {
						if ( type == 'checkbox' && ( defaultValue.indexOf( '|*|' ) != -1 ) ) {
							if ( defaultValue.split( '|*|' ).indexOf( $( this ).val() ) != -1 ) {
								$( this ).prop( 'checked', true );
							} else {
								$( this ).prop( 'checked', false );
							}
						} else {
							if ( $( this ).val() == defaultValue ) {
								$( this ).prop( 'checked', true );
							} else {
								$( this ).prop( 'checked', false );
							}
						}
					} else {
						if ( ( type == 'radio' ) && ( ( $( this ).siblings( 'input[type="radio"]' ).length + 1 ) == 2 ) && ( $( this ).val() == 0 ) ) {
							$( this ).prop( 'checked', true );
						} else {
							$( this ).prop( 'checked', false );
						}
					}

					// Workaround fixes for Joomla conflicting with our yesno usage:
					if ( ( type == 'radio' ) && ( ( $( this ).siblings( 'input[type="radio"]' ).length + 1 ) == 2 ) ) {
						if ( $( this ).val() == 0 ) {
							$( this ).next( 'label' ).addClass( 'btn-danger' );
						} else {
							$( this ).next( 'label' ).addClass( 'btn-success' );
						}
					}

					if ( type == 'radio' ) {
						$( this ).next( 'label' ).removeClass( 'active' );
					}
				} else {
					if ( defaultValue ) {
						if ( $( this ).is( 'select[multiple]' ) ) {
							defaultValue = defaultValue.split( '|*|' );
						}

						$( this ).val( defaultValue );
					} else {
						$( this ).val( '' );

						if ( $( this ).is( 'select' ) ) {
							if ( ! $( this ).children( 'option[value=""]:first' ).length ) {
								$( this ).val( $( this ).children( 'option[value!=""]:first' ).val() );
							}
						}
					}
				}
			}

			$( this ).triggerHandler( 'cloned' );

			if ( $( this ).is( 'input,select,textarea' ) ) {
				$( this ).trigger( 'change' );
			}
		});

		updateRepeat.call( this, cbrepeat );

		$.each( checked, function( checkedElementId, checkedElement ) {
			checkedElement.prop( 'checked', true );
		});

		cbrepeat.element.triggerHandler( 'cbrepeat.add', [cbrepeat] );

		return true;
	}

	function removeRow( row ) {
		var cbrepeat = $( this ).data( 'cbrepeat' );

		if ( ! cbrepeat ) {
			return false;
		}

		if ( cbrepeat.settings.min && ( cbrepeat.element.children( '.cbRepeatRow' ).length <= cbrepeat.settings.min ) ) {
			return false;
		}

		if ( ! row ) {
			row = cbrepeat.element.children( '.cbRepeatRow' ).last();
		}

		row.find( '.cbTabs,.cbTooltip,[data-hascbtooltip=\"true\"]' ).off( 'remove removeqtip' );
		row.find( '.cbRepeat' ).off( 'remove' );

		row.remove();

		updateRepeat.call( this, cbrepeat );

		cbrepeat.element.triggerHandler( 'cbrepeat.remove', [cbrepeat] );

		return true;
	}

	function updateRepeat( cbrepeat ) {
		var row = cbrepeat.element.children( '.cbRepeatRow' );

		if ( row.length > cbrepeat.settings.min ) {
			row.find( '.cbRepeatRowMove,.cbRepeatRowRemove' ).filter( function() {
				return $( this ).closest( '.cbRepeatRow' ).is( row );
			}).removeClass( 'hidden' );

			if ( cbrepeat.settings.sortable ) {
				cbrepeat.element.sortable( 'enable' );
			}
		} else {
			row.find( '.cbRepeatRowMove,.cbRepeatRowRemove' ).filter( function() {
				return $( this ).closest( '.cbRepeatRow' ).is( row );
			}).addClass( 'hidden' );

			if ( cbrepeat.settings.sortable ) {
				cbrepeat.element.sortable( 'disable' );
			}
		}

		if ( cbrepeat.settings.max ) {
			if ( row.length >= cbrepeat.settings.max ) {
				row.find( '.cbRepeatRowAdd' ).filter( function() {
					return $( this ).closest( '.cbRepeatRow' ).is( row );
				}).addClass( 'hidden' );
			} else {
				row.find( '.cbRepeatRowAdd' ).filter( function() {
					return $( this ).closest( '.cbRepeatRow' ).is( row );
				}).removeClass( 'hidden' );
			}
		}

		if ( ! cbrepeat.settings.sortable ) {
			row.find( '.cbRepeatRowMove' ).filter( function() {
				return $( this ).closest( '.cbRepeatRow' ).is( row );
			}).addClass( 'hidden' );
		}

		row.each( function( index ) {
			var items = $( this ).find( '*[id],*[for],*[name],*[data-cbrepeat-fallback-id],*[data-cbrepeat-fallback-for],*[data-cbrepeat-fallback-name]' ).filter( function() {
							return $( this ).closest( '.cbRepeatRow' ).is( row );
						});

			if ( cbrepeat.settings.ignore ) {
				items = items.not( cbrepeat.settings.ignore );
			}

			items.each( function() {
				if ( $( this ).attr( 'id' ) || $( this ).attr( 'data-cbrepeat-fallback-id' ) ) {
					var idAttribute = 'id';

					if ( ! $( this ).attr( 'id' ) ) {
						idAttribute = 'data-cbrepeat-fallback-id';
					}

					var oldId = $( this ).attr( idAttribute );

					if ( ! $( this ).data( 'orgId' ) ) {
						$( this ).data( 'orgId', $( this ).attr( idAttribute ) );
					}

					$( this ).attr( idAttribute, $( this ).attr( idAttribute ).replace( /^(.*__)(\d+)(__\w+)$/g, '$1' + index + '$3' ) );

					var newId = $( this ).attr( idAttribute );

					$( this ).triggerHandler( 'modified', [ oldId, newId, index ] );

					var oldIdNormalized = oldId.replace( 'cbfr_', '' ).replace( 'cbfv_', '' ).replace( /__[a-zA-Z0-9]+$/g, '' );
					var newIdNormalized = newId.replace( 'cbfr_', '' ).replace( 'cbfv_', '' ).replace( /__[a-zA-Z0-9]+$/g, '' );

					if ( oldIdNormalized !== newIdNormalized ) {
						var idItems = $( this ).closest( '.cbRepeatRow' ).find( '*[id*="' + oldIdNormalized + '"],*[id*="' + oldIdNormalized.replace( /_{2,}/g, '__' ) + '"],*[data-cbrepeat-fallback-id*="' + oldIdNormalized + '"],*[id*="' + oldIdNormalized.replace( /_{2,}/g, '__' ) + '"]' );

						if ( cbrepeat.settings.ignore ) {
							idItems = idItems.not( cbrepeat.settings.ignore );
						}

						idItems.each( function() {
							if ( $( this ).attr( 'id' ) ) {
								var itemOldId = $( this ).attr( 'id' );

								if ( ! $( this ).data( 'orgId' ) ) {
									$( this ).data( 'orgId', $( this ).attr( 'id' ) );
								}

								$( this ).attr( 'id', $( this ).attr( 'id' ).replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) ) );

								var itemNewId = $( this ).attr( 'id' );

								if ( typeof cbHideFields != 'undefined' ) {
									var conditions = [];

									$.each( cbHideFields, function( conditionId, condition ) {
										if ( ( condition[0] == itemOldId ) || ( condition[1] == itemOldId ) ) {
											conditions.push( condition );
										}
									});

									$.each( conditions, function( conditionId, condition ) {
										var newCondition = [];

										newCondition[0] = condition[0].replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );
										newCondition[1] = condition[1].replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );
										newCondition[2] = condition[2];
										newCondition[3] = condition[3];
										newCondition[4] = [];

										$.each( condition[4], function( conditionShowHideId, conditionShowHide ) {
											newCondition[4].push( conditionShowHide.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) ) );
										});

										newCondition[5] = [];

										$.each( condition[5], function( conditionSetId, conditionSet ) {
											newCondition[5].push( conditionSet.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) ) );
										});

										var conditionExists = false;

										$.each( cbHideFields, function( condId, cond ) {
											if ( ( cond[0] == newCondition[0] ) && ( cond[1] == newCondition[1] ) && ( cond[2] == newCondition[2] ) && ( cond[3] == newCondition[3] ) ) {
												conditionExists = true;
											}
										});

										if ( ! conditionExists ) {
											cbHideFields.push( newCondition );
										}
									});
								}

								$( this ).triggerHandler( 'modified', [ itemOldId, itemNewId, index ] );
							}

							if ( $( this ).attr( 'data-cbrepeat-fallback-id' ) ) {
								$( this ).attr( 'data-cbrepeat-fallback-id', $( this ).attr( 'data-cbrepeat-fallback-id' ).replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) ) );
							}
						});
					}
				}

				if ( $( this ).attr( 'for' ) || $( this ).attr( 'data-cbrepeat-fallback-for' ) ) {
					var forAttribute = 'for';

					if ( ! $( this ).attr( 'for' ) ) {
						forAttribute = 'data-cbrepeat-fallback-for';
					}

					var oldForNormalized = $( this ).attr( forAttribute ).replace( /__[a-zA-Z0-9]+$/g, '' );

					if ( ! $( this ).data( 'orgFor' ) ) {
						$( this ).data( 'orgFor', $( this ).attr( forAttribute ) );
					}

					$( this ).attr( forAttribute, $( this ).attr( forAttribute ).replace( /^(.*__)(\d+)(__\w+)$/g, '$1' + index + '$3' ) );

					var newForNormalized = $( this ).attr( forAttribute ).replace( /__[a-zA-Z0-9]+$/g, '' );

					if ( oldForNormalized !== newForNormalized ) {
						var forItems = $( this ).closest( '.cbRepeatRow' ).find( '*[for*="' + oldForNormalized + '"],*[data-cbrepeat-fallback-for*="' + oldForNormalized + '"]' );

						if ( cbrepeat.settings.ignore ) {
							forItems = forItems.not( cbrepeat.settings.ignore );
						}

						forItems.each( function() {
							if ( $( this ).attr( 'for' ) ) {
								$( this ).attr( 'for', $( this ).attr( 'for' ).replace( oldForNormalized, newForNormalized ) );
							}

							if ( $( this ).attr( 'data-cbrepeat-fallback-for' ) ) {
								$( this ).attr( 'data-cbrepeat-fallback-for', $( this ).attr( 'data-cbrepeat-fallback-for' ).replace( oldForNormalized, newForNormalized ) );
							}
						});
					}
				}

				if ( $( this ).attr( 'name' ) || $( this ).attr( 'data-cbrepeat-fallback-name' ) ) {
					var nameAttribute = 'name';

					if ( ! $( this ).attr( 'name' ) ) {
						nameAttribute = 'data-cbrepeat-fallback-name';
					}

					var oldNameNormalized = $( this ).attr( nameAttribute ).replace( /\[[a-zA-Z0-9]+\]$/g, '' );

					if ( ! $( this ).data( 'orgName' ) ) {
						$( this ).data( 'orgName', $( this ).attr( nameAttribute ) );
					}

					$( this ).attr( nameAttribute, $( this ).attr( nameAttribute ).replace( /^(.*)(\[\d+\])(\[\w+\])$/g, '$1[' + index + ']$3' ) );

					var newNameNormalized = $( this ).attr( nameAttribute ).replace( /\[[a-zA-Z0-9]+\]$/g, '' );

					if ( oldNameNormalized !== newNameNormalized ) {
						var nameItems = $( this ).closest( '.cbRepeatRow' ).find( '*[name*="' + oldNameNormalized + '"],*[data-cbrepeat-fallback-name*="' + oldNameNormalized + '"]' );

						if ( cbrepeat.settings.ignore ) {
							nameItems = nameItems.not( cbrepeat.settings.ignore );
						}

						nameItems.each( function() {
							if ( $( this ).attr( 'name' ) ) {
								$( this ).attr( 'name', $( this ).attr( 'name' ).replace( oldNameNormalized, newNameNormalized ) );
							}

							if ( $( this ).attr( 'data-cbrepeat-fallback-name' ) ) {
								$( this ).attr( 'data-cbrepeat-fallback-name', $( this ).attr( 'data-cbrepeat-fallback-name' ).replace( oldNameNormalized, newNameNormalized ) );
							}
						});
					}
				}
			});
		});

		if ( typeof cbHideFields != 'undefined' ) {
			var conditions = [];

			$.each( cbHideFields, function ( conditionId, condition ) {
				if ( ! $( '#' + condition[0] ).length ) {
					conditions.push( condition );
				}
			});

			$.each( conditions, function( conditionId, condition ) {
				cbHideFields.splice( cbHideFields.indexOf( condition ), 1 );
			});
		}

		if ( typeof cbInitFields != 'undefined' ) {
			cbInitFields();
		}

		if ( row.length == cbrepeat.settings.min ) {
			cbrepeat.element.addClass( 'cbRepeatMin' );
		} else if ( row.length == cbrepeat.settings.max ) {
			cbrepeat.element.addClass( 'cbRepeatMax' );
		} else {
			cbrepeat.element.removeClass( 'cbRepeatMin cbRepeatMax' );
		}

		cbrepeat.element.triggerHandler( 'cbrepeat.updated', [cbrepeat] );
	}

	$.fn.cbrepeat = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbrepeat.defaults = {
		init: true,
		useData: true,
		sortable: true,
		ignore: null,
		add: true,
		remove: true,
		min: 1,
		max: 0,
		limit: 25
	};
})(jQuery);