jQuery(document).ready(function($){
	//add tag from meta box
	$('body').on('click','.add-new-hreflang-tag',function() {
		$('#validate-hreflang').hide();
		var $div = $('div[id^="hreflang-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone(true).prop('id', 'hreflang-'+num ).appendTo('.href-container');
		$('#hreflang-'+(num-1)+' button').remove();
	});
	// remove tag from meta box
	$('body').on('click','.remove-new-hreflang-tag',function() {
		$('#validate-hreflang').hide();
		var $div = $('div[id^="hreflang-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) -1;
		var $klon = $div.remove();
		$('<button class="add-new-hreflang-tag"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-hreflang-tag"><span class="dashicons dashicons-minus"></span></button>').clone(true).appendTo('#hreflang-'+num);
	});
	$('body').on('click','.add-new-cat-hreflang-tag',function(e) {
		e.preventDefault();
		var $div = $('div[id^="hreflang-cat-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) + 1;
		var $klon = $div.clone(true).prop('id', 'hreflang-cat-'+num);

		// Clear input values in cloned entry
		$klon.find('input[type="text"]').val('');
		$klon.find('select').prop('selectedIndex', 0);

		// Remove buttons from previous entry
		$('#hreflang-cat-'+(num-1)+' .hreflang-actions').empty();

		// Append to container
		$klon.appendTo('.href-container-cat');
	});
	$('body').on('click','.remove-new-cat-hreflang-tag',function(e) {
		e.preventDefault();
		var $div = $('div[id^="hreflang-cat-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) - 1;

		// Remove the last entry
		$div.remove();

		// Add buttons back to the new last entry
		var $actions = $('#hreflang-cat-'+num+' .hreflang-actions');
		$actions.html('<button type="button" class="hreflang-btn add-new-cat-hreflang-tag" title="Add Entry"><span class="dashicons dashicons-plus"></span></button> <button type="button" class="hreflang-btn btn-remove remove-new-cat-hreflang-tag" title="Remove Entry"><span class="dashicons dashicons-minus"></span></button>');
	});
	$('body').on('click','.add-new-cat-edit-hreflang-tag',function(e) {
		e.preventDefault();
		var $div = $('div[id^="hreflang-cat-edit-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) + 1;
		var $klon = $div.clone(true).prop('id', 'hreflang-cat-edit-'+num);

		// Clear input values in cloned entry
		$klon.find('input[type="text"]').val('');
		$klon.find('select').prop('selectedIndex', 0);

		// Remove buttons from previous entry
		$('#hreflang-cat-edit-'+(num-1)+' .hreflang-actions').empty();

		// Append to container
		$klon.appendTo('.term-hreflang-data .hreflang-taxonomy-container');
	});
	$('body').on('click','.remove-new-cat-edit-hreflang-tag',function(e) {
		e.preventDefault();
		var $div = $('div[id^="hreflang-cat-edit-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) - 1;

		// Remove the last entry
		$div.remove();

		// Add buttons back to the new last entry
		var $actions = $('#hreflang-cat-edit-'+num+' .hreflang-actions');
		$actions.html('<button type="button" class="hreflang-btn add-new-cat-edit-hreflang-tag" title="Add Entry"><span class="dashicons dashicons-plus"></span></button> <button type="button" class="hreflang-btn btn-remove remove-new-cat-edit-hreflang-tag" title="Remove Entry"><span class="dashicons dashicons-minus"></span></button>');
	});
	$('#generate_tags').click(function() {
		html = '';
		$('.href-lang').each(function() {
				if ($(this).children('.hreflang-region').val() != '') {
					html += '<link rel="alternative" href="'+$(this).children('.hreflang-href').val()+'" hreflang="'+$(this).children('.hreflang-lang').val()+'-'+$(this).children('.hreflang-region').val()+'">\n';
				}
				else {
					html += '<link rel="alternative" href="'+$(this).children('.hreflang-href').val()+'" hreflang="'+$(this).children('.hreflang-lang').val()+'">\n';
				}
    });
		$('#hreflang-html').css('display','block').val(html).height( $('#hreflang-html')[0].scrollHeight );
    });
	$('body').on('click','.add-new-gen-hreflang-tag',function() {
		var $div = $('div[id^="hreflang-gen-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone(true).prop('id', 'hreflang-gen-'+num ).appendTo('.href-container-gen');
		$('#hreflang-gen-'+(num-1)+' button').remove();
	});
	$('body').on('click','.remove-new-gen-hreflang-tag',function() {
		var $div = $('div[id^="hreflang-gen-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) -1;
		var $klon = $div.remove();
		$('<button class="add-new-gen-hreflang-tag"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-gen-hreflang-tag"><span class="dashicons dashicons-minus"></span></button>').clone(true).appendTo('#hreflang-gen-'+num);
	});
	$('body').on('click','.add-new-blog-hreflang-tag',function(e) {
		e.preventDefault;
		var $div = $('div[id^="hreflang-blog-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone(true).prop('id', 'hreflang-blog-'+num ).appendTo('.href-container-blog');
		$('#hreflang-gen-'+(num-1)+' button').remove();
	});
	$('body').on('click','.remove-new-blog-hreflang-tag',function(e) {
		e.preventDefault;
		var $div = $('div[id^="hreflang-blog-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) -1;
		var $klon = $div.remove();
		$('<button class="add-new-blog-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-blog-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-minus"></span></button>').clone(true).appendTo('#hreflang-blog-'+num);
	});
	$('body').on('click','.add-new-home-hreflang-tag',function(e) {
		e.preventDefault;
		var $div = $('div[id^="hreflang-home-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone(true).prop('id', 'hreflang-home-'+num ).appendTo('.href-container-home');
		$('#hreflang-gen-'+(num-1)+' button').remove();
	});
	$('body').on('click','.remove-new-home-hreflang-tag',function(e) {
		e.preventDefault;
		var $div = $('div[id^="hreflang-home-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) -1;
		var $klon = $div.remove();
		$('<button class="add-new-home-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-home-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-minus"></span></button>').clone(true).appendTo('#hreflang-home-'+num);
	});
	$('body').on('click','.add-new-shop-hreflang-tag',function(e) {
		e.preventDefault;
		var $div = $('div[id^="hreflang-shop-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone(true).prop('id', 'hreflang-shop-'+num ).appendTo('.href-container-shop');
		$('#hreflang-gen-'+(num-1)+' button').remove();
	});
	$('body').on('click','.remove-new-shop-hreflang-tag',function(e) {
		e.preventDefault;
		var $div = $('div[id^="hreflang-shop-"]:last');
		var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) -1;
		var $klon = $div.remove();
		$('<button class="add-new-shop-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-shop-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-minus"></span></button>').clone(true).appendTo('#hreflang-shop-'+num);
	});
	$('#validate-hreflang').click(function() {
		$(this).text('Please wait...');
		$('.validation-response-holder').each(function() {
			$(this).css({'width':'40px','display':'inline-block','height':'18px'}).html('<span class="spinner is-active"></span>')
		});
		id = $(this).attr('data-id');
		$.ajax({
		  type:'POST',
		  data:{
			  action:'validate_hreflang_tags',
			  post_id: id,
			  nonce: hreflang_ajax.nonce
		  },
		  url: ajaxurl,
		  timeout: 30000, // 30 second timeout
		  success: function(data) {
		  	$('#validate-hreflang').text('Validate');
		  	if (!data || Object.keys(data).length === 0) {
		  		alert('No validation data returned. Please check your hreflang tags.');
		  		$('.validation-response-holder').html('');
		  		return;
		  	}
		  	var count = 1;
		  	max_count = Object.keys(data).length;
		  	$.each(data, function(index, value) {
		  		if (value.result == 'success' && value.validate == 'pass') {
		  			if (count == max_count) {
		  				html = '<button class="add-new-hreflang-tag"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-hreflang-tag"><span class="dashicons dashicons-minus"></span></button> <span class="dashicons dashicons-yes" style="color:green"></span>';
		  			}
		  			else {
		  				html = '<span class="dashicons dashicons-yes" style="color:green"></span>';
		  			}
		  		}
		  		else if (value.result == 'success' && value.validate == 'fail') {
		  			if (count == max_count) {
		  				html = '<button class="add-new-hreflang-tag"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-hreflang-tag"><span class="dashicons dashicons-minus"></span></button> <span class="dashicons dashicons-no" style="color:red"></span> <span style="color:red">'+value.message+'</span>';
		  			}
		  			else {
		  				html = '<span class="dashicons dashicons-no" style="color:red"></span> <span style="color:red">'+value.message+'</span>';
		  			}
		  		}
				$('span#validate-'+count).css({'width':'auto','display':'inline-block','height':'auto'}).html(html);
				console.log(count+':'+max_count);
				count++;
			});
		  },
		  error: function(xhr, status, error) {
		  	$('#validate-hreflang').text('Validate');
		  	$('.validation-response-holder').html('');
		  	if (status === 'timeout') {
		  		alert('Validation timed out. The URLs may be too slow to respond. Please try again.');
		  	} else {
		  		alert('Validation error: ' + (xhr.responseJSON && xhr.responseJSON.data ? xhr.responseJSON.data.message : error));
		  	}
		  	console.error('AJAX error:', status, error, xhr);
		  }
		});
	});
	$('input#hreflang_pro_show_admin_bar').change(function(){
		this.value = (Number(this.checked));
	});
	$('button[name="trigger_sitemap"]').click(function() {
		$(this).text('Please wait...');
		action = $(this).attr('data-action');
		$.ajax({
		  type:'POST',
		  data:{
			  action:'hreflang_pro_do_sitemap',
			  method: action,
			  nonce: hreflang_ajax.nonce
		  },
		  url: ajaxurl,
		  success: function(data) {
			if (confirm(data.result)) {
				location.reload(true);
			}
		  }
		});
	});
	$("#slug_filter").on('change paste keyup',function() {
		var empty = $("#slug_filter").filter(function() {
			return $(this).val()==''
		}).length
		$("#allowed_wildcard").prop('disabled',empty)
		if (empty) {
			$("#allowed_wildcard").prop('checked',false);
		}
	}).change();
})
