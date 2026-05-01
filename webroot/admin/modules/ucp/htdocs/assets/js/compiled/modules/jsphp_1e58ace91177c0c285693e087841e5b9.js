var CallforwardC = UCPMC.extend({
	init: function(){
		this.stopPropagation = {
			'CFU': {},
			'CFB': {},
			'CF': {},
			'ringtimer': {}
		};
	},
	prepoll: function() {
		var exts = [];
		$(".grid-stack-item[data-rawname=callforward]").each(function() {
			exts.push($(this).data("widget_type_id"));
		});
		return exts;
	},
	poll: function(data) {
		var self = this;
		$.each(data.states, function(extension,data) {
			$.each(data, function(type,number) {
				var state = (number !== false);
				if(typeof self.stopPropagation[type][extension] !== "undefined" && self.stopPropagation[type][extension]) {
					return true;
				}
				var widget = $(".grid-stack-item[data-rawname=callforward][data-widget_type_id='"+extension+"']:visible input[data-type='"+type+"']"),
					sidebar = $(".widget-extra-menu[data-module=callforward][data-widget_type_id='"+extension+"']:visible input[data-type='"+type+"']"),
					sstate = state ? "on" : "off";
				if(widget.length && (widget.is(":checked") !== state)) {
					self.stopPropagation[type][extension] = true;
					widget.bootstrapToggle(sstate);
					if(state) {
						widget.parents(".parent").find(".display").removeClass("hidden").find(".text").text(number);
					} else {
						widget.parents(".parent").find(".display").addClass("hidden").find(".text").text("");
					}
					self.stopPropagation[type][extension] = false;
				}
				if(sidebar.length && (sidebar.is(":checked") !== state)) {
					self.stopPropagation[type][extension] = true;
					sidebar.bootstrapToggle(sstate);
					if(state) {
						sidebar.parents(".parent").find(".display").removeClass("hidden").find(".text").text(number);
					} else {
						sidebar.parents(".parent").find(".display").addClass("hidden").find(".text").text("");
					}
					self.stopPropagation[type][extension] = false;
				}
			});
		});
	},
	displayWidget: function(widget_id,dashboard_id) {
		var self = this;
		$(".grid-stack-item[data-id='"+widget_id+"'][data-rawname=callforward] .widget-content input[type='checkbox']").change(function(e) {
			var name = $(this).prop("name"),
				nice = $(this).data("nice"),
				parent = $(this).parents("."+name),
				type = $(this).data("type"),
				checked = $(this).is(':checked'),
				extension = $(".grid-stack-item[data-id='"+widget_id+"']").data("widget_type_id"),
				widget = $(this),
				sidebar = $(".widget-extra-menu[data-module='callforward'][data-widget_type_id='"+extension+"']:visible input[data-type='"+type+"']");

			if(typeof self.stopPropagation[type][extension] !== "undefined" && self.stopPropagation[type][extension]) {
				return true;
			}

			if(!$(this).is(":checked")) {
				if(sidebar.length && sidebar.is(":checked")) {
					sidebar.bootstrapToggle('off');
					sidebar.parents("."+name).find(".display").addClass("hidden").find(".text").text("");
				}
				self.saveSettings(extension,type,"",function() {
					parent.find(".display").addClass("hidden").find(".text").text("");
				});
				return;
			}

			if(sidebar.length && !sidebar.is(":checked")) {
				sidebar.bootstrapToggle('on');
			}
			self.showDialog(this, extension, function(state, number) {
				if(state == 'off') {
					widget.bootstrapToggle('off');
					parent.find(".display").addClass("hidden").find(".text").text("");
					if(sidebar.length && sidebar.is(":checked")) {
						sidebar.bootstrapToggle('off');
						sidebar.parents("."+name).find(".display").addClass("hidden").find(".text").text("");
					}
				} else {
					if(sidebar.length) {
						sidebar.parents("."+name).find(".display").removeClass("hidden").find(".text").text(number);
					}
					parent.find(".display").removeClass("hidden").find(".text").text(number);
				}
			});
		});
	},
	showDialog: function(el, extension, callback) {
		var nice = $(el).data("nice"),
				type = $(el).data("type"),
				self = this;

		self.stopPropagation[type][extension] = true;
		UCP.showDialog(
			sprintf(_("Set Forwarding for %s"),nice),
			'<label for="cfnumber">'+_("Enter a number")+'</label><input id="cfnumber" name="cfnumber" class="form-control">',
			'<button class="btn btn-primary" id="cfsave">'+_("Save")+'</button>',
			function() {
				var value = '';
				$("#globalModal").one("hide.bs.modal", function() {
					self.stopPropagation[type][extension] = false;
					if(value === '') {
						callback('off','');
					}
				});
				$("#cfsave").click(function(e) {
					e.preventDefault();
					value = $("#cfnumber").val();
					if(value === "") {
						UCP.showAlert(_("A valid number needs to be entered"),"warning");
						return;
					}

					callback('on',value);
					self.stopPropagation[type][extension] = false;
					self.saveSettings(extension, type, value, function(data) {
						if(data.status) {
							UCP.closeDialog();
						} else {
							callback('off','');
							UCP.showAlert(data.message, 'danger');
						}
					});
				});
			}
		);
	},
	saveSettings: function(extension, type, value, callback) {
		var self = this;
		data = {
			ext: extension,
			type: type,
			module: "callforward",
			command: "settings"
		};
		if(value !== "") {
			data.value = value;
		}
		self.stopPropagation[type][extension] = true;
		$.post( UCP.ajaxUrl, data, callback).always(function() {
			self.stopPropagation[type][extension] = false;
		}).fail(function() {
			UCP.showAlert(_('An Unknown error occured'),'danger');
		});
	},
	displayWidgetSettings: function(widget_id,dashboard_id) {
		var self = this,
				extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");
		$("#cfringtimer").change(function() {
			self.saveSettings(extension, 'ringtimer', $(this).val(), function() {
				console.log("saved!");
			});
		});
	},
	displaySimpleWidget: function(widget_id) {
		var self = this;
		$(".widget-extra-menu[data-id='"+widget_id+"'] input[type='checkbox']").change(function(e) {
			var type = $(this).data("type"),
					checked = $(this).is(':checked'),
					extension = $(".widget-extra-menu[data-id='"+widget_id+"']").data("widget_type_id"),
					name = $(this).prop("name"),
					parent = $(this).parents("."+name),
					el = $(".grid-stack-item[data-widget_type_id='"+extension+"'][data-rawname=callforward] .widget-content input[data-type='"+type+"']");

			if(typeof self.stopPropagation[type][extension] !== "undefined" && self.stopPropagation[type][extension]) {
				return true;
			}

			if(!checked) {
				parent.find(".display").addClass("hidden").find(".text").text("");
			}

			if(el.length) {
				if(el.is(":checked") !== checked) {
					var state = checked ? "on" : "off";
					el.bootstrapToggle(state);
				}
			} else {
				if(checked) {
					self.showDialog(this, extension, function(state, number) {
						if(state == 'on') {
							parent.find(".display").removeClass("hidden").find(".text").text(number);
						} else {
							el.bootstrapToggle('off');
							parent.find(".display").addClass("hidden").find(".text").text("");
						}
					});
				} else {
					self.saveSettings(extension, type, '', function(data) {
						if(!data.status) {
							UCP.showAlert(data.message, 'danger');
						}
					});
				}
			}
		});
	},
	displaySimpleWidgetSettings: function(widget_id) {
		this.displayWidgetSettings(widget_id);
	}
});

var CallwaitingC = UCPMC.extend({
	init: function(){
		this.stopPropagation = {};
	},
	prepoll: function() {
		var exts = [];
		$(".grid-stack-item[data-rawname=callwaiting]").each(function() {
			exts.push($(this).data("widget_type_id"));
		});
		return exts;
	},
	poll: function(data) {
		var self = this;
		$.each(data.states, function(ext,state) {
			if(typeof self.stopPropagation[ext] !== "undefined" && self.stopPropagation[ext]) {
				return true;
			}
			var widget = $(".grid-stack-item[data-rawname=callwaiting][data-widget_type_id='"+ext+"']:visible input[name='cwenable']"),
				sidebar = $(".widget-extra-menu[data-module='callwaiting'][data-widget_type_id='"+ext+"']:visible input[name='cwenable']"),
				sstate = state ? "on" : "off";
			if(widget.length && (widget.is(":checked") !== state)) {
				self.stopPropagation[extension] = true;
				widget.bootstrapToggle(sstate);
				self.stopPropagation[extension] = false;
			} else if(sidebar.length && (sidebar.is(":checked") !== state)) {
				self.stopPropagation[extension] = true;
				sidebar.bootstrapToggle(sstate);
				self.stopPropagation[extension] = false;
			}
		});
	},
	displayWidget: function(widget_id,dashboard_id) {
		var self = this;
		$(".grid-stack-item[data-id='"+widget_id+"'][data-rawname=callwaiting] .widget-content input[name='cwenable']").change(function() {
			var extension = $(".grid-stack-item[data-id='"+widget_id+"'][data-rawname=callwaiting]").data("widget_type_id"),
				el = $(".widget-extra-menu[data-module='callwaiting'][data-widget_type_id='"+extension+"']:visible input[name='cwenable']"),
				checked = $(this).is(':checked'),
				name = $(this).prop('name');
			if(el.length && el.is(":checked") !== checked) {
				var state = checked ? "on" : "off";
				el.bootstrapToggle(state);
			}
			self.saveSettings(extension, {enable: checked});
		});
	},
	saveSettings: function(extension, data, callback) {
		var self = this;
		data.ext = extension;
		data.module = "Callwaiting";
		data.command = "enable";
		this.stopPropagation[extension] = true;
		$.post( UCP.ajaxUrl, data, callback).always(function() {
			self.stopPropagation[extension] = false;
		});
	},
	displaySimpleWidget: function(widget_id) {
		var self = this;
		$(".widget-extra-menu[data-id='"+widget_id+"'] input[name='cwenable']").change(function(e) {
			var extension = $(".widget-extra-menu[data-id='"+widget_id+"']").data("widget_type_id"),
				checked = $(this).is(':checked'),
				name = $(this).prop('name'),
				el = $(".grid-stack-item[data-rawname=callwaiting][data-widget_type_id='"+extension+"']:visible input[name='cwenable']");

			if(el.length) {
				if(el.is(":checked") !== checked) {
					var state = checked ? "on" : "off";
					el.bootstrapToggle(state);
				}
			} else {
				self.saveSettings(extension, {enable: checked});
			}
		});
	}
});

var CdrC = UCPMC.extend({
	init: function() {
		this.playing = null;
	},
	resize: function(widget_id) {
		$(".grid-stack-item[data-id='"+widget_id+"'] .cdr-grid").bootstrapTable('resetView',{height: $(".grid-stack-item[data-id='"+widget_id+"'] .widget-content").height()-1});
	},
	poll: function(data, url) {

	},
	displayWidget: function(widget_id, dashboard_id) {
		var self = this,
				extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");

		$(".grid-stack-item[data-id='"+widget_id+"'] .cdr-grid").one("post-body.bs.table", function() {
			setTimeout(function() {
				self.resize(widget_id);
			},250);
		});

		$('.grid-stack-item[data-id='+widget_id+'] .cdr-grid').on("post-body.bs.table", function () {
			self.bindPlayers(widget_id);
			$(".cdr-grid .clickable").click(function(e) {
				var text = $(this).text();
				if (UCP.validMethod("Contactmanager", "showActionDialog")) {
					UCP.Modules.Contactmanager.showActionDialog("number", text, "phone");
				}
			});
		});
	},
	formatDescription: function (value, row, index) {
		var icons = '';
		if(typeof row.icons !== "undefined") {
			$.each(row.icons, function(i, v) {
				icons += '<i class="fa '+v+'"></i> ';
			});
		}
		return icons + " " + value;
	},
	formatActions: function (value, row, index) {
		var settings = UCP.Modules.Cdr.staticsettings;
		if(row.recordingfile === '' || settings.showDownload === "0") {
			return '';
		}
		var link = '<a class="download" alt="'+_("Download")+'" href="'+UCP.ajaxUrl+'?module=cdr&amp;command=download&amp;msgid='+row.uniqueid+'&amp;type=download&amp;ext='+row.requestingExtension+'"><i class="fa fa-cloud-download"></i></a>';
		return link;
	},
	formatPlayback: function (value, row, index) {
		var settings = UCP.Modules.Cdr.staticsettings,
				rand = Math.floor(Math.random() * 10000);
		if(row.recordingfile.length === 0 || settings.showPlayback === "0") {
			return '';
		}
		return '<div id="jquery_jplayer_'+row.niceUniqueid+'-'+rand+'" class="jp-jplayer" data-container="#jp_container_'+row.niceUniqueid+'-'+rand+'" data-id="'+row.uniqueid+'"></div><div id="jp_container_'+row.niceUniqueid+'-'+rand+'" data-player="jquery_jplayer_'+row.niceUniqueid+'-'+rand+'" class="jp-audio-freepbx" role="application" aria-label="media player">'+
			'<div class="jp-type-single">'+
				'<div class="jp-gui jp-interface">'+
					'<div class="jp-controls">'+
						'<i class="fa fa-play jp-play"></i>'+
						'<i class="fa fa-undo jp-restart"></i>'+
					'</div>'+
					'<div class="jp-progress">'+
						'<div class="jp-seek-bar progress">'+
							'<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>'+
							'<div class="progress-bar progress-bar-striped active" style="width: 100%;"></div>'+
							'<div class="jp-play-bar progress-bar"></div>'+
							'<div class="jp-play-bar">'+
								'<div class="jp-ball"></div>'+
							'</div>'+
							'<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>'+
						'</div>'+
					'</div>'+
					'<div class="jp-volume-controls">'+
						'<i class="fa fa-volume-up jp-mute"></i>'+
						'<i class="fa fa-volume-off jp-unmute"></i>'+
					'</div>'+
				'</div>'+
				'<div class="jp-no-solution">'+
					'<span>Update Required</span>'+
					sprintf(_("You are missing support for playback in this browser. To fully support HTML5 browser playback you will need to install programs that can not be distributed with the PBX. If you'd like to install the binaries needed for these conversions click <a href='%s'>here</a>"),"http://wiki.freepbx.org/display/FOP/Installing+Media+Conversion+Libraries")+
				'</div>'+
			'</div>'+
		'</div>';
	},
	formatDuration: function (value, row, index) {
		return row.niceDuration;
	},
	formatDate: function(value, row, index) {
		return UCP.dateTimeFormatter(value);
	},
	bindPlayers: function(widget_id) {
		var extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");
		$(".grid-stack-item[data-id="+widget_id+"] .jp-jplayer").each(function() {
			var container = $(this).data("container"),
					player = $(this),
					id = $(this).data("id");
			$(this).jPlayer({
				ready: function() {
					$(container + " .jp-play").click(function() {
						if($(this).parents(".jp-controls").hasClass("recording")) {
							var type = $(this).parents(".jp-audio-freepbx").data("type");
							$this.recordGreeting(type);
							return;
						}
						if(!player.data("jPlayer").status.srcSet) {
							$(container).addClass("jp-state-loading");
							$.ajax({
								type: 'POST',
								url: "index.php?quietmode=1",
								data: {module: "cdr", command: "gethtml5", id: id, ext: extension},
								dataType: 'json',
								timeout: 30000,
								success: function(data) {
									if(data.status) {
										player.on($.jPlayer.event.error, function(event) {
											$(container).removeClass("jp-state-loading");
											console.log(event);
										});
										player.one($.jPlayer.event.canplay, function(event) {
											$(container).removeClass("jp-state-loading");
											player.jPlayer("play");
										});
										player.jPlayer( "setMedia", data.files);
									} else {
										alert(data.message);
										$(container).removeClass("jp-state-loading");
									}
								}
							});
						}
					});
					var $this = this;
					$(container).find(".jp-restart").click(function() {
						if($($this).data("jPlayer").status.paused) {
							$($this).jPlayer("pause",0);
						} else {
							$($this).jPlayer("play",0);
						}
					});
				},
				timeupdate: function(event) {
					$(container).find(".jp-ball").css("left",event.jPlayer.status.currentPercentAbsolute + "%");
				},
				ended: function(event) {
					$(container).find(".jp-ball").css("left","0%");
				},
				swfPath: "/js",
				supplied: UCP.Modules.Cdr.staticsettings.supportedHTML5,
				cssSelectorAncestor: container,
				wmode: "window",
				useStateClassSkin: true,
				remainingDuration: true,
				toggleDuration: true
			});
			$(this).on($.jPlayer.event.play, function(event) {
				$(this).jPlayer("pauseOthers");
			});
		});

		var acontainer = null;
		$('.jp-play-bar').mousedown(function (e) {
			acontainer = $(this).parents(".jp-audio-freepbx");
			updatebar(e.pageX);
		});
		$(document).mouseup(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
				acontainer = null;
			}
		});
		$(document).mousemove(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
			}
		});

		//update Progress Bar control
		var updatebar = function (x) {
			var player = $("#" + acontainer.data("player")),
					progress = acontainer.find('.jp-progress'),
					maxduration = player.data("jPlayer").status.duration,
					position = x - progress.offset().left,
					percentage = 100 * position / progress.width();

			//Check within range
			if (percentage > 100) {
				percentage = 100;
			}
			if (percentage < 0) {
				percentage = 0;
			}

			player.jPlayer("playHead", percentage);

			//Update progress bar and video currenttime
			acontainer.find('.jp-ball').css('left', percentage+'%');
			acontainer.find('.jp-play-bar').css('width', percentage + '%');
			player.jPlayer.currentTime = maxduration * percentage / 100;
		};
	}
});

var CelC = UCPMC.extend({
	init: function() {
	},
	poll: function(data, url) {
	},
	resize: function(widget_id) {
		$(".grid-stack-item[data-id='"+widget_id+"'] .cel-grid").bootstrapTable('resetView',{height: $(".grid-stack-item[data-id='"+widget_id+"'] .widget-content").height()-1});
	},
	displayWidget: function(widget_id) {
		var self = this,
				extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");

		$(".grid-stack-item[data-id='"+widget_id+"'] .cel-grid").one("post-body.bs.table", function() {
			setTimeout(function() {
				self.resize(widget_id);
			},250);
		});

		$(".grid-stack-item[data-id='"+widget_id+"'] .cel-grid").on("post-body.bs.table", function() {
			self.bindPlayers(widget_id);
		});
		$(".grid-stack-item[data-id='"+widget_id+"'] .cel-grid").on("click-cell.bs.table", function(event, field, value, row) {
			if(field == "file" || field == "controls") {
				return;
			}

			$.getJSON(UCP.ajaxUrl+'?module=cel&command=eventmodal', function(data){
				if (data.status === true){
					UCP.showDialog(_("Call Events"),
						data.message,
						'<button type="button" class="btn btn-primary" data-dismiss="modal">'+_("Close")+'</button>',
						function() {
							$("#globalModal .cel-detail-grid").bootstrapTable();
							$("#globalModal .cel-detail-grid").bootstrapTable('load', row.moreinfo);
						}
					);
				} else {
					UCP.showAlert(_("Error getting form"),'danger');
				}
			}).always(function() {
			}).fail(function() {
				UCP.showAlert(_("Error getting form"),'danger');
			});
		});
	},
	formatDuration: function (value, row, index) {
		return sprintf(_("%s seconds"),value);
	},
	formatDate: function(value, row, index) {
		return UCP.dateTimeFormatter(value);
	},
	formatControls: function (value, row, index) {
		var settings = UCP.Modules.Cel.staticsettings;
		if(typeof row.file === "undefined" || settings.showDownload === "0") {
			return '';
		}
		var links = '';
		links = '<a class="download" alt="'+_("Download")+'" href="'+UCP.ajaxUrl+'?module=cel&amp;command=download&amp;id='+encodeURIComponent(row.uniqueid)+'&amp;type=download"><i class="fa fa-cloud-download"></i></a>';
		return links;
	},
	formatPlayback: function (value, row, index) {
		var settings = UCP.Modules.Cel.staticsettings,
				rand = Math.floor(Math.random() * 10000);

		if(typeof row.file === "undefined" || settings.showPlayback === "0") {
			return '';
		}

		var recordings = [row.file];

		var html = '',
			count = 0;
		$.each(recordings, function(k, v){
			if(v === false) {
				return true;
			}
			html += '<div id="jquery_jplayer_'+index+'_'+count+'-'+rand+'" class="jp-jplayer" data-container="#jp_container_'+index+'_'+count+'-'+rand+'" data-playbackuniqueid="'+row.uniqueid+'" data-id="'+k+'"></div>'+
			'<div id="jp_container_'+index+'_'+count+'-'+rand+'" data-player="jquery_jplayer_'+index+'_'+count+'-'+rand+'" class="jp-audio-freepbx" role="application" aria-label="media player">'+
				'<div class="jp-type-single">'+
				'<div class="jp-gui jp-interface">'+
					'<div class="jp-controls">'+
						'<i class="fa fa-play jp-play"></i>'+
						'<i class="fa fa-undo jp-restart"></i>'+
					'</div>'+
					'<div class="jp-progress">'+
						'<div class="jp-seek-bar progress">'+
							'<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>'+
							'<div class="progress-bar progress-bar-striped active" style="width: 100%;"></div>'+
							'<div class="jp-play-bar progress-bar"></div>'+
							'<div class="jp-play-bar">'+
								'<div class="jp-ball"></div>'+
							'</div>'+
							'<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>'+
						'</div>'+
					'</div>'+
					'<div class="jp-volume-controls">'+
						'<i class="fa fa-volume-up jp-mute"></i>'+
						'<i class="fa fa-volume-off jp-unmute"></i>'+
					'</div>'+
				'</div>'+
				'<div class="jp-no-solution">'+
					'<span>Update Required</span>'+
					sprintf(_("You are missing support for playback in this browser. To fully support HTML5 browser playback you will need to install programs that can not be distributed with the PBX. If you'd like to install the binaries needed for these conversions click <a href='%s'>here</a>"),"http://wiki.freepbx.org/display/FOP/Installing+Media+Conversion+Libraries")+
				'</div>'+
			'</div>';
});
		return html;
	},
	bindPlayers: function(widget_id) {
		$(".grid-stack-item[data-id='"+widget_id+"'] .jp-jplayer").each(function() {
			var container = $(this).data("container"),
					player = $(this),
					playback = $(this).data("playbackuniqueid");

			$(this).jPlayer({
				ready: function() {
					$(container + " .jp-play").click(function() {
						if($(this).parents(".jp-controls").hasClass("recording")) {
							var type = $(this).parents(".jp-audio-freepbx").data("type");
							$this.recordGreeting(type);
							return;
						}
						if(!player.data("jPlayer").status.srcSet) {
							$(container).addClass("jp-state-loading");
							$.ajax({
								type: 'POST',
								url: "ajax.php",
								data: {module: "cel", command: "gethtml5", uniqueid: playback, ext: extension},
								dataType: 'json',
								timeout: 30000,
								success: function(data) {
									if(data.status) {
										player.on($.jPlayer.event.error, function(event) {
											$(container).removeClass("jp-state-loading");
											console.log(event);
										});
										player.one($.jPlayer.event.canplay, function(event) {
											$(container).removeClass("jp-state-loading");
											player.jPlayer("play");
										});
										player.jPlayer( "setMedia", data.files);
									} else {
										alert(data.message);
										$(container).removeClass("jp-state-loading");
									}
								}
							});
						}
					});
					var $this = this;
					$(container).find(".jp-restart").click(function() {
						if($($this).data("jPlayer").status.paused) {
							$($this).jPlayer("pause",0);
						} else {
							$($this).jPlayer("play",0);
						}
					});
				},
				timeupdate: function(event) {
					$(container).find(".jp-ball").css("left",event.jPlayer.status.currentPercentAbsolute + "%");
				},
				ended: function(event) {
					$(container).find(".jp-ball").css("left","0%");
				},
				swfPath: "/js",
				supplied: UCP.Modules.Cel.staticsettings.supportedHTML5,
				cssSelectorAncestor: container,
				wmode: "window",
				useStateClassSkin: true,
				remainingDuration: true,
				toggleDuration: true
			});
			$(this).on($.jPlayer.event.play, function(event) {
				$(this).jPlayer("pauseOthers");
			});
		});

		var acontainer = null;
		$(".grid-stack-item[data-id='"+widget_id+"'] .jp-play-bar").mousedown(function (e) {
			acontainer = $(this).parents(".jp-audio-freepbx");
			updatebar(e.pageX);
		});
		$(document).mouseup(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
				acontainer = null;
			}
		});
		$(document).mousemove(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
			}
		});

		//update Progress Bar control
		var updatebar = function (x) {
			var player = $("#" + acontainer.data("player")),
					progress = acontainer.find('.jp-progress'),
					maxduration = player.data("jPlayer").status.duration,
					position = x - progress.offset().left,
					percentage = 100 * position / progress.width();

			//Check within range
			if (percentage > 100) {
				percentage = 100;
			}
			if (percentage < 0) {
				percentage = 0;
			}

			player.jPlayer("playHead", percentage);

			//Update progress bar and video currenttime
			acontainer.find('.jp-ball').css('left', percentage+'%');
			acontainer.find('.jp-play-bar').css('width', percentage + '%');
			player.jPlayer.currentTime = maxduration * percentage / 100;
		};
	}
});

var DonotdisturbC = UCPMC.extend({
	init: function(){
		this.stopPropagation = {};
	},
	prepoll: function() {
		var exts = [];
		$(".grid-stack-item[data-rawname=donotdisturb]").each(function() {
			exts.push($(this).data("widget_type_id"));
		});
		return exts;
	},
	poll: function(data) {
		var self = this;
		$.each(data.states, function(ext,state) {
			if(typeof self.stopPropagation[ext] !== "undefined" && self.stopPropagation[ext]) {
				return true;
			}
			var widget = $(".grid-stack-item[data-rawname=donotdisturb][data-widget_type_id='"+ext+"']:visible input[name='dndenable']"),
				sidebar = $(".widget-extra-menu[data-module='donotdisturb'][data-widget_type_id='"+ext+"']:visible input[name='dndenable']"),
				sstate = state ? "on" : "off";
			if(widget.length && (widget.is(":checked") !== state)) {
				self.stopPropagation[ext] = true;
				widget.bootstrapToggle(sstate);
				self.stopPropagation[ext] = false;
			} else if(sidebar.length && (sidebar.is(":checked") !== state)) {
				self.stopPropagation[ext] = true;
				sidebar.bootstrapToggle(sstate);
				self.stopPropagation[ext] = false;
			}
		});
	},
	displayWidget: function(widget_id,dashboard_id) {
		var self = this;
		$(".grid-stack-item[data-id='"+widget_id+"'][data-rawname=donotdisturb] .widget-content input[name='dndenable']").change(function() {
			var extension = $(".grid-stack-item[data-id='"+widget_id+"'][data-rawname=donotdisturb]").data("widget_type_id"),
				sidebar = $(".widget-extra-menu[data-module='donotdisturb'][data-widget_type_id='"+extension+"']:visible input[name='dndenable']"),
				checked = $(this).is(':checked'),
				name = $(this).prop('name');
			if(sidebar.length && sidebar.is(":checked") !== checked) {
				var state = checked ? "on" : "off";
				sidebar.bootstrapToggle(state);
			}
			self.saveSettings(extension, {enable: checked});
		});
	},
	saveSettings: function(extension, data, callback) {
		var self = this;
		data.ext = extension;
		data.module = "donotdisturb";
		data.command = "enable";
		this.stopPropagation[extension] = true;
		$.post( UCP.ajaxUrl, data, callback).always(function() {
			self.stopPropagation[extension] = false;
		});
	},
	displaySimpleWidget: function(widget_id) {
		var self = this;
		$(".widget-extra-menu[data-id='"+widget_id+"'] input[name='dndenable']").change(function(e) {
			var extension = $(".widget-extra-menu[data-id='"+widget_id+"']").data("widget_type_id"),
				checked = $(this).is(':checked'),
				name = $(this).prop('name'),
				el = $(".grid-stack-item[data-rawname=donotdisturb][data-widget_type_id='"+extension+"']:visible input[name='dndenable']");

			if(el.length) {
				if(el.is(":checked") !== checked) {
					var state = checked ? "on" : "off";
					el.bootstrapToggle(state);
				}
			} else {
				self.saveSettings(extension, {enable: checked});
			}
		});
	}
});

var HomeC = UCPMC.extend({
	init: function() {
		this.packery = false;
		this.doit = null;
	},
	poll: function(data) {
		//console.log(data)
	},
	display: function(event) {
		$(window).on("resize.Home", this.resize);
		this.resize();
	},
	hide: function(event) {
		$(window).off("resize.Home");
		//$(".masonry-container").packery("destroy");
		this.packery = false;
	},
	contactClickOptions: function(type) {
		if (type != "number" || !UCP.Modules.Home.staticsettings.enableOriginate) {
			return false;
		}
		return [ { text: _("Originate Call"), function: "contactClickInitiate", type: "phone" } ];
	},
	contactClickInitiate: function(did) {
		var Webrtc = this,
				sfrom = "",
				temp = "",
				name = did,
				selected = "";
		if (UCP.validMethod("Contactmanager", "lookup")) {
			if (typeof UCP.Modules.Contactmanager.lookup(did).displayname !== "undefined") {
				name = UCP.Modules.Contactmanager.lookup(did).displayname;
			} else {
				temp = String(did).length == 11 ? String(did).substring(1) : did;
				if (typeof UCP.Modules.Contactmanager.lookup(temp).displayname !== "undefined") {
					name = UCP.Modules.Contactmanager.lookup(temp).displayname;
				}
			}
		}
		$.each(UCP.Modules.Home.staticsettings.extensions, function(i, v) {
			sfrom = sfrom + "<option>" + v + "</option>";
		});

		selected = "<option value=\"" + did + "\" selected>" + name + "</option>";
			UCP.showDialog(_("Originate Call"),
			"<label for=\"originateFrom\">From:</label><select id=\"originateFrom\" class=\"form-control\">" + sfrom + "</select><label for=\"originateTo\">To:</label><select class=\"form-control\" id=\"originateTo\" data-toggle=\"select\" data-size=\"auto\">" + selected + "</select>",
			"<button class=\"btn btn-primary text-center\" id=\"originateCall\" style=\"margin-left: 72px;\">" + _("Originate") + "</button>",
			function() {
				$("#originateCall").click(function() {
					setTimeout(function() {
						UCP.Modules.Home.originate();
					}, 50);
				});
				$("#originateTo").keypress(function(event) {
					if (event.keyCode == 13) {
						setTimeout(function() {
							UCP.Modules.Home.originate();
						}, 50);
					}
				});
			}
		);
	},
	refresh: function(module, id) {
		$("#"  +  module  +  "-title-"  +  id + " i.fa-refresh").addClass("fa-spin");
		$.post( "?quietmode=1&module=" + module + "&command=homeRefresh&id=" + id, {}, function( data ) {
			$("#" + module + "-title-" + id + " i.fa-refresh").removeClass("fa-spin");
			$("#" + module + "-content-" + id).html(data.content);
		});
	},
	originate: function() {
		if ($("#originateTo").val() !== null && $("#originateTo").val()[0] === "") {
			alert(_("Nothing Entered"));
			return;
		}
		$.post( "index.php?quietmode=1&module=home&command=originate",
						{ from: $("#originateFrom").val(),
						to: $("#originateTo").val() },
						function( data ) {
							if (data.status) {
								UCP.closeDialog();
							}
						}
		)
		.fail(function(xhr, status, error) {
			alert(status +" "+ error);
		});
	},
	resize: function() {
		return;
		var wasPackeryEnabled = this.packery;
		this.packery = $(window).width() >= 768;
		if (this.packery !== wasPackeryEnabled) {
			if (this.packery) {
				clearTimeout(this.doit);
				this.doit = setTimeout(function() {
					$(".widget").css("width", "33.33%");
					$(".widget").css("margin-bottom", "");
					$(".masonry-container").packery({
						columnWidth: 40,
						gutter: 10,
						itemSelector: ".widget"
					});
				}, 100);
			} else {
				this.packery = false;
				$(".masonry-container").packery("destroy");
				$(".widget").css("width", "100%");
				$(".widget").css("margin-bottom", "10px");
			}
		} else if (!this.packery) {
			$(".widget").css("width", "100%");
			$(".widget").css("margin-bottom", "10px");
		}
	}
});

$(document).bind("logIn", function( event ) {
	$("#settings-menu a.originate").on("click", function() {
		var sfrom = "";
		$.each(UCP.Modules.Home.staticsettings.extensions, function(i, v) {
			sfrom = sfrom + "<option>" + v + "</option>";
		});

		UCP.showDialog(_("Originate Call"),
			"<label for=\"originateFrom\">From:</label> <select id=\"originateFrom\" class=\"form-control\">" + sfrom + "</select><label for=\"originateTo\">To:</label><select class=\"form-control Tokenize Fill\" id=\"originateTo\" multiple></select><button class=\"btn btn-default\" id=\"originateCall\" style=\"margin-left: 72px;\">" + _("Originate") + "</button>",
			200,
			250,
			function() {
				$("#originateTo").tokenize({ maxElements: 1, datas: "index.php?quietmode=1&module=home&command=contacts" });
				$("#originateCall").click(function() {
					setTimeout(function() {
						UCP.Modules.Home.originate();
					}, 50);
				});
				$("#originateTo").keypress(function(event) {
					if (event.keyCode == 13) {
						setTimeout(function() {
							UCP.Modules.Home.originate();
						}, 50);
					}
				});
			}
		);
	});
});

var SettingsC = UCPMC.extend({
	init: function() {
		this.language = language;
		this.timezone = timezone;
		this.datetimeformat = datetimeformat;
		this.timeformat = timeformat;
		this.dateformat = dateformat;
	},
	poll: function(data) {
		//console.log(data)
	},
	showMessage: function(message, type, timeout, html = false) {
		type = typeof type !== "undefined" ? type : "info";
		timeout = typeof timeout !== "undefined" ? timeout : 2000;
		if(html){
			$("#settings-message").removeClass().addClass("alert alert-"+type+" text-left").html(message);
		}
		else{
			$("#settings-message").removeClass().addClass("alert alert-"+type+" text-center").text(message);
		}
		
		setTimeout(function() {
			$("#settings-message").addClass("hidden");
		}, timeout);
	},
	updateTimeDisplay: function() {
		if(language === "") {
			language = this.language;
			Cookies.set("lang", language, { path: window.location.pathname.replace(/\/?$/,'') });
		}
		if(timezone === "") {
			timezone = this.timezone;
		}
		moment.locale(language);

		var userdtf = $("#datetimeformat").val();
		userdtf = (userdtf !== "") ? userdtf : datetimeformat;
		$("#datetimeformat-now").text(moment().tz(timezone).format(userdtf));

		var usertf = $("#timeformat").val();
		usertf = (usertf !== "") ? usertf : timeformat;
		$("#timeformat-now").text(moment().tz(timezone).format(usertf));

		var userdf = $("#dateformat").val();
		userdf = (userdf !== "") ? userdf : dateformat;
		$("#dateformat-now").text(moment().tz(timezone).format(userdf));
	},
	displaySimpleWidgetSettings: function(widget_id) {
		var $this = this;
		setInterval(function() {
			$this.updateTimeDisplay();
		},1000);
		$("#datetimeformat, #timeformat, #dateformat").keydown(function() {
			$this.updateTimeDisplay();
		});
		$("#browserlang").on("click", function(e){
			e.preventDefault();
			var bl =  browserLocale();
			bl = bl.replace("-","_");
			if(typeof bl === 'undefined'){
				UCP.showAlert(_("The Browser Language could not be determined"),"warning");
			}else{
				$("#lang").multiselect('select', bl);
				$("#lang").multiselect('refresh');
				$("#lang").trigger("onchange",[$("#lang option:selected"), $("#lang option:selected").is(":checked")]);
			}
		});
		$("#systemlang").on("click", function(e){
			e.preventDefault();
			var sl = UIDEFAULTLANG;
			if(typeof sl === 'undefined'){
				UCP.showAlert(_("The PBX Language is not set"),"warning");
			}else{
				$("#lang").multiselect('select', sl);
				$("#lang").multiselect('refresh');
				$("#lang").trigger("onchange",[$("#lang option:selected"), $("#lang option:selected").is(":checked")]);
			}
		});
		$("#browsertz").on("click", function(e){
			e.preventDefault();
			var btz =  moment.tz.guess();
			if(typeof btz === 'undefined'){
				UCP.showAlert(_("The Browser Timezone could not be determined"),"warning");
			}else{
				$("#timezone").multiselect('select', btz);
				$("#timezone").multiselect('refresh');
				$("#timezone").trigger("onchange",[$("#timezone option:selected"), $("#timezone option:selected").is(":checked")]);
			}
		});
		$("#systemtz").on("click", function(e){
			e.preventDefault();
			var stz = PHPTIMEZONE;
			if(typeof stz === 'undefined'){
				UCP.showAlert(_("The PBX Timezone is not set"),"warning");
			}else{
				$("#timezone").multiselect('select', stz);
				$("#timezone").multiselect('refresh');
				$("#timezone").trigger("onchange",[$("#timezone option:selected"), $("#timezone option:selected").is(":checked")]);
			}
		});
		$("#timezone").on("onchange", function(el, option, checked) {
			$.post( "ajax.php?module=Settings&command=settings", { key: "timezone", value: option.val() }, function( data ) {
				if(data.status) {
					timezone = option.val();
					$this.updateTimeDisplay();
					$this.showMessage(_("Success!"),"success");
					UCP.showConfirm(_("UCP needs to reload, ok?"), 'warning', function() {
						window.location.reload();
					});
				} else {
					$this.showMessage(data.message,"danger");
				}
			});
		});
		$("#lang").on("onchange", function(el, option, checked) {
			$.post( "ajax.php?module=Settings&command=settings", { key: "language", value: option.val() }, function( data ) {
				if(data.status) {
					language = option.val();
					$this.showMessage(_("Success!"),"success");
					$this.updateTimeDisplay();
					Cookies.set("lang", option.val(), { path: window.location.pathname.replace(/\/?$/,'') });
					UCP.showConfirm(_("UCP needs to reload, ok?"), 'warning', function() {
						window.location.reload();
					});
				} else {
					$this.showMessage(data.message,"danger");
				}
			});

		});
		if (Notify.isSupported()) {
			$("#ucp-settings .desktopnotifications-group").removeClass("hidden");
			$("#ucp-settings input[name=\"desktopnotifications\"]").prop("checked", UCP.notify);
			$("#ucp-settings input[name=\"desktopnotifications\"]").change(function() {
				if (!UCP.notify && $(this).is(":checked")) {
					Notify.requestPermission(function() {
						UCP.notificationsAllowed();
						$("#ucp-settings input[name=\"desktopnotifications\"]").prop("checked", true);
					}, function() {
						UCP.showAlert(_("Enabling notifications was denied"),"danger");
						UCP.notificationsDenied();
						$("#ucp-settings input[name=\"desktopnotifications\"]").prop("checked", false);
					});
				} else {
					UCP.notify = false;
				}
			});
		}

		var restartTour = false;
		$("#ucp-settings input[name=\"tour\"]").prop("checked", false);
		$("#ucp-settings input[name=\"tour\"]").change(function() {
			if($(this).is(":checked")) {
				restartTour = true;
			} else {
				restartTour = false;
			}
			$.post( UCP.ajaxUrl + "?module=ucptour&command=tour", { state: (restartTour ? 1 : 0) }, function( data ) {

			});
		});

		$("#widget_settings").one('hidden.bs.modal', function() {
			if(restartTour) {
				UCP.Modules.Ucptour.tour.restart();
			}
		});

		$("#update-pwd").click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			var password = $("#pwd").val(), confirm = $("#pwd-confirm").val();
			if (password !== "" && password != "******" && confirm !== "") {
				if (confirm != password) {
					$this.showMessage(_("Password Confirmation Didn't Match!"),"danger");
				} else {
					$.post( "ajax.php?module=Settings&command=settings", { key: "password", value: confirm }, function( data ) {
						if (data.status) {
							$this.showMessage(_("Saved!"),"success");
							UCP.showConfirm(_("UCP needs to reload, ok?"), 'warning', function() {
								window.location.reload();
							});
						} else {
							$this.showMessage(data.message,"warning", 3000,  true);

						}
					});
				}
			} else {
				$this.showMessage(_("Password has not changed!"));
			}
		});

		$("#username").blur(function() {
			new_user = $(this).val();
			if($(this).val() != $(this).data("prevusername")) {				
				UCP.showConfirm(_("Are you sure you wish to change your username? UCP will reload after"), 'warning', function() {
					$.post( "ajax.php?module=Settings&command=settings", { key: "username", value: new_user}, function( data ) {
						if(data.status) {
							$this.showMessage(_("Username has been changed, reloading"),"success");
							window.location.reload();
						} else {
							$this.showMessage(data.message,"danger");
						}
					});
				});
			}
		});
		$("#userinfo input[type!=checkbox][type!=radio][name!=dateformat][name!=timeformat][name!=datetimeformat]").blur(function() {
			$.post( "ajax.php?module=Settings&command=settings", { key: $(this).prop("name"), value: $(this).val() }, function( data ) {
				if (data.status) {
					$this.showMessage(_("Saved!"),"success");
				} else {
					$this.showMessage(data.message,"danger");
				}
				$(this).off("blur");
			});
		});
		$("#dateformat, #timeformat, #datetimeformat").blur(function() {
			var name = $(this).prop("name"),
					value = $(this).val();
			$.post( "ajax.php?module=Settings&command=settings", { key: name, value: value }, function( data ) {
				if (data.status) {
					if(value === "" && typeof $this[name] === "string") {
						window[name] = $this[name];
					} else {
						window[name] = value;
					}
					$this.showMessage(_("Saved!"),"success");
				} else {
					$this.showMessage(data.message,"danger");
				}
				$(this).off("blur");
			});
		});
		if($("#Contactmanager-image").length) {
			/**
			 * Drag/Drop/Upload Files
			 */
			$('#contactmanager_dropzone').on('drop dragover', function (e) {
				e.preventDefault();
			});
			$('#contactmanager_dropzone').on('dragleave drop', function (e) {
				$(this).removeClass("activate");
			});
			$('#contactmanager_dropzone').on('dragover', function (e) {
				$(this).addClass("activate");
			});
			var supportedRegExp = "png|jpg|jpeg";
			$( document ).ready(function() {
				$('#contactmanager_imageupload').fileupload({
					dataType: 'json',
					dropZone: $("#contactmanager_dropzone"),
					add: function (e, data) {
						//TODO: Need to check all supported formats
						var sup = "\.("+supportedRegExp+")$",
								patt = new RegExp(sup),
								submit = true;
						$.each(data.files, function(k, v) {
							if(!patt.test(v.name.toLowerCase())) {
								submit = false;
								alert(_("Unsupported file type"));
								return false;
							}
						});
						if(submit) {
							$("#contactmanager_upload-progress .progress-bar").addClass("progress-bar-striped active");
							data.submit();
						}
					},
					drop: function () {
						$("#contactmanager_upload-progress .progress-bar").css("width", "0%");
					},
					dragover: function (e, data) {
					},
					change: function (e, data) {
					},
					done: function (e, data) {
						$("#contactmanager_upload-progress .progress-bar").removeClass("progress-bar-striped active");
						$("#contactmanager_upload-progress .progress-bar").css("width", "0%");

						if(data.result.status) {
							$("#contactmanager_dropzone img").attr("src",data.result.url);
							$("#contactmanager_image").val(data.result.filename);
							$("#contactmanager_dropzone img").removeClass("hidden");
							$("#contactmanager_del-image").removeClass("hidden");
							$("#contactmanager_gravatar").prop('checked', false);
						} else {
							alert(data.result.message);
						}
					},
					progressall: function (e, data) {
						var progress = parseInt(data.loaded / data.total * 100, 10);
						$("#contactmanager_upload-progress .progress-bar").css("width", progress+"%");
					},
					fail: function (e, data) {
					},
					always: function (e, data) {
					}
				});

				$("#contactmanager_del-image").click(function(e) {
					e.preventDefault();
					e.stopPropagation();
					var id = $("input[name=user]").val(),
							grouptype = 'userman';
					$.post( "ajax.php?&module=Contactmanager&command=delimage", {id: id, img: $("#contactmanager_image").val()}, function( data ) {
						if(data.status) {
							$("#contactmanager_image").val("");
							$("#contactmanager_dropzone img").addClass("hidden");
							$("#contactmanager_dropzone img").attr("src","");
							$("#contactmanager_del-image").addClass("hidden");
							$("#contactmanager_gravatar").prop('checked', false);
						}
					});
				});

				$("#contactmanager_gravatar").change(function() {
					if($(this).is(":checked")) {
						var id = $("input[name=user]").val(),
								grouptype = 'userman';
						if($("#email").val() === "") {
							alert(_("No email defined"));
							$("#contactmanager_gravatar").prop('checked', false);
							return;
						}
						var t = $("label[for=contactmanager_gravatar]").text();
						$("label[for=contactmanager_gravatar]").text(_("Loading..."));
						$.post( "ajax.php?module=Contactmanager&command=getgravatar", {id: id, grouptype: grouptype, email: $("#email").val()}, function( data ) {
							$("label[for=contactmanager_gravatar]").text(t);
							if(data.status) {
								$("#contactmanager_dropzone img").data("oldsrc",$("#dropzone img").attr("src"));
								$("#contactmanager_dropzone img").attr("src",data.url);
								$("#contactmanager_image").data("old",$("#image").val());
								$("#contactmanager_image").val(data.filename);
								$("#contactmanager_dropzone img").removeClass("hidden");
								$("#contactmanager_del-image").removeClass("hidden");
							} else {
								alert(data.message);
								$("#contactmanager_gravatar").prop('checked', false);
							}
						});
					} else {
						var oldsrc = $("#contactmanager_dropzone img").data("oldsrc");
						if(typeof oldsrc !== "undefined" && oldsrc !== "") {
							$("#contactmanager_dropzone img").attr("src",oldsrc);
							$("#contactmanager_image").val($("#image").data("old"));
						} else {
							$("#contactmanager_image").val("");
							$("#contactmanager_dropzone img").addClass("hidden");
							$("#contactmanager_dropzone img").attr("src","");
							$("#contactmanager_del-image").addClass("hidden");
						}
					}
				});
			});
		}
	}
});

/* ========================================================================
 * bootstrap-tour - v0.10.3
 * http://bootstraptour.com
 * ========================================================================
 * Copyright 2012-2015 Ulrich Sossou
 *
 * ========================================================================
 * Licensed under the MIT License (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

(function(window, factory) {
  if (typeof define === 'function' && define.amd) {
    return define(['jquery'], function(jQuery) {
      return window.Tour = factory(jQuery);
    });
  } else if (typeof exports === 'object') {
    return module.exports = factory(require('jQuery'));
  } else {
    return window.Tour = factory(window.jQuery);
  }
})(window, function($) {
  var Tour, document;
  document = window.document;
  Tour = (function() {
    function Tour(options) {
      var storage;
      try {
        storage = window.localStorage;
      } catch (_error) {
        storage = false;
      }
      this._options = $.extend({
        name: 'tour',
        steps: [],
        container: 'body',
        autoscroll: true,
        keyboard: true,
        storage: storage,
        debug: false,
        backdrop: false,
        backdropContainer: 'body',
        backdropPadding: 0,
        redirect: true,
        orphan: false,
        duration: false,
        delay: false,
        basePath: '',
        template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; Prev</button> <button class="btn btn-sm btn-default" data-role="next">Next &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div> <button class="btn btn-sm btn-default" data-role="end">End tour</button> </div> </div>',
        afterSetState: function(key, value) {},
        afterGetState: function(key, value) {},
        afterRemoveState: function(key) {},
        onStart: function(tour) {},
        onEnd: function(tour) {},
        onShow: function(tour) {},
        onShown: function(tour) {},
        onHide: function(tour) {},
        onHidden: function(tour) {},
        onNext: function(tour) {},
        onPrev: function(tour) {},
        onPause: function(tour, duration) {},
        onResume: function(tour, duration) {},
        onRedirectError: function(tour) {}
      }, options);
      this._force = false;
      this._inited = false;
      this._current = null;
      this.backdrop = {
        overlay: null,
        $element: null,
        $background: null,
        backgroundShown: false,
        overlayElementShown: false
      };
      this;
    }

    Tour.prototype.addSteps = function(steps) {
      var step, _i, _len;
      for (_i = 0, _len = steps.length; _i < _len; _i++) {
        step = steps[_i];
        this.addStep(step);
      }
      return this;
    };

    Tour.prototype.addStep = function(step) {
      this._options.steps.push(step);
      return this;
    };

    Tour.prototype.getStep = function(i) {
      if (this._options.steps[i] != null) {
        return $.extend({
          id: "step-" + i,
          path: '',
          host: '',
          placement: 'right',
          title: '',
          content: '<p></p>',
          next: i === this._options.steps.length - 1 ? -1 : i + 1,
          prev: i - 1,
          animation: true,
          container: this._options.container,
          autoscroll: this._options.autoscroll,
          backdrop: this._options.backdrop,
          backdropContainer: this._options.backdropContainer,
          backdropPadding: this._options.backdropPadding,
          redirect: this._options.redirect,
          reflexElement: this._options.steps[i].element,
          backdropElement: this._options.steps[i].element,
          orphan: this._options.orphan,
          duration: this._options.duration,
          delay: this._options.delay,
          template: this._options.template,
          onShow: this._options.onShow,
          onShown: this._options.onShown,
          onHide: this._options.onHide,
          onHidden: this._options.onHidden,
          onNext: this._options.onNext,
          onPrev: this._options.onPrev,
          onPause: this._options.onPause,
          onResume: this._options.onResume,
          onRedirectError: this._options.onRedirectError
        }, this._options.steps[i]);
      }
    };

    Tour.prototype.init = function(force) {
      this._force = force;
      if (this.ended()) {
        this._debug('Tour ended, init prevented.');
        return this;
      }
      this.setCurrentStep();
      this._initMouseNavigation();
      this._initKeyboardNavigation();
      this._onResize((function(_this) {
        return function() {
          return _this.showStep(_this._current);
        };
      })(this));
      if (this._current !== null) {
        this.showStep(this._current);
      }
      this._inited = true;
      return this;
    };

    Tour.prototype.start = function(force) {
      var promise;
      if (force == null) {
        force = false;
      }
      if (!this._inited) {
        this.init(force);
      }
      if (this._current === null) {
        promise = this._makePromise(this._options.onStart != null ? this._options.onStart(this) : void 0);
        this._callOnPromiseDone(promise, this.showStep, 0);
      }
      return this;
    };

    Tour.prototype.next = function() {
      var promise;
      promise = this.hideStep(this._current, this._current + 1);
      return this._callOnPromiseDone(promise, this._showNextStep);
    };

    Tour.prototype.prev = function() {
      var promise;
      promise = this.hideStep(this._current, this._current - 1);
      return this._callOnPromiseDone(promise, this._showPrevStep);
    };

    Tour.prototype.goTo = function(i) {
      var promise;
      promise = this.hideStep(this._current, i);
      return this._callOnPromiseDone(promise, this.showStep, i);
    };

    Tour.prototype.end = function() {
      var endHelper, promise;
      endHelper = (function(_this) {
        return function(e) {
          $(document).off("click.tour-" + _this._options.name);
          $(document).off("keyup.tour-" + _this._options.name);
          $(window).off("resize.tour-" + _this._options.name);
          _this._setState('end', 'yes');
          _this._inited = false;
          _this._force = false;
          _this._clearTimer();
          if (_this._options.onEnd != null) {
            return _this._options.onEnd(_this);
          }
        };
      })(this);
      promise = this.hideStep(this._current);
      return this._callOnPromiseDone(promise, endHelper);
    };

    Tour.prototype.ended = function() {
      return !this._force && !!this._getState('end');
    };

    Tour.prototype.restart = function() {
      this._removeState('current_step');
      this._removeState('end');
      this._removeState('redirect_to');
      return this.start();
    };

    Tour.prototype.pause = function() {
      var step;
      step = this.getStep(this._current);
      if (!(step && step.duration)) {
        return this;
      }
      this._paused = true;
      this._duration -= new Date().getTime() - this._start;
      window.clearTimeout(this._timer);
      this._debug("Paused/Stopped step " + (this._current + 1) + " timer (" + this._duration + " remaining).");
      if (step.onPause != null) {
        return step.onPause(this, this._duration);
      }
    };

    Tour.prototype.resume = function() {
      var step;
      step = this.getStep(this._current);
      if (!(step && step.duration)) {
        return this;
      }
      this._paused = false;
      this._start = new Date().getTime();
      this._duration = this._duration || step.duration;
      this._timer = window.setTimeout((function(_this) {
        return function() {
          if (_this._isLast()) {
            return _this.next();
          } else {
            return _this.end();
          }
        };
      })(this), this._duration);
      this._debug("Started step " + (this._current + 1) + " timer with duration " + this._duration);
      if ((step.onResume != null) && this._duration !== step.duration) {
        return step.onResume(this, this._duration);
      }
    };

    Tour.prototype.hideStep = function(i, iNext) {
      var hideDelay, hideStepHelper, promise, step;
      step = this.getStep(i);
      if (!step) {
        return;
      }
      this._clearTimer();
      promise = this._makePromise(step.onHide != null ? step.onHide(this, i) : void 0);
      hideStepHelper = (function(_this) {
        return function(e) {
          var $element, next_step;
          $element = $(step.element);
          if (!($element.data('bs.popover') || $element.data('popover'))) {
            $element = $('body');
          }
          $element.popover('destroy').removeClass("tour-" + _this._options.name + "-element tour-" + _this._options.name + "-" + i + "-element").removeData('bs.popover').focus();
          if (step.reflex) {
            $(step.reflexElement).removeClass('tour-step-element-reflex').off("" + (_this._reflexEvent(step.reflex)) + ".tour-" + _this._options.name);
          }
          if (step.backdrop) {
            next_step = (iNext != null) && _this.getStep(iNext);
            if (!next_step || !next_step.backdrop || next_step.backdropElement !== step.backdropElement) {
              _this._hideBackdrop();
            }
          }
          if (step.onHidden != null) {
            return step.onHidden(_this);
          }
        };
      })(this);
      hideDelay = step.delay.hide || step.delay;
      if ({}.toString.call(hideDelay) === '[object Number]' && hideDelay > 0) {
        this._debug("Wait " + hideDelay + " milliseconds to hide the step " + (this._current + 1));
        window.setTimeout((function(_this) {
          return function() {
            return _this._callOnPromiseDone(promise, hideStepHelper);
          };
        })(this), hideDelay);
      } else {
        this._callOnPromiseDone(promise, hideStepHelper);
      }
      return promise;
    };

    Tour.prototype.showStep = function(i) {
      var path, promise, showDelay, showStepHelper, skipToPrevious, step;
      if (this.ended()) {
        this._debug('Tour ended, showStep prevented.');
        return this;
      }
      step = this.getStep(i);
      if (!step) {
        return;
      }
      skipToPrevious = i < this._current;
      promise = this._makePromise(step.onShow != null ? step.onShow(this, i) : void 0);
      this.setCurrentStep(i);
      path = (function() {
        switch ({}.toString.call(step.path)) {
          case '[object Function]':
            return step.path();
          case '[object String]':
            return this._options.basePath + step.path;
          default:
            return step.path;
        }
      }).call(this);
      if (step.redirect && this._isRedirect(step.host, path, document.location)) {
        this._redirect(step, i, path);
        if (!this._isJustPathHashDifferent(step.host, path, document.location)) {
          return;
        }
      }
      showStepHelper = (function(_this) {
        return function(e) {
          var showPopoverAndOverlay;
          if (_this._isOrphan(step)) {
            if (step.orphan === false) {
              _this._debug("Skip the orphan step " + (_this._current + 1) + ".\nOrphan option is false and the element does not exist or is hidden.");
              if (skipToPrevious) {
                _this._showPrevStep();
              } else {
                _this._showNextStep();
              }
              return;
            }
            _this._debug("Show the orphan step " + (_this._current + 1) + ". Orphans option is true.");
          }
          if (step.backdrop) {
            _this._showBackdrop(step);
          }
          showPopoverAndOverlay = function() {
            if (_this.getCurrentStep() !== i || _this.ended()) {
              return;
            }
            if ((step.element != null) && step.backdrop) {
              _this._showOverlayElement(step, true);
							console.log("redraw");
              _this.redraw();
            }
            _this._showPopover(step, i);
            if (step.onShown != null) {
              step.onShown(_this);
            }
            return _this._debug("Step " + (_this._current + 1) + " of " + _this._options.steps.length);
          };
          if (step.autoscroll) {
            _this._scrollIntoView(step, showPopoverAndOverlay);
          } else {
            showPopoverAndOverlay();
          }
          if (step.duration) {
            return _this.resume();
          }
        };
      })(this);
      showDelay = step.delay.show || step.delay;
      if ({}.toString.call(showDelay) === '[object Number]' && showDelay > 0) {
        this._debug("Wait " + showDelay + " milliseconds to show the step " + (this._current + 1));
        window.setTimeout((function(_this) {
          return function() {
            return _this._callOnPromiseDone(promise, showStepHelper);
          };
        })(this), showDelay);
      } else {
        this._callOnPromiseDone(promise, showStepHelper);
      }
      return promise;
    };

    Tour.prototype.getCurrentStep = function() {
      return this._current;
    };

    Tour.prototype.setCurrentStep = function(value) {
      if (value != null) {
        this._current = value;
        this._setState('current_step', value);
      } else {
        this._current = this._getState('current_step');
        this._current = this._current === null ? null : parseInt(this._current, 10);
      }
      return this;
    };

    Tour.prototype.redraw = function() {
      return this._showOverlayElement(this.getStep(this.getCurrentStep()).element, true);
    };

    Tour.prototype._setState = function(key, value) {
      var e, keyName;
      if (this._options.storage) {
        keyName = "" + this._options.name + "_" + key;
        try {
          this._options.storage.setItem(keyName, value);
        } catch (_error) {
          e = _error;
          if (e.code === DOMException.QUOTA_EXCEEDED_ERR) {
            this._debug('LocalStorage quota exceeded. State storage failed.');
          }
        }
        return this._options.afterSetState(keyName, value);
      } else {
        if (this._state == null) {
          this._state = {};
        }
        return this._state[key] = value;
      }
    };

    Tour.prototype._removeState = function(key) {
      var keyName;
      if (this._options.storage) {
        keyName = "" + this._options.name + "_" + key;
        this._options.storage.removeItem(keyName);
        return this._options.afterRemoveState(keyName);
      } else {
        if (this._state != null) {
          return delete this._state[key];
        }
      }
    };

    Tour.prototype._getState = function(key) {
      var keyName, value;
      if (this._options.storage) {
        keyName = "" + this._options.name + "_" + key;
        value = this._options.storage.getItem(keyName);
      } else {
        if (this._state != null) {
          value = this._state[key];
        }
      }
      if (value === void 0 || value === 'null') {
        value = null;
      }
      this._options.afterGetState(key, value);
      return value;
    };

    Tour.prototype._showNextStep = function() {
      var promise, showNextStepHelper, step;
      step = this.getStep(this._current);
      showNextStepHelper = (function(_this) {
        return function(e) {
          return _this.showStep(step.next);
        };
      })(this);
      promise = this._makePromise(step.onNext != null ? step.onNext(this) : void 0);
      return this._callOnPromiseDone(promise, showNextStepHelper);
    };

    Tour.prototype._showPrevStep = function() {
      var promise, showPrevStepHelper, step;
      step = this.getStep(this._current);
      showPrevStepHelper = (function(_this) {
        return function(e) {
          return _this.showStep(step.prev);
        };
      })(this);
      promise = this._makePromise(step.onPrev != null ? step.onPrev(this) : void 0);
      return this._callOnPromiseDone(promise, showPrevStepHelper);
    };

    Tour.prototype._debug = function(text) {
      if (this._options.debug) {
        return window.console.log("Bootstrap Tour '" + this._options.name + "' | " + text);
      }
    };

    Tour.prototype._isRedirect = function(host, path, location) {
      var currentPath;
      if ((host != null) && host !== '' && (({}.toString.call(host) === '[object RegExp]' && !host.test(location.origin)) || ({}.toString.call(host) === '[object String]' && this._isHostDifferent(host, location)))) {
        return true;
      }
      currentPath = [location.pathname, location.search, location.hash].join('');
      return (path != null) && path !== '' && (({}.toString.call(path) === '[object RegExp]' && !path.test(currentPath)) || ({}.toString.call(path) === '[object String]' && this._isPathDifferent(path, currentPath)));
    };

    Tour.prototype._isHostDifferent = function(host, location) {
      switch ({}.toString.call(host)) {
        case '[object RegExp]':
          return !host.test(location.origin);
        case '[object String]':
          return this._getProtocol(host) !== this._getProtocol(location.href) || this._getHost(host) !== this._getHost(location.href);
        default:
          return true;
      }
    };

    Tour.prototype._isPathDifferent = function(path, currentPath) {
      return this._getPath(path) !== this._getPath(currentPath) || !this._equal(this._getQuery(path), this._getQuery(currentPath)) || !this._equal(this._getHash(path), this._getHash(currentPath));
    };

    Tour.prototype._isJustPathHashDifferent = function(host, path, location) {
      var currentPath;
      if ((host != null) && host !== '') {
        if (this._isHostDifferent(host, location)) {
          return false;
        }
      }
      currentPath = [location.pathname, location.search, location.hash].join('');
      if ({}.toString.call(path) === '[object String]') {
        return this._getPath(path) === this._getPath(currentPath) && this._equal(this._getQuery(path), this._getQuery(currentPath)) && !this._equal(this._getHash(path), this._getHash(currentPath));
      }
      return false;
    };

    Tour.prototype._redirect = function(step, i, path) {
      var href;
      if ($.isFunction(step.redirect)) {
        return step.redirect.call(this, path);
      } else {
        href = {}.toString.call(step.host) === '[object String]' ? "" + step.host + path : path;
        this._debug("Redirect to " + href);
        if (this._getState('redirect_to') === ("" + i)) {
          this._debug("Error redirection loop to " + path);
          this._removeState('redirect_to');
          if (step.onRedirectError != null) {
            return step.onRedirectError(this);
          }
        } else {
          this._setState('redirect_to', "" + i);
          return document.location.href = href;
        }
      }
    };

    Tour.prototype._isOrphan = function(step) {
      return (step.element == null) || !$(step.element).length || $(step.element).is(':hidden') && ($(step.element)[0].namespaceURI !== 'http://www.w3.org/2000/svg');
    };

    Tour.prototype._isLast = function() {
      return this._current < this._options.steps.length - 1;
    };

    Tour.prototype._showPopover = function(step, i) {
      var $element, $tip, isOrphan, options, shouldAddSmart;
      $(".tour-" + this._options.name).remove();
      options = $.extend({}, this._options);
      isOrphan = this._isOrphan(step);
      step.template = this._template(step, i);
      if (isOrphan) {
        step.element = 'body';
        step.placement = 'top';
      }
      $element = $(step.element);
      $element.addClass("tour-" + this._options.name + "-element tour-" + this._options.name + "-" + i + "-element");
      if (step.options) {
        $.extend(options, step.options);
      }
      if (step.reflex && !isOrphan) {
        $(step.reflexElement).addClass('tour-step-element-reflex').off("" + (this._reflexEvent(step.reflex)) + ".tour-" + this._options.name).on("" + (this._reflexEvent(step.reflex)) + ".tour-" + this._options.name, (function(_this) {
          return function() {
            if (_this._isLast()) {
              return _this.next();
            } else {
              return _this.end();
            }
          };
        })(this));
      }
      shouldAddSmart = step.smartPlacement === true && step.placement.search(/auto/i) === -1;
      $element.popover({
        placement: shouldAddSmart ? "auto " + step.placement : step.placement,
        trigger: 'manual',
        title: step.title,
        content: step.content,
        html: true,
        animation: step.animation,
        container: step.container,
        template: step.template,
        selector: step.element
      }).popover('show');
      $tip = $element.data('bs.popover') ? $element.data('bs.popover').tip() : $element.data('popover').tip();
      $tip.attr('id', step.id);
      this._focus($tip, $element, step.next < 0);
      this._reposition($tip, step);
      if (isOrphan) {
        return this._center($tip);
      }
    };

    Tour.prototype._template = function(step, i) {
      var $navigation, $next, $prev, $resume, $template, template;
      template = step.template;
      if (this._isOrphan(step) && {}.toString.call(step.orphan) !== '[object Boolean]') {
        template = step.orphan;
      }
      $template = $.isFunction(template) ? $(template(i, step)) : $(template);
      $navigation = $template.find('.popover-navigation');
      $prev = $navigation.find('[data-role="prev"]');
      $next = $navigation.find('[data-role="next"]');
      $resume = $navigation.find('[data-role="pause-resume"]');
      if (this._isOrphan(step)) {
        $template.addClass('orphan');
      }
      $template.addClass("tour-" + this._options.name + " tour-" + this._options.name + "-" + i);
      if (step.reflex) {
        $template.addClass("tour-" + this._options.name + "-reflex");
      }
      if (step.prev < 0) {
        $prev.addClass('disabled').prop('disabled', true).prop('tabindex', -1);
      }
      if (step.next < 0) {
        $next.addClass('disabled').prop('disabled', true).prop('tabindex', -1);
      }
      if (!step.duration) {
        $resume.remove();
      }
      return $template.clone().wrap('<div>').parent().html();
    };

    Tour.prototype._reflexEvent = function(reflex) {
      if ({}.toString.call(reflex) === '[object Boolean]') {
        return 'click';
      } else {
        return reflex;
      }
    };

    Tour.prototype._focus = function($tip, $element, end) {
      var $next, role;
      role = end ? 'end' : 'next';
      $next = $tip.find("[data-role='" + role + "']");
      return $element.on('shown.bs.popover', function() {
        return $next.focus();
      });
    };

    Tour.prototype._reposition = function($tip, step) {
      var offsetBottom, offsetHeight, offsetRight, offsetWidth, originalLeft, originalTop, tipOffset;
      offsetWidth = $tip[0].offsetWidth;
      offsetHeight = $tip[0].offsetHeight;
      tipOffset = $tip.offset();
      originalLeft = tipOffset.left;
      originalTop = tipOffset.top;
      offsetBottom = $(document).outerHeight() - tipOffset.top - $tip.outerHeight();
      if (offsetBottom < 0) {
        tipOffset.top = tipOffset.top + offsetBottom;
      }
      offsetRight = $('html').outerWidth() - tipOffset.left - $tip.outerWidth();
      if (offsetRight < 0) {
        tipOffset.left = tipOffset.left + offsetRight;
      }
      if (tipOffset.top < 0) {
        tipOffset.top = 0;
      }
      if (tipOffset.left < 0) {
        tipOffset.left = 0;
      }
      $tip.offset(tipOffset);
      if (step.placement === 'bottom' || step.placement === 'top') {
        if (originalLeft !== tipOffset.left) {
          return this._replaceArrow($tip, (tipOffset.left - originalLeft) * 2, offsetWidth, 'left');
        }
      } else {
        if (originalTop !== tipOffset.top) {
          return this._replaceArrow($tip, (tipOffset.top - originalTop) * 2, offsetHeight, 'top');
        }
      }
    };

    Tour.prototype._center = function($tip) {
      return $tip.css('top', $(window).outerHeight() / 2 - $tip.outerHeight() / 2);
    };

    Tour.prototype._replaceArrow = function($tip, delta, dimension, position) {
      return $tip.find('.arrow').css(position, delta ? 50 * (1 - delta / dimension) + '%' : '');
    };

    Tour.prototype._scrollIntoView = function(step, callback) {
      var $element, $window, counter, height, offsetTop, scrollTop, windowHeight;
      $element = $(step.element);
      if (!$element.length) {
        return callback();
      }
      $window = $(window);
      offsetTop = $element.offset().top;
      height = $element.outerHeight();
      windowHeight = $window.height();
      scrollTop = 0;
      switch (step.placement) {
        case 'top':
          scrollTop = Math.max(0, offsetTop - (windowHeight / 2));
          break;
        case 'left':
        case 'right':
          scrollTop = Math.max(0, (offsetTop + height / 2) - (windowHeight / 2));
          break;
        case 'bottom':
          scrollTop = Math.max(0, (offsetTop + height) - (windowHeight / 2));
      }
      this._debug("Scroll into view. ScrollTop: " + scrollTop + ". Element offset: " + offsetTop + ". Window height: " + windowHeight + ".");
      counter = 0;
      return $('body, html').stop(true, true).animate({
        scrollTop: Math.ceil(scrollTop)
      }, (function(_this) {
        return function() {
          if (++counter === 2) {
            callback();
            return _this._debug("Scroll into view.\nAnimation end element offset: " + ($element.offset().top) + ".\nWindow height: " + ($window.height()) + ".");
          }
        };
      })(this));
    };

    Tour.prototype._onResize = function(callback, timeout) {
      return $(window).on("resize.tour-" + this._options.name, function() {
        clearTimeout(timeout);
        return timeout = setTimeout(callback, 100);
      });
    };

    Tour.prototype._initMouseNavigation = function() {
      var _this;
      _this = this;
      return $(document).off("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='prev']").off("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='next']").off("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='end']").off("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='pause-resume']").on("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='next']", (function(_this) {
        return function(e) {
          e.preventDefault();
          return _this.next();
        };
      })(this)).on("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='prev']", (function(_this) {
        return function(e) {
          e.preventDefault();
          if (_this._current > 0) {
            return _this.prev();
          }
        };
      })(this)).on("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='end']", (function(_this) {
        return function(e) {
          e.preventDefault();
          return _this.end();
        };
      })(this)).on("click.tour-" + this._options.name, ".popover.tour-" + this._options.name + " *[data-role='pause-resume']", function(e) {
        var $this;
        e.preventDefault();
        $this = $(this);
        $this.text(_this._paused ? $this.data('pause-text') : $this.data('resume-text'));
        if (_this._paused) {
          return _this.resume();
        } else {
          return _this.pause();
        }
      });
    };

    Tour.prototype._initKeyboardNavigation = function() {
      if (!this._options.keyboard) {
        return;
      }
      return $(document).on("keyup.tour-" + this._options.name, (function(_this) {
        return function(e) {
          if (!e.which) {
            return;
          }
          switch (e.which) {
            case 39:
              e.preventDefault();
              if (_this._isLast()) {
                return _this.next();
              } else {
                return _this.end();
              }
              break;
            case 37:
              e.preventDefault();
              if (_this._current > 0) {
                return _this.prev();
              }
          }
        };
      })(this));
    };

    Tour.prototype._makePromise = function(result) {
      if (result && $.isFunction(result.then)) {
        return result;
      } else {
        return null;
      }
    };

    Tour.prototype._callOnPromiseDone = function(promise, cb, arg) {
      if (promise) {
        return promise.then((function(_this) {
          return function(e) {
            return cb.call(_this, arg);
          };
        })(this));
      } else {
        return cb.call(this, arg);
      }
    };

    Tour.prototype._showBackdrop = function(step) {
      if (this.backdrop.backgroundShown) {
        return;
      }
      this.backdrop = $('<div>', {
        "class": 'tour-backdrop'
      });
      this.backdrop.backgroundShown = true;
      return $(step.backdropContainer).append(this.backdrop);
    };

    Tour.prototype._hideBackdrop = function() {
      this._hideOverlayElement();
      return this._hideBackground();
    };

    Tour.prototype._hideBackground = function() {
      if (this.backdrop && this.backdrop.remove) {
        this.backdrop.remove();
        this.backdrop.overlay = null;
        return this.backdrop.backgroundShown = false;
      }
    };

    Tour.prototype._showOverlayElement = function(step, force) {
      var $backdropElement, $element, elementData;
      $element = $(step.element);
      $backdropElement = $(step.backdropElement);
      if (!$element || $element.length === 0 || this.backdrop.overlayElementShown && !force) {
        return;
      }
      if (!this.backdrop.overlayElementShown) {
        this.backdrop.$element = $backdropElement.addClass('tour-step-backdrop');
        this.backdrop.$background = $('<div>', {
          "class": 'tour-step-background'
        });
        this.backdrop.$background.appendTo(step.backdropContainer);
        this.backdrop.overlayElementShown = true;
      }
      elementData = {
        width: $backdropElement.innerWidth(),
        height: $backdropElement.innerHeight(),
        offset: $backdropElement.offset()
      };
      if (step.backdropPadding) {
        elementData = this._applyBackdropPadding(step.backdropPadding, elementData);
      }
      return this.backdrop.$background.width(elementData.width).height(elementData.height).offset(elementData.offset);
    };

    Tour.prototype._hideOverlayElement = function() {
      if (!this.backdrop.overlayElementShown) {
        return;
      }
      this.backdrop.$element.removeClass('tour-step-backdrop');
      this.backdrop.$background.remove();
      this.backdrop.$element = null;
      this.backdrop.$background = null;
      return this.backdrop.overlayElementShown = false;
    };

    Tour.prototype._applyBackdropPadding = function(padding, data) {
      if (typeof padding === 'object') {
        if (padding.top == null) {
          padding.top = 0;
        }
        if (padding.right == null) {
          padding.right = 0;
        }
        if (padding.bottom == null) {
          padding.bottom = 0;
        }
        if (padding.left == null) {
          padding.left = 0;
        }
        data.offset.top = data.offset.top - padding.top;
        data.offset.left = data.offset.left - padding.left;
        data.width = data.width + padding.left + padding.right;
        data.height = data.height + padding.top + padding.bottom;
      } else {
        data.offset.top = data.offset.top - padding;
        data.offset.left = data.offset.left - padding;
        data.width = data.width + (padding * 2);
        data.height = data.height + (padding * 2);
      }
      return data;
    };

    Tour.prototype._clearTimer = function() {
      window.clearTimeout(this._timer);
      this._timer = null;
      return this._duration = null;
    };

    Tour.prototype._getProtocol = function(url) {
      url = url.split('://');
      if (url.length > 1) {
        return url[0];
      } else {
        return 'http';
      }
    };

    Tour.prototype._getHost = function(url) {
      url = url.split('//');
      url = url.length > 1 ? url[1] : url[0];
      return url.split('/')[0];
    };

    Tour.prototype._getPath = function(path) {
      return path.replace(/\/?$/, '').split('?')[0].split('#')[0];
    };

    Tour.prototype._getQuery = function(path) {
      return this._getParams(path, '?');
    };

    Tour.prototype._getHash = function(path) {
      return this._getParams(path, '#');
    };

    Tour.prototype._getParams = function(path, start) {
      var param, params, paramsObject, _i, _len;
      params = path.split(start);
      if (params.length === 1) {
        return {};
      }
      params = params[1].split('&');
      paramsObject = {};
      for (_i = 0, _len = params.length; _i < _len; _i++) {
        param = params[_i];
        param = param.split('=');
        paramsObject[param[0]] = param[1] || '';
      }
      return paramsObject;
    };

    Tour.prototype._equal = function(obj1, obj2) {
      var k, obj1Keys, obj2Keys, v, _i, _len;
      if ({}.toString.call(obj1) === '[object Object]' && {}.toString.call(obj2) === '[object Object]') {
        obj1Keys = Object.keys(obj1);
        obj2Keys = Object.keys(obj2);
        if (obj1Keys.length !== obj2Keys.length) {
          return false;
        }
        for (k in obj1) {
          v = obj1[k];
          if (!this._equal(obj2[k], v)) {
            return false;
          }
        }
        return true;
      } else if ({}.toString.call(obj1) === '[object Array]' && {}.toString.call(obj2) === '[object Array]') {
        if (obj1.length !== obj2.length) {
          return false;
        }
        for (k = _i = 0, _len = obj1.length; _i < _len; k = ++_i) {
          v = obj1[k];
          if (!this._equal(v, obj2[k])) {
            return false;
          }
        }
        return true;
      } else {
        return obj1 === obj2;
      }
    };

    return Tour;

  })();
  return Tour;
});

var UcptourC = UCPMC.extend({
	init: function() {
		this.tour = null;
	},
	poll: function(data) {
		//console.log(data)
	},

});

$(document).bind("logIn", function( event ) {
	UCP.Modules.Ucptour.tour = new Tour({
		debug: false,
		storage: false,
		keyboard: false,
		onEnd: function (tour) {
			$.post( UCP.ajaxUrl + "?module=ucptour&command=tour", { state: 0 }, function( data ) {

			});
		},
		steps: [
			{
				orphan: true,
				title: sprintf(_("Welcome to %s!"),UCP.Modules.Ucptour.staticsettings.brand),
				content: _("Congratulations!")+"<br><br> "+_("You just successfully logged in for the first time!")+" <br>"+_("This tour will take you on a brief walkthrough of the new User Control Panel in a few simple steps.")+"<br><br>"+_("You can always exit the tour if you'd like, and you can restart the tour at anytime by clicking your User Settings and then 'Restart Tour'")+"<br><br><u>"+_("To continue just click Next")+"</u>",
				backdrop: true,
			}, {
				backdrop: true,
				backdropContainer: "#nav-bar-background",
				element: "#add_new_dashboard",
				placement: "left",
				title: _("Adding a dashboard"),
				content: _("The User Control Panel is now separated by 'Dashboards'. You can add a new dashboard by clicking this symbol")+"<br><br>"+_("Click this symbol to continue"),
				next: -1,
				reflex: true,
				onShow: function(tour) {
					$(".navbar.navbar-inverse.navbar-fixed-left").css("z-index","1029");
				},
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$("#add_dashboard").one("shown.bs.modal", function() {
						tour.goTo(step + 1);
					});
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_dashboard .modal-dialog",
				element: "#dashboard_name",
				placement: "bottom",
				title: _("Name your dashboard"),
				content: _("Enter a name for your dashboard in this input box"),
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$("#dashboard_name").keyup(function(e) {
						if (e.keyCode == '13') {
							$(document).one("addDashboard",function(e, id) {
								$(".dashboard-menu[data-id="+id+"]").addClass("tour-step");
								$(".dashboard-menu[data-id="+id+"] a").click();
								tour.goTo(step + 2);
							});
						}
					});
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_dashboard .modal-dialog",
				element: "#create_dashboard",
				placement: "bottom",
				title: _("Save your dashboard"),
				content: _("When you are finished simply hit 'Create Dashboard' to create your dashboard"),
				reflex: true,
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					if($("#dashboard_name").val() === "") {
						tour.goTo(step - 1);
					}
				},
				onNext: function(tour) {
					var step = tour.getCurrentStep();
					$(document).one("addDashboard",function(e, id) {
						$(".dashboard-menu[data-id="+id+"]").addClass("tour-step");
						$(".dashboard-menu[data-id="+id+"] a").click();
						tour.goTo(step + 1);
					});
					return (new jQuery.Deferred()).promise();
				}
			}, {
				backdrop: true,
				backdropContainer: "#nav-bar-background",
				element: ".dashboard-menu.tour-step",
				placement: "bottom",
				title: _("Dashboards"),
				content: _("Your dashboard has been added here"),
				previous: -1
			}, {
				backdrop: true,
				backdropContainer: ".main-content-object",
				element: "#dashboard-content",
				placement: "bottom",
				title: _("Dashboard Widgets"),
				content: _("Dashboard widgets will be displayed here"),
				previous: -1,
				onShown: function(tour) {
					$("#dashboard-content").css("height","calc(100vh - 66px)");
				},
				onNext: function(tour) {
					$("#dashboard-content").css("height","");
				}
			}, {
				backdrop: true,
				backdropContainer: "#nav-bar-background",
				element: ".dashboard-menu.tour-step .edit-dashboard",
				placement: "bottom",
				title: _("Editing a Dashboard"),
				content: _("The dashboard's name can be changed by clicking the pencil")
			}, {
				backdrop: true,
				backdropContainer: "#nav-bar-background",
				element: ".dashboard-menu.tour-step .remove-dashboard",
				placement: "left",
				title: _("Delete a Dashboard"),
				content: sprintf(_("A dashboard can be deleted by clicking the '%s'"),'X')
			}, {
				backdrop: true,
				backdropContainer: "#nav-bar-background",
				element: ".dashboard-menu.tour-step",
				placement: "bottom",
				title: _("Ordering dashboards"),
				content: _("Multiple dashboard can be re-ordered by hovering with your mouse until the move cursor is shown. Then clicking and dragging the dashboard in the order you want"),
				onHidden: function(tour) {
					$(".navbar.navbar-inverse.navbar-fixed-left").css("z-index","");
				}
			}, {
				backdrop: true,
				backdropContainer: "#side_bar_content",
				element: "#side_bar_content .add-widget",
				placement: "right",
				title: _("Adding Widgets"),
				content: sprintf(_("Widgets can be added by clicking the '%s' symbol"),'(+)')+"<br><br>"+_("Click this symbol to continue"),
				reflex: true,
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$("#add_widget").one("shown.bs.modal", function() {
						tour.goTo(step + 1);
					});
					$(".tour-step-background").css("background-color","white");
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .modal-body",
				element: "#add_widget .modal-body .nav-tabs",
				placement: "left",
				title: _("Selecting Widgets"),
				content: _("There are two different types of widgets. Dashboard Widgets and Side Bar widgets. Let's start with dashboard widgets"),
				previous: -1,
				onShown: function(tour) {
					$("a[href=#red]").click();
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .tab-pane.active .bhoechie-tab-container",
				element: "#add_widget .tab-pane.active .list-group-item.active",
				placement: "right",
				title: _("Selecting Dashboard Widgets"),
				content: _("Dashboard Widgets are sorted into categories on the left. These widgets will appear directly on your dashboard. You can click on any category to get a listing of the widgets available"),
				previous: -1
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .tab-pane.active .bhoechie-tab-container",
				element: "#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first",
				placement: "bottom",
				title: _("Selecting Widgets"),
				content: _("Widgets are listed on the right. The titles and descriptions will be shown for each widget"),
				onShown: function(tour) {
					$("#add_widget .modal-body").scrollTop(0);
					var myStep = tour.getCurrentStep();
					$("#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first .add-widget-button").one("click",function() {
						tour.goTo(myStep + 2);
					});
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first",
				element: "#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first .add-widget-button",
				placement: "right",
				title: _("Adding Widgets"),
				content: sprintf(_("Clicking the '%s' symbol will add this widget to the currently active dashboard."),'(+)')+"<br><br>"+_("Click this symbol to continue"),
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$("#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first .add-widget-button").off("click");
					$(document).one("post-body.widgets", function(e, widget_id) {
						$(".grid-stack-item[data-id="+widget_id+"]").addClass("tour-step");
						tour.goTo(step + 1);
					});
				}
			}, {
				element: ".grid-stack-item.tour-step",
				placement: "right",
				title: _("Dashboard Widget"),
				content: _("Widgets are placed automatically on the dashboard after they have been added")
			}, {
				element: ".grid-stack-item.tour-step .widget-title",
				placement: "bottom",
				title: _("Widget Placement"),
				content: _("Widgets can be moved around by clicking and dragging on the title bar"),
				onNext: function(tour) {
					$(".grid-stack-item.tour-step .ui-icon-gripsmall-diagonal-se").show();
				}
			}, {
				element: ".grid-stack-item.tour-step .ui-icon-gripsmall-diagonal-se",
				placement: "right",
				title: _("Widget Size"),
				content: _("Widgets can be resized by placing your mouse near the corner of the widget. Click and drag to resize the widget.")+"<br><br>"+_("Note: some widgets have size restrictions!"),
				onNext: function(tour) {
					$(".grid-stack-item.tour-step .ui-icon-gripsmall-diagonal-se").hide();
				}
			}, {
				element: ".grid-stack-item.tour-step .widget-title .lock-widget",
				placement: "right",
				title: _("Widget Locking"),
				content: _("Widgets can be locked into place to prevent their movement")
			}, {
				element: ".grid-stack-item.tour-step .widget-title .edit-widget",
				placement: "right",
				title: _("Widget Settings"),
				content: _("Widgets settings can be changed by clicking this icon")
			}, {
				element: ".grid-stack-item.tour-step .widget-title .remove-widget",
				placement: "right",
				title: _("Widget Removal"),
				content: sprintf(_("Widgets can also be removed by clicking the '%s' symbol"),'X')
			}, {
				element: ".dashboard-menu.active .lock-dashboard",
				placement: "bottom",
				title: _("Dashboard Locking"),
				content: sprintf(_("All widgets in a dashboard can also be locked globally by clicking the '%s' symbol on the dashboard tab"),'X')
			}, {
				element: ".navbar.navbar-inverse.navbar-fixed-left",
				placement: "right",
				title: _("Side Bar Widgets"),
				content: _("This is where side bar widgets live. Side bar widgets do not change when you change dashboards. They are global throughout UCP")
			}, {
				backdrop: true,
				backdropContainer: "#side_bar_content",
				element: "#side_bar_content .add-widget",
				placement: "right",
				title: _("Adding Side Bar Widgets"),
				content: sprintf(_("Side bar Widgets can also be added by clicking the '%s' symbol. These appear under the '%s' symbol in this side bar"),'(+)','(+)')+"<br><br>"+_("Click this symbol to continue"),
				reflex: true,
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$("#add_widget").one("shown.bs.modal", function() {
						tour.goTo(step + 1);
					});
					$(".tour-step-background").css("background-color","white");
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .modal-body",
				element: "#add_widget .modal-body .nav-tabs",
				placement: "right",
				title: _("Selecting Side Bar Widgets"),
				content: _("Side Bar widgets are grouped in a single category called 'Side Bar Widgets'"),
				onShown: function(tour) {
					$("#add_widget .modal-body").scrollTop(0);
					$("a[href=#small]").click();
				}
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .tab-pane.active .bhoechie-tab-container",
				element: "#add_widget .tab-pane.active .list-group-item.active",
				placement: "bottom",
				title: _("Selecting Small Widgets"),
				content: _("Small Widgets are listed on the right. The titles and descriptions will be shown for each widget"),
			}, {
				backdrop: true,
				backdropContainer: "#add_widget .tab-pane.active .bhoechie-tab-container",
				element: "#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first",
				placement: "bottom",
				title: _("Selecting Widgets"),
				content: _("Widgets are listed on the right. The titles and descriptions will be shown for each widget"),
				onShown: function(tour) {
					$("#add_widget .modal-body").scrollTop(0);
					var myStep = tour.getCurrentStep();
					$("#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first .add-small-widget-button").one("click",function() {
						tour.goTo(myStep + 2);
					});
				}
			}, {
				backdrop: true,
				orphan: true,
				backdropContainer: "#add_widget .tab-pane.active .bhoechie-tab-content.active .ibox-content-widget:first",
				element: ".add-small-widget-button",
				placement: "right",
				title: _("Adding Small Widgets"),
				content: sprintf(_("Clicking the '%s' symbol will add this small widget to the display. It will be visible on all dashboards"),'(+)')+"<br><br>"+_("Click this symbol to continue"),
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$(document).one("post-body.addsimplewidget", function(e, widget_id) {
						$(".custom-widget[data-widget_id="+widget_id+"]").addClass("tour-step");
						tour.goTo(step + 1);
					});
				}
			}, {
				element: "#side_bar_content .custom-widget.tour-step",
				placement: "right",
				title: _("Small Widget Display"),
				content: _("Once a small widget has been added it will show up in the left sidebar")+"<br><br>"+_("Click the widget's icon to continue"),
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$(document).one("post-body.simplewidget", function(e, widget_id, widget_type_id) {
						tour.goTo(step + 1);
					});
				}
			}, {
				element: ".widget-extra-menu:visible .small-widget-content",
				placement: "right",
				title: _("Small Widget Display"),
				content: _("The widget's content is displayed here")
			}, {
				element: ".widget-extra-menu:visible .remove-small-widget",
				placement: "top",
				title: _("Small Widget Display"),
				content: _("To remove this widget from the side bar click 'Remove Widget'")
			}, {
				element: ".widget-extra-menu:visible .close-simple-widget-menu",
				placement: "bottom",
				title: _("Small Widget Display"),
				content: sprintf(_("To just close/hide the widget's content click the '%s' symbol"),'(X)')+"<br><br>"+_("Click this symbol to continue"),
				next: -1,
				onShown: function(tour) {
					var step = tour.getCurrentStep();
					$(document).one("post-body.closesimplewidget", function(e, widget_id, widget_type_id) {
						tour.goTo(step + 1);
					});
				}
			}, {
				element: "#side_bar_content .settings-widget",
				placement: "right",
				title: _("User Settings"),
				content: _("Your specific settings are defined when clicking the 'gear' icon in the side bar")
			}, {
				element: "#side_bar_content .logout-widget",
				placement: "right",
				title: _("Logout"),
				content: _("Your can logout of UCP by clicking this logout button")
			}, {
				orphan: true,
				title: _("End of tour"),
				content: sprintf(_("You have finished the tour of User Control Panel for %s 14. You can restart this tour at any time in your User Settings"),UCP.Modules.Ucptour.staticsettings.brand)
			}
		]
	});
	if(UCP.Modules.Ucptour.staticsettings.show) {
		// Initialize the tour
		UCP.Modules.Ucptour.tour.init();

		// Start the tour
		UCP.Modules.Ucptour.tour.start();
	}
});

var VoicemailC = UCPMC.extend({
	init: function() {
		this.loaded = null;
		this.recording = false;
		this.recorder = null;
		this.recordTimer = null;
		this.startTime = null;
		this.soundBlobs = {};
		this.placeholders = [];
	},
	resize: function(widget_id) {
		$(".grid-stack-item[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('resetView',{height: $(".grid-stack-item[data-id='"+widget_id+"'] .widget-content").height()});
	},
	findmeFollowState: function() {
		if (!$("#vmx-p1_enable").is(":checked") && $("#ddial").is(":checked") && $("#vmx-state").is(":checked")) {
			$("#vmxerror").text(_("Find me Follow me is enabled when VmX locator option 1 is disabled. This means VmX Locator will be skipped, instead going directly to Find Me/Follow Me")).addClass("alert-danger").fadeIn("fast");
		} else {
			$("#vmxerror").fadeOut("fast");
		}
	},
	saveVmXSettings: function(ext, key, value) {
		var data = { ext: ext, settings: { key: key, value: value } };
		$.post( UCP.ajaxUrl + "?module=voicemail&command=vmxsettings", data, function( data ) {
			if (data.status) {
				$("#vmxmessage").text(data.message).addClass("alert-" + data.alert).fadeIn("fast", function() {

				});
			} else {
				return false;
			}
		});
	},
	poll: function(data) {
		if (typeof data.boxes === "undefined") {
			return;
		}

		var notify = false;
		var self = this;

		/**
		 * Check all extensions and boxes at once.
		 */
		$.ajax({
			type: "POST",
			url: UCP.ajaxUrl + "?module=voicemail&command=checkextensions",
			async: false,
			data: data.boxes,
			success: function(vm_data){				
				window.vm_data = vm_data;
			},
			error: function (xhr, ajaxOptions, thrownError) {
                console.error('Unable to check extensions', thrownError, xhr);
            },
		  });

		async.forEachOf(window.vm_data, function (value, extension, callback) {	
			var el = $(".grid-stack-item[data-rawname='voicemail'][data-widget_type_id='"+extension+"'] .mailbox");
			self.refreshFolderCount(extension);
			if(el.length && el.data("inbox").status != value.status || window.update_table == true) {
				notify = false;
				if(el.data("inbox") < value){
					notify = true;
				}
				el.data("inbox",value);	
				if((typeof Cookies.get('vm-refresh-'+extension) === "undefined" && (typeof Cookies.get('vm-refresh-'+extension) === "undefined" || Cookies.get('vm-refresh-'+extension) == 1)) || Cookies.get('vm-refresh-'+extension) == 1) {
					$(".grid-stack-item[data-rawname='voicemail'][data-widget_type_id='"+extension+"'] .voicemail-grid").bootstrapTable('refresh',{silent: true});
				}
			}			
			callback();
		}, function(err) {
			if( err ) {
			} else if(notify) {
				voicemailNotification = new Notify("Voicemail", {
					body: _("You have a new voicemail"),
					icon: "modules/Voicemail/assets/images/mail.png"
				});
				if (UCP.notify) {
					voicemailNotification.show();
				}
			}
		});
	},
	displayWidgetSettings: function(widget_id, dashboard_id) {
		var self = this,
				extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");

		/* Settings changes binds */
		$("#widget_settings .widget-settings-content input[type!='checkbox'][id!=vm-refresh]").change(function() {
			$(this).blur(function() {
				self.saveVMSettings(extension);
				$(this).off("blur");
			});
		});
		$("#widget_settings .widget-settings-content input[type='checkbox'][id!=vm-refresh]").change(function() {
			self.saveVMSettings(extension);
		});

		$("#widget_settings .widget-settings-content input[id=vm-refresh]").change(function() {
			Cookies.remove('vm-refresh-'+extension, {path: ''});
			if($(this).is(":checked")) {
				Cookies.set('vm-refresh-'+extension, 1);
			} else {
				Cookies.set('vm-refresh-'+extension, 0);
			}
		});
		if((typeof Cookies.get('vm-refresh-'+extension) === "undefined" && (typeof Cookies.get('vm-refresh-'+extension) === "undefined" || Cookies.get('vm-refresh-'+extension) == 1)) || Cookies.get('vm-refresh-'+extension) == 1) {
			$("#widget_settings .widget-settings-content input[id=vm-refresh]").prop("checked",true);
		} else {
			$("#widget_settings .widget-settings-content input[id=vm-refresh]").prop("checked",false);
		}
		$("#widget_settings .widget-settings-content input[id=vm-refresh]").bootstrapToggle('destroy');
		$("#widget_settings .widget-settings-content input[id=vm-refresh]").bootstrapToggle({
			on: _("Enable"),
			off: _("Disable")
		});
		this.greetingsDisplay(extension);
		this.bindGreetingPlayers(extension);
		$("#widget_settings .vmx-setting").change(function() {
			var name = $(this).attr("name"),
					val = $(this).val();
			if($(this).attr("type") == "checkbox") {
				self.saveVmXSettings(extension, name, $(this).is(":checked"));
			} else {
				self.saveVmXSettings(extension, name, val);
			}

		});
	},
	displayWidget: function(widget_id, dashboard_id) {
		var self = this,
				extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");
		$(".grid-stack-item[data-id='"+widget_id+"'] .voicemail-grid").one("post-body.bs.table", function() {
			setTimeout(function() {
				self.resize(widget_id);
			},250);
		});

		$("div[data-id='"+widget_id+"'] .voicemail-grid").on("post-body.bs.table", function (e) {
			$("div[data-id='"+widget_id+"'] .voicemail-grid a.listen").click(function() {
				var id = $(this).data("id"),
						select = '';
				$.each(self.staticsettings.extensions, function(i,v) {
					select = select + "<option value='"+v+"'>"+v+"</option>";
				});
				UCP.showDialog(_("Listen to Voicemail"),
					_("On") + ':</label><select class="form-control" data-toggle="select" id="VMto">'+select+"</select>",
					'<button class="btn btn-default" id="listenVM">' + _("Listen") + "</button>",
					function() {
						$("#listenVM").click(function() {
							var recpt = $("#VMto").val();
							self.listenVoicemail(id,extension,recpt);
						});
						$("#VMto").keypress(function(event) {
							if (event.keyCode == 13) {
								var recpt = $("#VMto").val();
								self.listenVoicemail(id,extension,recpt);
							}
						});
					}
				);
			});
			$("div[data-id='"+widget_id+"'] .voicemail-grid .clickable").click(function(e) {
				var text = $(this).text();
				if (UCP.validMethod("Contactmanager", "showActionDialog")) {
					UCP.Modules.Contactmanager.showActionDialog("number", text, "phone");
				}
			});
			$("div[data-id='"+widget_id+"'] .voicemail-grid a.forward").click(function() {
				var id = $(this).data("id"),
						select = '';

				$.each(self.staticsettings.mailboxes, function(i,v) {
					select = select + "<option value='"+v+"'>"+v+"</option>";
				});
				UCP.showDialog(_("Forward Voicemail"),
					_("To")+':</label><select class="form-control" id="VMto">'+select+'</select>',
					'<button class="btn btn-default" id="forwardVM">' + _("Forward") + "</button>",
					function() {
						$("#forwardVM").click(function() {
							var recpt = $("#VMto").val();
							self.forwardVoicemail(id,extension,recpt, function(data) {
								if(data.status) {
									UCP.showAlert(sprintf(_("Successfully forwarded voicemail to %s"),recpt));
									UCP.closeDialog();
								}
							});
						});
						$("#VMto").keypress(function(event) {
							if (event.keyCode == 13) {
								var recpt = $("#VMto").val();
								self.forwardVoicemail(id,extension,recpt, function(data) {
									if(data.status) {
										UCP.showAlert(sprintf(_("Successfully forwarded voicemail to %s"),recpt));
										UCP.closeDialog();
									}
								});
							}
						});
					}
				);
			});
			$("div[data-id='"+widget_id+"'] .voicemail-grid a.delete").click(function() {
				var extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");
				var id = $(this).data("id");
				UCP.showConfirm(_("Are you sure you wish to delete this voicemail?"),'warning',function() {
					self.deleteVoicemail(id, extension, function(data) {
						if(data.status) {
							$("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('remove', {field: "msg_id", values: [String(id)]});
						}
					});
				});
			});
			self.bindPlayers(widget_id);
		});
		$("div[data-id='"+widget_id+"'] .voicemail-grid").on("check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table", function () {
			var sel = $(this).bootstrapTable('getAllSelections'),
					dis = true;
			if(sel.length) {
				dis = false;
			}
			$("div[data-id='"+widget_id+"'] .delete-selection").prop("disabled",dis);
			$("div[data-id='"+widget_id+"'] .forward-selection").prop("disabled",dis);
			$("div[data-id='"+widget_id+"'] .move-selection").prop("disabled",dis);
		});

		$("div[data-id='"+widget_id+"'] .folder").click(function() {
			$("div[data-id='"+widget_id+"'] .folder").removeClass("active");
			$(this).addClass("active");
			folder = $(this).data("folder");
			$("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('refreshOptions',{
				url: UCP.ajaxUrl+'?module=voicemail&command=grid&folder='+folder+'&ext='+extension
			});
		});

		$("div[data-id='"+widget_id+"'] .move-selection").click(function() {
			var opts = '', cur = (typeof $.url().param("folder") !== "undefined") ? $.url().param("folder") : "INBOX", sel = $("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('getAllSelections');
			$.each($("div[data-id='"+widget_id+"'] .folder-list .folder"), function(i, v){
				var folder = $(v).data("folder");
				if(folder != cur) {
					opts += '<option>'+$(v).data("name")+'</option>';
				}
			});
			UCP.showDialog(_("Move Voicemail"),
				_("To")+':</label><select class="form-control" data-toggle="select" id="VMmove">'+opts+"</select>",
				'<button class="btn btn-default" id="moveVM"><span id="spin"></span>&nbsp;&nbsp;' + _("Move") + "</button>",
				function() {
					var total = sel.length;
					$("#moveVM").click(function() {
						$("#moveVM").prop("disabled",true);
						$("#spin").html('<i class="fa fa-spinner fa-spin"></i>')
						setTimeout(function () {
							let data = [];
							Object.keys(sel).forEach(key => {
								data.push({
									msg: sel[key].msg_id,
									folder: $("#VMmove").val(),
									ext: extension
								});
							})
							self.moveVoicemailBulk(data, extension, function (data) {
								if (data.status) {
									self.rebuildVM(extension);
									if (data.moveStatus.includes(false)) {
										UCP.showAlert('Not able to move some of the voicemails.');
									}
									setTimeout(function () {
										UCP.closeDialog();
									}, 2000);
								} else {
									$("#moveVM").prop("disabled", false);
									UCP.showAlert(data.error);
								}
							})
						}, 50);
					});
					$("#VMmove").keypress(function(event) {
						if (event.keyCode == 13) {
							$("#moveVM").prop("disabled",true);
							async.forEachOf(sel, function (v, i, callback) {
								self.moveVoicemail(v.msg_id, $("#VMmove").val(), extension, function(data) {
									if(data.status) {
										$("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('remove', {field: "msg_id", values: [String(v.msg_id)]});
									}
									callback();
								})
							}, function(err) {
								if( err ) {
									$("#moveVM").prop("disabled",false);
									UCP.showAlert(err);
								} else {
									UCP.closeDialog();
									self.rebuildVM(extension);
								}
							});
						}
					});
					$(".delete-selection").prop("disabled",true);
					$(".forward-selection").prop("disabled",true);
					$(".move-selection").prop("disabled",true);
				}
			);
		});
		$("div[data-id='" + widget_id + "'] .delete-selection").click(function () {
			$('#modal_confirm_button').attr("data-dismiss", '');
			UCP.showConfirm(_("Are you sure you wish to delete these voicemails?"),'warning',function() {
				var extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");
				var sel = $("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('getAllSelections');
				var accept = $("#modal_confirm_button").text();
				$("#modal_confirm_button").html('<i class="fa fa-spinner fa-spin"></i>&nbsp;'+ accept);
				setTimeout(function () {
					let data = [];
					Object.keys(sel).forEach(key => {
						data.push({
							msg: sel[key].msg_id,
							folder: $("#VMmove").val(),
							ext: extension
						});
					});
					self.deleteVoicemailBulk(data, extension, function (data) {
						if (data.status) {
							self.rebuildVM(extension);
							if (data.deleteStatus.includes(false)) {
								UCP.showAlert('Not able to delete some of the voicemails.');
							}
							setTimeout(function () {
								$("#modal_confirm_button").html(accept);
								$("#confirm_modal").modal('toggle');
							}, 2000);
						} else {
							UCP.showAlert(data.error);
						}
					});
				}, 50);
				$(".delete-selection").prop("disabled",true);
				$(".forward-selection").prop("disabled",true);
				$(".move-selection").prop("disabled",true);
			});
		});
		$("div[data-id='"+widget_id+"'] .forward-selection").click(function() {
			var sel = $("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('getAllSelections');
			UCP.showDialog(_("Forward Voicemail"),
				_("To")+":</label><input type='text' class='form-control' id='VMto'>",
				'<button class="btn btn-default" id="forwardVM">' + _("Forward") + "</button>",
				function() {
					$("#forwardVM").click(function() {
						setTimeout(function() {
							var recpt = $("#VMto").val();
							$.each(sel, function(i, v){
								self.forwardVoicemail(v.msg_id,extension,recpt, function(data) {
									if(data.status) {
										UCP.showAlert(sprintf(_("Successfully forwarded voicemail to %s"),recpt));
										$("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('uncheckAll');
										UCP.closeDialog();
									}
								});
							});
						}, 50);
					});
					$("#VMto").keypress(function(event) {
						if (event.keyCode == 13) {
							var recpt = $("#VMto").val();
							$.each(sel, function(i, v){
								self.forwardVoicemail(v.msg_id,extension,recpt, function(data) {
									if(data.status) {
										UCP.showAlert(sprintf(_("Successfully forwarded voicemail to %s"),recpt));
										$("div[data-id='"+widget_id+"'] .voicemail-grid").bootstrapTable('uncheckAll');
										UCP.closeDialog();
									}
								});
							});
						}
					});
				}
			);
		});


		$("div[data-id='"+widget_id+"'] .voicemail-grid .clickable").click(function(e) {
			var text = $(this).text();
			if (UCP.validMethod("Contactmanager", "showActionDialog")) {
				UCP.Modules.Contactmanager.showActionDialog("number", text, "phone");
			}
		});
	},
	greetingsDisplay: function(extension) {
		var self = this;
		$("#widget_settings .recording-controls .save").click(function() {
			var id = $(this).data("id");
			self.saveRecording(extension,id);
		});
		$("#widget_settings .recording-controls .delete").click(function() {
			var id = $(this).data("id");
			self.deleteRecording(extension,id);
		});
		$("#widget_settings .file-controls .record, .jp-record").click(function() {
			var id = $(this).data("id");
			self.recordGreeting(extension,id);
		});
		$("#widget_settings .file-controls .delete").click(function() {
			var id = $(this).data("id");
			self.deleteGreeting(extension,id);
		});
		$("#widget_settings .filedrop").on("dragover", function(event) {
			if (event.preventDefault) {
				event.preventDefault(); // Necessary. Allows us to drop.
			}
			$(this).addClass("hover");
		});
		$("#widget_settings .filedrop").on("dragleave", function(event) {
			$(this).removeClass("hover");
		});

		$("#widget_settings .greeting-control .jp-audio-freepbx").on("dragstart", function(event) {
			event.originalEvent.dataTransfer.effectAllowed = "move";
			event.originalEvent.dataTransfer.setData("type", $(this).data("type"))
			$(this).fadeTo( "fast", 0.5);
		});
		$("#widget_settings .greeting-control .jp-audio-freepbx").on("dragend", function(event) {
			$(this).fadeTo( "fast", 1.0);
		});
		$("#widget_settings .filedrop").on("drop", function(event) {
			if (event.originalEvent.dataTransfer.files.length === 0) {
				if (event.stopPropagation) {
					event.stopPropagation(); // Stops some browsers from redirecting.
				}
				if (event.preventDefault) {
					event.preventDefault(); // Necessary. Allows us to drop.
				}
				$(this).removeClass("hover");
				var target = $(this).data("type"),
				source = event.originalEvent.dataTransfer.getData("type");
				if (source === "") {
					alert(_("Not a valid Draggable Object"));
					return false;
				}
				if (source == target) {
					alert(_("Dragging to yourself is not allowed"));
					return false;
				}
				var data = { ext: extension, source: source, target: target },
				message = $(this).find(".message");
				message.text(_("Copying..."));
				$.post( UCP.ajaxUrl + "?module=voicemail&command=copy", data, function( data ) {
						if (data.status) {
							$("#"+target+" .filedrop .pbar").css("width", "0%");
							$("#"+target+" .filedrop .message").text($("#"+target+" .filedrop .message").data("message"));
							$("#freepbx_player_" + target).removeClass("greet-hidden");
							self.toggleGreeting(target, true);
						} else {
							return false;
						}
				});
			} else {}
		});
		$("#widget_settings .greeting-control").each(function() {
			var id = $(this).attr("id");
			$("#"+id+" input[type=\"file\"]").fileupload({
				url: UCP.ajaxUrl + "?module=voicemail&command=upload&type="+id+"&ext=" + extension,
				dropZone: $("#"+id+" .filedrop"),
				dataType: "json",
				add: function(e, data) {
					//TODO: Need to check all supported formats
					var sup = "\.("+self.staticsettings.supportedRegExp+")$",
							patt = new RegExp(sup),
							submit = true;
					$.each(data.files, function(k, v) {
						if(!patt.test(v.name)) {
							submit = false;
							UCP.showAlert(_("Unsupported file type"));
							return false;
						}
					});
					if(submit) {
						$("#"+id+" .filedrop .message").text(_("Uploading..."));
						data.submit();
					}
				},
				done: function(e, data) {
					if (data.result.status) {
						$("#"+id+" .filedrop .pbar").css("width", "0%");
						$("#"+id+" .filedrop .message").text($("#"+id+" .filedrop .message").data("message"));
						$("#freepbx_player_"+id).removeClass("greet-hidden");
						self.toggleGreeting(id, true);
					} else {
						console.warn(data.result.message);
					}
				},
				progressall: function(e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$("#"+id+" .filedrop .pbar").css("width", progress + "%");
				},
				drop: function(e, data) {
					$("#"+id+" .filedrop").removeClass("hover");
				}
			});
		});
		//If browser doesnt support get user media requests then just hide it from the display
		if (!Modernizr.getusermedia) {
			$("#widget_settings .jp-record-wrapper").hide();
			$("#widget_settings .record-greeting-btn").hide();
		} else {
			$("#widget_settings .jp-record-wrapper").show();
			$("#widget_settings .jp-stop-wrapper").hide();
			$("#widget_settings .record-greeting-btn").show();
		}
	},
	//Delete a voicemail greeting
	deleteGreeting: function(extension,type) {
		var self = this, data = { msg: type, ext: extension };
		$.post( UCP.ajaxUrl + "?module=voicemail&command=delete", data, function( data ) {
			if (data.status) {
				$("#freepbx_player_" + type).jPlayer( "clearMedia" );
				self.toggleGreeting(type, false);
			} else {
				return false;
			}
		});
	},
	refreshFolderCount: function(extension) {
		var data = window.vm_data[extension];
		if(data.status) {
			window.update_table = false;
			$.each(data.folders, function(i,v) {		
				cur_val = $(".grid-stack-item[data-rawname='voicemail'][data-widget_type_id="+extension+"] .mailbox .folder-list .folder[data-name='"+v.name+"'] .badge").text();
				if(cur_val != v.count){
					window.update_table = true;
				}
				$(".grid-stack-item[data-rawname='voicemail'][data-widget_type_id="+extension+"] .mailbox .folder-list .folder[data-name='"+v.name+"'] .badge").text(v.count);				
			});
		}

	},
	rebuildVM: function(extension){
		var data = {
			ext: extension
		},
		self = this;
		$.ajax({
			type: "POST",
			url: UCP.ajaxUrl + "?module=voicemail&command=rebuildVM",
			data: data,
			success: function(data) {
				self.refreshFolderCount(extension);
				if(typeof callback === "function") {
					callback(data);
				}	
			},
			error: function(data) {
				if(typeof callback === "function") {
					callback({status: false});
				}
			}
		});
	},
	moveVoicemail: function(msgid, folder, extension, callback) {
		var data = {
			msg: msgid,
			folder: folder,
			ext: extension
		},
		self = this;
		$.ajax({
			type: "POST",
			url: UCP.ajaxUrl + "?module=voicemail&command=moveToFolder",
			data: data,
			async: false,
			success: function(data) {
				self.refreshFolderCount(extension);
				if(typeof callback === "function") {
					callback(data);
				}	
			},
			error: function(data) {
				if(typeof callback === "function") {
					callback({status: false});
				}
			}
		});
	},
	moveVoicemailBulk: function (data, extension, callback) {
		var formData = new FormData();
		formData.append('data', JSON.stringify(data));
		self = this;
		$.ajax({
			type: "POST",
			enctype: 'multipart/form-data',
			url: UCP.ajaxUrl + "?module=voicemail&command=moveToFolderBulk",
			data: formData,
			async: false,
			processData: false,
			contentType: false,
			success: function (data) {
				self.refreshFolderCount(extension);
				if (typeof callback === "function") {
					callback(data);
				}
			},
			error: function (data) {
				if (typeof callback === "function") {
					callback({ status: false, error: data });
				}
			}
		});
	},
	forwardVoicemail: function(msgid, extension, recpt, callback) {
		var data = {
			id: msgid,
			to: recpt
		};
		$.post( UCP.ajaxUrl + "?module=voicemail&command=forward&ext="+extension, data, function(data) {
			if(typeof callback === "function") {
				callback(data);
			}
		}).fail(function() {
			if(typeof callback === "function") {
				callback({status: false});
			}
		});
	},
	//Used to delete a voicemail message
	deleteVoicemail: function(msgid, extension, callback) {
		var data = {
			msg: msgid,
			ext: extension
		},
		self = this;
		$.ajax({
			type: "POST",
			url: UCP.ajaxUrl + "?module=voicemail&command=delete",
			data: data,
			async: false,
			success: function(data) {
				self.refreshFolderCount(extension);
				if(typeof callback === "function") {
					callback(data);
				}	
			},
			error: function(data) {
				if(typeof callback === "function") {
					callback({status: false});
				}
			}
		});
	},
	deleteVoicemailBulk: function (data, extension, callback) {
		var formData = new FormData();
		formData.append('data', JSON.stringify(data));
		self = this;
		$.ajax({
			type: "POST",
			enctype: 'multipart/form-data',
			url: UCP.ajaxUrl + "?module=voicemail&command=deleteBulk",
			data: formData,
			async: false,
			processData: false,
			contentType: false,
			success: function (data) {
				self.refreshFolderCount(extension);
				if (typeof callback === "function") {
					callback(data);
				}
			},
			error: function (data) {
				if (typeof callback === "function") {
					callback({ status: false });
				}
			}
		});
	},
	//Toggle the html5 player for greeting
	toggleGreeting: function(type, visible) {
		if (visible === true) {
			$("#" + type + " button.delete").show();
			$("#jp_container_" + type).removeClass("greet-hidden");
			$("#freepbx_player_"+ type).jPlayer( "clearMedia" );
		} else {
			$("#" + type + " button.delete").hide();
			$("#jp_container_" + type).addClass("greet-hidden");
		}
	},
	//Save Voicemail Settings
	saveVMSettings: function(extension) {
		$("#message").fadeOut("slow");
		var data = { ext: extension };
		$("div[data-rawname='voicemail'] .widget-settings-content input[type!='checkbox']").each(function( index ) {
			data[$( this ).attr("name")] = $( this ).val();
		});
		$("div[data-rawname='voicemail'] .widget-settings-content input[type='checkbox']").each(function( index ) {
			data[$( this ).attr("name")] = $( this ).is(":checked");
		});
		$.post( UCP.ajaxUrl + "?module=voicemail&command=savesettings", data, function( data ) {
			if (data.status) {
				$("#message").addClass("alert-success");
				$("#message").text(_("Your settings have been saved"));
				$("#message").fadeIn( "slow", function() {
					setTimeout(function() { $("#message").fadeOut("slow"); }, 2000);
				});
			} else {
				$("#message").addClass("alert-error");
				$("#message").text(data.message);
				return false;
			}
		});
	},
	recordGreeting: function(extension,type) {
		var self = this;
		if (!Modernizr.getusermedia) {
			UCP.showAlert(_("Direct Media Recording is Unsupported in your Broswer!"));
			return false;
		}
		counter = $("#jp_container_" + type + " .jp-current-time");
		title = $("#jp_container_" + type + " .title-text");
		filec = $("#" + type + " .file-controls");
		recc = $("#" + type + " .recording-controls");
		var controls = $("#jp_container_" + type + " .jp-controls");
		controls.toggleClass("recording");
		if (self.recording) {
			clearInterval(self.recordTimer);
			title.text(_("Recorded Message"));
			self.recorder.stop();
			self.recorder.exportWAV(function(blob) {
				self.soundBlobs[type] = blob;
				var url = (window.URL || window.webkitURL).createObjectURL(blob);
				$("#freepbx_player_" + type).jPlayer( "clearMedia" );
				$("#freepbx_player_" + type).jPlayer( "setMedia", {
					wav: url
				});
			});
			self.recording = false;
			recc.show();
			filec.hide();
		} else {
			window.AudioContext = window.AudioContext || window.webkitAudioContext;

			var context = new AudioContext();

			var gUM = Modernizr.prefixed("getUserMedia", navigator);
			gUM({ audio: true }, function(stream) {
				var mediaStreamSource = context.createMediaStreamSource(stream);
				self.recorder = new Recorder(mediaStreamSource,{ workerPath: "assets/js/recorderWorker.js" });
				self.recorder.record();
				self.startTime = new Date();
				self.recordTimer = setInterval(function () {
					var mil = (new Date() - self.startTime);
					var temp = (mil / 1000);
					var min = ("0" + Math.floor((temp %= 3600) / 60)).slice(-2);
					var sec = ("0" + Math.round(temp % 60)).slice(-2);
					counter.text(min + ":" + sec);
				}, 1000);
				title.text(_("Recording..."));
				self.recording = true;
				$("#jp_container_" + type).removeClass("greet-hidden");
				recc.hide();
				filec.show();
			}, function(e) {
				UCP.showAlert(_("Your Browser Blocked The Recording, Please check your settings"));
				self.recording = false;
			});
		}
	},
	saveRecording: function(extension,type) {
		var self = this,
				filec = $("#" + type + " .file-controls"),
				recc = $("#" + type + " .recording-controls");
				title = $("#" + type + " .title-text");
		if (self.recording) {
			UCP.showAlert(_("Stop the Recording First before trying to save"));
			return false;
		}
		if ((typeof(self.soundBlobs[type]) !== "undefined") && self.soundBlobs[type] !== null) {
			$("#" + type + " .filedrop .message").text(_("Uploading..."));
			var data = new FormData();
			data.append("file", self.soundBlobs[type]);
			$.ajax({
				type: "POST",
				url: UCP.ajaxUrl + "?module=voicemail&command=record&type=" + type + "&ext=" + extension,
				xhr: function()
				{
					var xhr = new window.XMLHttpRequest();
					//Upload progress
					xhr.upload.addEventListener("progress", function(evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total,
							progress = Math.round(percentComplete * 100);
							$("#" + type + " .filedrop .pbar").css("width", progress + "%");
						}
					}, false);
					return xhr;
				},
				data: data,
				processData: false,
				contentType: false,
				success: function(data) {
					$("#" + type + " .filedrop .message").text($("#" + type + " .filedrop .message").data("message"));
					$("#" + type + " .filedrop .pbar").css("width", "0%");
					self.soundBlobs[type] = null;
					$("#freepbx_player_" + type).jPlayer("supplied",self.staticsettings.supportedHTML5);
					$("#freepbx_player_" + type).jPlayer( "clearMedia" );
					title.text(title.data("title"));
					filec.show();
					recc.hide();
				},
				error: function() {
					//error
					filec.show();
					recc.hide();
				}
			});
		}
	},
	deleteRecording: function(extension,type) {
		var self = this,
				filec = $("#" + type + " .file-controls"),
				recc = $("#" + type + " .recording-controls");
		if (self.recording) {
			UCP.showAlert(_("Stop the Recording First before trying to delete"));
			return false;
		}
		if ((typeof(self.soundBlobs[type]) !== "undefined") && self.soundBlobs[type] !== null) {
			self.soundBlobs[type] = null;
			$("#freepbx_player_" + type).jPlayer("supplied",self.staticsettings.supportedHTML5);
			$("#freepbx_player_" + type).jPlayer( "clearMedia" );
			title.text(title.data("title"));
			filec.show();
			recc.hide();
			self.toggleGreeting(type, false);
		} else {
			UCP.showAlert(_("There is nothing to delete"));
		}
	},
	//This function is here solely because firefox caches media downloads so we have to force it to not do that
	generateRandom: function() {
		return Math.round(new Date().getTime() / 1000);
	},
	dateFormatter: function(value, row, index) {
		return UCP.dateTimeFormatter(value);
	},
	listenVoicemail: function(msgid, extension, recpt) {
		var data = {
			id: msgid,
			to: recpt
		};
		$.post( UCP.ajaxUrl + "?module=voicemail&command=callme&ext="+extension, data, function( data ) {
			UCP.closeDialog();
		});
	},
	playbackFormatter: function (value, row, index) {
		var settings = UCP.Modules.Voicemail.staticsettings,
				rand = Math.floor(Math.random() * 10000);
		if(settings.showPlayback == "0" || row.duration === 0) {
			return '';
		}
		return '<div id="jquery_jplayer_'+row.msg_id+'-'+rand+'" class="jp-jplayer" data-container="#jp_container_'+row.msg_id+'-'+rand+'" data-id="'+row.msg_id+'"></div><div id="jp_container_'+row.msg_id+'-'+rand+'" data-player="jquery_jplayer_'+row.msg_id+'-'+rand+'" class="jp-audio-freepbx" role="application" aria-label="media player">'+
			'<div class="jp-type-single">'+
				'<div class="jp-gui jp-interface">'+
					'<div class="jp-controls">'+
						'<i class="fa fa-play jp-play"></i>'+
						'<i class="fa fa-undo jp-restart"></i>'+
					'</div>'+
					'<div class="jp-progress">'+
						'<div class="jp-seek-bar progress">'+
							'<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>'+
							'<div class="progress-bar progress-bar-striped active" style="width: 100%;"></div>'+
							'<div class="jp-play-bar progress-bar"></div>'+
							'<div class="jp-play-bar">'+
								'<div class="jp-ball"></div>'+
							'</div>'+
							'<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>'+
						'</div>'+
					'</div>'+
					'<div class="jp-volume-controls">'+
						'<i class="fa fa-volume-up jp-mute"></i>'+
						'<i class="fa fa-volume-off jp-unmute"></i>'+
					'</div>'+
				'</div>'+
				'<div class="jp-no-solution">'+
					'<span>Update Required</span>'+
					sprintf(_("You are missing support for playback in this browser. To fully support HTML5 browser playback you will need to install programs that can not be distributed with the PBX. If you'd like to install the binaries needed for these conversions click <a href='%s'>here</a>"),"http://wiki.freepbx.org/display/FOP/Installing+Media+Conversion+Libraries")+
				'</div>'+
			'</div>'+
		'</div>';
	},
	durationFormatter: function (value, row, index) {
		return (typeof UCP.durationFormatter === 'function') ? UCP.durationFormatter(value) : sprintf(_("%s seconds"),value);
	},
	controlFormatter: function (value, row, index) {
		var html = '<a class="listen" alt="'+_('Listen on your handset')+'" data-id="'+row.msg_id+'"><i class="fa fa-phone"></i></a>'+
						'<a class="forward" alt="'+_('Forward')+'" data-id="'+row.msg_id+'"><i class="fa fa-share"></i></a>';
		var settings = UCP.Modules.Voicemail.staticsettings;
		if(settings.showDownload == "1") {
			html += '<a class="download" alt="'+_('Download')+'" href="'+ UCP.ajaxUrl +'?module=voicemail&amp;command=download&amp;msgid='+row.msg_id+'&amp;ext='+row.origmailbox+'"><i class="fa fa-cloud-download"></i></a>';
		}

		html += '<a class="delete" alt="'+_('Delete')+'" data-id="'+row.msg_id+'"><i class="fa fa-trash-o"></i></a>';

		return html;
	},
	bindPlayers: function(widget_id) {
		var extension = $("div[data-id='"+widget_id+"']").data("widget_type_id");
		$(".grid-stack-item[data-id="+widget_id+"] .jp-jplayer").each(function() {
			var container = $(this).data("container"),
					player = $(this),
					msg_id = $(this).data("id");
			$(this).jPlayer({
				ready: function() {
					$(container + " .jp-play").click(function() {
						if($(this).parents(".jp-controls").hasClass("recording")) {
							var type = $(this).parents(".jp-audio-freepbx").data("type");
							self.recordGreeting(extension,type);
							return;
						}
						if(!player.data("jPlayer").status.srcSet) {
							$(container).addClass("jp-state-loading");
							$.ajax({
								type: 'POST',
								url: UCP.ajaxUrl,
								data: {module: "voicemail", command: "gethtml5", msg_id: msg_id, ext: extension},
								dataType: 'json',
								timeout: 30000,
								success: function(data) {
									if(data.status) {
										player.on($.jPlayer.event.error, function(event) {
											$(container).removeClass("jp-state-loading");
											console.warn(event);
										});
										player.one($.jPlayer.event.canplay, function(event) {
											$(container).removeClass("jp-state-loading");
											player.jPlayer("play");
										});
										player.jPlayer( "setMedia", data.files);
									} else {
										UCP.showAlert(data.message);
										$(container).removeClass("jp-state-loading");
									}
								}
							});
						}
					});
					var self = this;
					$(container).find(".jp-restart").click(function() {
						if($(self).data("jPlayer").status.paused) {
							$(self).jPlayer("pause",0);
						} else {
							$(self).jPlayer("play",0);
						}
					});
				},
				timeupdate: function(event) {
					$(container).find(".jp-ball").css("left",event.jPlayer.status.currentPercentAbsolute + "%");
				},
				ended: function(event) {
					$(container).find(".jp-ball").css("left","0%");
				},
				swfPath: "/js",
				supplied: UCP.Modules.Voicemail.staticsettings.supportedHTML5,
				cssSelectorAncestor: container,
				wmode: "window",
				useStateClassSkin: true,
				remainingDuration: true,
				toggleDuration: true
			});
			$(this).on($.jPlayer.event.play, function(event) {
				$(this).jPlayer("pauseOthers");
			});
		});

		var acontainer = null;
		$('.grid-stack-item[data-rawname=voicemail] .jp-play-bar').mousedown(function (e) {
			acontainer = $(this).parents(".jp-audio-freepbx");
			updatebar(e.pageX);
		});
		$(document).mouseup(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
				acontainer = null;
			}
		});
		$(document).mousemove(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
			}
		});

		//update Progress Bar control
		var updatebar = function (x) {
			var player = $("#" + acontainer.data("player")),
					progress = acontainer.find('.jp-progress'),
					maxduration = player.data("jPlayer").status.duration,
					position = x - progress.offset().left,
					percentage = 100 * position / progress.width();

			//Check within range
			if (percentage > 100) {
				percentage = 100;
			}
			if (percentage < 0) {
				percentage = 0;
			}

			player.jPlayer("playHead", percentage);

			//Update progress bar and video currenttime
			acontainer.find('.jp-ball').css('left', percentage+'%');
			acontainer.find('.jp-play-bar').css('width', percentage + '%');
			player.jPlayer.currentTime = maxduration * percentage / 100;
		};
	},
	bindGreetingPlayers: function(extension) {
		var settings = UCP.Modules.Voicemail.staticsettings,
				supportedHTML5 = settings.supportedHTML5,
				self = this;

		if(Modernizr.getusermedia) {
			supportedHTML5 = supportedHTML5.split(",");
			if(supportedHTML5.indexOf("wav") === -1) {
				supportedHTML5.push("wav");
			}
			supportedHTML5 = supportedHTML5.join(",");
		}

		$("#widget_settings .jp-jplayer, .grid-stack-item[data-rawname=voicemail] .jp-jplayer").each(function() {
			var container = $(this).data("container"),
					player = $(this),
					msg_id = $(this).data("id");
			$(this).jPlayer({
				ready: function() {
					$(container + " .jp-play").click(function() {
						if($(this).parents(".jp-controls").hasClass("recording")) {
							var type = $(this).parents(".jp-audio-freepbx").data("type");
							self.recordGreeting(extension,type);
							return;
						}
						if(!player.data("jPlayer").status.srcSet) {
							$(container).addClass("jp-state-loading");
							$.ajax({
								type: 'POST',
								url: UCP.ajaxUrl,
								data: {module: "voicemail", command: "gethtml5", msg_id: msg_id, ext: extension},
								dataType: 'json',
								timeout: 30000,
								success: function(data) {
									if(data.status) {
										player.on($.jPlayer.event.error, function(event) {
											$(container).removeClass("jp-state-loading");
											console.warn(event);
										});
										player.one($.jPlayer.event.canplay, function(event) {
											$(container).removeClass("jp-state-loading");
											player.jPlayer("play");
										});
										player.jPlayer( "setMedia", data.files);
									} else {
										UCP.showAlert(data.message);
										$(container).removeClass("jp-state-loading");
									}
								}
							});
						}
					});
					var self = this;
					$(container).find(".jp-restart").click(function() {
						if($(self).data("jPlayer").status.paused) {
							$(self).jPlayer("pause",0);
						} else {
							$(self).jPlayer("play",0);
						}
					});
				},
				timeupdate: function(event) {
					$(container).find(".jp-ball").css("left",event.jPlayer.status.currentPercentAbsolute + "%");
				},
				ended: function(event) {
					$(container).find(".jp-ball").css("left","0%");
				},
				swfPath: "/js",
				supplied: supportedHTML5,
				cssSelectorAncestor: container,
				wmode: "window",
				useStateClassSkin: true,
				remainingDuration: true,
				toggleDuration: true
			});
			$(this).on($.jPlayer.event.play, function(event) {
				$(this).jPlayer("pauseOthers");
			});
		});

		var acontainer = null;
		$('#widget_settings .jp-play-bar, .grid-stack-item[data-rawname=voicemail] .jp-play-bar').mousedown(function (e) {
			acontainer = $(this).parents(".jp-audio-freepbx");
			updatebar(e.pageX);
		});
		$(document).mouseup(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
				acontainer = null;
			}
		});
		$(document).mousemove(function (e) {
			if (acontainer) {
				updatebar(e.pageX);
			}
		});

		//update Progress Bar control
		var updatebar = function (x) {
			var player = $("#" + acontainer.data("player")),
					progress = acontainer.find('.jp-progress'),
					maxduration = player.data("jPlayer").status.duration,
					position = x - progress.offset().left,
					percentage = 100 * position / progress.width();

			//Check within range
			if (percentage > 100) {
				percentage = 100;
			}
			if (percentage < 0) {
				percentage = 0;
			}

			player.jPlayer("playHead", percentage);

			//Update progress bar and video currenttime
			acontainer.find('.jp-ball').css('left', percentage+'%');
			acontainer.find('.jp-play-bar').css('width', percentage + '%');
			player.jPlayer.currentTime = maxduration * percentage / 100;
		};
	}
});

var WebrtcC = UCPMC.extend({
	init: function() {
		this.phone = null;
		this.activeCalls = {};
		this.activeCallId = null;
		this.answering = false;
		this.userBlocked = false;
		this.silenced = false;
		this.autoRegister = false;
		this.displayState = null;
		this.state = null;
		this.timerObject = null;
		this.callBinds = [
			"progress",
			"accepted",
			"rejected",
			"failed",
			"terminated",
			"cancel",
			"refer",
			"replaced",
			"dtmf",
			"muted",
			"unmuted",
			"bye",
			"addStream"
		];

		this.callOptions = {
			"media": {
				"constraints": {
					"audio": true,
					"video": false
				},
				"render": {
					"remote": null
				}
			}
		};

		this.notification = null;
		var st = Cookies.get("webrtc-silenced");
		st = (st === "1") ? true : false;
		this.silence(st);

		var rg = Cookies.get("webrtc-register");
		this.autoRegister = (typeof rg === "undefined" || rg === "1") ? true : false;
	},
	settingsDisplay: function() {

	},
	settingsHide: function() {

	},
	addSimpleWidget: function(widget_id) {
		this.initiateLibrary();
	},
	displaySimpleWidgetSettings: function(widget_id) {
		var $this = this;

		var st = Cookies.get("webrtc-silenced");
		st = (st === "1") ? true : false;

		$("#webrtc-silence-switch").prop("checked",st);

		$("#webrtc-silence-switch").bootstrapToggle('destroy');
		$("#webrtc-silence-switch").bootstrapToggle({
			on: _("Enable"),
			off: _("Disable")
		});

		$("#webrtc-disconnect-switch").prop("checked",!this.autoRegister);

		$("#webrtc-disconnect-switch").bootstrapToggle('destroy');
		$("#webrtc-disconnect-switch").bootstrapToggle({
			on: _("Enable"),
			off: _("Disable")
		});

		if(this.phone === null) {
			$("#webrtc-silence-switch").bootstrapToggle('disable');
			$("#webrtc-disconnect-switch").bootstrapToggle('disable');
			return;
		}

		$("#webrtc-silence-switch").change(function() {
			$this.silence();
		});
		$("#webrtc-disconnect-switch").change(function(e) {
			$this.toggleRegister();
		});
	},
	displaySimpleWidget: function(widget_id) {
		var $this = this;
		$("#menu_webrtc_phone .status span").text(this.displayState);

		if(this.phone === null) {
			$("#menu_webrtc_phone input.dialpad").prop("disabled",true);
			return;
		}

		if(this.state == "hold") {
			this.switchState('accepted');
			this.switchState('hold');
		} else {
			this.switchState(this.state);
		}

		if(typeof this.phone === "object" && this.phone !== null && this.phone.isRegistered()) {
			$("#menu_webrtc_phone .action").prop("disable",false);
		}

		$("#menu_webrtc_phone .keypad td").click(function() {
			var text = $("#menu_webrtc_phone .dialpad").val() + $(this).data("num"),
					button = $("#menu_webrtc_phone button.action");
			if ($this.state == "registered" || $this.state == "accepted") {
				if ($this.state == "registered") {
					$( "#menu_webrtc_phone .message").text("To: " + text);
				}
				$("#menu_webrtc_phone .dialpad").val(text);
				$this.DTMF($(this).data("num"));
				button.prop("disabled", false);
				$("#menu_webrtc_phone .message-container").textfill();
			}
		});

		$("#menu_webrtc_phone .clear-input").click(function() {
			var button = $("#menu_webrtc_phone button.action");
			$("#menu_webrtc_phone .dialpad").val("");
			if ($this.state == "registered") {
				$( "#menu_webrtc_phone .message").text("");
				button.prop("disabled", true);
			}
		});
		$("#menu_webrtc_phone .dialpad").on('keyup paste', function() {
			var button = $("#menu_webrtc_phone button.action"),
				text = $("#menu_webrtc_phone .dialpad").val();
			if ($(this).val().length === 0 && ($this.state == "accepted" || $this.state == "registered")) {
				$( "#menu_webrtc_phone .message").text("");
				button.prop("disabled", true);
			} else {
				$( "#menu_webrtc_phone .message").text("To: " + text);
				$this.DTMF(text.slice(-1));
				button.prop("disabled", false);
			}
			$("#menu_webrtc_phone .message-container").textfill();
		});
		$("#menu_webrtc_phone button.action").click(function() {
			switch ($this.state) {
				case "registered":
					$this.call($("#menu_webrtc_phone .dialpad").val());
				break;
				case "hold":
				case "accepted":
					$this.hangup();
				break;
				case "invite":
					$this.answer();
				break;
			}
		});
		$("#menu_webrtc_phone button.secondaction").click(function() {
			switch ($this.state) {
				case "hold":
				case "accepted":
					$this.toggleHold();
				break;
				case "invite":
					$this.hangup();
				break;
			}
		});
		$("#menu_webrtc_phone .message-container").textfill();
	},
	deleteSimpleWidget: function(widget_id) {
		if(this.phone !== null) {
			this.disconnect();
		}
	},
	engineEvent: function(type, event) {
		console.log("Engine " + type);
		switch (type){
			case "invite":
				this.manageSession(event,"inbound");
				this.switchState("invite");
			break;
			case "registered":
				this.switchState("registered");
			break;
			case "unregistered":
				this.switchState("unregistered");
			break;
			case "registrationFailed":
				this.switchState("registrationfailed");
			break;
			case "connected":
				this.switchState("connected");
			break;
			case "disconnected":
				this.switchState("disconnected");
			break;
			case "connecting":
				this.switchState("connecting");
			break;
			case "registering": //custom event type
				this.switchState("registering");
			break;
		}
	},
	setDisplayState: function(state) {
		this.displayState = state;
		$("#menu_webrtc_phone .status span").text(this.displayState);
	},
	playRing: function() {
		if(!this.silenced) {
			$("#ringtone").trigger("play");
		}
	},
	stopRing: function() {
		$("#ringtone").trigger("pause");
		$("#ringtone").trigger("load");
	},
	playRingBack: function() {
                if(!this.silenced) {
                        $("#ringback").trigger("play");
                }
        },
        stopRingBack: function() {
                $("#ringback").trigger("pause");
                $("#ringback").trigger("load");
        },

	manageSession: function(session, direction) {
		var Webrtc = this,
				id,
				displayName,
				status,
				cnum,
				cnam,
				call = session;

		id = Math.floor((Math.random() * 100000) + 1);
		// If the session exists with active call reject it.
		// TODO this can be useful for call waiting
		if (this.activeCallId) {
			call.terminate();
			return false;
		}

		// If this is a new session create it
		if (!this.activeCallId) {
			this.activeCallId = id;
			this.activeCalls[id] = call;
		}

		cnum = this.activeCalls[id].remoteIdentity.uri.user;
		cnam = this.activeCalls[this.activeCallId].remoteIdentity.displayName || "";
		displayName = (cnam !== "") ? cnam + " <" + cnum + ">" : cnum;
		$("#menu_webrtc_phone .contactDisplay .contactImage").css("background-image",'url("?quietmode=1&module=Webrtc&command=cimage&did='+cnum+'")');
		Webrtc.answering = false;
		if (direction === "inbound") {
			if (UCP.notify) {
				this.notification = new Notify(sprintf(_("Incoming call from %s"), displayName), {
					body: _("Click this window to answer or close this window to ignore"),
					icon: "modules/Webrtc/assets/images/no_user_logo.png", //TODO: get the user logo
					notifyClose: function() {
						if (Webrtc.answering) {
							Webrtc.answering = false;
						} else {
							Webrtc.hangup();
						}
					},
					notifyClick: function() {
						Webrtc.answering = true;
						Webrtc.answer();
						$(".custom-widget[data-widget_rawname=webrtc]").click();
						Webrtc.notification.close();
					}
				});
				this.notification.show();
			}
		}

		$.each(this.callBinds, function(i, v) {
			Webrtc.activeCalls[Webrtc.activeCallId].on(v, function(data, cause) {
				Webrtc.sessionEvent(v, data, cause);
			});
		});
	},
	sessionEvent: function(type, data, cause) {
		console.log("Session " + type);
		switch (type){
			case "terminated":
				this.switchState("terminated");
				this.endCall(data, cause);
			break;
			case "accepted":
				this.switchState("accepted");
				this.startCall(data);
			break;
			case "progress":
				this.switchState("progress");
			break;
			case "dtmf":
				this.switchState("dtmf");
			break;
			case "muted":
				this.switchState("muted");
			break;
			case "unmuted":
				this.switchState("unmuted");
			break;
		}
	},
	endCall: function(message, cause) {
		this.activeCalls[this.activeCallId] = null;
		this.activeCallId = null;
		if (this.notification !== null) {
			this.notification.close();
		}
		if(typeof cause !== "undefined" && cause === SIP.C.causes.USER_DENIED_MEDIA_ACCESS) {
			this.userBlocked = true;
		}
		$("#menu_webrtc_phone .btn-primary").prop("disabled", false);
		this.stopRing();
		this.stopRingBack();
	},
	startCall: function(event) {
		if (this.notification !== null) {
			this.notification.close();
		}
		this.stopRing();
	},
	silence: function(state) {
		state = (typeof state !== "undefined") ? state : !this.silenced;
		if(!$("#webrtc-silence").length) {
			$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").after('<i id="webrtc-silence" class="fa fa-ban fa-stack-2x hidden"></i>');
		}
		if(state) {
			this.stopRing();
			$("#webrtc-silence").removeClass("hidden");
			$("#webrtc-silence .fa-check").removeClass("hidden");
		} else {
			$("#webrtc-silence").addClass("hidden");
			$("#webrtc-silence .fa-check").addClass("hidden");
		}
		Cookies.set("webrtc-silenced",(state ? "1" : "0"));
		this.silenced = state;
	},
	call: function(number) {
		if (this.phone.isConnected() && !this.userBlocked) {
			$("#menu_webrtc_phone .btn-primary").prop("disabled", true);
			var session = this.phone.invite(number, this.callOptions);
			this.manageSession(session,"outbound");
		} else if(this.phone.isConnected() && this.userBlocked) {
			alert(_("Unable to start call. Please allow the WebRTC session in your browser and refresh"));
		}
	},
	answer: function() {
		if (this.activeCallId !== null) {
			this.answering = true;
			this.activeCalls[this.activeCallId].accept(this.callOptions);
		}
	},
	toggleHold: function() {
		if (this.activeCallId !== null) {
			var call = this.activeCalls[this.activeCallId],
					holds = this.activeCalls[this.activeCallId].isOnHold();
			if (!holds.local) {
				this.switchState("hold");
				call.hold();
			} else {
				this.switchState("unhold");
				call.unhold();
			}
		}
	},
	DTMF: function(num) {
		if (this.state == "accepted" && this.activeCallId !== null) {
			this.activeCalls[this.activeCallId].dtmf(num);
		}
	},
	hangup: function() {
		if ((this.state == "accepted" || this.state == "invite") && this.activeCallId !== null) {
			this.activeCalls[this.activeCallId].terminate();
		}
		this.stopRing();
		this.stopRingBack();
	},
	poll: function(data) {

	},
	display: function(event) {

	},
	hide: function(event) {

	},
	switchState: function(t) {
		var button = $("#menu_webrtc_phone button.action"),
				secondbutton = $("#menu_webrtc_phone button.secondaction"),
				input = $("#menu_webrtc_phone input.dialpad"),
				type = (typeof t !== "undefined" && t !== null) ? t : "registered",
				$this = this;
		this.state = type;
		button.data("type", type);
		switch (type){
			case "dtmf":
				this.state = "accepted";
			break;
			case "invite":
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").addClass("shake");
				this.playRing();
				$("#menu_webrtc_phone .activeCallSession .keypad").hide();
				$("#menu_webrtc_phone .activeCallSession .input-container").hide();
				$("#menu_webrtc_phone .contactDisplay").show();
				secondbutton.removeClass().addClass("btn btn-danger secondaction").text("Ignore");
				$("#menu_webrtc_phone .actions .right").show();
				button.removeClass().addClass("btn btn-success action").text("Answer");
				button.prop("disabled", false);
			break;
			case "hold":
				secondbutton.removeClass().addClass("btn btn-success secondaction").text("Resume");
				secondbutton.css("background-color","orange");
				if(!$("#webrtc-hold").length) {
					$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").after('<i id="webrtc-hold" class="fa fa-pause fa-stack-2x blink hidden"></i>');
				}
				$("#webrtc-hold").removeClass("hidden");
			break;
			case "unhold":
				secondbutton.removeClass().addClass("btn btn-success secondaction").text("Hold");
				secondbutton.css("background-color","");
				if($("#webrtc-hold").length) {
					$("#webrtc-hold").addClass("hidden");
				}

				this.state = "accepted";
			break;
			case "progress":
				this.playRingBack();
			break;
			case "accepted":
				this.stopRingBack();
				this.stopRing();
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("shake");
				$("#menu_webrtc_phone .contactDisplay").hide();
				$("#menu_webrtc_phone .activeCallSession .keypad").show();
				$("#menu_webrtc_phone .activeCallSession .input-container").show();
				secondbutton.removeClass().addClass("btn btn-success secondaction").text("Hold");
				secondbutton.css("color","");
				$("#menu_webrtc_phone .actions .right").show();

				input.prop("disabled", false);
				button.prop("disabled", false);
				button.removeClass().addClass("btn btn-danger action").text("Hangup");
				$("#menu_webrtc_phone .contact-info").addClass("in");
				$("#webrtc-timer-container").remove();
				clearInterval(this.timerObject);
				$('#webrtc-disconnect-switch').bootstrapToggle('disable');
				var updateTimer = function() {
					if($this.activeCallId === null) {
						clearInterval($this.timerObject);
						$("#menu_webrtc_phone .contact-info").removeClass("in");
						$('#webrtc-disconnect-switch').bootstrapToggle('enable');
						return;
					}
					//
					var start = moment($this.activeCalls[$this.activeCallId].startTime);
					var end = moment();
					var duration = moment.duration(end.diff(start));

					var padLeft = function(nr){
						return Array(2-String(nr).length+1).join('0')+nr;
					};

					var time = padLeft(duration.hours())+":"+padLeft(duration.minutes())+":"+padLeft(duration.seconds());

					if($("#menu_webrtc_phone .contact-info .timer").is(":visible")) {
						$("#menu_webrtc_phone .contact-info .timer").text(time);
					} else {
						if(!$("#webrtc-timer-container").length) {
							$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").after('<div id="webrtc-timer-container"><div class="timer">'+time+'</div></div>');
						} else {
							$("#webrtc-timer-container .timer").text(time);
						}
					}
				};
				updateTimer();
				this.timerObject = setInterval(updateTimer,1000);

				var cnam = this.activeCalls[this.activeCallId].remoteIdentity.displayName || "",
						cnum = this.activeCalls[this.activeCallId].remoteIdentity.uri.user,
						displayName = (cnam !== "") ? cnam + " <" + cnum + ">" : cnum;
				$("#menu_webrtc_phone .contact-info .contact").text(displayName);
			break;
			case "terminated":
				this.stopRing();
				this.stopRingBack();
				$("#menu_webrtc_phone .actions .right").hide();
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("shake");
				$("#menu_webrtc_phone .activeCallSession .keypad").show();
				$("#menu_webrtc_phone .activeCallSession .input-container").show();
				$("#menu_webrtc_phone .contactDisplay").hide();
				button.removeClass().addClass("btn btn-primary action").text("Call");
				$("#menu_webrtc_phone .contact-info .contact").text("");
				this.state = "registered";
			break;
			case "registered":
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("registering");
				this.setDisplayState(_("Registered"));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "green");
				input.prop("disabled", false);
				input.val("");
				$("#menu_webrtc_phone .keypad").removeClass("disable");
				button.prop("disabled", true);
				$("#menu_webrtc_phone .actions .right").hide();
				button.removeClass().addClass("btn btn-primary action").text("Call");
			break;
			case "unregistered":
				this.setDisplayState(_("Unregistered"));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("registering");
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "yellow");
				$("#menu_webrtc_phone .keypad").addClass("disable");
				input.prop("disabled", true);
				input.val("");
			break;
			case "registrationfailed":
				this.setDisplayState(_("Registration Failed"));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("registering");
				$("#webrtc-dc a span").text(_("Connect Phone"));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
				$("#menu_webrtc_phone .keypad").addClass("disable");
				input.prop("disabled", true);
				input.val("");
			break;
			case "connected":
				this.setDisplayState(_("Unregistered"));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("connecting");
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "yellow");
			break;
			case "disconnected":
				this.setDisplayState(_("Disconnected"));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("connecting");
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("registering");
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
				$("#menu_webrtc_phone .keypad").addClass("disable");
				input.prop("disabled", true);
				input.val("");
			break;
			case "connecting":
				this.setDisplayState(_("Connecting to socket..."));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").addClass("connecting");
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").removeClass("registering");
			break;
			case "registering": //custom event type
				this.setDisplayState(_("Registering..."));
				$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").addClass("registering");
			break;
		}
	},
	connect: function() {
		if ((typeof this.staticsettings !== "undefined") &&
				this.staticsettings.enabled &&
				Modernizr.getusermedia &&
				this.phone !== null &&
				!this.phone.isConnected()) {
			this.phone.start();
		}
	},
	disconnect: function() {
		if (this.phone !== null &&
				this.phone.isConnected()) {
			this.phone.stop();
		}
	},
	register: function() {
		if(!this.phone.isConnected()) {
			this.connect();
		}
		if (this.phone !== null &&
				!this.phone.isRegistered()) {
		}
		this.phone.register();
	},
	unregister: function() {
		if(!this.phone.isConnected()) {
			throw "Phone is not connected, nothing to register";
		}
		if (this.phone !== null &&
				this.phone.isRegistered()) {
		}
		this.phone.unregister();
	},
	toggleRegister: function() {
		if(!this.phone.isConnected()) {
			return; //nope
		}
		if($(".custom-widget[data-widget_rawname=webrtc] .fa-phone").hasClass("registering")) {
			return; //we are already doing something
		}
		if(!this.phone.isRegistered()) {
			this.register();
			Cookies.set("webrtc-register",1);
		} else {
			this.unregister();
			Cookies.set("webrtc-register",0);
		}

	},
	initiateLibrary: function() {
		var $this = this,
				ver = "0.7.7";

		if(typeof SIP === "object") {
			return;
		}

		if(!$("html").hasClass("getusermedia")) {
			$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
			this.setDisplayState(_("Not supported in this browser"));
			console.warn("WebRTC is not supported in this browser");
			return;
		}

		if(document.location.protocol !== "https:") {
			$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
			this.setDisplayState(_("Only supported over HTTPS"));
			console.warn("WebRTC is not supported in non-SSL mode");
			return;
		}

		if(!$(".custom-widget[data-widget_rawname=webrtc]").length) {
			$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
			console.warn("WebRTC Widget has not been added");
			return;
		}

		if(typeof moduleSettings.Webrtc === "undefined") {
			$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
			console.warn("WebRTC is not configured properly");
			return;
		}

		if(!moduleSettings.Webrtc.enabled) {
			$(".custom-widget[data-widget_rawname=webrtc] .fa-phone").css("color", "red");
			console.warn(moduleSettings.Webrtc.message);
			this.setDisplayState(moduleSettings.Webrtc.message);
			return;
		}

		$.getScript("modules/Webrtc/assets/jssiplibs/sip-" + ver + ".min.js")
		.done(function( script, textStatus ) {
			$("#footer").append("<audio id=\"audio_remote\" autoplay=\"autoplay\" />");
			$("#footer").append("<audio id=\"ringtone\"><source src=\"modules/Webrtc/assets/sounds/ring.mp3\" type=\"audio/mpeg\"></audio>");
			$("#footer").append("<audio id=\"ringback\"><source src=\"modules/Webrtc/assets/sounds/US_Ringback.mp3\" type=\"audio/mpeg\"></audio>");
			$this.callOptions.media.render.remote = document.getElementById('audio_remote');
			$this.phone = new SIP.UA(
				{
					"wsServers": moduleSettings.Webrtc.settings.wsservers,
					"uri": moduleSettings.Webrtc.settings.uri,
					"password": moduleSettings.Webrtc.settings.password,
					"log": {
						"builtinEnabled": false,
						"level": moduleSettings.Webrtc.settings.log
					},
					"register": $this.autoRegister,
					"hackWssInTransport": true,
					"stunServers": moduleSettings.Webrtc.settings.iceServers,
					"iceCheckingTimeout": moduleSettings.Webrtc.settings.gatheringTimeout,
					// The rtcpMuxPolicy option is being considered for removal and may be removed no earlier than M60, around August 2017.
					// If you depend on it, please see https://www.chromestatus.com/features/5654810086866944 for more details.
					// https://nimblea.pe/monkey-business/2017/01/19/webrtc-asterisk-and-chrome-57/
					// https://issues.asterisk.org/jira/browse/ASTERISK-26732
					"rtcpMuxPolicy": "negotiate"
				}
			);

			var binds = [
				"connected",
				"disconnected",
				"registered",
				"unregistered",
				"registrationFailed",
				"invite",
				"message",
				"connecting"
				];
			$.each(binds, function(i, v) {
				$this.phone.on(v, function(e) {
					$this.engineEvent(v, e);
				});
			});

			$this.connect();
		}).fail(function( jqxhr, settings, exception ) {
			//could not load script, remove button
		});
	}
});

$(document).bind("logIn", function( event ) {
	console.log("loggedin");
});

$(document).bind("logOut", function( event ) {
	if (typeof UCP.Modules.Webrtc !== "undefined" && UCP.Modules.Webrtc.phone !== null && UCP.Modules.Webrtc.phone.isConnected()) {
		UCP.Modules.Webrtc.disconnect();
	}
});

$(window).bind("beforeunload", function() {
	if (typeof UCP.Modules.Webrtc !== "undefined" && UCP.Modules.Webrtc.phone !== null && UCP.Modules.Webrtc.phone.isConnected()) {
		UCP.Modules.Webrtc.disconnect();
	}
});

var WidgetsC = Class.extend({
	init: function() {
		this.activeDashboard = null;
		this.widgetMenuOpen = false;
	},
	ready: function() {
		this.setupAddDashboard();
		this.loadDashboard();
		this.initMenuDragabble();
		this.initDashboardDragabble();
		this.initCategoriesWidgets();
		this.initAddWidgetsButtons();
		this.initRemoveItemButtons();
		this.initLockItemButtons();
		this.initLeftNavBarMenus();
		this.deactivateFullLoading();
		var $this = this;
		var total = $(".custom-widget").length;
		var count = 0;
		var resave = false;
		$(".custom-widget").each(function() {
			var widget_rawname = $(this).data("widget_rawname");
			var widget_id = $(this).data("widget_id");
			UCP.callModuleByMethod(widget_rawname,"addSimpleWidget",widget_id);
			$(document).trigger("post-body.addsimplewidget",[ widget_id, $this.activeDashboard ]);
			if(typeof $(this).find("a").data("regenuuid") !== "undefined" && $(this).find("a").data("regenuuid")) {
				resave = true;
			}
			count++;
			if(total == count) {
				if(resave) {
					$this.saveSidebarContent();
				}
			}
		});
		window.onpopstate = function(event) {
			if(typeof event.state !== "undefined" && event.state !== null && typeof event.state.activeDashboard !== "undefined") {
				var el = $("#all_dashboards .dashboard-menu[data-id="+event.state.activeDashboard+"] a");
				//set popstate event to true so we dont destroy history
				el.data("popstate",true);
				el.click();
			}
		};
		var title = $("#all_dashboards .dashboard-menu.active a").text();
		//set tab title
		if(title !== "") {
			$("title").text(_("User Control Panel") + " - " + title);
		}
	},
	loadDashboard: function() {
		var $this = this;

		$("#dashboard-content .dashboard-error.no-dash").click(function() {
			$("#add_new_dashboard").click();
		});

		$('#add_dashboard').on('shown.bs.modal', function () {
			$('#dashboard_name').focus();
			$("#add_dashboard").off("keydown");
			$("#add_dashboard").on('keydown', function(event) {
				switch(event.keyCode) {
					case 13:
						$("#create_dashboard").click();
					break;
				}
			});
		});

		$('#add_dashboard').on('hidden.bs.modal', function () {
			$('#dashboard_name').val("");
		});

		$('#edit_dashboard').on('shown.bs.modal', function () {
			$('#edit_dashboard_name').focus();
		});

		$('#edit_dashboard').on('hidden.bs.modal', function () {
			$('#edit_dashboard_name').val("");
		});

		$(document).on("click", ".edit-widget", function(){
			var settings_container = $('#widget_settings .modal-body'),
					parent = $(this).parents(".grid-stack-item"),
					rawname = parent.data("rawname"),
					widget_type_id = parent.data("widget_type_id"),
					widget_id = parent.data("id"),
					title = parent.data("widget_module_name"),
					name = parent.data("name");

			$('#widget_settings').attr("data-rawname",rawname);
			$('#widget_settings').data('rawname',rawname);

			$('#widget_settings').attr("data-id",widget_id);
			$('#widget_settings').data('id',widget_id);

			$('#widget_settings').attr("data-widget_type_id",widget_type_id);
			$('#widget_settings').data('widget_type_id',widget_type_id);

			$this.activateSettingsLoading();
			$("#widget_settings .modal-title").html('<i class="fa fa-cog" aria-hidden="true"></i> '+title+" "+_("Settings")+" ("+name+")");
			$('#widget_settings').modal('show');
			$('#widget_settings').one('shown.bs.modal', function() {
				$this.getSettingsContent(settings_container, widget_id, widget_type_id, rawname, function() {
					$("#widget_settings .modal-body .fa-question-circle").click(function(e) {
						e.preventDefault();
						e.stopPropagation();
						var f = $(this).parents("label").attr("for");
						$(".help-block").addClass('help-hidden');
						$('.help-block[data-for="'+f+'"]').removeClass('help-hidden');
					});
					$(document).trigger("post-body.widgetsettings",[ widget_id, $this.activeDashboard ]);
				});
			});
		});

		$(window).resize(function() {
			var gridstack = $(".grid-stack").data('gridstack');
			if(typeof gridstack === "undefined") {
				return;
			}
			setTimeout(function() {
				if(window.innerWidth <= 768) {
					gridstack.resizable($(".grid-stack-item").not('[data-gs-no-resize]'),false);
					gridstack.enableMove(false);
				} else {
					gridstack.resizable($(".grid-stack-item").not('[data-gs-no-resize]'),true);
					gridstack.enableMove(true);
				}
			},100);
		});

		if(!$(".grid-stack").length) {
			this.activeDashboard = null;
			$(document).trigger("post-body.widgets",[ null, this.activeDashboard ]);
		} else {
			var dashboard_id = $(".grid-stack").data("dashboard_id");
			//Are we looking a dashboard?
			this.activeDashboard = dashboard_id;

			$this.setupGridStack();
			$this.bindGridChanges();

			var gridstack = $(".grid-stack").data('gridstack');
			var total = gridstack.grid.nodes.length;
			var count = 0;
			var resave = false;
			if(total > 0) {
				$.each(gridstack.grid.nodes, function(i,v){
					var el = v.el;
					if(!el.hasClass("add-widget-widget")){
						var widget_id = $(el).data('id');
						var widget_type_id = $(el).data('widget_type_id');
						var widget_rawname = $(el).data('rawname');
						if(typeof $(el).data("regenuuid") !== "undefined" && $(el).data("regenuuid")) {
							resave = true;
						}
						$this.getWidgetContent(widget_id, widget_type_id, widget_rawname, function() {
							count++;
							if(count == total) {
								$(document).trigger("post-body.widgets",[ null, $this.activeDashboard ]);
								if(resave) {
									$this.saveLayoutContent();
								}
							}
						});
					}
				});
			} else {
				$(document).trigger("post-body.widgets",[ null, $this.activeDashboard ]);
			}


			$(".dashboard-menu").removeClass("active");

			$(".dashboard-menu[data-id='"+this.activeDashboard+"']").addClass("active");
			UCP.callModulesByMethod("showDashboard",this.activeDashboard);
		}
	},
	/**
	 * Save Dashboard Layout State
	 * @method saveLayoutContent
	 */
	saveLayoutContent: function() {
		this.activateFullLoading();

		var $this = this,
				grid = $('.grid-stack').data('gridstack');

		//TODO: lodash :-|
		var gridDataSerialized = lodash.map($('.grid-stack .grid-stack-item:visible').not(".grid-stack-placeholder"), function (el) {
			el = $(el);
			var node = el.data('_gridstack_node'),
					locked = el.find(".lock-widget i").hasClass("fa-lock");

			return {
				id: el.data('id'),
				widget_module_name: el.data('widget_module_name'),
				name: el.data('name'),
				rawname: el.data('rawname'),
				widget_type_id: el.data('widget_type_id'),
				has_settings: el.data('has_settings'),
				size_x: node.x,
				size_y: node.y,
				col: node.width,
				row: node.height,
				locked: locked,
				uuid: el.data('uuid')
			};
		});

		dashboards[$this.activeDashboard] = gridDataSerialized;

		$.post( UCP.ajaxUrl,
			{
				module: "Dashboards",
				command: "savedashlayout",
				id: $this.activeDashboard,
				data: JSON.stringify(gridDataSerialized)
			},
			function( data ) {
				if(data.status){
					console.log("saved grid");
				}else {
					UCP.showAlert(_("Something went wrong saving the information (grid)"), "danger");
				}
		}).always(function() {
			$this.deactivateFullLoading();
		}).fail(function(jqXHR, textStatus, errorThrown) {
			UCP.showAlert(textStatus,'warning');
		});
	},
	saveSidebarContent: function(callback) {
		this.activateFullLoading();

		var $this = this,
				sidebar_objects = $("#side_bar_content li.custom-widget a"),
				all_content = [];

		sidebar_objects.each(function(){

			var widget_id = $(this).data('id'),
					widget_type_id = $(this).data('widget_type_id'),
					widget_module_name = $(this).data('module_name'),
					widget_rawname = $(this).data('rawname'),
					widget_name = $(this).data('name'),
					widget_icon = $(this).data('icon'),
					small_widget = {
						id:widget_id,
						widget_type_id: widget_type_id,
						module_name: widget_module_name,
						rawname: widget_rawname,
						name: widget_name,
						icon: widget_icon
					};

			all_content.push(small_widget);

		});

		var gridDataSerialized = JSON.stringify(all_content);

		$.post( UCP.ajaxUrl ,
			{
				module: "Dashboards",
				command: "savesimplelayout",
				data: gridDataSerialized
			},
			function( data ) {
				if(data.status){
					console.log("sidebar saved");
				}else {
					UCP.showAlert(_("Something went wrong saving the information (sidebar)"), "danger");
				}
				if(typeof callback === "function") {
					callback();
				}
		}).always(function() {
			$this.deactivateFullLoading();
		}).fail(function(jqXHR, textStatus, errorThrown) {
			UCP.showAlert(textStatus,'warning');
		});
	},
	/**
	 * Show the full screen loading
	 * @method activateFullLoading
	 */
	activateFullLoading: function(){
		$(".main-block").removeClass("hidden");
		NProgress.start();
	},
	/**
	 * Hide the full screen loading
	 * @method deactivateFullLoading
	 */
	deactivateFullLoading: function(){
		$(".main-block").addClass("hidden");
		NProgress.done();
	},
	/**
	 * Show the widget loading screen
	 * @method activateWidgetLoading
	 * @param  {object}              widget_object jQuery object of the widget content
	 * @return {string}                            Returns the html if no object provided
	 */
	activateWidgetLoading: function(widget_object){

		var loading_html = '<div class="widget-loading-box">' +
			'					<span class="fa-stack fa">' +
			'						<i class="fa fa-cloud fa-stack-2x text-internal-blue"></i>' +
			'						<i class="fa fa-cog fa-spin fa-stack-1x secundary-color"></i>' +
			'					</span>' +
			'				</div>';

		if(typeof widget_object !== "undefined") {
			widget_object.html(loading_html);
		} else {
			return loading_html;
		}
	},
	/**
	 * Show the settings loading screen
	 * @method activateSettingsLoading
	 */
	activateSettingsLoading: function() {
		var loading_html = '<div class="settings-loading-box">' +
			'					<span class="fa-stack fa">' +
			'						<i class="fa fa-cloud fa-stack-2x text-internal-blue"></i>' +
			'						<i class="fa fa-cog fa-spin fa-stack-1x secundary-color"></i>' +
			'					</span>' +
			'				</div>';
		$("#widget_settings .modal-body").html(loading_html);
	},
	/**
	 * Generate Widget Layout
	 * @method widget_layout
	 * @param  {string}      widget_id           The widget ID
	 * @param  {string}      widget_module_name  The widget module name
	 * @param  {string}      widget_name         The widget name
	 * @param  {string}      widget_type_id      The widget sub ID
	 * @param  {string}      widget_rawname      The widget rawname
	 * @param  {Boolean}     widget_has_settings If the widget has settings or not
	 * @param  {string}      widget_content      The widget content
	 * @param  {Boolean}      resizable           is resizable
	 * @param  {Boolean}      locked              is locked
	 * @return {string}                          The finalized html
	 */
	widget_layout: function(widget_id, widget_module_name, widget_name, widget_type_id, widget_rawname, widget_has_settings, widget_content, resizable, locked){
		var cased = widget_rawname.modularize(),
				icon = allWidgets[cased].icon,
				lockIcon = locked ? 'fa-lock' : 'fa-unlock-alt',
				settings_html = '';

		//TODO: boolean is checking by string reference??
		if(widget_has_settings == "1"){
			settings_html = '<div class="widget-option edit-widget" data-rawname="'+widget_rawname+'" data-widget_type_id="'+widget_type_id+'">' +
								'<i class="fa fa-cog" aria-hidden="true"></i>' +
							'</div>';
		}
		var rs_html = '';
		if(!resizable) {
			rs_html = 'data-no-resize="true"';
		}

		var html = '' +
					'<div data-widget_module_name="'+widget_module_name+'" data-id="'+widget_id+'" data-name="'+widget_name+'" data-rawname="'+widget_rawname+'" data-widget_type_id="'+widget_type_id+'" data-has_settings="'+widget_has_settings+'" class="flip-container" '+rs_html+'>' +
						'<div class="grid-stack-item-content flipper">' +
							'<div class="front">' +
								'<div class="widget-title">' +
									'<div class="widget-module-name truncate-text"><i class="fa-fw '+icon+'"></i>' + widget_name + '</div>' +
									'<div class="widget-module-subname truncate-text">('+widget_module_name+')</div>' +
									'<div class="widget-options">' +
										'<div class="widget-option remove-widget" data-widget_id="'+widget_id+'" data-widget_type_id="'+widget_type_id+'" data-widget_rawname="'+widget_rawname+'">' +
											'<i class="fa fa-times" aria-hidden="true"></i>' +
										'</div>' +
										settings_html +
										'<div class="widget-option lock-widget" data-widget_id="'+widget_id+'" data-widget_type_id="'+widget_type_id+'" data-widget_rawname="'+widget_rawname+'">' +
											'<i class="fa '+lockIcon+'" aria-hidden="true"></i>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="widget-content container">'+widget_content+'</div>' +
							'</div>' +
							'<div class="back">' +
								'<div class="widget-title settings-title">' +
									'<div class="widget-module-name truncate-text">'+_('Settings')+'</div>' +
									'<div class="widget-module-subname truncate-text">(' + widget_module_name + ' '+widget_name+')</div>' +
									'<div class="widget-options">' +
										'<div class="widget-option close-settings" data-rawname="'+widget_rawname+'" data-widget_type_id="'+widget_type_id+'">' +
											'<i class="fa fa-times" aria-hidden="true"></i>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="widget-settings-content">' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>';

		return html;
	},
	/**
	 * Generate Side Bar Icon Layout
	 * @method smallWidgetLayout
	 * @param  {string}          widget_id          The widget ID
	 * @param  {string}          widget_rawname     The widget rawname
	 * @param  {string}          widget_name        The widget name
	 * @param  {string}          widget_type_id     The widget sub id
	 * @param  {string}          widget_icon        The widget icon class
	 * @return {string}                             The finalized HTML
	 */
	smallWidgetLayout: function(widget_id, widget_rawname, widget_name, widget_type_id, widget_icon){
		var html = '' +
			'<li class="custom-widget" data-widget_id="'+widget_id+'" data-widget_rawname="'+widget_rawname+'" data-widget_type_id="'+widget_type_id+'">' +
				'<a href="#" title="'+widget_rawname+' '+widget_type_id+'" data-id="'+widget_id+'" data-name="'+widget_name+'" data-rawname="'+widget_rawname+'" data-widget_type_id="'+widget_type_id+'" data-icon="' + widget_icon + '"><i class="' + widget_icon + '" aria-hidden="true"></i></a>' +
			'</li>';

		return html;
	},
	/**
	 * Small Widget Menu Layout
	 * @method smallWidgetMenuLayout
	 * @param  {string}              widget_id      The Widget ID
	 * @param  {string}              widget_rawname The widget rawname
	 * @param  {string}              widget_name    The widget name
	 * @param  {string}              widget_type_id The widget name
	 * @param  {string}              widget_icon    The widget icon class
	 * @param  {string}              widget_sub     The widget sub name
	 * @param  {Boolean}             hasSettings    If the settings COG should be generated
	 * @return {string}                             The finalized HTML
	 */
	smallWidgetMenuLayout: function(widget_id, widget_rawname, widget_name, widget_type_id, widget_icon, widget_sub, hasSettings){
		var settings_html = '';
		if(hasSettings) {
			settings_html = '<i class="fa fa-cog show-simple-widget-settings" aria-hidden="true"></i>';
		}

		var html = '' +
			'<div class="widget-extra-menu" id="menu_'+widget_id+'" data-id="'+widget_id+'" data-widget_type_id="'+widget_type_id+'" data-module="'+widget_rawname+'" data-name="'+widget_name+'" data-widget_name="'+widget_type_id+'" data-icon="'+widget_icon+'">' +
				'<div class="menu-actions">' +
					'<i class="fa fa-times-circle-o close-simple-widget-menu" aria-hidden="true"></i>' +
					settings_html +
				'</div>' +
				'<h5 class="small-widget-title"><i class="fa '+widget_icon+'"></i> <span>'+widget_sub+'</span> <small>('+widget_name+')</small></h5>' +
				'<div class="small-widget-content">' +
				'</div>' +
				'<button type="button" class="btn btn-xs btn-danger remove-small-widget" data-widget_id="'+widget_id+'" data-widget_rawname="'+widget_rawname+'">'+_('Remove Widget')+'</button>' +
			'</div>';

		return html;
	},
	/**
	 * Show dashboard error
	 * @method showDashboardError
	 * @param  {string}           message The message to show
	 */
	showDashboardError: function(message) {
		//TODO: should we destroy the gird if it exists?
		$("#dashboard-content .module-page-widgets").html('<div class="dashboard-error"><div class="message"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><br/>'+message+'</div></div>');
	},
	/**
	 * Initalize Menu Dragging
	 * @method initMenuDragabble
	 */
	initMenuDragabble: function(){
		var $this = this,
				el = document.getElementById('side_bar_content');

		var sortable = Sortable.create(el, {
			draggable: ".custom-widget",
			filter: "i",
			onUpdate: function (evt) {
				sortable.option("disabled",true);
				$this.saveSidebarContent(function() {
					sortable.option("disabled",false);
				});
			}
		});
	},
	/**
	 * Initalize Dashboard Tab Dragging
	 * @method initDashboardDragabble
	 */
	initDashboardDragabble: function() {
		var $this = this,
				el = document.getElementById('all_dashboards');

		var sortable = Sortable.create(el, {
			draggable: ".dashboard-menu",
			filter: "i",
			onUpdate: function (evt) {
				sortable.option("disabled",true);
				$this.saveDashboardOrder(function() {
					sortable.option("disabled",false);
				});
			}
		});
	},
	/**
	 * Save Dashboard Tab order
	 * @method saveDashboardOrder
	 * @param  {Function}         callback Callback function when finished saving
	 */
	saveDashboardOrder: function(callback) {
		var dashboardOrder = [],
				$this = this;
		$this.activateFullLoading();
		$("#all_dashboards li").each(function() {
			dashboardOrder.push($(this).data("id"));
		});
		$.post( UCP.ajaxUrl,
			{
				module: "Dashboards",
				command: "reorder",
				order: dashboardOrder
			},
			function( data ) {
				if(typeof callback === "function") {
					callback();
				}
		}).always(function() {
			$this.deactivateFullLoading();
		}).fail(function(jqXHR, textStatus, errorThrown) {
			UCP.showAlert(textStatus,'warning');
		});
	},
	/**
	 * Open(Show) the extra widget menu
	 * @method openExtraWidgetMenu
	 * @param  {Function}          callback callback function when the menu is finished opening
	 */
	openExtraWidgetMenu: function(callback) {
		var previous = this.widgetMenuOpen;
		this.widgetMenuOpen = true;
		if(previous) {
			if(typeof callback === "function") {
				callback();
			}
			return;
		}
		$(".side-menu-widgets-container").one("transitionend",function() {
			if(typeof callback === "function") {
				callback();
			}
		});
		$(".side-menu-widgets-container").addClass("open");
	},
	/**
	 * Close the side bar menu
	 * @method closeExtraWidgetMenu
	 * @param  {Function}           callback Callback when the menu is finished closing
	 */
	closeExtraWidgetMenu: function(callback) {
		var previous = this.widgetMenuOpen;
		this.widgetMenuOpen = false;
		if(!previous) {
			$("#side_bar_content li.active").removeClass("active");
			if(typeof callback === "function") {
				callback();
			}
			return;
		}
		$(".side-menu-widgets-container").one("transitionend",function() {
			$(".widget-extra-menu:visible").addClass("hidden");
			$("#side_bar_content li.active").removeClass("active");
			$(document).trigger("post-body.closesimplewidget");
			if(typeof callback === "function") {
				callback();
			}
		});
		$(".side-menu-widgets-container").removeClass("open");
	},
	/**
	 * Initialize Side Bar Widgets
	 * @method initLeftNavBarMenus
	 */
	initLeftNavBarMenus: function(){
		var $this = this;

		$(document).on("click", ".close-simple-widget-menu", function() {
			$this.closeExtraWidgetMenu();
		});

		/**
		 * Click to show the simple widget settings
		 */
		$(document).on("click", ".show-simple-widget-settings", function() {
			var parent = $(this).parents(".widget-extra-menu"),
					rawname = parent.data("module"),
					widget_type_id = parent.data("widget_type_id"),
					widget_id = parent.data("id"),
					settings_container = $('#widget_settings .modal-body'),
					title = parent.data("name"),
					name = parent.data("widget_name");

			$('#widget_settings').attr("data-rawname",rawname);
			$('#widget_settings').data('rawname',rawname);

			$('#widget_settings').attr("data-id",widget_id);
			$('#widget_settings').data('id',widget_id);

			$('#widget_settings').attr("data-widget_type_id",widget_type_id);
			$('#widget_settings').data('widget_type_id',widget_type_id);

			$this.activateSettingsLoading();
			$("#widget_settings .modal-title").html('<i class="fa fa-cog" aria-hidden="true"></i> '+title+" "+_("Settings")+" ("+name+")");
			$('#widget_settings').modal('show');
			$('#widget_settings').one('shown.bs.modal', function() {
				$this.getSimpleSettingsContent(settings_container, widget_id, widget_type_id, rawname, function() {
					$("#widget_settings .modal-body .fa-question-circle").click(function(e) {
						e.preventDefault();
						e.stopPropagation();
						var f = $(this).parents("label").attr("for");
						$(".help-block").addClass('help-hidden');
						$('.help-block[data-for="'+f+'"]').removeClass('help-hidden');
					});
					$(document).trigger("post-body.simplewidgetsettings",[ widget_id ]);
				});
			});
		});

		/**
		 * Click the settings cog on a widget
		 */
		$(document).on("click", ".settings-widget", function(event){
			event.preventDefault();
			event.stopPropagation();

			var widget_type_id = 'user',
					widget_id = 'user',
					rawname = 'settings',
					settings_container = $('#widget_settings .modal-body');
			$this.activateSettingsLoading();
			$("#widget_settings .modal-title").html('<i class="fa fa-cog" aria-hidden="true"></i> '+_("User Settings"));
			$('#widget_settings').modal('show');
			$('#widget_settings').one('shown.bs.modal', function() {
				$this.getSimpleSettingsContent(settings_container, widget_id, widget_type_id, rawname, function() {
					$("#widget_settings .modal-body .fa-question-circle").click(function(e) {
						e.preventDefault();
						e.stopPropagation();
						var f = $(this).parents("label").attr("for");
						$(".help-block").addClass('help-hidden');
						$('.help-block[data-for="'+f+'"]').removeClass('help-hidden');
					});
					$(document).trigger("post-body.simplewidgetsettings",[ widget_id ]);
				});
			});
		});

		/**
		 * Click sidebar widgets (Simple widgets)
		 */
		$(document).on("click", ".custom-widget i", function(event){
			event.preventDefault();
			event.stopPropagation();

			var widget = $(this).parents(".custom-widget");

			//We are already looking at it so close it and move on
			if(widget.hasClass("active")) {
				$this.closeExtraWidgetMenu();
				return;
			}

			var clicked_module = widget.find("a").data("rawname"),
					clicked_id = widget.find("a").data("widget_type_id"),
					widget_id = widget.find("a").data("id"),
					content_object = $("#menu_"+widget_id).find(".small-widget-content");

			$("#side_bar_content li.active").removeClass("active");
			widget.addClass("active");

			$(".widget-extra-menu:visible").addClass("hidden");

			$this.activateWidgetLoading(content_object);
			$("#menu_"+widget_id).removeClass("hidden");
			$this.openExtraWidgetMenu();

			$.post( UCP.ajaxUrl,
				{
					module: "Dashboards",
					command: "getsimplewidgetcontent",
					id: clicked_id,
					rawname: clicked_module,
					uuid: uuid
				},
				function( data ) {
					if(typeof data.html !== "undefined"){
						content_object.html(data.html);

						UCP.callModuleByMethod(clicked_module,"displaySimpleWidget",widget_id);
						$(document).trigger("post-body.simplewidget",[ widget_id ]);
					}else {
						UCP.showAlert(_("There was an error getting the widget information, try again later"), "danger");
					}
				}).fail(function(jqXHR, textStatus, errorThrown) {
					UCP.showAlert(textStatus,'warning');
				});
		});
	},
	/**
	 * Initialize the item lock buttons
	 * @method initLockItemButtons
	 * @return {[type]}            [description]
	 */
	initLockItemButtons: function(){
		var $this = this;

		/**
		 * Lock a single widget on a dashboard
		 */
		$(document).on("click", ".lock-widget", function(event){
			event.preventDefault();
			event.stopPropagation();

			if(window.innerWidth <= 768) {
				UCP.showAlert(_("Widgets can not be locked on this device"),"warning");
				return;
			}

			var locked = $(this).find("i").hasClass("fa-lock"),
				id = $(this).data("widget_id"),
				grid = $('.grid-stack').data('gridstack');

			if(locked) {
				$(this).find("i").removeClass().addClass("fa fa-unlock-alt");
			} else {
				$(this).find("i").removeClass().addClass("fa fa-lock");
			}
			if($(".grid-stack-item[data-id="+id+"]").data("no-resize") != "true") {
				grid.resizable($(".grid-stack-item[data-id="+id+"]"), locked);
			}

			//set locking on widgets
			grid.movable($(".grid-stack-item[data-id="+id+"]"), locked);
			grid.locked($(".grid-stack-item[data-id="+id+"]"), !locked);

			//save layout
			$this.saveLayoutContent();
		});

		/**
		 * Lock all widgets on a dashboard
		 * TODO: this only works with the current dashboard for now
		 */
		$(document).on("click", ".lock-dashboard", function(event){
			event.preventDefault();
			event.stopPropagation();

			if($(this).hasClass("fa-unlock-alt")) {
				$(this).removeClass("fa-unlock-alt").addClass("fa-lock");
				$(".widget-options .fa-unlock-alt").click();
			} else {
				$(this).removeClass("fa-lock").addClass("fa-unlock-alt");
				$(".widget-options .fa-lock").click();
			}
		});
	},
	htmlEntities: function(str) {
		return $("<div/>").text(str).html();
	},
	/**
	 * Initalize the document remove buttons
	 * @method initRemoveItemButtons
	 */
	initRemoveItemButtons: function(){
		var $this = this;

		/**
		 * Remove widget button
		 */
		$(document).on("click", ".remove-widget", function(event){
			//stop browser
			event.preventDefault();
			event.stopPropagation();

			var widget_id = $(this).data("widget_id");
			var widget_rawname = $(this).data("widget_rawname");
			var widget_type_id = $(this).data("widget_type_id");

			UCP.showConfirm(_("Are you sure you want to delete this widget?"), "warning", function() {
				var grid = $('.grid-stack').data('gridstack');
				//remove widget
				grid.removeWidget($(".grid-stack-item[data-id='" + widget_id + "']"));
				//save layout
				$this.saveLayoutContent();
				//call module method
				UCP.callModuleByMethod(widget_rawname,"deleteWidget",widget_type_id,$this.activeDashboard);
				//TODO: does this need a document trigger?
			});
		});

		/**
		 * Remove small widget code
		 */
		$(document).on("click", ".remove-small-widget", function(event){
			//stop browser
			event.preventDefault();
			event.stopPropagation();

			var widget_to_remove = $(this).data("widget_id"),
					widget_rawname = $(this).data("widget_rawname"),
					sidebar_object_to_remove = $("#side_bar_content li.custom-widget[data-widget_id='" + widget_to_remove + "']"),
					sidebar_menu_to_remove = $(".side-menu-widgets-container .widget-extra-menu[data-id='menu_" + widget_rawname + "_"+widget_to_remove+"']");

			UCP.callModuleByMethod(widget_rawname,"deleteSimpleWidget",widget_to_remove);

			sidebar_object_to_remove.remove();

			//close the menu
			$this.closeExtraWidgetMenu(function() {
				sidebar_menu_to_remove.remove();
			});

			//save the page
			$this.saveSidebarContent();
		});

		/**
		 * Edit Dashboard Button
		 */
		$(document).on("click", ".edit-dashboard", function(event){
			//stop the browser
			event.preventDefault();
			event.stopPropagation();

			var parent = $(this).parents('.dashboard-menu'),
					dashboard_id = parent.data("id"),
					title = parent.find("a");

			//se the input to what we have now
			$('#edit_dashboard_name').val(title.text());

			//trigger when the modal is shown (once)
			$('#edit_dashboard').one('shown.bs.modal', function () {
				//unbind because we were bound previously
				$("#edit_dashboard").off("keydown");
				$("#edit_dashboard").on('keydown', function(event) {
					switch(event.keyCode) {
						case 13: //detect enter
							$("#edit_dashboard_btn").click();
						break;
					}
				});
				//click event
				$("#edit_dashboard_btn").one("click",function() {
					//get the new name
					var name = $this.htmlEntities($("#edit_dashboard_name").val());
					//show loading window so nothing changes
					$this.activateFullLoading();
					//send it off and save!
					$.post( UCP.ajaxUrl,
						{
							module: "Dashboards",
							command: "rename",
							id: dashboard_id,
							name: name
						},
						function( data ) {
							if(data.status) {
								title.replaceWith('<a data-dashboard>'+name+'</a>');
								$("#edit_dashboard").modal('hide');
							} else {
								UCP.showAlert(_("Something went wrong removing the dashboard"), "danger");
							}
					}).always(function() {
						$this.deactivateFullLoading();
					}).fail(function(jqXHR, textStatus, errorThrown) {
						UCP.showAlert(textStatus,'warning');
					});
				});
				//focus on the name
				$('#dashboard_name').focus();
			});
			//show the modal
			$("#edit_dashboard").modal('show');
		});

		/**
		 * Remve Dashboard
		 */
		$(document).on("click", ".remove-dashboard", function(event){
			//stop browser from doing what it wants
			event.preventDefault();
			event.stopPropagation();

			var dashboard_id = $(this).parents('.dashboard-actions').data("dashboard_id");

			//Check confirm
			UCP.showConfirm(_("Are you sure you want to delete this dashboard?"), "warning", function() {

				//show loading window so nothing changes
				$this.activateFullLoading();

				$.post( UCP.ajaxUrl ,
					{
						module: "Dashboards",
						command: "remove",
						id: dashboard_id
					},
					function( data ) {
						if (data.status) {
							$(".dashboard-menu[data-id='" + dashboard_id + "']").remove();

							if(dashboard_id == $this.activeDashboard) {
								if($(".dashboard-menu").length > 0) {
									$(".dashboard-menu").first().find("a").click();
								} else {
									$this.showDashboardError(_("You have no dashboards. Click here to add one"));
									$("#dashboard-content .dashboard-error").css("cursor","pointer");
									$("#dashboard-content .dashboard-error").click(function() {
										$("#add_new_dashboard").click();
									});
								}
							}

						} else {
							UCP.showAlert(_("Something went wrong removing the dashboard"), "danger");
						}
				}).always(function() {
					$this.deactivateFullLoading();
				}).fail(function(jqXHR, textStatus, errorThrown) {
					UCP.showAlert(textStatus,'warning');
				});
			});

		});
	},
	/**
	 * Initialize Widget Add Buttons
	 * TODO: needs cleanup
	 * @method initAddWidgetsButtons
	 */
	initAddWidgetsButtons: function(){
		$("#add_widget").on("show.bs.modal",function() {
			$this.closeExtraWidgetMenu();
			$(".navbar-nav .add-widget").addClass("active");
		});
		//tab select scroll position memory
		$('#add_widget .nav-tabs a[data-toggle=tab]').on('shown.bs.tab', function (e) {
			$("#add_widget .bhoechie-tab-menu .list-group-item").each(function() {
				$(this).data("position",$(this).position().top);
			});
			var container = $("#add_widget .tab-content");
			$(e.relatedTarget).data("scroll",container.scrollTop());

			var scroll = $(e.target).data("scroll");
			if(typeof scroll !== "undefined") {
				container.scrollTop(scroll);
			} else {
				container.scrollTop(0);
			}
		});
		$("#add_widget").on("shown.bs.modal",function() {
			$("#add_widget .bhoechie-tab-menu .list-group-item").each(function() {
				$(this).data("position",$(this).position().top);
			});
			$("#add_widget .tab-content").off("scroll");
			$("#add_widget .tab-content").scroll(function() {
				var top = $(this).scrollTop();
				var bottom = $(this).scrollTop() + $(this).height();
				if(($(this).find(".tab-pane.active .bhoechie-tab-menu").height() - (top - 30)) > $(this).height()) {
					$(this).find(".tab-pane.active .bhoechie-tab").css("top",top);
				}

				var active  = $(this).find(".tab-pane.active .list-group-item.active");
				active.removeClass("top-locked bottom-locked");
				if(top > (active.data("position") + 10)) {
					active.addClass("top-locked");
				} else if(bottom < (active.data("position") + active.height())) {
					active.addClass("bottom-locked");
				}
			});
		});
		$("#add_widget").on("hidden.bs.modal",function() {
			$(".navbar-nav .add-widget").removeClass("active top-locked bottom-locked");
		});
		var $this = this;
		$(document).on("click",".add-widget-button", function(){
			if($this.activeDashboard === null) {
				UCP.showAlert(_("There is no active dashboard to add widgets to"), "danger");
				return;
			}
			var current_dashboard_id = $this.activeDashboard,
					widget_id = $(this).data('widget_id'),
					widget_module_name = $(this).data('widget_module_name'),
					widget_rawname = $(this).data('rawname'),
					widget_name = $(this).data('widget_name'),
					new_widget_id = uuid.v4(),
					icon = allWidgets[widget_rawname.modularize()].icon,
					widget_info = allWidgets[widget_rawname.modularize()].list[widget_id],
					widget_has_settings = false,
					default_size_x = 2,
					default_size_y = 2,
					min_size_x = null,
					min_size_y = null,
					max_size_x = null,
					max_size_y = null,
					resizable = true,
					dynamic = false;

			if(typeof widget_info.defaultsize !== "undefined") {
				default_size_x = widget_info.defaultsize.width;
				default_size_y = widget_info.defaultsize.height;
			}

			if(typeof widget_info.maxsize !== "undefined") {
				max_size_x = widget_info.maxsize.width;
				max_size_y = widget_info.maxsize.height;
			}

			if(typeof widget_info.minsize !== "undefined") {
				min_size_x = widget_info.minsize.width;
				min_size_y = widget_info.minsize.height;
			}

			if(typeof widget_info.hasSettings !== "undefined") {
				widget_has_settings = widget_info.hasSettings;
			}

			if(typeof widget_info.resizable !== "undefined") {
				resizable = widget_info.resizable;
			}

			if(typeof widget_info.dynamic !== "undefined") {
				dynamic = widget_info.dynamic;
			}

			//Checking if the widget is already on the dashboard
			var object_on_dashboard = ($(".grid-stack-item[data-rawname='"+widget_rawname+"'][data-widget_type_id='"+widget_id+"']").length > 0);

			if(dynamic || !object_on_dashboard) {

				$this.activateFullLoading();

				$.post( UCP.ajaxUrl ,
					{
						module: "Dashboards",
						command: "getwidgetcontent",
						id: widget_id,
						rawname: widget_rawname,
						uuid: new_widget_id
					},
					function( data ) {

						$("#add_widget").modal("hide");

						if(typeof data.html !== "undefined"){
							//So first we go the HTML content to add it to the widget
							var widget_html = data.html;
							var full_widget_html = $this.widget_layout(new_widget_id, widget_module_name, widget_name, widget_id, widget_rawname, widget_has_settings, widget_html, resizable, false);
							var grid = $('.grid-stack').data('gridstack');
							//We are adding the widget always on the position 1,1
							grid.addWidget($(full_widget_html), 1, 1, default_size_x, default_size_y, true, min_size_x, max_size_x, min_size_y, max_size_y);
							grid.resizable($("div[data-id='"+new_widget_id+"']"), resizable);
							UCP.callModuleByMethod(widget_rawname,"displayWidget",new_widget_id,$this.activeDashboard);
							$(document).trigger("post-body.widgets",[ new_widget_id, $this.activeDashboard ]);
						}else {
							UCP.showAlert(_("There was an error getting the widget information, try again later"), "danger");
						}
					}).always(function() {
						$this.deactivateFullLoading();
					}).fail(function(jqXHR, textStatus, errorThrown) {
						UCP.showAlert(textStatus,'warning');
					});
			} else {
				UCP.showAlert(_("You already have this widget on this dashboard"), "info");
			}
		});

		/**
		 * Add Small Widget Button Bind
		 */
		$(".add-small-widget-button").click(function(){

			var widget_id = $(this).data('id'),
					widget_rawname = $(this).data('rawname'),
					widget_name = $(this).data('name'),
					widget_sub = $(this).data('widget_type_id'),
					new_widget_id = uuid.v4(),
					widget_info = allSimpleWidgets[widget_rawname.modularize()].list[widget_id],
					widget_icon = $(this).data('icon'),
					hasSettings = false,
					dynamic = false;

			if(typeof widget_info.hasSettings !== "undefined") {
				hasSettings = widget_info.hasSettings;
			}

			if(typeof widget_info.dynamic !== "undefined") {
				dynamic = widget_info.dynamic;
			}

			//Checking if the widget is already on the dashboard

			var object_on_dashboard = ($("#side_bar_content li.custom-widget[data-widget_rawname='"+widget_rawname+"'][data-widget_type_id='"+widget_id+"']").length > 0);

			//Checking if the widget is already on the bar
			if(dynamic || !object_on_dashboard){

				$this.activateFullLoading();

				$.post( UCP.ajaxUrl,
					{
						module: "Dashboards",
						command: "getsimplewidgetcontent",
						id: widget_id,
						rawname: widget_rawname,
						uuid: new_widget_id
					},
					function( data ) {
						$("#add_widget").modal("hide");

						if(typeof data.html !== "undefined"){
							//get small widget layout
							var full_widget_html = $this.smallWidgetLayout(new_widget_id, widget_rawname, widget_name, widget_id, widget_icon);
							//get small widget menu layout
							var menu_widget_html = $this.smallWidgetMenuLayout(new_widget_id, widget_rawname, widget_name, widget_id, widget_icon, widget_sub, hasSettings);

							//add icon to sidebar
							if($("#side_bar_content .custom-widget").length) {
								//we already have an element on the sidebar so add to the end
								$("#side_bar_content .custom-widget").last().after(full_widget_html);
							} else {
								//add widget after the add button because we dont have anything there
								$("#side_bar_content .add-widget").after(full_widget_html);
							}

							//now add the menu (hidden) to the widgets container for expansion later
							$(".side-menu-widgets-container").append(menu_widget_html);

							//execute module method
							UCP.callModuleByMethod(widget_rawname,"addSimpleWidget",new_widget_id);

							//execute trigger
							$(document).trigger("post-body.addsimplewidget",[ new_widget_id, $this.activeDashboard ]);

							//save side bar
							$this.saveSidebarContent();
						}else {
							UCP.showAlert(_("There was an error getting the widget information, try again later"), "danger");
						}
					}).always(function() {
						$this.deactivateFullLoading();
					}).fail(function(jqXHR, textStatus, errorThrown) {
						UCP.showAlert(textStatus,'warning');
					});
			}else {
				UCP.showAlert(_("You already have this widget on the side bar"), "info");
			}
		});
	},
	/**
	 * Initiate Category Binds
	 * @method initCategoriesWidgets
	 */
	initCategoriesWidgets: function(){
		$("#add_widget .bhoechie-tab-container").each(function() {
			var parent = $(this);
			$(this).find(".list-group-item").click(function(e) {
				e.preventDefault();
				$(this).siblings('a.active').removeClass("active top-locked bottom-locked");
				$(this).addClass("active");
				var id = $(this).data("id");
				parent.find(".bhoechie-tab-content").removeClass("active top-locked bottom-locked");
				parent.find(".bhoechie-tab-content[data-id='"+id+"']").addClass("active");
			});
		});
	},
	/**
	 * Get Widget content
	 * TODO: This is duplicated in certain places!!!
	 * @method getWidgetContent
	 * @param  {string}           widget_id             The widget ID
	 * @param  {string}           widget_type_id        The widget type ID
	 * @param  {string}           widget_rawname        The widget rawname
	 * @param  {Function}         callback              Callback Function when done (success + complete)
	 */
	getWidgetContent: function(widget_id, widget_type_id, widget_rawname, callback){
		var $this = this,
				widget_content_object = $(".grid-stack-item[data-id='"+widget_id+"'] .widget-content");
		this.activateWidgetLoading(widget_content_object);

		$.post( UCP.ajaxUrl,
			{
				module: "Dashboards",
				command: "getwidgetcontent",
				id: widget_type_id,
				rawname: widget_rawname,
				uuid: widget_id
			},
			function( data ) {

				var widget_html = data.html;

				if(typeof data.html === "undefined"){
					widget_html = '<div class="alert alert-danger">'+_('Something went wrong getting the content of the widget')+'</div>';
				}

				widget_content_object.html(widget_html);
				UCP.callModuleByMethod(widget_rawname,"displayWidget",widget_id,$this.activeDashboard);
				setTimeout(function() {
					UCP.callModuleByMethod(widget_rawname,"resize",widget_id,$this.activeDashboard);
				},100);

			}).done(function() {
				if(typeof callback === "function") {
					callback();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				UCP.showAlert(textStatus,'warning');
			});
	},
	/**
	 * Get Simple Widget Settings Content
	 * @method getSimpleSettingsContent
	 * @param  {object}           widget_content_object jQuery object of the settings container
	 * @param  {string}           widget_id             The widget ID
	 * @param  {string}           widget_type_id        The widget type ID
	 * @param  {string}           widget_rawname        The widget rawname
	 * @param  {Function}         callback              Callback Function when done (success + complete)
	 */
	getSimpleSettingsContent: function(widget_content_object, widget_id, widget_type_id, widget_rawname, callback){
		var $this = this;

		$.post( UCP.ajaxUrl,
			{
				module: "Dashboards",
				command: "getsimplewidgetsettingscontent",
				id: widget_type_id,
				rawname: widget_rawname,
				uuid: widget_id
			},
			function( data ) {

				var widget_html = data.html;

				if(typeof data.html === "undefined"){
					widget_html = '<div class="alert alert-danger">'+_('Something went wrong getting the settings from the widget')+'</div>';
				}

				widget_content_object.html(widget_html);
				UCP.callModuleByMethod(widget_rawname,"displaySimpleWidgetSettings",widget_id);
			}).done(function() {
				if(typeof callback === "function") {
					callback();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				UCP.showAlert(textStatus,'warning');
			});
	},
	/**
	 * Get Module Settings Content
	 * @method getSettingsContent
	 * @param  {object}           widget_content_object jQuery object of the settings container
	 * @param  {string}           widget_id             The widget ID
	 * @param  {string}           widget_type_id        The widget type ID
	 * @param  {string}           widget_rawname        The widget rawname
	 * @param  {Function}         callback              Callback Function when done (success + complete)
	 */
	getSettingsContent: function(widget_content_object, widget_id, widget_type_id, widget_rawname, callback){
		var $this = this;

		$.post( UCP.ajaxUrl,
			{
				module: "Dashboards",
				command: "getwidgetsettingscontent",
				id: widget_type_id,
				rawname: widget_rawname,
				uuid: widget_id
			},
			function( data ) {

				var widget_html = data.html;

				if(typeof data.html === "undefined"){
					widget_html = '<div class="alert alert-danger">'+_('Something went wrong getting the settings from the widget')+'</div>';
				}

				widget_content_object.html(widget_html);
				UCP.callModuleByMethod(widget_rawname,"displayWidgetSettings",widget_id,$this.activeDashboard);
			}).done(function() {
				if(typeof callback === "function") {
					callback();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				UCP.showAlert(textStatus,'warning');
			});
	},
	/**
	 * Setup grid stack!
	 * @method setupGridStack
	 * @return {object}       The gridstack object!
	 */
	setupGridStack: function() {
		var gridstack = $(".grid-stack").data('gridstack');
		if(typeof gridstack === "undefined") {
			$('.grid-stack').gridstack({
				cellHeight: 35,
				verticalMargin: 10,
				animate: true,
				float: true,
				draggable: {
					handle: '.widget-title',
					scroll: false,
					appendTo: 'body'
				}
			});
			gridstack = $(".grid-stack").data('gridstack');
		}
		return gridstack;
	},
	/**
	 * Bind Grid Stack changes
	 * @method bindGridChanges
	 */
	bindGridChanges: function() {
		var $this = this;
		$('.grid-stack').on('resizestop', function(event, ui) {
			//Never on mobile, Always on Desktop
			if(window.innerWidth > 768) {
				UCP.callModulesByMethod("resize",ui.element.data("id"),$this.activeDashboard);
			}
		});

		$('.grid-stack').on('removed', function(event, items) {
			//Never on Desktop, Always on mobile
			if(window.innerWidth <= 768) {
				//save layout
				$this.saveLayoutContent();
			}
		});

		$('.grid-stack').on('added', function(event, items) {
			//Never on Desktop, Always on mobile
			if(window.innerWidth <= 768) {
				//save layout
				$this.saveLayoutContent();
			}
		});

		$('.grid-stack').on('change', function(event, items) {
			//This triggers on any bubbling change so if items
			//is undefined then return
			if(typeof items === "undefined") {
				return;
			}
			//Always on Desktop, Never on mobile
			if(window.innerWidth > 768) {
				//save layout
				$this.saveLayoutContent();
			}
		});
		//some gitchy crap going on here, we have to relock the widget
		$('.grid-stack').on('dragstop', function(event, ui) {
			var grid = $(".grid-stack").data('gridstack');
			$('.grid-stack .grid-stack-item:visible').not(".grid-stack-placeholder").each(function(){
				var el = $(this);
						locked = el.find(".lock-widget i").hasClass("fa-lock");
				grid.movable(el, !locked);
				grid.locked(el, locked);
				grid.resizable(el, !locked);
			});
		});
		//some gitchy crap going on here, we have to relock the widget
		$('.grid-stack').on('resizestop', function(event, ui) {
			var grid = $(".grid-stack").data('gridstack');
			$('.grid-stack .grid-stack-item:visible').not(".grid-stack-placeholder").each(function(){
				var el = $(this);
						locked = el.find(".lock-widget i").hasClass("fa-lock");
				grid.movable(el, !locked);
				grid.locked(el, locked);
				grid.resizable(el, !locked);
			});
		});
	},
	/**
	 * Setup Add Dashboard Button Binds
	 * @method setupAddDashboard
	 */
	setupAddDashboard: function() {
		var $this = this;
		$("#create_dashboard").click(function() {
			//make sure there is something in the name
			if ($("#dashboard_name").val().trim() === "") {
				//if empty then return back and focus on name
				UCP.showAlert(_("You must set a dashboard name!"),'warning', function() {
					$("#dashboard_name").focus();
				});
			} else {
				let dashboard_name = $this.htmlEntities($("#dashboard_name").val());
				//show loading screen while we save this dashboard
				$this.activateFullLoading();

				$.post( UCP.ajaxUrl, {module: "Dashboards", command: "add", name: dashboard_name}, function( data ) {
					if (!data.status) {
						UCP.showAlert(data.message,'warning');
					} else {
						var select = $("#all_dashboards li").length;
						var new_dashboard_html = '<li class="menu-order dashboard-menu" data-id="'+data.id+'"><a data-dashboard>'+dashboard_name+'</a> <div class="dashboard-actions" data-dashboard_id="'+data.id+'"><i class="fa fa-unlock-alt lock-dashboard" aria-hidden="true"></i><i class="fa fa-pencil edit-dashboard" aria-hidden="true"></i><i class="fa fa-times remove-dashboard" aria-hidden="true"></i></div></li>';
						$("#all_dashboards").append(new_dashboard_html);

						dashboards[data.id] = null;

						$(document).trigger("addDashboard",[data.id]);

						if(!select) {
							$("#all_dashboards li a").click();
						}

						//hide modal we are done
						$("#add_dashboard").modal("hide");
					}
				}).fail(function(jqXHR, textStatus, errorThrown) {
					UCP.showAlert(textStatus,'warning');
				}).always(function() {
					$this.deactivateFullLoading();
				});
			}
		});

		//dashboard tab click
		$(document).on("click",".dashboard-menu a[data-dashboard]", function(e) {
			//stop default browser actions
			e.preventDefault();
			e.stopPropagation();

			var gridstack = $(".grid-stack").data('gridstack'),
					id = $(this).parents(".dashboard-menu").data("id"),
					popstate = $(this).data("popstate");

			popstate = (typeof popstate !== "undefined") ? popstate : false;
			//we are on this dashboard. So do nothing
			if($this.activeDashboard == id) {
				return;
			}

			//remove active from any dashboard tab
			$(".dashboard-menu").removeClass("active");
			//remove the click block from all
			$(".dashboards a[data-dashboard]").removeClass("pjax-block");
			//add click block to this one
			$(this).addClass("pjax-block");
			//activate our tab
			$(".dashboard-menu[data-id='"+id+"']").addClass("active");
			//push browser history (pjax like) only if we aren't in a popstate event
			if(!popstate) {
				history.pushState({ activeDashboard: id }, $(this).text(), "?dashboard="+id);
			} else {
				$(this).data("popstate",false);
			}
			//set tab title
			$("title").text(_("User Control Panel") + " - " + $(this).text());
			//set our active dashboard
			$this.activeDashboard = id;

			if(typeof gridstack !== "undefined") {
				//destroy the grid (which also deletes the elements!)
				gridstack.destroy(true);
			}

			//add back grid container
			$("#module-page-widgets").html('<div class="grid-stack" data-dashboard_id="'+id+'">');

			//setup grid
			gridstack = $this.setupGridStack();

			//load widgets
			$this.activateFullLoading();
			var resave = false;
			async.each(dashboards[id], function(widget, callback) {
				//uppercase the module rawname
				var cased = widget.rawname.modularize();
				if(typeof allWidgets[cased] === "undefined") {
					callback();
					return;
				}
				//get loading html
				var widget_html = $this.activateWidgetLoading();
				//TODO: fix this
				widget.resizable = true;
				if(!widget.id.match(/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i)) {
					widget.id = uuid.v4();
					resave = true;
				}
				//get widget content
				var full_widget_html = $this.widget_layout(widget.id, widget.widget_module_name, widget.name, widget.widget_type_id, widget.rawname, widget.has_settings, widget_html, widget.resizable, widget.locked);
				//get max/min size of this widget
				var min_size_x = (typeof allWidgets[cased].list[widget.widget_type_id].minsize !== "undefined" && typeof allWidgets[cased].list[widget.widget_type_id].minsize.width !== "undefined") ? allWidgets[cased].list[widget.widget_type_id].minsize.width : null;
				var min_size_y = (typeof allWidgets[cased].list[widget.widget_type_id].minsize !== "undefined" && typeof allWidgets[cased].list[widget.widget_type_id].minsize.height !== "undefined") ? allWidgets[cased].list[widget.widget_type_id].minsize.height : null;
				var max_size_x = (typeof allWidgets[cased].list[widget.widget_type_id].maxsize !== "undefined" && typeof allWidgets[cased].list[widget.widget_type_id].maxsize.width !== "undefined") ? allWidgets[cased].list[widget.widget_type_id].maxsize.width : null;
				var max_size_y = (typeof allWidgets[cased].list[widget.widget_type_id].maxsize !== "undefined" && typeof allWidgets[cased].list[widget.widget_type_id].maxsize.height !== "undefined") ? allWidgets[cased].list[widget.widget_type_id].maxsize.height : null;
				//is this widget resizable?
				var resizable = (typeof allWidgets[cased].list[widget.widget_type_id].resizable !== "undefined") ? allWidgets[cased].list[widget.widget_type_id].resizable : true;

				//now add the widget
				gridstack.addWidget($(full_widget_html), widget.size_x, widget.size_y, widget.col, widget.row, false, min_size_x, max_size_x, min_size_y, max_size_y);

				//set resizable
				setTimeout(function() {
					gridstack.resizable($(".grid-stack-item[data-id="+widget.id+"]"), !widget.locked);
				});

				//set locked/or not
				gridstack.movable($(".grid-stack-item[data-id="+widget.id+"]"), !widget.locked);
				gridstack.locked($(".grid-stack-item[data-id="+widget.id+"]"), widget.locked);

				//get widget content
				$.post( UCP.ajaxUrl,
					{
						module: "Dashboards",
						command: "getwidgetcontent",
						id: widget.widget_type_id,
						rawname: widget.rawname,
						uuid: widget.id
					},
					function( data ) {
						//set the content from what we got
						$(".grid-stack .grid-stack-item[data-id="+widget.id+"] .widget-content").html(data.html);
						//execute module method
						UCP.callModuleByMethod(widget.rawname,"displayWidget",widget.id,$this.activeDashboard);
						//execute resize module method
						setTimeout(function() {
							UCP.callModuleByMethod(widget.rawname,"resize",widget.id,$this.activeDashboard);
						},100);

						//trigger event
						$(document).trigger("post-body.widgets",[ widget.id, $this.activeDashboard ]);
					}
				).done(function() {
					callback(); //trigger callback to async
				}).fail(function(jqXHR, textStatus, errorThrown) {
					callback(textStatus); //trigger error to async
				});
			}, function(err) {
				if(err) {
					//show error because there was an error
					UCP.showAlert(err,'danger');
				} else {
					//hide loading window
					$this.deactivateFullLoading();
					//bind grid events
					$this.bindGridChanges();
					//execute module methods
					UCP.callModulesByMethod("showDashboard",$this.activeDashboard);
					//trigger all widgets loaded event
					$(document).trigger("post-body.widgets",[ null, $this.activeDashboard ]);
					if(resave) {
						$this.saveLayoutContent();
					}
				}
			});
		});
	}
});

