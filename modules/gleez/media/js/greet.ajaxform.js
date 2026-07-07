/*
 * This turns any form into a "post-in-place" form so it is ajaxed to save
 * without a refresh. Requires jquery form plugin @link
 *  https://github.com/gleez/greet
 *
 * @package    Greet\AjaxForm
 * @version    2.0
 * @requires   jQuery v1.9 or later
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2005-2015 Gleez Technologies
 * @license    The MIT License (MIT)
 * @link       https://github.com/malsup/form
 *
 */

+function ($) {
	'use strict';

	// GREET AJAXFROM CLASS DEFINITION
	// ======================

    const Ajaxform = function (element, options) {
        // Set the options
        options.dataType = options.datatype
        options.beforeSubmit = options.beforeSubmit || Ajaxform.prototype.beforeSubmit
        options.success = options.success || Ajaxform.prototype.showResponse
        options.error = options.error || Ajaxform.prototype.errorResponse

        // Delete unused options
        delete options.datatype

        this.init(element, options)
    };

    Ajaxform.prototype.init = function (element, options) {
		this.$element   = element
        $(element).ajaxSubmit(options)
	}

	Ajaxform.prototype.beforeSubmit = function(formData, form, options) {
		//add submit button to form array if its from popup request
        if (options.button && options.button.length === 1) {
            const subButton = {
                name: options.button.attr('name'),
                value: options.button.attr('value'),
                type: options.button.attr('type')
            };

            // Append to form data
			formData.push(subButton)
		}
	
		// Hide any errorContainers
		$(form).find('.error-message-container').slideUp(250)
        $(options.clickedButton).attr('disabled', true).addClass('InProgress')
	
		return true
	}

	Ajaxform.prototype.showResponse = function(data, status, xhr, form) {
        let text;
        if (data.formSaved === false && data.errors) {
			Ajaxform.prototype.validationErrors(data, form);
        } else if (data.formSaved === true) {
            const popup = $(form).data('popup') || false,
                dataTable = $(form).data('datatable') || false;

            // Remove the form from the dom
			$(form).remove()

			if(dataTable){
                // Redraw dataTables if it's a dataTable popup or form add/edit/delete
                $(dataTable).DataTable().draw();
			}

            // Let's check if the form is in popup window
			if(popup && typeof data.messages !== undefined && data.messages.length > 0){
                text = '<div class="alert alert-success alert-block"><i class="fas fa-info"></i>&nbsp' + data.messages[0].text + '</div>';
                $(popup).find('.popup-title').html(data.messages[0].type)
				$(popup).find('.popup-body').html(text)
				$(popup).find('.popup-footer').html('&nbsp')
			}
			else if(popup){
                text = '<div class="alert alert-success alert-block"><i class="fas fa-info"></i>&nbspSuccess</div>';
                $(popup).find('.popup-body').html(text)
				$(popup).find('.popup-footer').html('&nbsp')
			}
		}
	}

    Ajaxform.prototype.errorResponse = function (xhr, status, error) {
		console.log('Error Response')
		console.log(error)
	}

	Ajaxform.prototype.validationErrors = function(data, form) {
        let title = 'Error',
            tmpl = '<div class="alert alert-danger alert-block">';

        tmpl += '<h4 class="alert-heading">' + title + '</h4><ul>'

		// Loop through the errors
		$.each(data.errors, function(i, value) {
			// And add the error to the list.
			tmpl += '<li>' + value + '</li>';
			// Let's guesstimate the input that gave us an error
            const $inputField = $('[name*="' + i + '"]');

            if ($inputField.length) {
				$($inputField).parent('div.controls').parent('div.form-group').addClass('has-error')
			}
		})

		tmpl += '</ul></div>'

        // If the target block doesn't exist
		if (!$('.error-message-container').length){
			$(form).prepend('<div class="error-message-container" style="display:none"></div>')
		}

		// Empty any previous error messages, insert the new errors and slide it in to view.
		$(form).find('.error-message-container').empty().html(tmpl).slideDown(250)
        $(form).data('clickedButton').removeAttr('disabled').removeClass('InProgress')
	}

	Ajaxform.prototype.captureSubmittingElement = function(e) {
        let target = e.target;
        const $el = $(target);

        if (!($el.is("[type=submit],[type=image]"))) {
            // Is this a child element of the submit element? (e.g., a span within a button)
            const t = $el.closest('[type=submit]');
            if (t.length === 0) {
				return
			}
			
			target = t[0]
		}

        const form = this;
        form.clk = target
        if (target.type === 'image') {
			if (e.offsetX !== undefined) {
			form.clk_x = e.offsetX;
			form.clk_y = e.offsetY;
			} else if (typeof $.fn.offset == 'function') {
                const offset = $el.offset();
                form.clk_x = e.pageX - offset.left;
			form.clk_y = e.pageY - offset.top;
			} else {
			form.clk_x = e.pageX - target.offsetLeft;
			form.clk_y = e.pageY - target.offsetTop;
			}
		}
		// clear form vars
		setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100)
	}

	// GREET AJAXFROM PLUGIN DEFINITION
	// =======================

    const old = $.fn.ajaxform;

    $.fn.ajaxform = function (option) {
		return this.each(function () {
            const $this = $(this);
            let data = $this.data('ajaxForm');
            const options = $.extend({}, $.fn.ajaxform.defaults, $this.data(), typeof option == 'object' && option);

            if (!data) $this.data('ajaxForm', (data = new Ajaxform(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

    $.fn.ajaxform.defaults = {
		keyboard: true
		, loading: true
		, delegation: true
		, datatype: 'json'
        , type: 'POST',
        beforeSerialize: false,
        beforeSubmit: false,
        resetForm: false,
        clearForm: false,
        button: false,
        clickedButton: false,
        target: false
		, success: false
		, context: false
		, error: false
		, complete: false
		, traditional: false
		, iframe: false
        , semantic: false,
        closeKeepAlive: false,
        extraData: false,
        replaceTarget: 'html',
        includeHidden: true,
        uploadProgress: false
	}

    $.fn.ajaxform.Constructor = Ajaxform


	// GREET AJAXFROM NO CONFLICT
	// =================

    $.fn.ajaxform.noConflict = function () {
        $.fn.ajaxform = old
		return this
	}


   // GREET AJAXFROM DATA-API
   // ==============

    $(document).on('submit.ajaxform.data-api, click.ajaxform.data-api', '[data-toggle="ajaxForm"]', function (e) {
        const $this = $(this),
            $target = $this.data('form') || $this.parents('form'),
            option = $.extend({}, $target.data(), $this.data());

        // If event has been canceled, don't proceed
		if (!e.isDefaultPrevented()) {
			e.preventDefault()

            option.clickedButton = $this
            $target.data('clickedButton', $this)
			$target.data('popup', option.popup)
			$target.data('datatable', option.datatable)
            $target.removeData('ajaxForm').ajaxform(option)
		}
	})

	$(document.body).on('submit.ajaxform.data-api', 'form', Ajaxform.prototype.captureSubmittingElement)
	$(document.body).on('click.ajaxform.data-api', 'form', Ajaxform.prototype.captureSubmittingElement)

}(jQuery);