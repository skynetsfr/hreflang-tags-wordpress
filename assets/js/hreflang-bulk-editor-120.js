jQuery(document).ready(function($) {
  $('body').on('click', '#wp-link-submit-hreflang', function(e) {
    e.preventDefault();
    var post_id = $('#wrap_post_id').val();
    var num = $('#wrap_entry_number').val();
    var link = $('#wp-link-url').val();
    $('#hreflang-href-post-'+post_id+'-'+num).val(link);
    $('#wp-link-wrap').hide();
    $('#wp-link-url').val("");
    return false;
  })
  $('body').on('click', '#wp-link-close', function(e) {
    e.preventDefault();
    $('#wp-link-wrap').hide();
    return false;
  })
  $('body').on('click', '#wp-link-cancel button', function(e) {
    e.preventDefault();
    $('#wp-link-wrap').hide();
    return false;
  })
  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: {
      action: "hreflang_pro_bulk_get_all_pages",
      nonce: hreflang_ajax.nonce
    },
    success: function(data) {
      var html = "<ul>";
      $.each(data.items, function(index,item) {
          if (1 == index % 2) {
            html += '<li class="alternate"><input type="hidden" class="item-permalink" value="'+item.permalink+'"><span class="item-title">'+item.post_title+'</span><span class="item-info">'+item.post_type+'</span></li>';
          }
          else {
            html += '<li><input type="hidden" class="item-permalink" value="'+item.permalink+'"><span class="item-title">'+item.post_title+'</span><span class="item-info">'+item.post_type+'</span></li>';
          }
      })
      html += "</ul>";
      $(html).appendTo('#most-recent-results');
    }
  })
  $( 'body' ).on( 'click', '.add-new-hreflang-tag', function () {
    rec_id = $( this ).attr( 'data-id' );
    var $div = $( 'div[id^="hreflang-bulk-' + rec_id + '-"]:last' );
    var num = parseInt( $div.prop( "id" ).match( /\d+/g ), 10 ) + 1;
    var $klon = $div.clone( true ).prop( 'id', 'hreflang-bulk-' + rec_id + '-' + num ).appendTo( '#' + rec_id );
    if ( $( '#' + rec_id ).hasClass( 'existing-data' ) ) {
      $( '#hreflang-bulk-' + rec_id + '-' + ( num ) + ' button' ).remove();
      $( '#' + rec_id + ' button.remove-new-hreflang-tag' ).remove();
    } else {
      $( '#hreflang-bulk-' + rec_id + '-' + ( num - 1 ) + ' button' ).remove();
    }
  } );
  $( 'body' ).on( 'click', '.remove-new-hreflang-tag', function () {
    rec_id = $( this ).attr( 'data-id' );
    var $div = $( 'div[id^="hreflang-bulk-' + rec_id + '-"]:last' );
    var num = parseInt( $div.prop( "id" ).match( /\d+/g ), 10 ) - 1;
    var $klon = $div.remove();
    $( '<button class="add-new-hreflang-tag" data-id="' + rec_id + '"><span class="dashicons dashicons-plus"></span></button> <button class="remove-new-hreflang-tag" data-id="' + rec_id + '"><span class="dashicons dashicons-minus"></span></button>' ).clone( true ).appendTo( '#hreflang-bulk-' + rec_id + '-' + num );
  } );
  $('input[name="html-option-selection"]:checkbox').change(function(){
    if ($(this).is(':checked')) {
        $('.hreflang-pro-html-lang').show();
    }
    else {
      $('.hreflang-pro-html-lang').hide();
    }
  });
  $( 'body' ).on( 'click', '.hreflang_pro_link_trigger a', function( event ) {
    input = $(this).next('.hreflang-href');
    pieces = input.attr('id').split("-");
    post_id = pieces[3];
    $('#wrap_post_id').val(post_id);
    num = pieces[4];
    $('#wrap_entry_number').val(num);
    wpActiveEditor = true;
    wpLink.open();
    return false;
});
  $('body').on('click','.bulk-add-new-hreflang-tag',function() {
		var data_id = $(this).attr("data-id");
		var $div = $('div[id^="hreflang-bulk-'+data_id+'-"]:last');
		var num = parseInt($div.prop("id").replace('hreflang-bulk-'+data_id+'-','')) +1;
		var $klon = $div.clone(true).prop('id', 'hreflang-bulk-'+data_id+'-'+num ).insertAfter($div);
		$('#hreflang-bulk-'+data_id+'-'+(num-1)+' button.bulk-add-new-hreflang-tag').remove();
		$('#hreflang-bulk-'+data_id+'-'+(num-1)+' button.bulk-remove-new-hreflang-tag').remove();
		$('<button class="bulk-delete-new-hreflang-tag" data-id="'+data_id+'"><span class="dashicons dashicons-no"></span></button>').appendTo('#hreflang-bulk-'+data_id+'-'+(num-1));
	});
	// remove tag from meta box
	$('body').on('click','.bulk-remove-new-hreflang-tag',function() {
    if (confirm('Are you sure you wish to delete this entry? It will be immediately deleted. You do not need to click "Save".')) {
      var data_id = $(this).attr("data-id");
      parent = $(this).parent();
      parent_selector = parent.attr('id');
      url = $('#'+parent_selector+' .hreflang-href').val();
      lang = $('#'+parent_selector+' .hreflang-lang').val();
      region = $('#'+parent_selector+' .hreflang-region').val();
      $.ajax({
        type: "POST",
        data:  {
          action: "hreflang_pro_delete_entry_from_bulk_editor",
          post_id: data_id.replace('post-',''),
          language: lang,
          region: region,
          nonce: hreflang_ajax.nonce
        },
        url: ajaxurl,
        success: function(data) {
          if (data.result == 'success') {
        		var $div = $('div[id^="hreflang-bulk-'+data_id+'-"]:last');
        		var num = parseInt($div.prop("id").replace('hreflang-bulk-'+data_id+'-','')) -1;
        		var $klon = $div.remove();
        		$('#hreflang-bulk-'+data_id+'-'+(num)+' button.bulk-delete-new-hreflang-tag').remove();
        		$('<button class="bulk-add-new-hreflang-tag" data-id="'+data_id+'"><span class="dashicons dashicons-plus"></span></button> <button class="bulk-remove-new-hreflang-tag" data-id="'+data_id+'"><span class="dashicons dashicons-minus"></span></button>').clone(true).appendTo($('#hreflang-bulk-'+data_id+'-'+num));
          }
        }
      })
    }
	});
  $('body').on('click','.bulk-delete-new-hreflang-tag',function(e) {
    e.preventDefault();
    if (confirm('Are you sure you wish to delete this entry? It will be immediately deleted. You do not need to click "Save".')) {
      var data_id = $(this).attr("data-id");
      parent = $(this).parent();
      parent_selector = parent.attr('id');
      url = $('#'+parent_selector+' .hreflang-href').val();
      lang = $('#'+parent_selector+' .hreflang-lang').val();
      region = $('#'+parent_selector+' .hreflang-region').val();
      $.ajax({
        type: "POST",
        data:  {
          action: "hreflang_pro_delete_entry_from_bulk_editor",
          post_id: data_id.replace('post-',''),
          language: lang,
          region: region,
          nonce: hreflang_ajax.nonce
        },
        url: ajaxurl,
        success: function(data) {
          if (data.result == 'success') {
            $('#'+parent_selector).remove();
          }
        }
      })
    }
	});
  $('body').on('click','.bulk-delete-new-html-tag',function(e) {
    e.preventDefault();
    if (confirm('Are you sure you wish to delete this entry? It will be immediately deleted. You do not need to click "Save".')) {
      var data_id = $(this).attr("data-id");
      parent = $(this).parent();
      parent_selector = parent.attr('id');
      lang = $('#'+parent_selector+' .html-lang').val();
      region = $('#'+parent_selector+' .html-region').val();
      $.ajax({
        type: "POST",
        data:  {
          action: "hreflang_pro_delete_html_entry_from_bulk_editor",
          post_id: data_id.replace('post-',''),
          language: lang,
          region: region,
          nonce: hreflang_ajax.nonce
        },
        url: ajaxurl,
        success: function(data) {
          if (data.result == 'success') {
            $('#record_'+data.post_id+' .hreflang-pro-html-lang').hide();
          }
        }
      })
    }
	});
  $('body').on('click','.hreflang-save',function() {
    hreflang_pro_post_id = $(this).attr('data-id');
		hreflang_pro_href = $('#record_'+hreflang_pro_post_id+' input[name="hreflang-href[]"]').map(function(){return $(this).val();}).get();
		hreflang_pro_lang = $('#record_'+hreflang_pro_post_id+' select[name="hreflang-lang[]"]').map(function(){return $(this).val();}).get();
    hreflang_pro_region = $('#record_'+hreflang_pro_post_id+' select[name="hreflang-region[]"]').map(function(){return $(this).val();}).get();
    hreflang_pro_html_lang = $('#record_'+hreflang_pro_post_id+' select[name="html-lang"]').val();
    hreflang_pro_html_region = $('#record_'+hreflang_pro_post_id+' select[name="html-region"]').val();
		 jQuery.ajax({
        type:'POST',
        data:{
          action:'hreflang_pro_save_from_bulk_editor',
          post_id: hreflang_pro_post_id,
          urls: hreflang_pro_href,
          langs: hreflang_pro_lang,
          regions: hreflang_pro_region,
          html_lang: hreflang_pro_html_lang,
          html_region: hreflang_pro_html_region,
          nonce: hreflang_ajax.nonce
		    },
        url: ajaxurl,
        success: function(data) {
			  if (data.result == 'success') {
				  alert('Your data has been saved.');
			  }
      }
    });
  });
	$('body').on('click','.apply_to_all_page',function() {
		var answer = confirm('This will overwrite your previous entries on this page, after you click "Save All". Are you sure you want to do this?');
		if (answer) {
			$(".href-lang input").each(function(){
				$(this).attr("value", $(this).val());
			});
			$('.href-lang select option').each(function(){
				this.defaultSelected = this.selected;
			});
			master_template = '';
			$('.master').each(function() {
				master_template += $(this).prop('outerHTML');
			});
      html_master_template = '';
			$('.html-master').each(function() {
				html_master_template += $(this).prop('outerHTML');
			});
			$('.hreflang-rows td').each(function(index, element) {
				post_name = $(this).children('.href-content-wrapper').attr('id');
        master_html = html_master_template + "<hr />" + master_template;
				$(this).html(master_html);
				$(this).children('.href-lang').attr('id','hreflang-bulk-'+post_name);
				$(this).children('.href-lang').children('button').attr('data-id',post_name);
				$(this).children('.href-lang').wrap('<div class="href-content-wrapper existing-data" id="'+post_name+'"></div>');
				$('#'+post_name+' .href-lang').each(function(index, element) {
          element_id = $(this).attr('id');
					new_element_id = element_id + '-'+(index+1);
					$(this).attr('id',new_element_id);
        });
        $('#'+post_name+' .href-region').each(function(index, element) {
          element_id = $(this).attr('id');
					new_element_id = element_id + '-'+(index+1);
					$(this).attr('id',new_element_id);
        });
			});
      $('select[name="html-lang"]').each(function(index,element){
        $(this).attr('id','html-lang-master-copy-'+index);
        $(this).val($('#html-lang-master-copy-0').val());
      });
      $('select[name="html-region"]').each(function(index,element){
        $(this).attr('id','html-region-master-copy-'+index);
        $(this).val($('#html-region-master-copy-0').val());
      });
			$('button.save_all_page').css('display','inline-block');
			alert('You should now click the "Save All" button to save your changes.');
		}
	});
	$('body').on('click','.save_all_page',function() {
		var answer = confirm('This will overwrite your previous entries on this page. This cannot be undone. Are you sure you want to do this?');
		if (answer) {
			$('button.save_all_page').html('Saving, please wait');
			$('button.save_all_page').prop('disabled',true);
			var count = 1;
			$('.hreflang-rows').each(function() {
				row_id = $(this).attr('id');
				post_array = row_id.split('_');
				hreflang_pro_post_id = post_array[1];
				hreflang_pro_href = $('#'+row_id+' input[name="hreflang-href[]"]').map(function(){
					return $(this).val();
				}).get();
				hreflang_pro_lang = $('#'+row_id+' select[name="hreflang-lang[]"]').map(function(){
					return $(this).val();
				}).get();
        hreflang_pro_region = $('#'+row_id+' select[name="hreflang-region[]"]').map(function(){
					return $(this).val();
				}).get();
        hreflang_pro_html_lang = $('#'+row_id+' select[name="html-lang"]').val();
        hreflang_pro_html_region = $('#'+row_id+' select[name="html-region"]').val();
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'hreflang_pro_save_from_bulk_editor',
						post_id: hreflang_pro_post_id,
						urls: hreflang_pro_href,
						langs: hreflang_pro_lang,
            regions: hreflang_pro_region,
            html_lang: hreflang_pro_html_lang,
            html_region: hreflang_pro_html_region,
            nonce: hreflang_ajax.nonce
					},
					success: function(response) {
						$('button.save_all_page').html('Saving, please wait ('+count+' of '+$('.hreflang-rows').length+')');
						count++;
						if (count == $('.hreflang-rows').length) {
							$('button.save_all_page').html('Save All');
							$('button.save_all_page').prop('disabled',false);
							if (	confirm('All your hreflang tags have been successfully saved')) {
                var nonce;
                var page;
                var type;
                var orderby;
                var order;
                var post_status;
                var post_type_filter;
                var paged;
                if (getUrlParameter('nonce') === undefined) {
                  nonce = "";
                }
                else {
                  nonce = getUrlParameter('nonce');
                }
                if (getUrlParameter('page') === undefined) {
                  page = "";
                }
                else {
                  page = getUrlParameter('page');
                }
                if (getUrlParameter('type') === undefined) {
                  type = "";
                }
                else {
                  type = getUrlParameter('type');
                }
                if (getUrlParameter('orderby') === undefined) {
                  orderby = "";
                }
                else {
                  orderby = getUrlParameter('orderby');
                }
                if (getUrlParameter('order') === undefined) {
                  order = "";
                }
                else {
                  order = getUrlParameter('order');
                }
                if (getUrlParameter('post_status') === undefined) {
                  post_status = "";
                }
                else {
                  post_status = getUrlParameter('post_status');
                }
                if (getUrlParameter('post_type_filter') === undefined) {
                  post_type_filter = "";
                }
                else {
                  post_type_filter = getUrlParameter('post_type_filter');
                }
                if (getUrlParameter('paged') === undefined) {
                  paged = "";
                }
                else {
                  paged = getUrlParameter('paged');
                }
                window.location.href = response.site_url  + '/wp-admin/admin.php?nonce='+nonce+'&page='+page+'&type='+type+'&orderby='+orderby+'&order='+order+'&post_status='+post_status+'&post_type_filter='+post_type_filter+'&paged='+paged;
							}
						}
					}
				});
			})
		}
	});
	$('body').on('click','.delete_all_page',function() {
		var answer = confirm('This will delete your HREFLANG Tag and HTML lang entries on this page. This cannot be undone. Are you sure you want to do this?');
		if (answer) {
			$('button.delete_all_page').html('Deleting, please wait');
			$('button.delete_all_page').prop('disabled',true);
			var count = 0;
			$('.hreflang-rows').each(function() {
				row_id = $(this).attr('id');
				post_array = row_id.split('_');
				hreflang_pro_post_id = post_array[1];
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'hreflang_pro_delete_from_bulk_editor',
						post_id: hreflang_pro_post_id,
						nonce: hreflang_ajax.nonce
					},
					success: function(response) {
						$('button.delete_all_page').html('Deleting, please wait ('+count+' of '+$('.hreflang-rows').length+')');
						count++;
						if (count == $('.hreflang-rows').length) {
							$('button.delete_all_page').html('Delete All');
							$('button.delete_all_page').prop('disabled',false);
							if (	confirm('All your hreflang tags have been successfully deleted')) {
                var nonce;
                var page;
                var type;
                var orderby;
                var order;
                var post_status;
                var post_type_filter;
                var paged;
                if (getUrlParameter('nonce') === undefined) {
                  nonce = "";
                }
                else {
                  nonce = getUrlParameter('nonce');
                }
                if (getUrlParameter('page') === undefined) {
                  page = "";
                }
                else {
                  page = getUrlParameter('page');
                }
                if (getUrlParameter('type') === undefined) {
                  type = "";
                }
                else {
                  type = getUrlParameter('type');
                }
                if (getUrlParameter('orderby') === undefined) {
                  orderby = "";
                }
                else {
                  orderby = getUrlParameter('orderby');
                }
                if (getUrlParameter('order') === undefined) {
                  order = "";
                }
                else {
                  order = getUrlParameter('order');
                }
                if (getUrlParameter('post_status') === undefined) {
                  post_status = "";
                }
                else {
                  post_status = getUrlParameter('post_status');
                }
                if (getUrlParameter('post_type_filter') === undefined) {
                  post_type_filter = "";
                }
                else {
                  post_type_filter = getUrlParameter('post_type_filter');
                }
                if (getUrlParameter('paged') === undefined) {
                  paged = "";
                }
                else {
                  paged = getUrlParameter('paged');
                }
                window.location.href = response.site_url  + '/wp-admin/admin.php?nonce='+nonce+'&page='+page+'&type='+type+'&orderby='+orderby+'&order='+order+'&post_status='+post_status+'&post_type_filter='+post_type_filter+'&paged='+paged;
							}
						}
					}
				});
			});
		}
	});
});
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        var key = decodeURIComponent(sParameterName[0]);
        var value = decodeURIComponent(sParameterName[1]);

        if (key === sParam) {
            return value === undefined ? "" : value;
        }
    }
};
