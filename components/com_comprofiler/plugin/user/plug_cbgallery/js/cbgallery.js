(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbgallery = $( $this ).data( 'cbgallery' );

				if ( cbgallery ) {
					return; // cbgallery is already bound; so no need to rebind below
				}

				cbgallery = {};
				cbgallery.options = options;
				cbgallery.defaults = $.fn.cbgallery.defaults;
				cbgallery.settings = $.extend( true, {}, cbgallery.defaults, cbgallery.options );
				cbgallery.element = $( $this );

				if ( cbgallery.settings.useData ) {
					$.each( cbgallery.defaults, function( key, value ) {
						if ( ( key != 'init' ) && ( key != 'useData' ) ) {
							// Dash Separated:
							var dataValue = cbgallery.element.data( 'cbgallery' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ) );

							if ( typeof dataValue != 'undefined' ) {
								cbgallery.settings[key] = dataValue;
							} else {
								// No Separater:
								dataValue = cbgallery.element.data( 'cbgallery' + key.charAt( 0 ).toUpperCase() + key.slice( 1 ).toLowerCase() );

								if ( typeof dataValue != 'undefined' ) {
									cbgallery.settings[key] = dataValue;
								}
							}
						}
					});
				}

				cbgallery.element.triggerHandler( 'cbgallery.init.before', [cbgallery] );

				if ( ! cbgallery.settings.init ) {
					return;
				}

				if ( ( cbgallery.settings.request === null ) || ( ( typeof cbgallery.settings.request != 'object' ) && ( ! $.isArray( cbgallery.settings.request ) ) ) ) {
					cbgallery.settings.request = {};
				}

				$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
					options.async = true;
				});

				if ( cbgallery.settings.mode == 'modal' ) {
					if ( ! cbgallery.settings.url ) {
						return;
					}

					if ( cbgallery.settings.previous ) {
						cbgallery.settings.request['previous'] = cbgallery.settings.previous;
					}

					if ( cbgallery.settings.next ) {
						cbgallery.settings.request['next'] = cbgallery.settings.next;
					}

					cbgallery.navigationHandler = function( e ) {
						var cbtooltip = cbgallery.element.data( 'cbtooltip' );

						if ( ( ! cbtooltip ) || $( e.target ).is( 'input,textarea' ) ) {
							return;
						}

						var nav = 0;

						if ( e.type == 'swipeleft' ) {
							nav = 37;
						} else if ( e.type == 'swiperight' ) {
							nav = 39;
						} else {
							nav = e.which;
						}

						switch( nav ) {
							case 27: // escape
								var close = cbtooltip.tooltip.qtip( 'api' ).elements.content.find( '.galleryModalClose' );

								if ( close.length ) {
									close.click();
								} else {
									return;
								}
								break;
							case 37: // left
								var previous = cbtooltip.tooltip.qtip( 'api' ).elements.content.find( '.galleryModalScrollLeftIcon' );

								if ( previous.length ) {
									previous.click();
								} else {
									return;
								}
								break;
							case 39: // right
								var next = cbtooltip.tooltip.qtip( 'api' ).elements.content.find( '.galleryModalScrollRightIcon' );

								if ( next.length ) {
									next.click();
								} else {
									return;
								}
								break;
							default:
								return;
						}

						e.preventDefault();
					};

					cbgallery.actionHandler = function( e ) {
						e.preventDefault();

						if ( cbgallery.element.hasClass( 'galleryRequesting' ) ) {
							return;
						}

						var cbtooltip = cbgallery.element.data( 'cbtooltip' );

						if ( ! cbtooltip ) {
							return;
						}

						var element = $( e.target );
						var actionUrl = element.data( 'cbgallery-action-url' );

						if ( ! actionUrl ) {
							actionUrl = element.attr( 'href' );
						}

						if ( ! actionUrl ) {
							return;
						}

						var confirmMessage = element.data( 'cbgallery-confirm' );
						var confirmButton = element.data( 'cbgallery-confirm-button' );

						cbgallery.element.addClass( 'galleryRequesting' );

						var callback = function() {
							var ajax = null;

							cbtooltip.tooltip.qtip( 'api' ).set( 'content.text', function( e, api ) {
								if ( ajax == null ) {
									ajax = $.ajax({
										url: actionUrl,
										type: 'GET',
										dataType: 'html',
										cache: false,
										beforeSend: function( jqXHR, textStatus, errorThrown ) {
											cbgallery.element.triggerHandler( 'cbgallery.modal.action.send', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
										}
									}).fail( function( jqXHR, textStatus, errorThrown ) {
										cbgallery.element.removeClass( 'galleryRequesting' );

										cbgallery.element.triggerHandler( 'cbgallery.modal.action.error', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
									}).done( function( data, textStatus, jqXHR ) {
										cbgallery.element.removeClass( 'galleryRequesting' );

										if ( ! api.destroyed ) {
											$( document ).off( 'keydown.cbgallery', cbgallery.navigationHandler );
											$( document ).off( 'click.cbgallery', '.galleryModalAction', cbgallery.actionHandler );

											var response = null;

											try {
												response = JSON.parse( data );
											} catch( e ) {
												response = { status: true, message: data };
											}

											var modal = cbgallery.displayHandler( e, cbtooltip );

											if ( modal ) {
												modal.done( function( data, textStatus, jqXHR ) {
													if ( response && ( typeof response.status !== 'undefined' ) ) {
														if ( response.status === true ) {
															if ( ( typeof response.message !== 'undefined' ) && response.message ) {
																if ( api.elements.content ) {
																	api.elements.content.find( '.galleryModalDisplay' ).prepend( '<div class="galleryModalAlert alert alert-sm alert-success">' + response.message + '</div>' );
																}
															}

															cbgallery.element.triggerHandler( 'cbgallery.modal.action.success', [cbgallery, cbtooltip, response, textStatus, jqXHR] );
														} else if ( response.status === false ) {
															if ( ( typeof response.message !== 'undefined' ) && response.message ) {
																if ( api.elements.content ) {
																	api.elements.content.find( '.galleryModalDisplay' ).prepend( '<div class="galleryModalAlert alert alert-sm alert-danger">' + response.message + '</div>' );
																}
															}

															cbgallery.element.triggerHandler( 'cbgallery.modal.action.failed', [cbgallery, cbtooltip, response, textStatus, jqXHR] );
														}
													}
												});
											}
										}

										cbgallery.element.triggerHandler( 'cbgallery.modal.action.always', [cbgallery, cbtooltip, data, textStatus, jqXHR] );
									});
								}

								return '<div class="galleryModalLoading text-center"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>';
							});
						};

						if ( confirmMessage ) {
							$.cbconfirm( confirmMessage, { buttonYes: confirmButton } ).done( callback ).fail( function() {
								cbgallery.element.removeClass( 'galleryRequesting' );
							});
						} else {
							callback();
						}
					};

					cbgallery.closeHandler = function() {
						$( document ).off( 'keydown.cbgallery', cbgallery.navigationHandler );
						$( document ).off( 'click.cbgallery', '.galleryModalAction', cbgallery.actionHandler );

						$( 'body' ).removeClass( 'galleryModalOpen' );
						$( 'body > .galleryHeadersScripts' ).remove();
					};

					cbgallery.element.on( 'cbtooltip.hidden', cbgallery.closeHandler );

					cbgallery.moveHandler = function( e, cbtooltip, event, api ) {
						if ( api.elements.tooltip ) {
							api.elements.content.find( '.galleryModalItem,.galleryModalLoading' ).css( 'line-height', api.elements.content.css( 'max-height' ) );
							api.elements.content.find( '.galleryRotate90,.galleryRotate270' ).css( 'max-width', api.elements.content.css( 'max-height' ) );
						}
					};

					cbgallery.element.on( 'cbtooltip.move', cbgallery.moveHandler );

					cbgallery.displayHandler = function( e, cbtooltip ) {
						if ( cbgallery.element.hasClass( 'galleryRequesting' ) ) {
							return;
						}

						cbgallery.element.addClass( 'galleryRequesting' );

						if ( cbgallery.settings.preload ) {
							var image = new Image();

							image.src = cbgallery.settings.preload;
						}

						cbtooltip.tooltip.qtip( 'api' ).set({
							'show.effect': false,
							'hide.effect': false
						});

						var ajax = null;

						cbtooltip.tooltip.qtip( 'api' ).set( 'content.text', function( e, api ) {
							if ( ajax == null ) {
								ajax = $.ajax({
									url: cbgallery.settings.url,
									type: 'GET',
									dataType: 'html',
									cache: false,
									data: cbgallery.settings.request,
									beforeSend: function( jqXHR, textStatus, errorThrown ) {
										cbgallery.element.triggerHandler( 'cbgallery.modal.send', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
									}
								}).fail( function( jqXHR, textStatus, errorThrown ) {
									cbgallery.element.removeClass( 'galleryRequesting' );

									if ( ! api.destroyed ) {
										api.hide();
									}

									cbgallery.element.triggerHandler( 'cbgallery.modal.error', [cbgallery, cbtooltip, jqXHR, textStatus, errorThrown] );
								}).done( function( data, textStatus, jqXHR ) {
									cbgallery.element.removeClass( 'galleryRequesting' );

									if ( ! api.destroyed ) {
										var dataHtml = $( data );
										var loadScripts = parseHeaders.call( dataHtml, cbgallery );

										api.set( 'content.text', dataHtml.html() );

										parseScripts.call( api.elements.content, cbgallery, loadScripts );

										if ( api.elements.content ) {
											api.elements.content.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

											api.elements.content.find( '.galleryModalScrollLeftIcon' ).on( 'click', function () {
												var previous = $( this ).data( 'cbgallery-previous' );

												if ( previous ) {
													api.toggle( false );

													cbgallery.element.closest( '.galleryContainer' ).siblings( previous ).find( '.galleryModalToggle:first' ).click();
												}
											});

											api.elements.content.find( '.galleryModalScrollRightIcon' ).on( 'click', function () {
												var next = $( this ).data( 'cbgallery-next' );

												if ( next ) {
													api.toggle( false );

													cbgallery.element.closest( '.galleryContainer' ).siblings( next ).find( '.galleryModalToggle:first' ).click();
												}
											});

											api.elements.content.find( '.galleryModalDisplay' ).on( 'swipeleft swiperight', cbgallery.navigationHandler );
										}

										$( document ).off( 'keydown.cbgallery' ).on( 'keydown.cbgallery', cbgallery.navigationHandler );
										$( document ).off( 'click.cbgallery' ).on( 'click.cbgallery', '.galleryModalAction', cbgallery.actionHandler );

										$( 'body' ).addClass( 'galleryModalOpen' );
									}

									cbgallery.element.triggerHandler( 'cbgallery.modal.success', [cbgallery, cbtooltip, data, textStatus, jqXHR] );
								});
							}

							return '<div class="galleryModalLoading text-center"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>';
						});

						return ajax;
					};

					cbgallery.element.on( 'cbtooltip.render', cbgallery.displayHandler );
				} else if ( cbgallery.settings.mode == 'share' ) {
					if ( ! cbgallery.settings.url ) {
						return;
					}

					cbgallery.element.find( '.galleryShareUpload' ).fileupload({
						url: cbgallery.settings.url,
						dataType: 'html',
						sequentialUploads: true,
						replaceFileInput: false,
						dropZone: cbgallery.element.find( '.galleryShareUploadDropZone' ),
						pasteZone: $( document ),
						processQueue: [
							{
								action: 'clientSideResize',
								cbgallery: cbgallery
							}
						],
						add: function( e, data ) {
							if ( cbgallery.settings.callback.upload.add ) {
								cbgallery.element.find( '.galleryShareUploadProgress' ).removeClass( 'hidden' );
							}

							$.each( data.files, function( index, file ) {
								file.error = null;
								file.context = null;

								if ( cbgallery.settings.callback.upload.add ) {
									file.context = cbgallery.settings.callback.upload.add.call( cbgallery.element, cbgallery, data, file );
								}

								if ( file.context ) {
									file.context.find( '.galleryShareUploadProgressCancel' ).on( 'click', function() {
										data.abort();

										file.context.find( '.progress-bar' ).css( 'width', '100%' ).removeClass( 'progress-bar-striped active' ).addClass( 'progress-bar-warning' );
										file.context.find( '.galleryShareUploadProgressClear' ).removeClass( 'hidden' );
										file.context.find( '.galleryShareUploadProgressCancel' ).remove();
									});

									file.context.find( '.galleryShareUploadProgressClear' ).on( 'click', function() {
										file.context.next( '.galleryShareUploadProgressError' ).remove();
										file.context.remove();

										if ( ! cbgallery.element.find( '.galleryShareUploadProgressRow' ).length ) {
											cbgallery.element.find( '.galleryShareUploadProgress' ).addClass( 'hidden' );
										}
									});

									file.context.appendTo( cbgallery.element.find( '.galleryShareUploadProgressRows' ) );
								}

								cbgallery.element.triggerHandler( 'cbgallery.upload.add.file', [cbgallery, file, data] );
							});

							if ( typeof $.blueimp.fileupload.prototype.process !== 'undefined' ) {
								data.process( function () {
									return cbgallery.element.find( '.galleryShareUpload' ).fileupload( 'process', data );
								});
							}

							data.process().done( function () {
								data.submit();
							});

							$( this ).val( '' );

							cbgallery.element.triggerHandler( 'cbgallery.upload.add', [cbgallery, data] );
						},
						progress: function( e, data ) {
							var file = data.files[0].context;

							if ( file ) {
								file.find( '.progress-bar' ).css( 'width', parseInt( ( ( data.loaded / data.total ) * 100 ), 10 ) + '%' );
							}

							cbgallery.element.triggerHandler( 'cbgallery.upload.progress', [cbgallery, file, data] );
						},
						fail: function( e, data ) {
							var file = data.files[0].context;

							if ( file ) {
								file.find( '.progress-bar' ).css( 'width', '100%' ).removeClass( 'progress-bar-striped active' ).addClass( 'progress-bar-danger' );
								file.find( '.galleryShareUploadProgressClear' ).removeClass( 'hidden' );
								file.find( '.galleryShareUploadProgressCancel' ).remove();
							}

							cbgallery.element.triggerHandler( 'cbgallery.upload.error', [cbgallery, file, data] );
						},
						done: function( e, data ) {
							var response = null;

							try {
								response = JSON.parse( data.result );
							} catch( e ) {
								response = { status: true, message: data.result };
							}

							var file = data.files[0].context;

							if ( file ) {
								var progressBar = file.find( '.progress-bar' );

								file.find( '.galleryShareUploadProgressClear' ).removeClass( 'hidden' );
								file.find( '.galleryShareUploadProgressCancel' ).remove();

								progressBar.css( 'width', '100%' ).removeClass( 'progress-bar-striped active' );
							}

							if ( response && ( typeof response.status !== 'undefined' ) ) {
								if ( response.status === true ) {
									if ( file ) {
										progressBar.addClass( 'progress-bar-success' );
									}

									if ( ( typeof response.message !== 'undefined' ) && response.message ) {
										var dataHtml = $( response.message ).hide();
										var loadScripts = parseHeaders.call( dataHtml, cbgallery );

										cbgallery.element.find( '.galleryShareEdit' ).append( dataHtml );

										parseScripts.call( dataHtml, cbgallery, loadScripts );

										dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

										if ( file ) {
											file.fadeOut( 'slow', function() {
												file.find( '.galleryShareUploadProgressClear' ).click();
											});
										}

										dataHtml.fadeIn( 'slow' );

										var setInitialValues = function() {
											var currentValue = $( this ).val();

											if ( $.isArray( currentValue ) ) {
												currentValue = currentValue.join( '|*|' );
											}

											$( this ).data( 'cbgallery-initial-value', currentValue );
										};

										dataHtml.find( 'input,select,textarea' ).each( setInitialValues );

										var editSaveToggleDone = function( subevent, subcbgallery ) {
											subcbgallery.element.on( 'cbgallery.save.success', editSaveToggleDone );
											subcbgallery.element.on( 'cbgallery.delete.success', editDeleteToggleDone );

											subcbgallery.element.find( 'input,select,textarea' ).each( setInitialValues );

											if ( cbgallery.element.find( '.galleryShareEdit .galleryEditChanged' ).length ) {
												cbgallery.element.find( '.galleryShareEditConfirm' ).removeClass( 'hidden' );
												cbgallery.element.find( '.galleryShareEditDone' ).addClass( 'hidden' );
											} else {
												cbgallery.element.find( '.galleryShareEditConfirm' ).addClass( 'hidden' );
												cbgallery.element.find( '.galleryShareEditDone' ).removeClass( 'hidden' );
											}

											cbgallery.element.find( '.galleryShareEditBack' ).addClass( 'hidden' );
										};

										var editDeleteToggleDone = function() {
											if ( cbgallery.element.find( '.galleryShareEdit .galleryEditChanged' ).length ) {
												cbgallery.element.find( '.galleryShareEditConfirm' ).removeClass( 'hidden' );
												cbgallery.element.find( '.galleryShareEditDone' ).addClass( 'hidden' );
											} else {
												cbgallery.element.find( '.galleryShareEditConfirm' ).addClass( 'hidden' );
												cbgallery.element.find( '.galleryShareEditDone' ).removeClass( 'hidden' );
											}

											cbgallery.element.find( '.galleryShareEditBack' ).addClass( 'hidden' );
										};

										dataHtml.on( 'cbgallery.save.success', editSaveToggleDone );
										dataHtml.on( 'cbgallery.delete.success', editDeleteToggleDone );

										cbgallery.element.find( '.galleryShareEditConfirm' ).addClass( 'hidden' );
										cbgallery.element.find( '.galleryShareEditDone' ).removeClass( 'hidden' );
										cbgallery.element.find( '.galleryShareEditBack' ).addClass( 'hidden' );
									}

									cbgallery.element.triggerHandler( 'cbgallery.upload.success', [cbgallery, file, data] );
								} else if ( response.status === false ) {
									if ( file ) {
										progressBar.addClass( 'progress-bar-danger' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											if ( cbgallery.settings.callback.upload.error ) {
												data.files[0].error = cbgallery.settings.callback.upload.error.call( cbgallery.element, cbgallery, response, file, data );
											}

											if ( data.files[0].error ) {
												file.after( data.files[0].error );
											}
										}
									}

									cbgallery.element.triggerHandler( 'cbgallery.upload.failed', [cbgallery, file, data] );
								}
							}

							cbgallery.element.triggerHandler( 'cbgallery.upload.always', [cbgallery, file, data] );
						}
					});

					cbgallery.uploadChangeHandler = function() {
						var currentValue = $( this ).val();

						if ( $.isArray( currentValue ) ) {
							currentValue = currentValue.join( '|*|' );
						}

						if ( currentValue === $( this ).data( 'cbgallery-initial-value' ) ) {
							$( this ).removeClass( 'galleryEditChanged' );
						} else {
							$( this ).addClass( 'galleryEditChanged' );
						}

						if ( cbgallery.element.find( '.galleryShareEdit .galleryEditChanged' ).length ) {
							cbgallery.element.find( '.galleryShareEditConfirm' ).removeClass( 'hidden' );
							cbgallery.element.find( '.galleryShareEditDone' ).addClass( 'hidden' );
						} else {
							cbgallery.element.find( '.galleryShareEditConfirm' ).addClass( 'hidden' );
							cbgallery.element.find( '.galleryShareEditDone' ).removeClass( 'hidden' );
						}

						cbgallery.element.find( '.galleryShareEditBack' ).addClass( 'hidden' );
					};

					cbgallery.element.find( '.galleryShareEdit' ).on( 'keyup change', 'input,select,textarea', cbgallery.uploadChangeHandler );

					cbgallery.dropZoneHandler = function( e ) {
						if ( ! $( e.target ).is( 'input' ) ) {
							e.preventDefault();

							cbgallery.element.find( '.galleryShareUpload' ).click();
						}
					};

					cbgallery.element.find( '.galleryShareUploadDropZone' ).on( 'click', cbgallery.dropZoneHandler );

					cbgallery.linkSaveHandler = function( e ) {
						e.preventDefault();

						if ( cbgallery.element.hasClass( 'galleryRequesting' ) ) {
							return;
						}

						var button = $( this );
						var link = cbgallery.element.find( '.galleryShareLink' );

						if ( link.val() ) {
							cbgallery.element.addClass( 'galleryRequesting' );

							cbgallery.settings.request['value'] = link.val();

							$.ajax({
								url: cbgallery.settings.url,
								type: 'POST',
								dataType: 'html',
								cache: false,
								data: cbgallery.settings.request,
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									link.prop( 'disabled', true );
									button.prop( 'disabled', true );

									cbgallery.element.find( '.galleryShareLinkLoading' ).removeClass( 'hidden' );
									cbgallery.element.find( '.galleryShareLinkArea' ).removeClass( 'has-error' );
									cbgallery.element.find( '.galleryShareLinkError' ).remove();

									cbgallery.element.triggerHandler( 'cbgallery.link.send', [cbgallery, jqXHR, textStatus, errorThrown] );
								}
							}).fail( function( jqXHR, textStatus, errorThrown ) {
								cbgallery.element.removeClass( 'galleryRequesting' );
								cbgallery.element.find( '.galleryShareLinkLoading' ).addClass( 'hidden' );
								cbgallery.element.find( '.galleryShareLinkArea' ).addClass( 'has-error' );

								cbgallery.element.triggerHandler( 'cbgallery.link.error', [cbgallery, jqXHR, textStatus, errorThrown] );
							}).done( function( data, textStatus, jqXHR ) {
								cbgallery.element.removeClass( 'galleryRequesting' );

								var response = null;

								try {
									response = JSON.parse( data );
								} catch( e ) {
									response = { status: true, message: data };
								}

								cbgallery.element.find( '.galleryShareLinkLoading' ).addClass( 'hidden' );

								link.prop( 'disabled', false );
								button.prop( 'disabled', false );

								if ( response && ( typeof response.status !== 'undefined' ) ) {
									if ( response.status === true ) {
										link.val( '' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var dataHtml = $( response.message ).hide();
											var loadScripts = parseHeaders.call( dataHtml, cbgallery );

											cbgallery.element.find( '.galleryShareEdit' ).append( dataHtml );

											parseScripts.call( dataHtml, cbgallery, loadScripts );

											dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

											dataHtml.fadeIn( 'slow' );
										}

										cbgallery.element.triggerHandler( 'cbgallery.link.success', [cbgallery, response, textStatus, jqXHR] );
									} else if ( response.status === false ) {
										cbgallery.element.find( '.galleryShareLinkArea' ).addClass( 'has-error' );

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var error = null;

											if ( cbgallery.settings.callback.link.error ) {
												error = cbgallery.settings.callback.link.error.call( cbgallery.element, cbgallery, response, data );
											}

											if ( error ) {
												cbgallery.element.find( '.galleryShareLinkArea' ).append( error );
											}
										}

										cbgallery.element.triggerHandler( 'cbgallery.link.failed', [cbgallery, response, textStatus, jqXHR] );
									}
								}

								cbgallery.element.triggerHandler( 'cbgallery.link.always', [cbgallery, response, textStatus, jqXHR] );
							});
						} else {
							cbgallery.element.find( '.galleryShareLinkArea' ).addClass( 'has-error' );
						}
					};

					cbgallery.element.find( '.galleryShareLinkSave' ).on( 'click', cbgallery.linkSaveHandler );
				} else if ( cbgallery.settings.mode == 'edit' ) {
					cbgallery.deleteHandler = function( e ) {
						e.preventDefault();

						if ( cbgallery.element.hasClass( 'galleryRequesting' ) ) {
							return;
						}

						var deleteUrl = $( this ).data( 'cbgallery-delete-url' );

						if ( ! deleteUrl ) {
							return false;
						}

						$.cbconfirm( $( this ).data( 'cbgallery-delete-message' ) ).done( function() {
							cbgallery.element.addClass( 'galleryRequesting' );

							$.ajax({
								url: deleteUrl,
								type: 'POST',
								dataType: 'html',
								cache: false,
								beforeSend: function( jqXHR, textStatus, errorThrown ) {
									if ( cbgallery.element.hasClass( 'panel' ) ) {
										cbgallery.element.removeClass( 'panel-danger' ).addClass( 'panel-default' );
									}

									cbgallery.element.find( '.galleryEditError' ).remove();
									cbgallery.element.find( '.galleryEditLoading' ).removeClass( 'hidden' ).css( 'line-height', cbgallery.element.outerHeight() + 'px' );

									cbgallery.element.triggerHandler( 'cbgallery.delete.send', [cbgallery, jqXHR, textStatus, errorThrown] );
								}
							}).fail( function( jqXHR, textStatus, errorThrown ) {
								cbgallery.element.removeClass( 'galleryRequesting' );
								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );

								if ( cbgallery.element.hasClass( 'panel' ) ) {
									cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );
								}

								cbgallery.element.triggerHandler( 'cbgallery.delete.error', [cbgallery, jqXHR, textStatus, errorThrown] );
							}).done( function( data, textStatus, jqXHR ) {
								cbgallery.element.removeClass( 'galleryRequesting' );

								var response = null;

								try {
									response = JSON.parse( data );
								} catch( e ) {
									response = { status: true, message: data };
								}

								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );

								if ( response && ( typeof response.status !== 'undefined' ) ) {
									if ( response.status === true ) {
										// We are deleting the element so lets make a clone for the sake of the delete event:
										var previous = cbgallery.element.clone( true );

										cbgallery.element.fadeOut( 'slow', function() {
											$( this ).remove();

											previous.triggerHandler( 'cbgallery.delete.success', [cbgallery, response, textStatus, jqXHR] );
										});
									} else if ( response.status === false ) {
										if ( cbgallery.element.hasClass( 'panel' ) ) {
											cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );
										}

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var error = null;

											if ( cbgallery.settings.callback.delete.error ) {
												error = cbgallery.settings.callback.delete.error.call( cbgallery.element, cbgallery, response, data, textStatus, jqXHR );
											}

											if ( error ) {
												cbgallery.element.prepend( error );
											}
										}

										cbgallery.element.triggerHandler( 'cbgallery.delete.failed', [cbgallery, response, textStatus, jqXHR] );
									}
								}

								cbgallery.element.triggerHandler( 'cbgallery.delete.always', [cbgallery, response, textStatus, jqXHR] );
							});
						});

						return false;
					};

					cbgallery.element.find( '.galleryEditDelete' ).on( 'click', cbgallery.deleteHandler );

					cbgallery.editHandler = function( e ) {
						e.preventDefault();

						if ( cbgallery.element.hasClass( 'galleryRequesting' ) ) {
							return;
						}

						cbgallery.element.addClass( 'galleryRequesting' );

						cbgallery.element.find( '.galleryEditForm' ).ajaxSubmit({
							type: 'POST',
							dataType: 'html',
							beforeSerialize: function( form, options ) {
								cbgallery.element.triggerHandler( 'cbgallery.save.serialize', [cbgallery, form, options] );
							},
							beforeSubmit: function( formData, form, options ) {
								var validator = cbgallery.element.data( 'cbvalidate' );

								if ( validator ) {
									if ( ! validator.element.cbvalidate( 'validate' ) ) {
										return false;
									}
								}

								if ( cbgallery.element.hasClass( 'panel' ) ) {
									cbgallery.element.removeClass( 'panel-danger' ).addClass( 'panel-default' );
								}

								cbgallery.element.find( '.galleryEditError' ).remove();
								cbgallery.element.find( '.galleryEditLoading' ).removeClass( 'hidden' ).css( 'line-height', cbgallery.element.outerHeight() + 'px' );

								cbgallery.element.triggerHandler( 'cbgallery.save.submit', [cbgallery, formData, form, options] );
							},
							error: function( jqXHR, textStatus, errorThrown ) {
								cbgallery.element.removeClass( 'galleryRequesting' );
								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );

								if ( cbgallery.element.hasClass( 'panel' ) ) {
									cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );
								}

								cbgallery.element.triggerHandler( 'cbgallery.save.error', [cbgallery, jqXHR, textStatus, errorThrown] );
							},
							success: function( data, textStatus, jqXHR ) {
								cbgallery.element.removeClass( 'galleryRequesting' );

								var response = null;

								try {
									response = JSON.parse( data );
								} catch( e ) {
									response = { status: true, message: data };
								}

								cbgallery.element.find( '.galleryEditLoading' ).addClass( 'hidden' );

								if ( response && ( typeof response.status !== 'undefined' ) ) {
									if ( response.status === true ) {
										var dataHtml = $( data ).hide();
										var loadScripts = parseHeaders.call( dataHtml, cbgallery );

										// We are replacing the element so lets make a clone for the sake of the save event:
										var previous = cbgallery.element.clone( true );

										cbgallery.element.replaceWith( dataHtml );

										parseScripts.call( dataHtml, cbgallery, loadScripts );

										dataHtml.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();

										dataHtml.fadeIn( 'slow' );

										// Update the element since we replaced it above:
										cbgallery.element = dataHtml;

										previous.triggerHandler( 'cbgallery.save.success', [cbgallery, response, data, textStatus, jqXHR] );
									} else if ( response.status === false ) {
										if ( cbgallery.element.hasClass( 'panel' ) ) {
											cbgallery.element.removeClass( 'panel-default' ).addClass( 'panel-danger' );
										}

										if ( ( typeof response.message !== 'undefined' ) && response.message ) {
											var error = null;

											if ( cbgallery.settings.callback.edit.error ) {
												error = cbgallery.settings.callback.edit.error.call( cbgallery.element, cbgallery, response, data, textStatus, jqXHR );
											}

											if ( error ) {
												cbgallery.element.prepend( error );
											}
										}

										cbgallery.element.triggerHandler( 'cbgallery.save.failed', [cbgallery, response, data, textStatus, jqXHR] );
									}
								}
							}
						});

						return false;
					};

					cbgallery.element.find( '.galleryEditForm' ).on( 'submit', cbgallery.editHandler );
				}

				// Destroy the cbgallery element:
				cbgallery.element.on( 'remove destroy.cbgallery', function() {
					cbgallery.element.cbgallery( 'destroy' );
				});

				// Rebind the cbgallery element to pick up any data attribute modifications:
				cbgallery.element.on( 'rebind.cbgallery', function() {
					cbgallery.element.cbgallery( 'rebind' );
				});

				// If the cbgallery element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbgallery.element.on( 'modified.cbgallery', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbgallery.element.cbgallery( 'destroy' );
						cbgallery.element.cbgallery( cbgallery.options );
					}
				});

				// If the cbgallery is cloned we need to rebind it back:
				cbgallery.element.on( 'cloned.cbgallery', function( e, oldId ) {
					$( this ).off( '.cbgallery' );

					if ( cbgallery.settings.mode == 'modal' ) {
						$( document ).off( 'keydown.cbgallery', cbgallery.navigationHandler );
						$( document ).off( 'click.cbgallery', '.galleryModalAction', cbgallery.actionHandler );

						$( this ).off( 'cbtooltip.hidden', cbgallery.closeHandler );
						$( this ).off( 'cbtooltip.move', cbgallery.moveHandler );
						$( this ).off( 'cbtooltip.render', cbgallery.displayHandler );
					} else if ( cbgallery.settings.mode == 'share' ) {
						$( this ).find( '.galleryShareUpload' ).fileupload( 'destroy' );
						$( this ).find( '.galleryShareEdit' ).off( 'keyup change', 'input,select,textarea', cbgallery.uploadChangeHandler );
						$( this ).find( '.galleryShareUploadDropZone' ).off( 'click', cbgallery.dropZoneHandler );
						$( this ).find( '.galleryShareLinkSave' ).off( 'click', cbgallery.linkSaveHandler );
					} else if ( cbgallery.settings.mode == 'edit' ) {
						$( this ).find( '.galleryEditDelete' ).off( 'click', cbgallery.deleteHandler );
						$( this ).find( '.galleryEditSave' ).off( 'click', cbgallery.editHandler );
					}

					$( this ).removeData( 'cbgallery' );
					$( this ).cbgallery( cbgallery.options );
				});

				cbgallery.element.triggerHandler( 'cbgallery.init.after', [cbgallery] );

				// Bind the cbgallery to the element so it's reusable and chainable:
				cbgallery.element.data( 'cbgallery', cbgallery );

				// Add this instance to our instance array so we can keep track of our cbgallery instances:
				instances.push( cbgallery );
			});
		},
		rebind: function() {
			var cbgallery = $( this ).data( 'cbgallery' );

			if ( ! cbgallery ) {
				return this;
			}

			cbgallery.element.cbgallery( 'destroy' );
			cbgallery.element.cbgallery( cbgallery.options );

			return this;
		},
		destroy: function() {
			var cbgallery = $( this ).data( 'cbgallery' );

			if ( ! cbgallery ) {
				return false;
			}

			cbgallery.element.off( '.cbgallery' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbgallery.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			if ( cbgallery.settings.mode == 'modal' ) {
				$( document ).off( 'keydown.cbgallery', cbgallery.navigationHandler );
				$( document ).off( 'click.cbgallery', '.galleryModalAction', cbgallery.actionHandler );

				cbgallery.element.off( 'cbtooltip.hidden', cbgallery.closeHandler );
				cbgallery.element.off( 'cbtooltip.move', cbgallery.moveHandler );
				cbgallery.element.off( 'cbtooltip.render', cbgallery.displayHandler );
			} else if ( cbgallery.settings.mode == 'share' ) {
				cbgallery.element.find( '.galleryShareUpload' ).fileupload( 'destroy' );
				cbgallery.element.find( '.galleryShareEdit' ).off( 'keyup change', 'input,select,textarea', cbgallery.uploadChangeHandler );
				cbgallery.element.find( '.galleryShareUploadDropZone' ).off( 'click', cbgallery.dropZoneHandler );
				cbgallery.element.find( '.galleryShareLinkSave' ).off( 'click', cbgallery.linkSaveHandler );
			} else if ( cbgallery.settings.mode == 'edit' ) {
				cbgallery.element.find( '.galleryEditDelete' ).off( 'click', cbgallery.deleteHandler );
				cbgallery.element.find( '.galleryEditSave' ).off( 'click', cbgallery.editHandler );
			}

			cbgallery.element.removeData( 'cbgallery' );
			cbgallery.element.triggerHandler( 'cbgallery.destroyed', [cbgallery] );

			return true;
		},
		instances: function() {
			return instances;
		}
	};

	$.widget( 'blueimp.fileupload', $.blueimp.fileupload, {
		processActions: {
			clientSideResize: function ( data, options ) {
				var $that = this;
				var deferred = $.Deferred();
				var file = data.files[data.index];
				var cbgallery = options.cbgallery;

				if ( ( ! window.FileReader ) || ( ! window.Blob ) || ( ! cbgallery.settings.clientResize ) || ( ! file.type.match( /image/ ) ) ) {
					deferred.resolveWith( $that, [data] );
				} else {
					var reader = new FileReader();
					var maxWidth = cbgallery.settings.maxWidth;
					var maxHeight = cbgallery.settings.maxHeight;
					var aspectRatio = cbgallery.settings.aspectRatio;

					reader.mimeType = file.type;

					reader.onload = function( readerEvent ) {
						var image = new Image();

						image.mimeType = this.mimeType;

						image.onload = function( imageEvent ) {
							var originalWidth = image.width;
							var originalHeight = image.height;
							var mimeType = image.mimeType;

							var width = originalWidth;

							if ( ! maxWidth ) {
								maxWidth = width;
							}

							var height = originalHeight;

							if ( ! maxHeight ) {
								maxHeight = height;
							}

							var dx = 0;
							var dy = 0;

							if ( ! aspectRatio ) {
								width = maxWidth;
								height = maxHeight;
							} else if ( aspectRatio == 1 ) {
								if ( width > maxWidth ) {
									height = ( height * ( maxWidth / width ) );
									width = maxWidth;
								}

								if ( height > maxHeight ) {
									width = ( width * ( maxHeight / height ) );
									height = maxHeight;
								}
							} else if ( aspectRatio == 2 ) {
								if ( ( maxWidth > maxHeight ) || ( ( maxHeight == maxWidth ) && ( height > width ) ) ) {
									height = ( height * ( maxWidth / width ) );
									width = maxWidth;
									dy = ( ( maxHeight / 2 ) - ( height / 2 ) );
								} else if ( ( maxHeight > maxWidth ) || ( ( maxHeight == maxWidth ) && ( width > height ) ) ) {
									width = ( width * ( maxHeight / height ) );
									height = maxHeight;
									dx = ( ( maxWidth / 2 ) - ( width / 2 ) );
								} else if ( width == height ) {
									width = maxWidth;
									height = maxHeight;
								}
							}

							var cropOrientation = null;

							if ( height > width ) {
								cropOrientation = 'portrait';

								if ( height <= maxHeight ) {
									cropOrientation = 'landscape';
								}
							} else if ( width > height ) {
								cropOrientation = 'landscape';

								if ( width <= maxWidth ) {
									cropOrientation = 'portrait';
								}
							} else {
								cropOrientation = 'square';
							}

							if ( ( originalWidth == width ) && ( originalHeight == height ) && ( aspectRatio != 3 ) ) {
								// No change in size so lets abort resizing:
								deferred.resolveWith( $that, [data] );
							} else {
								var canvas = document.createElement( 'canvas' );

								if ( aspectRatio == 2 ) {
									canvas.width = maxWidth;
									canvas.height = maxHeight;

									if ( cropOrientation == 'square' ) {
										dx = 0;
										dy = 0;
									}
								} else {
									canvas.width = width;
									canvas.height = height;
								}

								var context = canvas.getContext( '2d' );

								context.mozImageSmoothingEnabled = false;
								context.webkitImageSmoothingEnabled = false;
								context.msImageSmoothingEnabled = false;
								context.imageSmoothingEnabled = false;

								context.drawImage( image, dx, dy, width, height );

								var blobRequest = new XMLHttpRequest();

								blobRequest.open( 'GET', canvas.toDataURL( mimeType ), true );
								blobRequest.responseType = 'arraybuffer';

								blobRequest.onload = function() {
									if ( this.response ) {
										var newFile = new Blob( [this.response], {type: file.type} );

										newFile.name = file.name;
										newFile.context = file.context;
										newFile.error = file.error;

										data.files[data.index] = newFile;
									}

									deferred.resolveWith( $that, [data] );
								};

								blobRequest.onerror = function() {
									deferred.resolveWith( $that, [data] );
								};

								blobRequest.send();
							}
						};

						image.src = readerEvent.target.result;
					};

					reader.onerror = function() {
						deferred.resolveWith( $that, [data] );
					};

					reader.readAsDataURL( file );
				}

				return deferred.promise();
			}
		}
	});

	function parseHeaders( cbgallery ) {
		var element = ( this.jquery ? this : $( this ) );
		var headers = element.find( '.galleryHeaders' );

		if ( ! headers.length ) {
			headers = element.filter( '.galleryHeaders' );
		}

		if ( ! headers.length ) {
			return [];
		}

		var head = $( 'head' );
		var loadedCSS = [];
		var loadedScripts = [];

		if ( cbgallery.settings.mode == 'modal' ) {
			$( 'body > .galleryHeadersScripts' ).remove();
		}

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

	function parseScripts( cbgallery, loadScripts ) {
		if ( ! loadScripts.length ) {
			return;
		}

		var element = ( this.jquery ? this : $( this ) );
		var scripts = $( '<div class="galleryHeadersScripts" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;" />' );

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

		if ( cbgallery.settings.mode == 'modal' ) {
			$( 'body' ).append( scripts );
		} else {
			var headers = element.find( '.galleryHeaders' );

			if ( ! headers.length ) {
				headers = element.filter( '.galleryHeaders' );
			}

			headers.append( scripts );
		}
	}

	$.fn.cbgallery = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbgallery.defaults = {
		init: true,
		useData: true,
		mode: 'modal',
		clientResize: true,
		maxWidth: 0,
		maxHeight: 0,
		aspectRatio: 1,
		url: null,
		preload: null,
		previous: null,
		next: null,
		request: null,
		callback: {
			upload: {
				add: null,
				error: null
			},
			link: {
				error: null
			},
			delete: {
				error: null
			},
			edit: {
				error: null
			}
		}
	};
})(jQuery);