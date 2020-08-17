var nzchatobj = jQuery.noConflict();

var nzsid = getcookie('sid', true);
var nztime1 = new Date().getTime();
var nztime2 = 0;
var nztouid = 0;
var nzquota = 0;
var nzlastid = 0;
var nzonol = false;
var nzcommandz = '';
var formhash = '';
var nzChatPopupContent = '';
var nzscroll = true;

function nzolover() {
	nzonol = true;
}

function nzolout() {
	nzonol = false;
}

function nzalert(text) {
	nzchatobj('#nzalertbox').text(text);
	nzchatobj('#nzalertbox').slideDown(200);
	setTimeout(function () {
		nzchatobj('#nzalertbox').slideUp(200);
	}, 2000);
}

nzchatobj.ajaxSetup({
	timeout: 2000,
	error: function(jqXHR, textStatus, errorThrown) {
		nzalert('เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อกับเซิฟเวอร์ได้');
		setTimeout(nzLoadText, nzsetting.reload);
    }
});

nzchatobj(function () {
	nzchatobj("#nzchatmessage").keydown(function (event) {
		if (event.keyCode == '13') {
			nzSend();
		}
		if (event.keyCode == '27') {
			nzTouid(0);
		}
	});
	nzchatobj('#nzchatmessage').bind('paste', function (e) {
		if (e.originalEvent.clipboardData.files.length !== 1) {
			return;
		}
		nzchatobj('#nzimguploadl').text('กำลังอัปโหลด...');
		nzchatobj('#nzimgupload').prop('disabled', true);
		var nzFormData = new FormData();
		nzFormData.append("pictures", e.originalEvent.clipboardData.files[0]);
		nzchatobj('#nzimgupload').val('');
		nzchatobj.ajax({
			url: 'plugin.php?id=th_chat:img',
			type: 'POST',
			data: nzFormData,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			success: function (data, textStatus, jqXHR) {
				if (typeof data.error === 'undefined') {
					seditor_insertunit('nzchat', '[img]' + data.url + '[/img]', '');
					nzchatobj("#nzchatmessage").focus();
				} else {
					nzalert(data.error);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				nzalert('เกิดข้อผิดพลาด: ' + textStatus);
			},
			complete: function (jqXHR, textStatus, errorThrown) {
				nzchatobj('#nzimguploadl').text('อัปโหลดไฟล์ภาพ');
				nzchatobj('#nzimgupload').prop('disabled', false);
			}
		});
	});
	nzchatobj('#nzimgup').click(function () {
		nzchatobj('#nzimgupload').click();
	});
	nzchatobj('#nzimgupload').change(function () {
		if (nzchatobj('#nzimgupload').val()) {
			nzchatobj('#nzimguploadl').text('กำลังอัปโหลด...');
			nzchatobj('#nzimgupload').prop('disabled', true);
			var nzFormData = new FormData();
			nzFormData.append("pictures", nzchatobj('#nzimgupload').prop('files')[0]);
			nzchatobj('#nzimgupload').val('');
			nzchatobj.ajax({
				url: 'plugin.php?id=th_chat:img',
				type: 'POST',
				data: nzFormData,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function (data, textStatus, jqXHR) {
					if (typeof data.error === 'undefined') {
						seditor_insertunit('nzchat', '[img]' + data.url + '[/img]', '');
						nzchatobj("#nzchatmessage").focus();
					} else {
						nzalert(data.error);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					nzalert('เกิดข้อผิดพลาด: ' + textStatus);
				},
				complete: function (jqXHR, textStatus, errorThrown) {
					nzchatobj('#nzimguploadl').text('อัปโหลดไฟล์ภาพ');
					nzchatobj('#nzimgupload').prop('disabled', false);
				}
			});
		}
	});
	if (nzsetting.autoconnect == 1) {
		nzLoadTextInit();
	}
	const button = nzchatobj('#nzemoji');
	const picker = new EmojiButton();
	picker.on('emoji', emoji => {
		nzchatobj('#nzchatmessage').val(nzchatobj('#nzchatmessage').val() + emoji);
	});
	nzchatobj('#nzemoji').click(function () {
		picker.togglePicker(this);
	});
	nzchatobj('#nznewmessage').click(function () {
		nzScrollChat(true);
	});
	nzchatobj("#nzchatmessage").keydown(function (event) {
		if (event.keyCode == '13') {
			nzSend();
		}
	});
	nzchatobj('#nzchatcontent').scroll(function () {
		var objDiv = document.getElementById("nzchatcontent");
		if (objDiv.scrollHeight - objDiv.scrollTop == nzchatheight) {
			nzchatobj("#nznewmessage").hide();
			objDiv.scrollTop = objDiv.scrollHeight;
		}
	});
});

function nzNotice() {
	nzcommandz = 'notice';
	nzchatobj(".nzquoteboxi").html('<div><span class="nzquoteboxh">แก้ไขประกาศ</span>: ' + nzchatobj('#nzchatnotice').html() + '</div><div class="nzcancel" onclick="nzTouid(0)" title="ยกเลิก"></div>');
	nzchatobj(".nzquoteboxo").show();
	nzchatobj("#nzchatcontent").css('height',nzchatheight - nzchatobj(".nzquoteboxo").height() - 2);
	nzScrollChat(true);
	nzchatobj('#nzchatmessage').val(nzchatobj('#nzchatnotice').text());
	nzchatobj('#nzchatmessage').focus();
}

function nzChatPopup(con) {
	nzChatPopupContent = nzchatobj(con).next(".nzchatpopuph").html();
	nzchatobj('#th_chat_popup_box').html(nzChatPopupContent);
	showWindow('th_chat_popup', 'plugin.php?id=th_chat:popup');
}

function nzChatBig() {
	if (nzchatheight < 830) {
		nzchatheight += 50;
		nzchatobj("#nzchatcontent").animate({
			height: nzchatheight
		}, 200);
		nzchatobj("#nzchatolcontent").animate({
			height: nzchatheight
		}, 200);
	}
}

function nzChatSmall() {
	if (nzchatheight > 230) {
		nzchatheight -= 50;
		nzchatobj("#nzchatcontent").animate({
			height: nzchatheight
		}, 200);
		nzchatobj("#nzchatolcontent").animate({
			height: nzchatheight
		}, 200);
	}
}

function nzChatReset() {
	nzchatheight = nzchatdefaultheight;
	nzchatobj("#nzchatcontent").animate({
		height: nzchatheight
	}, 200);
	nzchatobj("#nzchatolcontent").animate({
		height: nzchatheight
	}, 200);
}

function nzSend() {
	var data = nzchatobj.trim(nzchatobj("#nzchatmessage").val());
	if (data === '') {
		return false;
	}
	nztime1 = new Date().getTime();
	if (nztime1 > nztime2) {
		nzchatobj("#nzchatmessage").val('');
		nztime2 = nztime1 + nzsetting.delay;
		nzchatobj.post("plugin.php?id=th_chat:post" + formhash, {
			'text': data,
			'lastid': nzlastid,
			'touid': nztouid,
			'quota': nzquota,
			'command': nzcommandz
		}, function (data) {
			if (nzquota > 0 || nzcommandz == 'notice' || nzcommandz.substr(0, 4) == 'edit') {
				nzTouid(0);
			}
			data = JSON.parse(data);
			if (data.type == 1) {
				nzalert(data.error);
				if (data.script == 1) {
					eval(data.script);
				}
			} else {
				var listmess = sortObject(data);
				nzReadyForScroll();
				nzchatobj.each(listmess, function (k, v) {
					k = parseInt(k);
					if (k > nzlastid) {
						nzlastid = k;
						nzchatobj("#afterme").before(v);
						nzScrollChat();
					}
				});
				nzchatobj('.nzinnercontent img').one('load', function () {
					nzScrollChat();
				});
				if (nzsetting.iscleardata == 1) {
					var nzchatrr = nzchatobj(".nzchatrow");
					if (nzchatrr.size() > nzsetting.chatrowmax) {
						nzchatrr.first().remove();
					}
				}
			}
		});
	} else {
		nzalert('ส่งข้อความบ่อยไป');
	}
}

function nzCommand(command, xid) {
	if (command == '') {
		nzalert('คำสั่งผิดพลาด');
	} else {
		if (command == 'del') {
			var show = 'ลบข้อความ';
			var showid = ' ' + nzchatobj("#nzchatcontent" + xid).text();
		} else if (command == 'edit') {
			nzTouid(0);
			nzcommandz = 'edit ' + xid;
			nzchatobj(".nzquoteboxi").html('<div><div class="nzquoteboxh">แก้ไขข้อความ</div>' + nzchatobj("#nzrows_" + xid + " .nzinnercontent")[0].outerHTML + '</div><div class="nzcancel" onclick="nzTouid(0)" title="ยกเลิก"></div>');
			nzchatobj(".nzquoteboxi .nzcq").remove();
			nzchatobj(".nzquoteboxi .nzblockquote").remove();
			nzchatobj(".nzquoteboxi .nztag").remove();
			nzchatobj(".nzquoteboxi .nztag2").remove();
			nzchatobj(".nzquoteboxi .nztag3").remove();
			nzchatobj(".nzquoteboxo").show();
			nzchatobj("#nzchatcontent").css('height',nzchatheight - nzchatobj(".nzquoteboxo").height() - 2);
			nzScrollChat(true);
			nzchatobj("#nzchatmessage").val(nzchatobj(".nzquoteboxi .nzinnercontent").text());
			nzchatobj("#nzchatmessage").focus();
			return;
		} else if (command == 'ban') {
			var show = 'แบน';
			var showid = ' ' + nzchatobj("#nzolpro_" + xid).text() + '(UID: ' + xid + ')';
		} else if (command == 'unban') {
			var show = 'ปลดแบน';
			var showid = ' ' + nzchatobj("#nzolpro_" + xid).text() + '(UID: ' + xid + ')';
		} else if (command == 'clear') {
			var show = 'ล้างห้องแชท';
			var showid = '';
		}
		if (confirm('คุณต้องการที่จะ' + show + showid + ' ?') == true) {
			nzchatobj("#nzchatmessage").val("!" + command + " " + xid);
			nzSend();
		}
	}
}

function nzLoadTextInit() {
	nzchatobj.post("plugin.php?id=th_chat:newinit", function (data) {
		data = JSON.parse(data);
		nzlastid = data.lastid;
		nzchatobj("#nzchatcontent").html('<table class="nzcallrow">' + data.datahtml + '<tr id="afterme"><td colspan="2"></td></tr></table>');
		nzScrollChat(true);
		nzchatobj('.nzinnercontent img').one('load', function () {
			nzScrollChat();
		});
		if (!nzonol) {
			nzchatobj("#nzchatolcontent").html(data.datachatonline);
		}
		nzchatobj("#nzoltotal").html(data.chat_online_total);
		nzchatobj("#nzchatnotice").html(data.welcometext);
		setTimeout(nzLoadText, nzsetting.reload);
	});
}

function nzScrollChat(force = false) {
	var objDiv = document.getElementById("nzchatcontent");
	if (force) {
		nzscroll = true;
		nzchatobj("#nznewmessage").hide();
	}
	if (nzscroll) {
		objDiv.scrollTop = objDiv.scrollHeight;
	} else {
		nzchatobj("#nznewmessage").show();
	}
}

function nzReadyForScroll() {
	var objDiv = document.getElementById("nzchatcontent");
	if(nzchatobj(".nzquoteboxo:visible")){
		nzscroll = true;
	}else{
		if (objDiv.scrollHeight - objDiv.scrollTop == nzchatheight) {
			nzscroll = true;
		} else {
			nzscroll = false;
		}
	}
}

function nzLoadText() {
	nzchatobj.post("plugin.php?id=th_chat:new", {
		'lastid': nzlastid
	}, function (data) {
		data = JSON.parse(data);
		var listmess = sortObject(data.chat_row);
		nzReadyForScroll();
		nzchatobj.each(listmess, function (k, v) {
			k = parseInt(k);
			if (k > nzlastid) {
				nzlastid = k;
				nzchatobj("#afterme").before(v);
				nzScrollChat();
			}
		});
		nzchatobj('.nzinnercontent img').one('load', function () {
			nzScrollChat();
		});
		if (nzsetting.iscleardata == 1) {
			var nzchatrr = nzchatobj(".nzchatrow");
			if (nzchatrr.size() > nzsetting.chatrowmax) {
				nzchatrr.first().remove();
			}
		}
		if (data.chat_online) {
			if (!nzonol) {
				nzchatobj("#nzchatolcontent").html(data.chat_online);
			}
			nzchatobj("#nzoltotal").html(data.chat_online_total);
			}
		setTimeout(nzLoadText, nzsetting.reload);
	});
}

function nzQuota(i) {
	nzTouid(0);
	nzchatobj(".nzquoteboxi").html('<div class="nzinnercontent"><div class="nzblockquote">' + nzchatobj("#nzrows_" + i + " .nzuserat2")[0].outerHTML + ': ' + nzchatobj("#nzchatcontent" + i).html() + '</div></div><div class="nzcancel" onclick="nzTouid(0)" title="ยกเลิก"></div>');
	nzchatobj(".nzquoteboxi .nzcq").remove();
	nzchatobj(".nzquoteboxi .nzuserat2").toggleClass('nzuserat2 nzuserat');
	nzchatobj(".nzquoteboxo").show();
	nzchatobj("#nzchatcontent").css('height',nzchatheight - nzchatobj(".nzquoteboxo").height() - 2);
	nzScrollChat(true);
	nzquota = i;
	nzchatobj("#nzchatmessage").focus();
}

function nzAt(i) {
	seditor_insertunit('nzchat', '@' + i + ' ', '');
	nzchatobj("#nzchatmessage").focus();
}

function nzTouid(i) {
	if (i > 0) {
		nzTouid(0);
		nzchatobj(".nzquoteboxi").html('<div><span class="nzquoteboxh">กระซิบถึง</span> <img src="uc_server/avatar.php?uid=' + i +'&size=small" class="nzchatavatar" width="32" height="32" onerror="this.src=\'uc_server/images/noavatar_small.gif\';" align="absmiddle"> ' + nzchatobj(".nzat_" + i).last()[0].outerHTML + '</div><div class="nzcancel" onclick="nzTouid(0)" title="ยกเลิก"></div>');
		nzchatobj(".nzquoteboxi .nzcq").remove();
		nzchatobj(".nzquoteboxi .nzinnercontent").remove();
		nzchatobj(".nzquoteboxo").show();
		nzchatobj("#nzchatcontent").css('height',nzchatheight - nzchatobj(".nzquoteboxo").height() - 2);
		nzScrollChat(true);
		nztouid = i;
	} else {
		nzchatobj("#nzchatcontent").css('height',nzchatheight);
		if(nzcommandz.substr(0, 4) == 'edit'){
			if(nzchatobj(".nzquoteboxi .nzinnercontent").text() == nzchatobj('#nzchatmessage').val()){
				nzchatobj('#nzchatmessage').val('');
			}
		}else if(nzcommandz == 'notice'){
			if (nzchatobj('#nzchatmessage').val() == nzchatobj('#nzchatnotice').text()) {
				nzchatobj('#nzchatmessage').val('');
			}
		}
		nzchatobj(".nzquoteboxi").html('');
		nzchatobj("#nztouid").html("");
		nzchatobj(".nzquoteboxo").hide();
		nztouid = 0;
		nzquota = 0;
		nzcommandz = '';
	}
}

function nzReload() {
	nzchatobj("#nzchatolcontent").html('');
	nzchatobj("#nzchatnotice").html('กำลังโหลดประกาศล่าสุด...');
	nzchatobj("#nzchatcontent").html('<br /><br /><br /><br /><br /><br /><center><img src="source/plugin/th_chat/images/loading.svg" alt="Load" /></center>');
	nzchatobj.post("plugin.php?id=th_chat:newinit", function (data) {
		data = JSON.parse(data);
		nzlastid = data.lastid;
		nzchatobj("#nzchatcontent").html('<table class="nzcallrow">' + data.datahtml + '<tr id="afterme" style="display: none;"><td class="nzavatart"></td><td class="nzcontentt"></td></tr></table>');
		nzScrollChat(true);
		nzchatobj('.nzinnercontent img').one('load', function () {
			nzScrollChat();
		});
		nzchatobj("#nzchatolcontent").html(data.datachatonline);
		nzchatobj("#nzchatnotice").html(data.welcometext);
	});
}

function nzClean() {
	nzchatobj(".nzchatrow").fadeOut('slow');
}

function nzCheckImg(i) {
	var maxheight = 240;
	var maxwidth = 500;
	var w = parseInt(i.width);
	var h = parseInt(i.height);
	if (w > maxwidth) {
		i.style.cursor = "pointer";
		i.onclick = function () {
			var iw = window.open(this.src, 'ImageViewer', 'resizable=1');
			iw.focus();
		};
		h = (maxwidth / w) * h;
		w = maxwidth;
		i.height = h;
		i.width = w;
	}
	if (h > maxheight) {
		i.style.cursor = "pointer";
		i.onclick = function () {
			var iw = window.open(this.src, 'ImageViewer', 'resizable=1');
			iw.focus();
		};
		i.width = (maxheight / h) * w;
		i.height = maxheight;
	}
}

function sortObject(a) {
	var b = {},
		c, d = [];
	for (c in a) {
		if (a.hasOwnProperty(c)) {
			d.push(c);
		}
	}
	d.sort();
	for (c = 0; c < d.length; c++) {
		b[d[c]] = a[d[c]]
	}
	return b;
}