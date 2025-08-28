jQuery(document).ready(function($) {
	
	$('.so-groups-sticky .sticky-backtop').on('click', function() {
		$('html, body').animate({ scrollTop: 0 }, 'slow', function () {});
	});

	$('.so-groups-sticky *[data-target="popup"]').on('click', function() {
		$('html').addClass('overflow-hidden');
		$($(this).attr('data-popup')).removeClass('popup-hidden');
		$('.popup').animate({
			scrollTop:'0px'
		}, 500);
	});

	$('.so-groups-sticky *[data-target="popup-close"]').on('click', function() {
		$('html').removeClass('overflow-hidden');
		$($(this).attr('data-popup-close')).addClass('popup-hidden');
	});

	$(document).keyup(function(e) {
	     if (e.keyCode == 27) {
	        $('html').removeClass('overflow-hidden');
			$('.so-groups-sticky .popup').addClass('popup-hidden');
	    }
	});

	$('.so-groups-sticky .nav-secondary ul li span i').click(function(){
		if ($(this).hasClass('more')) {
			$('.so-groups-sticky .nav-secondary ul li').removeClass('active');
			$(this).parent().parent().addClass('active');
	    	$(this).parent().parent().children('ul').stop(true, true).slideDown('slow');
	    	$('.so-groups-sticky .nav-secondary ul li').each(function() {
				if ($(this).hasClass('active')) {
					$(this).parent('ul').parent('li').addClass('active');
					$(this).children('ul').slideDown('slow');
				}
			})
			$('.so-groups-sticky .nav-secondary ul li:not(".active") ul').stop(true, true).slideUp('slow');
	    }
	    else {
	    	$(this).parent().parent().children('ul').stop(true, true).slideUp('slow');
	    	$(this).parent().parent().removeClass('active');
	    }
	});

	$('.so-groups-sticky #button-search, .so-groups-sticky .sbmsearch').on('click', function() {
		$('.so-groups-sticky #button-search').attr('disabled','disabled');
		$('.so-groups-sticky #button-search').addClass('loading disabled');
		$('.so-groups-sticky #button-search').prepend('<i class="fa fa-refresh fa-spin"></i>');
		var url = $('base').attr('href') + 'index.php?route=product/search&language='+$('body').attr('data-lang');
		var value = $('.so-groups-sticky #input-search').val();
		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}
		location = url;
	});
	$('.so-groups-sticky #input-search').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('.so-groups-sticky #button-search').trigger('click');
		}
	});

	$('.so-groups-sticky select[name="select-currency"]').on('change', function() {
		$(this).attr('disabled','disabled');
		$('#form-currency input[name="code"]').val(this.value);
		$('#form-currency').submit();
	});

	$('.so-groups-sticky select[name="select-language"]').on('change', function() {
		$(this).attr('disabled','disabled');
		location = $(location).attr('href')+'&language='+this.value;
	});
})



// Forms
$(document).on('submit', 'form[data-oc-toggle=\'ajax\']', function (e) {
    e.preventDefault();

    var element = this;

    var form = e.target;

    var action = $(form).attr('action');

    var button = e.originalEvent.submitter;

    var formaction = $(button).attr('formaction');

    if (formaction !== undefined) {
        action = formaction;
    }

    var method = $(form).attr('method');

    if (method === undefined) {
        method = 'post';
    }

    var enctype = $(element).attr('enctype');

    if (enctype === undefined) {
        enctype = 'application/x-www-form-urlencoded';
    }

    // https://github.com/opencart/opencart/issues/9690
    if (typeof CKEDITOR != 'undefined') {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }
	
    $.ajax({
        url: action,
        type: method,
        data: $(form).serialize(),
        dataType: 'json',
        cache: false,
        contentType: enctype,
        processData: false,
        beforeSend: function () {
            $(button).prop('disabled', true).addClass('loading');
        },
        complete: function () {
           $(button).prop('disabled', false).removeClass('loading');
        },
        success: function (json) {
            $('.alert-dismissible').remove();
            $(form).find('.is-invalid').removeClass('is-invalid');
            $(form).find('.invalid-feedback').removeClass('d-block');

            console.log(json);

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (typeof json['error'] == 'string') {
                $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fas fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
            }

            if (typeof json['error'] == 'object') {
                if (json['error']['warning']) {
                    $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fas fa-exclamation-circle"></i> ' + json['error']['warning'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                }

                for (key in json['error']) {
                    $('#input-' + key.replaceAll('_', '-')).addClass('is-invalid').find('.form-control, .form-select, .form-check-input, .form-check-label').addClass('is-invalid');
                    $('#error-' + key.replaceAll('_', '-')).html(json['error'][key]).addClass('d-block');
                }
            }

            if (json['success']) {
                $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fas fa-exclamation-circle"></i> ' + json['success'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');

                // Refresh
                var url = $(form).attr('data-oc-load');
                var target = $(form).attr('data-oc-target');

                if (url !== undefined && target !== undefined) {
					$('#cart  .total-shopping-cart ').load('index.php?route=common/cart|info #cart .total-shopping-cart');
					$('#cart > ul').load('index.php?route=common/cart|info ul li');
					$('.so-groups-sticky .popup-mycart .popup-content').load('index.php?route=extension/so_theme/module/so_tools|info&language=en-gb .cart-header');
                    $(target).load(url);
                }
            }

            // Replace any form values that correspond to form names.
            for (key in json) {
                $(form).find('[name=\'' + key + '\']').val(json[key]);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});

