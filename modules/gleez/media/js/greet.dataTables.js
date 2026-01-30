/*
 * This is a highly modified version of the bootstrap dataTables.
 *
 * @link https://github.com/gleez/greet
 * @package    Greet\DataTables
 * @requires   jQuery v1.9 or later
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2005-2015 Gleez Technologies
 * @license    The MIT License (MIT)
 */

+function ($) {
	'use strict';

	// GREET DATATABLE CLASS DEFINITION
	// ======================

	var DataTable = function(table, options) {
		var $table = $(table)
		,   columns = []
		
		//dont't init if it's already initialised
        if ($.fn.dataTable.isDataTable(table)) return
		
		//exit if no url
        if (options.ajax === false) return

		//use data sortable value to disable sorting/searching for a column
		$('thead th', $(table)).each(function(){
			var obj   = $(this).data("columns")
		
			if(obj && obj != undefined){
				columns.push(obj);
			}else{
				columns.push(null)
			}
		})

		var oTable = $table.DataTable({
			"columns": columns
            , "order": options.order
			, "processing": options.processing
            , "serverSide": options.serverSide,
            "stateSave": options.stateSave,
            "stateDuration": options.stateDuration,
            "layout": options.layout
			, "language": {
                "emptyTable": options.emptyTable
				}
			, "ajax": function (data, fnCallback, settings ) {
				settings.jqXHR = $.ajax( {
                    "url": options.ajax,
					"data": data,
					"dataType": "json",
					"cache": false,
                    "type": settings.ajax.type || 'GET'
				}, 300)
				.done(function(response, textStatus, jqXHR){
					$(settings.oInstance).trigger('xhr', settings)
					fnCallback( response )
				})
				.fail(function (jqXHR, textStatus, errorThrown) {
					var errorText = '<div class="empty_page alert alert-block"><i class="fa fa-info-circle"></i>&nbsp'+errorThrown+'</div>'
					$(settings.oInstance).parent().html(errorText)
				})
			}
		})
	}

	/* Default class modification */
    $.extend(true, $.fn.dataTable.ext.classes, {
        container: "dt-container form-inline",
        search: {
            input: "form-control input-sm"
        },
        length: {
            select: "form-control input-sm"
        },
        processing: {
            container: "dt-processing panel panel-default"
        },
        layout: {
            row: "row dt-layout-row",
            cell: "dt-layout-cell",
            tableCell: 'col-12',
            start: "dt-layout-start col-sm-6",
            end: "dt-layout-end col-sm-6",
            full: "dt-layout-full col-sm-12"
        }
	} )

	// Pagination renderers to draw the Bootstrap paging,
	if ( $.fn.dataTable.Api ) {
		$.fn.dataTable.defaults.renderer = 'bootstrap';

        $.fn.dataTable.ext.renderer.pagingButton.bootstrap = function (settings, buttonType, content, active, disabled) {
			var api = new $.fn.dataTable.Api( settings );
			var classes = settings.oClasses;
            var btnDisplay;

            switch (buttonType) {
                case 'ellipsis':
                    btnDisplay = '&hellip;';
                    break;
                case 'first':
                    btnDisplay = api.i18n('paginate.first', 'First');
                    break;
                case 'previous':
                    btnDisplay = api.i18n('paginate.previous', 'Previous');
                    break;
                case 'next':
                    btnDisplay = api.i18n('paginate.next', 'Next');
                    break;
                case 'last':
                    btnDisplay = api.i18n('paginate.last', 'Last');
                    break;
                default:
                    btnDisplay = buttonType + 1;
                    break;
            }

            var btnClass = active ? 'active' : (disabled ? 'disabled' : '');

            var li = $('<li>').addClass(classes.paging.button + ' ' + btnClass);
            var a = $('<a>', {
                'href': (disabled || active) ? null : '#'
            })
            .html(btnDisplay)
            .appendTo(li);

            return {
                display: li,
                clicker: a
            };
        };

        $.fn.dataTable.ext.renderer.pagingContainer.bootstrap = function (settings, buttons) {
            return $('<ul/>').addClass('pagination').append(buttons);
        };
	}

	/* Set the defaults for DataTables initialisation */
	$.extend(true, $.fn.dataTable.defaults, {
		"initComplete": function (oSettings, json) {
		}
	})

	DataTable.DEFAULTS = {
        ajax: false,
        order: false
		, processing   : true
        , stateSave: true,
        stateDuration: 7200,
        serverSide: true,
        emptyTable: "No active record(s) here. Would you like to create one?",
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
	}

	// GREET DATATABLEs PLUGIN DEFINITION
	// =======================

	var old = $.fn.gdatatable

	$.fn.gdatatable = function (option) {
		return this.each(function () {
			var $this   = $(this)
			var data    = $this.data('gdatatable')
			var options = $.extend({}, DataTable.DEFAULTS, $this.data(), typeof option == 'object' && option)
			
			if (!data) $this.data('gdatatable', (data = new DataTable(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	$.fn.gdatatable.Constructor = DataTable

	// GREET DATATABLES NO CONFLICT
	// =================

	$.fn.gdatatable.noConflict = function () {
		$.fn.gdatatable = old
		return this
	}

	// GREET DATATABLES DATA-API
	// ==============

	$(window).on('load.datatable.data-api', function (e) {
		if (!$.fn.dataTable) return
		
		$('[data-toggle="datatable"]').each(function () {
			var $table = $(this)
			$table.gdatatable($table.data())
		})
	})

	// Added pajax and jquery mobile support
	$(document).on('pjax:complete pagecontainerchange', function (e) {
		if (!$.fn.dataTable) return
		
		$('[data-toggle="datatable"]').each(function () {
			var $table = $(this)
			$table.gdatatable($table.data())
		})
	})

}(jQuery);