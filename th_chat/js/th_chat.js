var nzchatobj = jQuery.noConflict();
var nzsid = getcookie('sid', true);
var nztime1 = new Date().getTime();
var nztime2 = 0;
var nztouid=0;
var nzquota=0;
var nzlastid=0;
var nzonol='no';
var nzcommandz = '';
var formhash = '';
var nzChatPopupContent = '';
var nzscroll = true;
function nzolover() {nzonol='yes';}
function nzolout() {nzonol='no';}
function nzalert(text){
	nzchatobj('#nzalertbox').text(text);
	nzchatobj('#nzalertbox').slideDown();
	setTimeout(function(){ nzchatobj('#nzalertbox').slideUp(); }, 3000);
}
nzchatobj(function() {
	nzchatobj("#nzchatmessage").keydown(function(event){
		if (event.keyCode == '13') {
			nzSend();
		}
	});
	nzchatobj("#nznoticecheck").change(function(event){
		if(nzchatobj(this).is(':checked')){
			nzcommandz='notice';
			nzchatobj('.nzchatrow').each(function() {
				if(nzchatobj(this).css('background-color')=='rgb(255, 187, 187)'){
					nzchatobj(this).css('background-color', 'unset');
				}
			});
			nzchatobj('#nzchatmessage').val(nzchatobj('#nzchatnotice').text());
			nzchatobj('#nzchatmessage').focus();
		}else{
			if(nzchatobj('#nzchatmessage').val()==nzchatobj('#nzchatnotice').text()){
				nzchatobj('#nzchatmessage').val('');
				nzchatobj('#nzchatmessage').focus();
			}
			nzcommandz='';
		}
	});
	nzchatobj('#nzchatmessage').bind('paste', function(e){
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
			success: function(data, textStatus, jqXHR)
			{
				if(typeof data.error === 'undefined')
				{
					seditor_insertunit('nzchat', '[img]'+data.url+'[/img]', '');
					nzchatobj("#nzchatmessage").focus();
				}
				else
				{
					nzalert(data.error);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				nzalert('เกิดข้อผิดพลาด: ' + textStatus);
			},
			complete: function(jqXHR, textStatus, errorThrown)
			{
				nzchatobj('#nzimguploadl').text('อัปโหลดไฟล์ภาพ');
				nzchatobj('#nzimgupload').prop('disabled', false);
			}
		});
	});
	nzchatobj('#nzimgupload').change(function() {
		if(nzchatobj('#nzimgupload').val()){
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
				success: function(data, textStatus, jqXHR)
				{
					if(typeof data.error === 'undefined')
					{
						seditor_insertunit('nzchat', '[img]'+data.url+'[/img]', '');
						nzchatobj("#nzchatmessage").focus();
					}
					else
					{
						nzalert(data.error);
					}
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					nzalert('เกิดข้อผิดพลาด: ' + textStatus);
				},
				complete: function(jqXHR, textStatus, errorThrown)
				{
					nzchatobj('#nzimguploadl').text('อัปโหลดไฟล์ภาพ');
					nzchatobj('#nzimgupload').prop('disabled', false);
				}
			});
		}
	});
	if(nzsetting.autoconnect==1){
		nzLoadTextInit();
	}
	const button = nzchatobj('#nzemoji');
	const picker = new EmojiButton();
	picker.on('emoji', emoji => {
		nzchatobj('#nzchatmessage').val(nzchatobj('#nzchatmessage').val()+emoji);
	});
	nzchatobj('#nzemoji').click(function(){
		picker.togglePicker(this);
	});
	nzchatobj('#nznewmessage').click(function(){
		nzScrollChat(true);
	});
	nzchatobj("#nzchatmessage").keydown(function(event){
		if (event.keyCode == '13') {
			nzSend();
		}
	});
});
function nzChatPopup(con){
	nzChatPopupContent = nzchatobj(con).next(".nzchatpopuph").html();
	nzchatobj('#th_chat_popup_box').html(nzChatPopupContent);
	showWindow('th_chat_popup', 'plugin.php?id=th_chat:popup');
}
function nzChatBig(){
	if(nzchatheight<830){
		nzchatheight+=50;
		nzchatobj("#nzchatcontent").animate({height:nzchatheight}, 200 );
		nzchatobj("#nzchatolcontent").animate({height:nzchatheight+4}, 200 );
	}
}
function nzChatSmall(){
	if(nzchatheight>130){
		nzchatheight-=50;
		nzchatobj("#nzchatcontent").animate({height:nzchatheight}, 200 );
		nzchatobj("#nzchatolcontent").animate({height:nzchatheight+4}, 200 );
	}
}
function nzChatReset(){
	nzchatheight=nzchatdefaultheight;
	nzchatobj("#nzchatcontent").animate({height:nzchatheight}, 200 );
	nzchatobj("#nzchatolcontent").animate({height:nzchatheight+4}, 200 );
}
function nzSend(){
	var data=nzchatobj.trim(nzchatobj("#nzchatmessage").val());
	if(data===''){
		return false;
	}
	nztime1 = new Date().getTime();
	if(nztime1>nztime2){
		nzchatobj("#nzchatmessage").val('');
		nztime2 = nztime1 + nzsetting.delay;
		nzchatobj.post("plugin.php?id=th_chat:post"+formhash, {'text':data,'lastid':nzlastid,'touid':nztouid,'quota':nzquota,'command':nzcommandz},function(data) {
			if(nzcommandz=='notice'){
				nzcommandz='';
				nzchatobj('#nznoticecheck').removeAttr('checked');
			}else{
				nzcommandz='';
				nzchatobj('.nzchatrow').each(function() {
					if(nzchatobj(this).css("background-color")=='rgb(255, 187, 187)'){
						nzchatobj(this).css("background-color", "unset");
					}
				});
			}
			if(nzquota>0){
				nzTouid(0);
			}
			data = JSON.parse(data);
			if(data.type==1){
				nzalert(data.error);
				if(data.script_add==1){
					eval(data.script);
				}
			}else{
				var listmess = sortObject(data);
				nzReadyForScroll();
				nzchatobj.each(listmess, function(k, v) {
					k=parseInt(k);
					if(k>nzlastid){
						nzlastid=k;
						nzchatobj("#afterme").before(v);
						nzScrollChat();
					}
				});
				nzchatobj('.nzinnercontent img').one('load',function() {
					nzScrollChat();
				});
				if(nzsetting.iscleardata==1){
					var nzchatrr = nzchatobj(".nzchatrow");
					if(nzchatrr.size()>nzsetting.chatrowmax){
					nzchatrr.first().remove();
					}
				}
			}
		});
	}else{
		nzalert('ส่งข้อความบ่อยไป');
	}
}
function nzCommand(command,xid){
	if(command==''){
		nzalert('คำสั่งผิดพลาด');
	}else{
		if(command=='del'){
			var show='ลบข้อความ';var showid = ' ' + nzchatobj("#nzchatcontent"+xid).text();
		}else if(command=='edit'){
			var nzsamedit = false;
			nzchatobj('.nzchatrow').each(function() {
				if(nzchatobj(this).css("background-color")=='rgb(255, 187, 187)'){
					nzchatobj(this).css("background-color", "unset");
					if(nzchatobj("#nzchatmessage").val()==nzchatobj("#nzchatcontent"+xid).text()){
						nzchatobj("#nzchatmessage").val('');
					}
					if(nzchatobj(this).attr('id')=='nzrows_'+xid){
						nzcommandz='';nzsamedit = true;return;
					}
				}
			});
			if(nzcommandz=='notice'){
				nzchatobj('#nznoticecheck').removeAttr('checked');
			}
			if(!nzsamedit){
				nzcommandz='edit '+xid;
				nzchatobj('#nzrows_'+xid).css("background-color", "#fbb");
				nzchatobj("#nzchatmessage").val(nzchatobj("#nzchatcontent"+xid).text());
				nzchatobj("#nzchatmessage").focus();
				return;
			}else{
				return;
			}
		}
		else if(command=='ban'){var show='แบน';var showid = ' ' + nzchatobj("#nzolpro_"+xid).text() + '(UID: '+xid+')';}
		else if(command=='unban'){var show='ปลดแบน';var showid = ' ' + nzchatobj("#nzolpro_"+xid).text() + '(UID: '+xid+')';}
		else if(command=='point'){var show='ให้คะแนน';var n=xid.split("|");var showid = ' ' + n[1] + ' แก่ ' + nzchatobj("#nzolpro_"+n[0]).text() + '(UID: '+xid+')';}
		else if(command=='clear'){var show='ล้างห้องแชท';var showid = '';}
		if(confirm('คุณต้องการที่จะ'+show+showid+' ?')==true){
			nzchatobj("#nzchatmessage").val("!"+command+" "+xid);
			nzSend();
		}
	}
}

function nzLoadTextInit(){
	nzchatobj.post("plugin.php?id=th_chat:newinit",function(data){
		data = JSON.parse(data);
		nzlastid=data.lastid;
		nzchatobj("#nzchatcontent").html('<table class="nzcallrow">'+data.datahtml+'<tr id="afterme" style="height:0px;"><td class="nzavatart"></td><td class="nzcontentt"></td></tr></table>');
		nzScrollChat(true);
		nzchatobj('.nzinnercontent img').one('load',function() {
			nzScrollChat();
		});
		if(nzonol=='no'){
			nzchatobj("#nzchatolcontent").html(data.datachatonline);
		}
		nzchatobj("#nzoltotal").html(data.chat_online_total);
		nzchatobj("#nzchatnotice").html(data.welcometext);
		setTimeout(nzLoadText,nzsetting.reload);
	});
}
function nzScrollChat(force = false){
	var objDiv = document.getElementById("nzchatcontent");
	if(force){
		nzscroll = true;
		nzchatobj("#nznewmessage").hide();
	}
	if (nzscroll) {
		objDiv.scrollTop = objDiv.scrollHeight;
	}else{
		nzchatobj("#nznewmessage").show();
	}
}
function nzReadyForScroll(){
	var objDiv = document.getElementById("nzchatcontent");
	if (objDiv.scrollHeight - objDiv.scrollTop == nzchatheight + 4) {
		nzscroll = true;
	}else{
		nzscroll = false;
	}
}
function nzLoadText(){
	nzchatobj.post("plugin.php?id=th_chat:new", {'lastid':nzlastid},function(data) {
		if(data!='not'){
			data = JSON.parse(data);
			var listmess = sortObject(data.chat_row);
			nzReadyForScroll();
			nzchatobj.each(listmess, function(k, v) {
				k=parseInt(k);
				if(k>nzlastid){
					nzlastid=k;
					nzchatobj("#afterme").before(v);
					nzScrollChat();
				}
			});
			nzchatobj('.nzinnercontent img').one('load',function() {
				nzScrollChat();
			});
			if(nzsetting.iscleardata==1){
				var nzchatrr = nzchatobj(".nzchatrow");
				if(nzchatrr.size()>nzsetting.chatrowmax){
					nzchatrr.first().remove();
				}
			}
			if(data.chat_online){
				if(nzonol=='no'){
					nzchatobj("#nzchatolcontent").html(data.chat_online);
				}
				nzchatobj("#nzoltotal").html(data.chat_online_total);
			}
		}
	});
	setTimeout(nzLoadText,nzsetting.reload);
}

function nzQuota(i){
	nzTouid(0);
	nzchatobj("#nztouid").html("<span style='color:#3366CC'>อ้างอิง</span>: "+nzchatobj("#nzchatcontent"+i).html()+" <a href='javascript:void(0);' onClick='nzTouid(0)'>(ยกเลิก)</a>");
	nzquota = i;
	nzchatobj("#nzchatmessage").focus();
}
function nzAt(i){
	seditor_insertunit('nzchat', '@'+i+' ', '');
	nzchatobj("#nzchatmessage").focus();
}
function nzTouid(i){
	if(i>0){
		nzTouid(0);
		nzchatobj("#nztouid").html("<span style='color:#3366CC'>กระซิบ:</span> <a href='home.php?mod=space&uid="+i+"' class='nzca' target='_blank'>"+nzchatobj(".nzat_"+i).last()[0].outerHTML+"</a> ["+i+"] <a href='javascript:void(0);' onClick='nzTouid(0)'>(ยกเลิก)</a>");
		nztouid = i;
	}else{
		nzchatobj("#nztouid").html("");
		nztouid = 0;
		nzquota = 0;
	}
}
function nzReload(){
	nzchatobj("#nzchatolcontent").html('');
	nzchatobj("#nzchatnotice").html('กำลังโหลดประกาศล่าสุด...');
	nzchatobj("#nzchatcontent").html('<br /><br /><br /><br /><br /><br /><center><img src="source/plugin/th_chat/images/loading.svg" alt="Load" /></center>');
	nzchatobj.post("plugin.php?id=th_chat:newinit",function(data){
		data = JSON.parse(data);
		nzlastid=data.lastid;
		nzchatobj("#nzchatcontent").html('<table class="nzcallrow">'+data.datahtml+'<tr id="afterme" style="height:0px;"><td class="nzavatart"></td><td class="nzcontentt"></td></tr></table>');
		nzScrollChat(true);
		nzchatobj('.nzinnercontent img').one('load',function() {
			nzScrollChat();
		});
		nzchatobj("#nzchatolcontent").html(data.datachatonline);
		nzchatobj("#nzchatnotice").html(data.welcometext);
	});
}
function nzClean(){
	nzchatobj(".nzchatrow").fadeOut('slow');
}
function nzCheckImg(i){
	var maxheight=240;
	var maxwidth= 500;
	var w = parseInt( i.width );
	var h = parseInt( i.height );
	if ( w > maxwidth )
	{
	i.style.cursor = "pointer";
	i.onclick = function( )
	{
	var iw = window.open ( this.src, 'ImageViewer','resizable=1' );
	iw.focus();
	};
	h = ( maxwidth / w ) * h;
	w = maxwidth;
	i.height = h;
	i.width = w;
	}
	if ( h > maxheight )
	{
		i.style.cursor="pointer";
		i.onclick = function( )
		{ 
			var iw = window.open ( this.src, 'ImageViewer','resizable=1' );
			iw.focus();
		};
		i.width = ( maxheight / h ) * w;
		i.height = maxheight;
	}
}
function nzPlusone(nz_uid,nz_type){
	var nz_res = prompt('เหตุผล');
	if(nz_res==null){
		nz_res="";
	}
	nzCommand('point',nz_uid+'|'+nz_type+'|'+nz_res);
}
function sortObject(a) {
	var b = {},
	c,d = [];
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