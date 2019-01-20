(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbactivity = $( $this ).data( 'cbactivity' );

				if ( cbactivity ) {
					return; // cbactivity is already bound; so no need to rebind below
				}

				cbactivity = {};
				cbactivity.options = ( typeof options != 'undefined' ? options : {} );
				cbactivity.defaults = $.fn.cbactivity.defaults;
				cbactivity.settings = $.extend( true, {}, cbactivity.defaults, cbactivity.options );
				cbactivity.element = $( $this );

				if ( cbactivity.settings.useData ) {
					$.each( cbactivity.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbactivity.element.data( 'cbactivity' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbactivity.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbactivity.element.data( 'cbactivity' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbactivity.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbactivity.element.triggerHandler( 'cbactivity.init.before', [cbactivity] );

				if ( ! cbactivity.settings.init ) {
					return;
				}

				$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
					options.async = true;
				});

				cbactivity.actinHandler = function( e ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamRequesting' ) ) {
						return;
					}

					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var actionUrl = element.data( 'cbactivity-action-url' );

					if ( ! actionUrl ) {
						actionUrl = element.attr( 'href' );
					}

					if ( ! actionUrl ) {
						return;
					}

					var actionOutput = element.data( 'cbactivity-action-output' );
					var actionTarget = element.data( 'cbactivity-action-target' );

					var confirmMessage = element.data( 'cbactivity-confirm' );
					var confirmButton = element.data( 'cbactivity-confirm-button' );

					var overlay = ( element.data( 'cbactivity-action-overlay' ) != false );
					var fade = ( element.data( 'cbactivity-action-fade' ) != false );

					element.addClass( 'streamRequesting' );

					var callback = function() {
						$.ajax({
							url: actionUrl,
							type: 'GET',
							dataType: 'html',
							beforeSend: function( jqXHR, textStatus, errorThrown ) {
								if ( overlay ) {
									container.addClass( 'streamOverlay' );
								}

								cbactivity.element.triggerHandler( 'cbactivity.action.send', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
							}
						}).fail( function( jqXHR, textStatus, errorThrown ) {
							if ( overlay ) {
								container.removeClass( 'streamOverlay' );
							}

							element.removeClass( 'streamRequesting' );

							cbactivity.element.triggerHandler( 'cbactivity.action.error', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
						}).done( function( data, textStatus, jqXHR ) {
							if ( overlay ) {
								container.removeClass( 'streamOverlay' );
							}

							element.removeClass( 'streamRequesting' );

							var response = null;

							try {
								response = JSON.parse( data );
							} catch( e ) {
								response = { message: data, type: null, output: null, target: null };
							}

							var responseMessage = ( typeof response.message !== 'undefined' ? response.message : null );
							var newContent = null;

							filterSelector.call( container.find( '.streamItemAlert' ), cbactivity ).remove();

							var previousElement = cbactivity.element;

							if ( response && responseMessage ) {
								var loadScripts = [];

								var responseOutput = ( actionOutput ? actionOutput : ( typeof response.output !== 'undefined' ? response.output : null ) );
								var responseTarget = ( actionTarget ? actionTarget : ( typeof response.target !== 'undefined' ? response.target : null ) );

								if ( responseOutput || responseTarget ) {
									var target = null;

									if ( responseTarget ) {
										if ( responseTarget == 'self' ) {
											target = element;
										} else if ( responseTarget == 'items' ) {
											target = container.siblings( '.streamItems' );
										} else if ( responseTarget == 'container' ) {
											target = container;
										} else if ( responseTarget == 'stream' ) {
											target = cbactivity.element;
										} else {
											responseTarget = filterSelector.call( container.find( responseTarget ), cbactivity );

											if ( responseTarget.length ) {
												target = responseTarget;
											}
										}
									} else {
										target = element;
									}

									newContent = $( responseMessage );

									if ( fade ) {
										newContent.hide();
									}

									loadScripts = parseHeaders.call( newContent, cbactivity );

									switch( responseOutput ) {
										case 'before':
											target.before( newContent );
											break;
										case 'after':
											target.after( newContent );
											break;
										case 'prepend':
											target.prepend( newContent );
											break;
										case 'append':
											target.append( newContent );
											break;
										case 'replace':
										default:
											if ( responseTarget == 'container' ) {
												target.siblings( '.streamItemHeaders' ).remove();
											} else if ( responseTarget == 'stream' ) {
												previousElement = cbactivity.element.clone( true );
											}

											target.replaceWith( newContent );

											if ( responseTarget == 'container' ) {
												container = newContent;
											} else if ( responseTarget == 'stream' ) {
												cbactivity.element = newContent;
											}
											break;
									}
								} else {
									newContent = $( '<div class=\"streamItemActionResponse\">' + responseMessage + '</div>' );

									if ( fade ) {
										newContent.hide();
									}

									loadScripts = parseHeaders.call( newContent, cbactivity );

									container.children().addClass( 'hidden' );
									container.children( '.streamItemActionResponse' ).remove();
									container.append( newContent );
								}

								parseScripts.call( newContent, cbactivity, loadScripts );

								if ( fade ) {
									newContent.fadeIn( 'slow' );
								}
							} else {
								if ( element.hasClass( 'streamItemActionResponseRevert' ) ) {
									container.children().removeClass( 'hidden' ).hide().fadeIn( 'slow' );
									container.children( '.streamItemActionResponse' ).remove();
								} else {
									container.siblings( '.streamItemHeaders' ).remove();

									container.remove();
								}
							}

							previousElement.triggerHandler( 'cbactivity.action.success', [cbactivity, element, container, newContent, response, data, textStatus, jqXHR] );

							if ( response && responseMessage ) {
								bindContainer.call( container, cbactivity, true );
							}
						});
					};

					if ( confirmMessage ) {
						$.cbconfirm( confirmMessage, { buttonYes: confirmButton } ).done( callback ).fail( function() {
							element.removeClass( 'streamRequesting' );
						});
					} else {
						callback();
					}
				};

				$( document ).on( 'click.cbactivity', '.streamItemAction', cbactivity.actinHandler );

				cbactivity.cancelNewHandler = function( e ) {
					e.preventDefault();

					cancelStreamNew.call( this, cbactivity );
				};

				cbactivity.saveHandler = function( e ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamRequesting' ) ) {
						return false;
					}

					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return false;
					}

					var isNew = ( typeof container.data( 'cbactivity-id' ) == 'undefined' );

					element.ajaxSubmit({
						type: 'POST',
						dataType: 'html',
						beforeSend: function( jqXHR, textStatus, errorThrown ) {
							container.addClass( 'streamOverlay' );
							element.addClass( 'streamRequesting' );

							cbactivity.element.triggerHandler( 'cbactivity.save.send', [cbactivity, element, container, isNew, jqXHR, textStatus, errorThrown] );
						},
						error: function( jqXHR, textStatus, errorThrown ) {
							container.removeClass( 'streamOverlay' );
							element.removeClass( 'streamRequesting' );

							cbactivity.element.triggerHandler( 'cbactivity.save.error', [cbactivity, element, container, isNew, jqXHR, textStatus, errorThrown] );
						},
						success: function( data, textStatus, jqXHR ) {
							container.removeClass( 'streamOverlay' );
							element.removeClass( 'streamRequesting' );

							var response = null;

							try {
								response = JSON.parse( data );
							} catch( e ) {
								response = { message: data, type: null, output: null, target: null };
							}

							var responseMessage = ( typeof response.message !== 'undefined' ? response.message : null );

							filterSelector.call( container.find( '.streamItemAlert' ), cbactivity ).remove();

							var previousElement = cbactivity.element;

							if ( response && responseMessage ) {
								var newContent = $( responseMessage ).hide();

								var loadScripts = parseHeaders.call( newContent, cbactivity );

								var responseOutput = ( typeof response.output !== 'undefined' ? response.output : null );
								var responseTarget = ( typeof response.target !== 'undefined' ? response.target : null );

								if ( responseOutput || responseTarget ) {
									var target = null;

									if ( responseTarget ) {
										if ( responseTarget == 'self' ) {
											target = element;
										} else if ( responseTarget == 'items' ) {
											target = container.siblings( '.streamItems' );
										} else if ( responseTarget == 'container' ) {
											target = container;
										} else if ( responseTarget == 'stream' ) {
											target = cbactivity.element;
										} else {
											responseTarget = filterSelector.call( container.find( responseTarget ), cbactivity );

											if ( responseTarget.length ) {
												target = responseTarget;
											}
										}
									} else {
										target = element;
									}

									switch( responseOutput ) {
										case 'before':
											target.before( newContent );
											break;
										case 'after':
											target.after( newContent );
											break;
										case 'prepend':
											target.prepend( newContent );
											break;
										case 'append':
											target.append( newContent );
											break;
										case 'replace':
										default:
											if ( responseTarget == 'container' ) {
												target.siblings( '.streamItemHeaders' ).remove();
											} else if ( responseTarget == 'stream' ) {
												previousElement = cbactivity.element.clone( true );
											}

											target.replaceWith( newContent );

											if ( responseTarget == 'container' ) {
												container = newContent;
											} else if ( responseTarget == 'stream' ) {
												cbactivity.element = newContent;
											}
											break;
									}
								} else {
									if ( isNew ) {
										cancelStreamNew.call( element, cbactivity );

										var items = container.siblings( '.streamItems' );

										filterSelector.call( items.children( '.streamEmpty' ), cbactivity ).remove();

										var first = null;

										if ( cbactivity.settings.direction == 'up' ) {
											first = filterSelector.call( items.find( '.streamItem[data-cbactivity-id]:not(.streamItemPinned):last' ), cbactivity );
										} else {
											first = filterSelector.call( items.find( '.streamItem[data-cbactivity-id]:not(.streamItemPinned):first' ), cbactivity );
										}

										if ( cbactivity.settings.direction == 'up' ) {
											if ( first.length ) {
												newContent.insertAfter( first );
											} else {
												items.append( newContent );
											}
										} else {
											if ( first.length ) {
												newContent.insertBefore( first );
											} else {
												items.prepend( newContent );
											}
										}
									} else {
										container.siblings( '.streamItemHeaders' ).remove();

										container.replaceWith( newContent );
									}

									container = newContent;
								}

								parseScripts.call( newContent, cbactivity, loadScripts );

								newContent.fadeIn( 'slow' );
							}

							previousElement.triggerHandler( 'cbactivity.save.success', [cbactivity, element, container, newContent, isNew, response, data, textStatus, jqXHR] );

							if ( response && responseMessage ) {
								bindContainer.call( container, cbactivity, true );
							}
						}
					});

					return false;
				};

				cbactivity.closeActionsHandler = function( e ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamItemAction' ) ) {
						return;
					}

					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					container.siblings( '.streamItemHeaders' ).remove();

					container.remove();

					cbactivity.element.triggerHandler( 'cbactivity.actions.close', [cbactivity, element, container] );
				};

				cbactivity.revertActionsHandler = function( e ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamItemAction' ) ) {
						return;
					}

					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					container.children().removeClass( 'hidden' );

					filterSelector.call( container.find( '.streamItemActionResponse' ), cbactivity ).remove();
					filterSelector.call( container.find( '.streamItemAlert' ), cbactivity ).remove();

					cbactivity.element.triggerHandler( 'cbactivity.actions.revert', [cbactivity, element, container] );
				};

				cbactivity.closeActionHandler = function( e ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamItemAction' ) ) {
						return;
					}

					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					filterSelector.call( element.closest( '.streamItemActionResponse' ), cbactivity ).remove();

					cbactivity.element.triggerHandler( 'cbactivity.action.close', [cbactivity, element, container] );
				};

				cbactivity.displayNewHandler = function( e ) {
					var element = $( this );
					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var display = filterSelector.call( container.find( '.streamItemDisplay' ), cbactivity );

					if ( ! display.hasClass( 'hidden' ) ) {
						return;
					}

					var input = filterSelector.call( container.find( '.streamInputMessage' ), cbactivity );
					var size = input.data( 'cbactivity-input-size' );

					if ( typeof size != 'undefined' ) {
						input.attr( 'rows', size );
					}

					display.removeClass( 'hidden' );

					cbactivity.element.triggerHandler( 'cbactivity.new.display', [cbactivity, element, container] );

					bindContainer.call( container, cbactivity );
				};

				cbactivity.messageLimitHandler = function( e ) {
					var element = $( this );
					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var inputLimit = element.data( 'cbactivity-input-limit' );

					if ( typeof inputLimit != 'undefined' ) {
						var inputLength = element.val().length;

						if ( inputLength > inputLimit ) {
							element.val( element.val().substr( 0, inputLimit ) );
						} else {
							filterSelector.call( container.find( '.streamInputMessageLimitCurrent' ), cbactivity ).html( element.val().length );
						}
					}
				};

				cbactivity.scrollerLeftHandler = function( e ) {
					e.preventDefault();

					var element = $( this );
					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var scroll = element.closest( '.streamItemScroll' );
					var active = scroll.children( '.streamItemScrollContent:not(.hidden)' );
					var previous = active.prevAll(  '.streamItemScrollContent.hidden:first' );

					if ( ! previous.length ) {
						previous = scroll.children( '.streamItemScrollContent.hidden:last' );
					}

					if ( ! previous.length ) {
						return;
					}

					previous.removeClass( 'hidden' );
					active.addClass( 'hidden' );

					scroll.children( '.streamItemScrollContent' ).find( '.streamItemScrollDisable' ).addClass( 'disabled' ).prop( 'disabled', true );
					scroll.children( '.streamItemScrollContent' ).find( '.streamItemScrollHide' ).addClass( 'hidden' );

					previous.find( '.streamItemScrollDisable' ).removeClass( 'disabled' ).prop( 'disabled', false );
					previous.find( '.streamItemScrollHide' ).removeClass( 'hidden' );

					bindContainer.call( container, cbactivity );
				};

				cbactivity.scrollerRightandler = function( e ) {
					e.preventDefault();

					var element = $( this );
					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var scroll = element.closest( '.streamItemScroll' );
					var active = scroll.children( '.streamItemScrollContent:not(.hidden)' );
					var next = active.nextAll(  '.streamItemScrollContent.hidden:first' );

					if ( ! next.length ) {
						next = scroll.children( '.streamItemScrollContent.hidden:first' );
					}

					if ( ! next.length ) {
						return;
					}

					next.removeClass( 'hidden' );
					active.addClass( 'hidden' );

					scroll.children( '.streamItemScrollContent' ).find( '.streamItemScrollDisable' ).addClass( 'disabled' ).prop( 'disabled', true );
					scroll.children( '.streamItemScrollContent' ).find( '.streamItemScrollHide' ).addClass( 'hidden' );

					next.find( '.streamItemScrollDisable' ).removeClass( 'disabled' ).prop( 'disabled', false );
					next.find( '.streamItemScrollHide' ).removeClass( 'hidden' );

					bindContainer.call( container, cbactivity );
				};

				cbactivity.locationHandler = function( e ) {
					e.preventDefault();

					var element = $( this );
					var container = findContainer.call( element, cbactivity );

					if ( ! container.length ) {
						return;
					}

					var target = element.data( 'cbactivity-location-target' );
					var error = element.data( 'cbactivity-location-error' );
					var allowFilter = ( element.data( 'cbactivity-location-filter' ) != false );

					if ( typeof target == 'undefined' ) {
						if ( typeof error != 'undefined' ) {
							$.cbconfirm( error, { buttonNo: false } );
						}

						return;
					}

					if ( typeof navigator.geolocation == 'undefined' ) {
						if ( typeof error != 'undefined' ) {
							$.cbconfirm( error, { buttonNo: false } );
						}

						return;
					}

					navigator.geolocation.getCurrentPosition( function( position ) {
						var location = position.coords.latitude + ',' + position.coords.longitude;
						var containerTarget = container.find( target );

						if ( allowFilter ) {
							containerTarget = filterSelector.call( containerTarget, cbactivity );
						}

						if ( containerTarget.is( 'input' ) ) {
							containerTarget.val( location );
						} else {
							containerTarget.html( location );
						}
					}, function () {
						if ( typeof error != 'undefined' ) {
							$.cbconfirm( error, { buttonNo: false } );
						}
					});
				};

				cbactivity.toggleHandler = function( e ) {
					e.preventDefault();

					streamToggle.call( this, cbactivity );
				};

				cbactivity.toggleSelectHandler = function() {
					streamToggle.call( this, cbactivity );
				};

				cbactivity.moreHandler = function( e ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamRequesting' ) ) {
						return;
					}

					var type = 'more';

					if ( element.hasClass( 'streamUpdate' ) ) {
						type = 'update';
					}

					var container = filterSelector.call( element.closest( '.streamContainer' ), cbactivity );

					if ( ! container.length ) {
						return;
					}

					var items = filterSelector.call( container.find( '.streamItems' ), cbactivity );
					var post = {};
					var first = null;
					var url = null;

					if ( type == 'update' ) {
						if ( cbactivity.settings.direction == 'up' ) {
							first = filterSelector.call( container.find( '.streamItem[data-cbactivity-timestamp]:not(.streamItemPinned):last' ), cbactivity );
						} else {
							first = filterSelector.call( container.find( '.streamItem[data-cbactivity-timestamp]:not(.streamItemPinned):first' ), cbactivity );
						}

						var updateTime = ( Date.now ? Date.now() : +( new Date() ) );

						if ( first.length )  {
							var firstTime = first.data( 'cbactivity-timestamp' );

							if ( firstTime ) {
								updateTime = firstTime;
							}
						}

						post.id = updateTime;

						url = element.data( 'cbactivity-update-url' );
					} else {
						url = element.data( 'cbactivity-more-url' );
					}

					if ( ! url ) {
						url = element.attr( 'href' );
					}

					if ( ! url ) {
						return;
					}

					$.ajax({
						url: url,
						type: 'GET',
						data: post,
						dataType: 'html',
						beforeSend: function( jqXHR, textStatus, errorThrown ) {
							element.addClass( 'streamRequesting' );

							if ( type == 'update' ) {
								cbactivity.element.triggerHandler( 'cbactivity.update.send', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
							} else {
								element.addClass( 'disabled' ).prop( 'disabled', true ).html( '<span class=\"streamIconMoreLoading fa fa-spinner fa-pulse\"></span>' );

								cbactivity.element.triggerHandler( 'cbactivity.more.send', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
							}
						}
					}).fail( function( jqXHR, textStatus, errorThrown ) {
						element.removeClass( 'streamRequesting' );

						if ( type == 'update' ) {
							cbactivity.element.triggerHandler( 'cbactivity.update.error', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
						} else {
							element.remove();

							cbactivity.element.triggerHandler( 'cbactivity.more.error', [cbactivity, element, container, jqXHR, textStatus, errorThrown] );
						}
					}).done( function( data, textStatus, jqXHR ) {
						element.removeClass( 'streamRequesting' );

						if ( type == 'more' ) {
							element.remove();
						}

						if ( data ) {
							var dataHtml = $( data ).hide();

							dataHtml.find( '.streamItems > .streamItem' ).each( function() {
								if ( filterSelector.call( container.find( '.streamItem[data-cbactivity-id="' + $( this ).data( 'cbactivity-id' ) + '"]' ), cbactivity ).length ) {
									$( this ).remove();
								}
							});

							var loadScripts = parseHeaders.call( dataHtml, cbactivity );

							if ( cbactivity.settings.direction == 'up' ) {
								if ( type == 'update' ) {
									if ( first.length ) {
										dataHtml.insertAfter( first );
									} else {
										items.append( dataHtml );
									}
								} else {
									items.prepend( dataHtml );

									filterSelector.call( container.find( '.streamMore' ), cbactivity ).insertBefore( items );
								}
							} else {
								if ( type == 'update' ) {
									if ( first.length ) {
										dataHtml.insertBefore( first );
									} else {
										items.prepend( dataHtml );
									}
								} else {
									items.append( dataHtml );

									filterSelector.call( container.find( '.streamMore' ), cbactivity ).insertAfter( items );
								}
							}

							parseScripts.call( dataHtml, cbactivity, loadScripts );

							dataHtml.fadeIn( 'slow' );
						}

						if ( type == 'update' ) {
							cbactivity.element.triggerHandler( 'cbactivity.update.success', [cbactivity, element, container, dataHtml, data, textStatus, jqXHR] );
						} else {
							cbactivity.element.triggerHandler( 'cbactivity.more.success', [cbactivity, element, container, dataHtml, data, textStatus, jqXHR] );
						}

						if ( data ) {
							bindContainer.call( dataHtml, cbactivity, true );

							if ( type == 'update' ) {
								if ( cbactivity.settings.autoUpdate ) {
									if ( ! autoUpdaterReady ) {
										// Restart the auto updater if it has expired:
										autoUpdater();
									} else {
										autoUpdaterAttempts = 0;
									}
								}
							} else {
								filterSelector.call( cbactivity.element.find( '.streamMore' ), cbactivity ).off( 'click', cbactivity.moreHandler ).on( 'click', cbactivity.moreHandler );

								if ( cbactivity.settings.autoLoad && ( cbactivity.settings.direction != 'up' ) ) {
									if ( ! autoLoaderReady ) {
										// Restart the auto loader if it has expired:
										autoLoader();
									} else {
										autoLoaderAttempts = 0;
									}
								}
							}
						}
					});
				};

				cbactivity.cbselectFormat = function( option ) {
					var icon = $( option.element ).data( 'cbactivity-option-icon' );

					if ( typeof icon != 'undefined' ) {
						return $( '<span><span class="cb_template streamSelectOptionIcon">' + icon + '</span>' + option.text + '</span>' );
					} else {
						return option.text;
					}
				};

				cbactivity.modalHandler = function( e, cbtooltip ) {
					e.preventDefault();

					var element = $( this );

					if ( element.hasClass( 'streamRequesting' ) ) {
						return false;
					}

					var modalUrl = element.data( 'cbactivity-modal-url' );

					if ( ! modalUrl ) {
						modalUrl = element.attr( 'href' );
					}

					if ( ! modalUrl ) {
						return false;
					}

					element.addClass( 'streamRequesting' );

					var ajax = null;

					cbtooltip.tooltip.qtip( 'api' ).set( 'content.text', function( e, api ) {
						if ( ajax == null ) {
							ajax = $.ajax({
								url: modalUrl,
								type: 'GET',
								dataType: 'html',
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									cbactivity.element.triggerHandler( 'cbactivity.modal.send', [cbactivity, element, jqXHR, textStatus, errorThrown] );
								}
							}).fail( function( jqXHR, textStatus, errorThrown ) {
								element.removeClass( 'streamRequesting' );

								if ( ! api.destroyed ) {
									api.hide();
								}

								cbactivity.element.triggerHandler( 'cbactivity.modal.error', [cbactivity, element, jqXHR, textStatus, errorThrown] );
							}).done( function( data, textStatus, jqXHR ) {
								element.removeClass( 'streamRequesting' );

								if ( api.destroyed ) {
									return;
								}

								api.elements.tooltip.removeClass( 'streamModalContainerLoad streamModalContainerLoading' );

								var dataHtml = null;

								if ( data ) {
									dataHtml = $( data );

									var loadScripts = parseHeaders.call( dataHtml, cbactivity );

									api.set( 'content.text', dataHtml );

									parseScripts.call( api.elements.content, cbactivity, loadScripts );
								} else {
									api.hide();
								}

								cbactivity.element.triggerHandler( 'cbactivity.modal.success', [cbactivity, element, dataHtml, data, textStatus, jqXHR] );

								if ( data ) {
									bindContainer.call( api.elements.content, cbactivity, true );
								}
							});
						}

						return '<div class="streamModalLoading text-center"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>';
					});

					cbactivity.element.triggerHandler( 'cbactivity.modal', [cbactivity, element, cbtooltip, ajax, e] );

					return ajax;
				};

				cbactivity.cbselectBeforeHandler = function( e, cbselect ) {
					if ( cbselect.element.val() > 0 ) {
						var target = cbselect.element.data( 'cbactivity-toggle-target' );

						if ( typeof target == 'undefined' ) {
							return;
						}

						var container = findContainer.call( cbselect.element, cbactivity );

						if ( ! container.length ) {
							return;
						}

						var selected = cbselect.element.find( ':selected' );
						var placeholder = selected.data( 'cbactivity-toggle-placeholder' );
						var label = filterSelector.call( container.find( target ).find( '.streamInputSelectToggleLabel' ), cbactivity );

						if ( label.length ) {
							label.html( selected.text() );
						}

						if ( typeof placeholder != 'undefined' ) {
							var input = filterSelector.call( container.find( target ).find( '.streamInputSelectTogglePlaceholder' ), cbactivity );

							if ( label.length ) {
								input.attr( 'placeholder', placeholder );
							}
						}
					}
				};

				cbactivity.cbselectAfterHandler = function( e, cbselect ) {
					var icon = cbselect.element.data( 'cbactivity-toggle-icon' );

					if ( typeof icon == 'undefined' ) {
						return;
					}

					cbselect.container.find( '.select2-selection' ).addClass( icon );
				};

				// Destroy the cbactivity element:
				cbactivity.element.on( 'remove destroy.cbactivity', function() {
					cbactivity.element.cbactivity( 'destroy' );
				});

				// Rebind the cbactivity element to pick up any data attribute modifications:
				cbactivity.element.on( 'rebind.cbactivity', function() {
					cbactivity.element.cbactivity( 'rebind' );
				});

				// If the cbactivity element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbactivity.element.on( 'modified.cbactivity', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbactivity.element.cbactivity( 'rebind' );
					}
				});

				// If the cbactivity is cloned we need to rebind it back:
				cbactivity.element.on( 'cloned.cbactivity', function() {
					destroyStream.call( this, cbactivity );

					$( this ).cbactivity( cbactivity.options );
				});

				cbactivity.element.triggerHandler( 'cbactivity.init.after', [cbactivity] );

				// Bind the cbactivity to the element so it's reusable and chainable:
				cbactivity.element.data( 'cbactivity', cbactivity );

				// Add this instance to our instance array so we can keep track of our cbactivity instances:
				instances.push( cbactivity );

				bindContainer.call( $this, cbactivity, true );

				if ( cbactivity.settings.autoUpdate && ( ! autoUpdaterReady ) ) {
					// Don't start auto updater until at least 1 auto update stream is established and only if it hasn't already been started:
					autoUpdater();
				}

				if ( cbactivity.settings.autoLoad && ( cbactivity.settings.direction != 'up' ) && ( ! autoLoaderReady ) ) {
					// Don't start auto loader until at least 1 auto load stream is established and only if it hasn't already been started:
					autoLoader();
				}
			});
		},
		rebind: function() {
			var cbactivity = $( this ).data( 'cbactivity' );

			if ( ! cbactivity ) {
				return this;
			}

			cbactivity.element.cbactivity( 'destroy' );
			cbactivity.element.cbactivity( cbactivity.options );

			return this;
		},
		destroy: function() {
			var cbactivity = $( this ).data( 'cbactivity' );

			if ( ! cbactivity ) {
				return this;
			}

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbactivity.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			destroyStream.call( cbactivity.element, cbactivity );

			cbactivity.element.triggerHandler( 'cbactivity.destroyed', [cbactivity] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function destroyStream( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );

		element.off( 'destroy.cbactivity' );
		element.off( 'rebind.cbactivity' );
		element.off( 'modified.cbactivity' );
		element.off( 'cloned.cbactivity' );
		element.removeData( 'cbactivity' );

		$( document ).off( 'click.cbactivity', cbactivity.actinHandler );

		filterSelector.call( element.find( '.streamItemNewCancel' ), cbactivity ).off( 'click', cbactivity.cancelNewHandler );
		filterSelector.call( element.find( '.streamItemForm' ), cbactivity ).off( 'submit', cbactivity.saveHandler );
		filterSelector.call( element.find( '.streamItemActionResponsesClose' ), cbactivity ).off( 'click', cbactivity.closeActionsHandler );
		filterSelector.call( element.find( '.streamItemActionResponsesRevert' ), cbactivity ).off( 'click', cbactivity.revertActionsHandler );
		filterSelector.call( element.find( '.streamItemActionResponseClose' ), cbactivity ).off( 'click', cbactivity.closeActionHandler );
		filterSelector.call( element.find( '.streamItemNew .streamInputMessage.streamInputMessageCollapse' ), cbactivity ).off( 'click', cbactivity.displayNewHandler );
		filterSelector.call( element.find( '.streamInputMessage[data-cbactivity-input-limit]' ), cbactivity ).off( 'keyup input change', cbactivity.messageLimitHandler );
		filterSelector.call( element.find( '.streamItemScrollLeft' ), cbactivity ).off( 'click', cbactivity.scrollerLeftHandler );
		filterSelector.call( element.find( '.streamItemScrollRight' ), cbactivity ).off( 'click', cbactivity.scrollerRightandler );
		filterSelector.call( element.find( '.streamFindLocation' ), cbactivity ).off( 'click', cbactivity.locationHandler );
		filterSelector.call( element.find( '.streamToggle' ), cbactivity ).off( 'click', cbactivity.toggleHandler );
		filterSelector.call( element.find( 'select.streamInputSelect' ), cbactivity ).off( 'change', cbactivity.toggleSelectHandler );
		filterSelector.call( element.find( '.streamMore,.streamUpdate' ), cbactivity ).off( 'click', cbactivity.moreHandler );
		filterSelector.call( element.find( '.streamModal' ), cbactivity ).off( 'cbtooltip.render', cbactivity.modalHandler );

		filterSelector.call( element.find( '.cbMoreLess' ), cbactivity ).cbmoreless( 'destroy' );
		filterSelector.call( element.find( '.cbRepeat' ), cbactivity ).cbrepeat( 'destroy' );
		filterSelector.call( element.find( 'select.streamInputSelect' ), cbactivity ).cbselect( 'destroy' );

		filterSelector.call( element.find( '.streamInputAutosize' ), cbactivity ).trigger( 'autosize.destroy' );

		filterSelector.call( element.find( '.streamItem' ), cbactivity ).each( function() {
			var activeClasses = $( this ).data( 'cbactivity-active-classes' );
			var inactiveClasses = $( this ).data( 'cbactivity-inactive-classes' );

			if ( typeof activeClasses != 'undefined' ) {
				$( this ).removeClass( activeClasses );
			}

			if ( typeof inactiveClasses != 'undefined' ) {
				$( this ).addClass( inactiveClasses );
			}
		});
	}

	function filterSelector( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );

		return	element.filter( function() {
					return ( $( this ).closest( '.streamContainer' ).is( cbactivity.element ) || $( this ).is( cbactivity.element ) );
				});
	}

	function findContainer( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = filterSelector.call( element.closest( '.streamItem' ), cbactivity );
		var containerId = element.data( 'cbactivity-container' );

		if ( typeof containerId != 'undefined' ) {
			if ( containerId == 'self' ) {
				container = element;
			} else if ( containerId == 'items' ) {
				container = element.closest( '.streamItems' );
			} else if ( containerId == 'item' ) {
				container = element.closest( '.streamItem' );
			} else if ( containerId == 'stream' ) {
				container = element.closest( '.streamContainer' );
			} else {
				container = $( containerId );

				if ( element.data( 'cbactivity-container-filter' ) != false ) {
					container = filterSelector.call( container, cbactivity );
				}
			}
		}

		return container;
	}

	function bindContainer( cbactivity, init ) {
		var element = ( this.jquery ? this : $( this ) );

		if ( init === true ) {
			filterSelector.call( element.find( '.cbMoreLess' ), cbactivity ).cbmoreless();
			filterSelector.call( element.find( '.cbRepeat' ), cbactivity ).cbrepeat();

			filterSelector.call( element.find( 'select.streamInputSelect' ), cbactivity ).off( 'cbselect.init.before', cbactivity.cbselectBeforeHandler ).on( 'cbselect.init.before', cbactivity.cbselectBeforeHandler
			).off( 'cbselect.init.after', cbactivity.cbselectAfterHandler ).on( 'cbselect.init.after', cbactivity.cbselectAfterHandler
			).cbselect({
				width: 'auto',
				height: 'auto',
				minimumResultsForSearch: Infinity,
				templateSelection: cbactivity.cbselectFormat,
				templateResult: cbactivity.cbselectFormat
			});

			filterSelector.call( element.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ), cbactivity ).cbtooltip();

			filterSelector.call( element.find( '.streamItemNewCancel' ), cbactivity ).off( 'click', cbactivity.cancelNewHandler ).on( 'click', cbactivity.cancelNewHandler );
			filterSelector.call( element.find( '.streamItemForm' ), cbactivity ).off( 'submit', cbactivity.saveHandler ).on( 'submit', cbactivity.saveHandler );
			filterSelector.call( element.find( '.streamItemActionResponsesClose' ), cbactivity ).off( 'click', cbactivity.closeActionsHandler ).on( 'click', cbactivity.closeActionsHandler );
			filterSelector.call( element.find( '.streamItemActionResponsesRevert' ), cbactivity ).off( 'click', cbactivity.revertActionsHandler ).on( 'click', cbactivity.revertActionsHandler );
			filterSelector.call( element.find( '.streamItemActionResponseClose' ), cbactivity ).off( 'click', cbactivity.closeActionHandler ).on( 'click', cbactivity.closeActionHandler );
			filterSelector.call( element.find( '.streamItemNew .streamInputMessage.streamInputMessageCollapse' ), cbactivity ).off( 'click', cbactivity.displayNewHandler ).on( 'click', cbactivity.displayNewHandler );
			filterSelector.call( element.find( '.streamInputMessage[data-cbactivity-input-limit]' ), cbactivity ).off( 'keyup input change', cbactivity.messageLimitHandler ).on( 'keyup input change', cbactivity.messageLimitHandler );
			filterSelector.call( element.find( '.streamItemScrollLeft' ), cbactivity ).off( 'click', cbactivity.scrollerLeftHandler ).on( 'click', cbactivity.scrollerLeftHandler );
			filterSelector.call( element.find( '.streamItemScrollRight' ), cbactivity ).off( 'click', cbactivity.scrollerRightandler ).on( 'click', cbactivity.scrollerRightandler );
			filterSelector.call( element.find( '.streamFindLocation' ), cbactivity ).off( 'click', cbactivity.locationHandler ).on( 'click', cbactivity.locationHandler );
			filterSelector.call( element.find( '.streamToggle' ), cbactivity ).off( 'click', cbactivity.toggleHandler ).on( 'click', cbactivity.toggleHandler );
			filterSelector.call( element.find( 'select.streamInputSelect' ), cbactivity ).off( 'change', cbactivity.toggleSelectHandler ).on( 'change', cbactivity.toggleSelectHandler );
			filterSelector.call( element.find( '.streamMore,.streamUpdate' ), cbactivity ).off( 'click', cbactivity.moreHandler ).on( 'click', cbactivity.moreHandler );
			filterSelector.call( element.find( '.streamModal' ), cbactivity ).off( 'cbtooltip.render', cbactivity.modalHandler ).on( 'cbtooltip.render', cbactivity.modalHandler );
		}

		filterSelector.call( element.find( '.streamInputAutosize:visible' ), cbactivity ).trigger( 'autosize.destroy' ).autosize({
			append: '',
			resizeDelay: 0,
			placeholder: false
		});

		cbactivity.element.triggerHandler( 'cbactivity.bind', [cbactivity, element, init] );
	}

	function streamToggle( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		var cbselect = ( typeof element.data( 'cbselect' ) != 'undefined' );

		var target = element.data( 'cbactivity-toggle-target' );
		var activeClasses = element.data( 'cbactivity-toggle-active-classes' );
		var inactiveClasses = element.data( 'cbactivity-toggle-inactive-classes' );

		var allowOpen = ( element.data( 'cbactivity-toggle-open' ) != false );
		var allowClose = ( element.data( 'cbactivity-toggle-close' ) != false );
		var allowFilter = ( element.data( 'cbactivity-toggle-filter' ) != false );

		var open = false;

		if ( cbselect || element.is( 'select' ) ) {
			var value = element.val();

			if ( cbselect ) {
				value = element.cbselect( 'get' );
			}

			open = ( ( ! value ) || ( value == 0 ) || ( value == '' ) );
		} else {
			if ( element.hasClass( 'streamToggleOpen' ) ) {
				open = true;
			}
		}

		if ( open ) {
			if ( allowClose ) {
				element.removeClass( 'streamToggleOpen' );

				if ( cbselect ) {
					element.cbselect( 'container' ).removeClass( 'streamToggleOpen' );
				}

				if ( typeof activeClasses != 'undefined' ) {
					element.removeClass( activeClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).removeClass( activeClasses );
					}
				}

				if ( typeof inactiveClasses != 'undefined' ) {
					element.addClass( inactiveClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).addClass( inactiveClasses );
					}
				}

				if ( typeof target != 'undefined' ) {
					if ( allowFilter ) {
						filterSelector.call( container.find( target ), cbactivity ).addClass( 'hidden' );
						filterSelector.call( container.find( target ).find( 'input,textarea,select' ), cbactivity ).prop( 'disabled', true );
					} else {
						container.find( target ).addClass( 'hidden' );
						container.find( target ).find( 'input,textarea,select' ).prop( 'disabled', true );
					}
				}
			}
		} else {
			if ( allowOpen ) {
				element.addClass( 'streamToggleOpen' );

				if ( cbselect ) {
					element.cbselect( 'container' ).addClass( 'streamToggleOpen' );
				}

				if ( typeof activeClasses != 'undefined' ) {
					element.addClass( activeClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).addClass( activeClasses );
					}
				}

				if ( typeof inactiveClasses != 'undefined' ) {
					element.removeClass( inactiveClasses );

					if ( cbselect ) {
						element.cbselect( 'container' ).removeClass( inactiveClasses );
					}
				}

				if ( typeof target != 'undefined' ) {
					if ( allowFilter ) {
						filterSelector.call( container.find( target ), cbactivity ).removeClass( 'hidden' );
						filterSelector.call( container.find( target ).find( 'input,textarea,select' ), cbactivity ).prop( 'disabled', false );
					} else {
						container.find( target ).removeClass( 'hidden' );
						container.find( target ).find( 'input,textarea,select' ).prop( 'disabled', false );
					}
				}
			}
		}

		if ( element.hasClass( 'streamInputSelect' ) && ( typeof target != 'undefined' ) ) {
			var selected = element.find( ':selected' );
			var placeholder = selected.data( 'cbactivity-toggle-placeholder' );
			var label = filterSelector.call( container.find( target ).find( '.streamInputSelectToggleLabel' ), cbactivity );

			if ( label.length ) {
				label.html( selected.text() );
			}

			if ( typeof placeholder != 'undefined' ) {
				var input = filterSelector.call( container.find( target ).find( '.streamInputSelectTogglePlaceholder' ), cbactivity );

				if ( label.length ) {
					input.attr( 'placeholder', placeholder );
				}
			}
		}

		if ( element.hasClass( 'streamInputCheckbox' ) ) {
			var checkbox = element.find( 'input[type="checkbox"]' );

			if ( open ) {
				checkbox.prop( 'checked', false );
			} else {
				checkbox.prop( 'checked', true );
			}
		}
	}

	function cancelStreamNew( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var container = findContainer.call( element, cbactivity );

		if ( ! container.length ) {
			return;
		}

		filterSelector.call( container.find( '.streamItemActionResponse' ), cbactivity ).remove();

		var input = filterSelector.call( container.find( '.streamInputMessage' ), cbactivity );

		if ( input.hasClass( 'streamInputMessageCollapse' ) ) {
			filterSelector.call( container.find( '.streamItemDisplay' ), cbactivity ).addClass( 'hidden' );

			input.attr( 'rows', 1 );
		}

		filterSelector.call( container.find( 'input,textarea,select' ), cbactivity ).each( function() {
			var type = $( this ).attr( 'type' );

			if ( ( type == 'checkbox' ) || ( type == 'radio' ) ) {
				if ( ( type == 'radio' ) && ( ( $( this ).siblings( 'input[type="radio"]' ).length + 1 ) == 2 ) && ( $( this ).val() == 0 ) ) {
					$( this ).prop( 'checked', true );
				} else {
					$( this ).prop( 'checked', false );
				}
			} else {
				if ( $( this ).is( 'select' ) ) {
					var value = '';

					if ( ( ! $( this ).children( 'option[value=""]:first' ).length ) && ( ! $( this ).hasClass( 'streamInputTags' ) ) ) {
						value = $( this ).children( 'option[value!=""]:first' ).val();
					}

					if ( typeof $( this ).data( 'cbselect' ) != 'undefined' ) {
						$( this ).cbselect( 'set', value );
					} else {
						$( this ).val( value );
					}
				} else {
					$( this ).val( '' );
				}
			}

			$( this ).trigger( 'change' );
		});

		filterSelector.call( container.find( '.streamToggleOpen' ), cbactivity ).each( function() {
			streamToggle.call( this, cbactivity );
		});

		filterSelector.call( container.find( '.cbRepeat' ), cbactivity ).cbrepeat( 'reset' );

		cbactivity.element.triggerHandler( 'cbactivity.new.cancel', [cbactivity, element, container] );

		bindContainer.call( container, cbactivity );
	}

	function parseHeaders( cbactivity ) {
		var element = ( this.jquery ? this : $( this ) );
		var headers = element.find( '.streamItemHeaders' );

		if ( ! headers.length ) {
			headers = element.filter( '.streamItemHeaders' );
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

	function parseScripts( cbactivity, loadScripts ) {
		if ( ! loadScripts.length ) {
			return;
		}

		var element = ( this.jquery ? this : $( this ) );
		var scripts = $( '<div class="streamItemHeadersScripts" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;" />' );

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

		var headers = element.find( '.streamItemHeaders' );

		if ( ! headers.length ) {
			headers = element.filter( '.streamItemHeaders' );
		}

		headers.append( scripts );
	}

	var autoUpdaterReady = false;
	var autoUpdaterMaxAttempts = 60;
	var autoUpdaterAttempts = 0;

	function autoUpdater() {
		if ( autoUpdaterReady ) {
			return;
		}

		autoUpdaterReady = true;

		var autoUpdate = function() {
			if ( ! instances.length ) {
				return;
			}

			if ( ! document.hidden ) {
				$.each( instances, function( i, instance ) {
					if ( ( ! instance.settings.autoUpdate ) || ( ! instance.element.length ) ) {
						return false;
					}

					var update = filterSelector.call( instance.element.find( '.streamUpdate,.streamRefresh' ), instance );

					if ( ! update.length ) {
						return false;
					}

					update.click();
				});

				autoUpdaterAttempts++;
			}

			if ( autoUpdaterAttempts < autoUpdaterMaxAttempts ) {
				setTimeout( autoUpdate, 60000 );
			} else {
				autoUpdaterReady = false;
			}
		};

		if ( autoUpdaterAttempts < autoUpdaterMaxAttempts ) {
			setTimeout( autoUpdate, 60000 );
		}
	}

	var autoLoaderReady = false;
	var autoLoaderMaxAttempts = 150;
	var autoLoaderAttempts = 0;

	function autoLoader() {
		if ( autoLoaderReady ) {
			return;
		}

		autoLoaderReady = true;

		var autoLoader = function() {
			if ( ! instances.length ) {
				return;
			}

			if ( ! document.hidden ) {
				$.each( instances, function( i, instance ) {
					if ( ( ! instance.settings.autoLoad ) || ( instance.settings.direction == 'up' ) || ( ! instance.element.length ) ) {
						return false;
					}

					var more = filterSelector.call( instance.element.find( '.streamMore' ), instance );

					if ( ! more.length ) {
						return false;
					}

					var top = $( window ).scrollTop();
					var bottom = ( top + $( window ).height() );

					var items = filterSelector.call( instance.element.find( '.streamItem[data-cbactivity-id]' ), instance );
					var item = items.eq( - Math.round( items.length / 3 ) );
					var moreTop = null;
					var moreBottom = null;

					if ( item.length ) {
						moreTop = item.offset().top;
						moreBottom = ( moreTop + item.height() );
					} else {
						moreTop = more.offset().top;
						moreBottom = ( moreTop + more.height() );
					}

					if ( ( top >= moreTop ) || ( bottom >= moreBottom ) ) {
						more.click();
					}
				});

				autoLoaderAttempts++;
			}

			if ( autoLoaderAttempts < autoLoaderMaxAttempts ) {
				setTimeout( autoLoader, 2000 );
			} else {
				autoLoaderReady = false;
			}
		};

		if ( autoLoaderAttempts < autoLoaderMaxAttempts ) {
			setTimeout( autoLoader, 5000 );
		}
	}

	$.fn.cbactivity = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbactivity.defaults = {
		init: true,
		useData: true,
		direction: 'down',
		autoUpdate: false,
		autoLoad: false
	};
})(jQuery);