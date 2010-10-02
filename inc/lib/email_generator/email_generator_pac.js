// Static definition of Month names
Date.MONTH_NAMES = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
// Static definition of weekday names
Date.WEEKDAY_NAMES = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ];

var d = new Date();

$(document).ready(function () {
	
	// Templates

	tpl = {
		topicItem : 		'<li class="hidden"><div>' +
							'<!-- <h3 class="topicLabel">Topic 1</h3> -->' +
							'<!-- <label>Title</label> -->' +
							'<input name="#" type="text" size="50" maxlength="50" />' +
							'<!-- <label class="discloseText">Text</label> -->' +
							'<p>' +
							'<a class=" discloseText" href="#">Show text</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
							'[+] <a class=" addItem" href="#">Add another</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
							'[-] <a class=" removeItem" href="#">Remove this topic</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
							'<!-- <a class=" moveItemUp" href="#">Up</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
							'<a class=" moveItemDown" href="#">Down</a> --></p>' +
							'<textarea class="hidden" name="#" rows="5"></textarea>' +
							'</div></li><?php endfor ?>',
		textAreaItem : 		'<li class="hidden"><div>' +
												'<h3 class="topicLabel">Topic 1</h3>' +
												'<!-- <label>Title</label> -->' +
												'<!-- <label class="discloseText">Text</label> -->' +
												'<textarea name="#" rows="5"></textarea>' +
												'</div></li><?php endfor ?>',
		imageAreaItem : 	'<li class="hidden"><div>' +
												'<h3 class="topicLabel">Topic 1</h3>' +
												'<div class="imgSelect"></div>' +
												'</div></li><?php endfor ?>',
		inputMessage :		'The quick brown fox jumps over the lazy dog.',
		textAreaMessage__footerText: 'Association Affairs Department | NCTA | 25 Massachusetts Ave., NW, Suite 100, Washington, DC 20001\n' +
							'Phone: 202-222-2310 • Fax: 202-222-2311\n' +
							'Grassroots Staff: Jadz Janucik • Lisa Schoenthaler • Donna Anaya • Nilda Gumbs • Greg Saphier • Kerry Landon', 
		textAreaMessage :	'Põdur Zagrebi tšellomängija-följetonist Ciqo külmetas kehvas garaažis. Albert osti fagotin ja töräytti puhkuvan melodian. Laŭ Ludoviko Zamenhof bongustas freŝa ĉeĥa manĝaĵo kun spicoj.',
		contentHTML:		''
	}
	
	// Model Logic

	$.ajax({ 
		url: 'inc/lib/email_generator/tpl/ncta_key_contact_template_pac.html',
		success: function (htmlResponse) {
			createItem();
			createItem();
			createItem();
			createItem(false, "#footerText");
			createImageItem("toc");
			tpl.contentHTML = htmlResponse;
			refresh();
        }
	});
      
	// View Logic
	
	$('.topicAdder').sortable({
		axis: 'y',
		cursor: 'crosshair',
		delay: 250,
		opacity: 0.5,
		placeholder: 'empty'
	});
	$('body').click(function () { refresh() });
	$('input, textarea').blur(function () { refresh() });
	$('*').keypress(function (e) { var k = e.keyCode || e.which; if(k === 9 || k === 13) { refresh(); } });
	$('.previewArea').mouseenter(function () { $('.codeViewToggle').show(); });
	$('.codeViewToggle').mouseleave(function () { $('.codeViewToggle').hide(); });
	$('.codeViewToggle').click(function () {
		// Modal Version...
		/*
		refresh();
		$('.codeDisplay textarea').text($('.previewArea iframe').contents().find('body').html().toString());
		$('.codeOverlay, .codeDisplay').show();
		$('.codeDisplay textarea').focus();
		$('.codeDisplay textarea').select();
		*/
		
		// Pop Up Version...
		/*
		var codeView =  window.open('','codeViewWindow','width=600,height=800,menubar=1');
		var html = '<html><head><title>Your Email</title></head><body>'+$('.previewArea iframe').contents().find('body').html().toString()+'</body></html>';
		codeView.document.open();
		codeView.document.write(html);
		codeView.document.close();
		*/
		
		// Pop Up Version...
		//$('.previewArea iframe').contents().find('body').html().toString()
		var fn = d.getTime();
		$.post( "savemailcode.php", { "fn" : fn, "_c" : $('.previewArea iframe').contents().find('body').html().toString() }, function(){
			var codeView =  window.open('emailview.php?_c=' + fn,'codeViewWindow','width=600,height=800,menubar=1,scrolling=1');
		});
		
	    return false;
	});
	$('.codeDisplay p a').click(function () {
		$('.codeOverlay, .codeDisplay').hide();
		return false;
	});
	
	function createItem(afterItem, title) {
		var newItem = title ? $(tpl.textAreaItem) : $(tpl.topicItem);
		var adderContainer = title ? ".textAreaAdder" : ".topicAdder";

		if(typeof afterItem === 'object') {
			$(afterItem).after(newItem);
		} else {
			$(adderContainer).append(newItem);
		}
		
		$(newItem).fadeIn('slow').removeClass('hidden');
		
		$(newItem).find('input').val(tpl.inputMessage);
		
		if(title) {
			newItem.attr("title", title);
			newItem.find(".topicLabel").text(title);
			$(newItem).find('textarea').val(tpl.textAreaMessage__footerText);
		} else {
			$(newItem).find('textarea').val(tpl.textAreaMessage);
		}
		
		$(newItem).mousedown(function () {
			stopAutoPreview();
		});
		$(newItem).mouseup(function () {
			startAutoPreview();
		});

		$(newItem).find('input, textarea').focus(function () {
			if(!$(this).hasClass('active')) {
				title ? null : $(this).val('');
				$(this).addClass('active');
			}
		});
		$(newItem).find('input').blur(function () {
			if($(this).val() == '') {
				$(this).val(tpl.inputMessage);
				$(this).removeClass('active');
			}
		});
		$(newItem).find('textarea').blur(function () {
			if($(this).val() == '') {
				$(this).val(tpl.textAreaMessage);
				$(this).removeClass('active');
			}
		});
		$(newItem).find('.discloseText').toggle(
			function () {
				$(this).text('Hide text');
				$(this).parents('li').find('textarea').fadeIn('slow').removeClass('hidden');
			},
			function () {
				$(this).text('Show text');
				$(this).parents('li').find('textarea').hide().addClass('hidden');
			}
		);
		$(newItem).find('.addItem').click(function () {
			createItem($(this).parents('li'));
		});
		$(newItem).find('.removeItem').click(function () {
			removeItem($(this).parents('li'));
		});
		$(newItem).find('.moveItemUp').click(function () {
			$(this).parents('li').insertBefore($(this).parents('li').prev());
		});
		$(newItem).find('.moveItemDown').click(function () {
			$(this).parents('li').insertAfter($(this).parents('li').next());
		});
	}
	function removeItem(item) {
		item.fadeOut('slow').addClass('hidden');
	}
	
	function createImageItem(idpf) {
		var imgItem = $(tpl.imageAreaItem);
		$(imgItem).attr( "id", idpf + "Img" );
		
		$(".imgAreaAdder").append( imgItem );

		$(imgItem).find(".imgSelect").load( ("admin/addimg.php?list=" + idpf), function( data ){
			$(imgItem).fadeIn('slow').removeClass('hidden');
			
			var selList = $(imgItem).find("select");
			selList.bind("change", function(){
				//console.log( selList.val() );
			});
			
		});
	}
	// Controller Logic
	
	function refresh() {
		var html = $(tpl.contentHTML);
		var indexItem = html.find('.indexItem').clone();
		var contentItem = html.find('.contentItem').clone();
		var indexImgItem = html.find('.indexImgItem').clone();
		var dateItem = html.find('.dateline');
		
		html.find('.indexArea').html('');
		html.find('.contentArea').html('');
		
		$('.topicAdder li:not(.hidden)').each(function () {
			var newIndexItem = indexItem;
			newIndexItem.text($(this).find('input').val());
			html.find('.indexArea').append(newIndexItem.clone());

			var newContentItem = contentItem;
			newContentItem.find('.contentItemTitle').text($(this).find('input').val());
			newContentItem.find('.contentItemBody').html($(this).find('textarea').val().replace(/\n/g, '<br />'));
			
			html.find('.contentArea').append(newContentItem.clone());
			
		});
		
		$('.textAreaAdder li:not(.hidden)').each(function () {
			var newContentItem = html.find( $(this).attr("title") );
			
			//console.log(html, $(this).attr("title"), newContentItem);
			newContentItem.html($(this).find('textarea').val().replace(/\n/g, '<br />'));
			
			//html.find('.contentArea').append(newContentItem.clone());
			
		});
		
		$('.imgAreaAdder li:not(.hidden)').each(function () {
			var newImgItem = html.find(".indexImgItem");
			var imgSrc = $(this).find("select").val();
			
			//console.log(html, $(this), newImgItem, imgSrc);
			if ( imgSrc != "" ) newImgItem.html( '<img src="' + imgSrc + '" align="right" />');
			
			//html.find('.contentArea').append(newContentItem.clone());
			
		});
		
		dateItem.html( Date.MONTH_NAMES[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear() );
		
		tplClone = html.clone();
			
		$('.previewArea iframe').contents().find('body').html(tplClone.html());
	}
	function stopAutoPreview() {
		window.clearInterval(refreshCycle);
	}
	function startAutoPreview() {
		refreshCycle = window.setInterval(function () { refresh() }, 1000);
	}
	
	// Init
	
	startAutoPreview();
});