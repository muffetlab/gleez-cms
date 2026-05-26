/*
 * HTML5 File upload with image preview and fallback iframe for older browsers
 * https://github.com/gleez/greet
 * 
 * @package    Greet\FileUpload
 * @version    1.0
 * @requires   jQuery v1.9 or later
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2005-2015 Gleez Technologies
 * @license    The MIT License (MIT)
 *
 */

+function ($) {
	'use strict';

	// GREET FILEUPLOAD PUBLIC CLASS DEFINITION
	// ======================

    const Fileupload = function (element, options) {
        this.options = options
        this.$element = $(element)
        this.isHTML5 = false
        this.template = this.$element.clone(true)
        this.multipart = this.options.multipart || !$.support.xhrFileUpload
        this.files = []

        // Define queues to manage upload process
        this.workQueue = []
        this.processingQueue = []
        this.doneQueue = []

        // Check if HTML5 is available
        if (window.File && window.FileList && window.Blob && (window.FileReader || window.FormData)) {
            this.isHTML5 = true
        }

        // Read file using FormData interface
        this.canFormData = !!(window.FormData)

        this.$input = this.$element.find(':file')
        if (this.$input.length === 0) return

        this.name = this.$input.attr('name') || options.name

        this.$hidden = this.$element.find('input[type=hidden][name="' + this.name + '"]')
        if (this.$hidden.length === 0) {
            this.$hidden = $('<input type="hidden"/>')
            this.$element.prepend(this.$hidden)
        }

        this.$preview = this.$element.find('.fileupload-preview')
        const height = this.$preview.css('height');
        if (this.$preview.css('display') != 'inline' && height != '0px' && height != 'none') this.$preview.css('line-height', height)

        this.original = {
            exists: this.$element.hasClass('fileupload-exists'),
            preview: this.$preview.html(),
            hiddenVal: this.$hidden.val()
        }

        this.listen()
        this.$element.trigger('init.gt.fileupload', this)
    };

    Fileupload.prototype.listen = function () {
		this.$input.on('change.gt.fileupload', $.proxy(this.change, this))
		$(this.$input[0].form).on('reset.gt.fileupload', $.proxy(this.reset, this))

		this.$element.find('[data-trigger="fileupload"]').on('click.gt.fileupload', $.proxy(this.trigger, this))
		this.$element.find('[data-dismiss="fileupload"]').on('click.gt.fileupload', $.proxy(this.clear, this))
	}

	Fileupload.prototype.accept = function(file) {
		//restrict number of uploaded files when queue is 0
		if(this.options.maxfiles > 0 && this.total >= this.options.maxfiles && this.options.queuefiles === 0){
			this.acceptErrors(file, 'maxfiles')
			return false
		}

		// Check file against file size restrictions
		if (this.options.size > 0 && (typeof file.size !== 'undefined') && file.size > this.options.size) {
			this.acceptErrors(file, 'size')
			return false
		}

		// Check file against file type restrictions
		if (this.options.filetypes && this.options.filetypes.length > 0) {
			if(!file.type || $.inArray(file.type, this.options.filetypes) < 0) {
				this.acceptErrors(file, 'filetypes')
				return false
			}
		}

		return true
	}

	Fileupload.prototype.addFile = function(file, i) {
		file.upload = {
			progress    : 0
			, total     : file.size
			, bytesSent : 0
		}

		// Set some defaults
		file.iframe  = false
		file.chunked = false
		file.errors  = false

		file.status = Fileupload.ADDED
		this.files.push(file)

		// Show image preview
		this.preview(file)

		if(this.accept(file)) {
			this.workQueue.push(i)
			this.$element.trigger('add.gt.fileupload', [file, i])
		}
	}

	Fileupload.prototype.change = function(e) {
		if (e.target.files === undefined) e.target.files = e.target && e.target.value ? [ {name: e.target.value.replace(/^.+\\/, '')} ] : []
		if (e.target.files.length === 0) return

		this.$hidden.val('')
		this.$hidden.attr('name', '')
		this.$input.attr('name', this.name)
		this.$element.find('.fileupload-error').css('display', 'none')
		this.$element.find('.fileupload-success').css('display', 'none')
		this.$element.find('.fileupload-message').css('display', 'none')

        let files = e.target.files || [],
            i = 0,
            file;

        this.files = []
		this.total = e.target.files.length || 0

		// Add everything to the workQueue
		for (i = 0; i < this.total; i++) {
			file = files[i]
			this.addFile(file, i)
		}

		// Upload to server
		if (this.options.remote && this.options.auto){
			this.processUpload()
		}
	}

	Fileupload.prototype.processUpload = function() {
        let fileIndex,
            that = this;

        // Check to see if are in queue mode
		if (this.options.queuefiles > 0 && this.processingQueue.length >= this.options.queuefiles) {
			this.queueWait(this.options.queuewait)
		} 
		else {
			// Take first thing off work queue
			fileIndex = this.workQueue[0]
			this.workQueue.splice(0, 1)

			// Add to processing queue
			this.processingQueue.push(fileIndex)
		}

		try
		{
			this.upload(this.files[fileIndex], fileIndex)
		}
		catch (e) {
			$.each (this.processingQueue, function (value, key) {
				if (value === fileIndex) {
					that.processingQueue.splice(key, 1)
				}
			})
		}

		// If we still have work to do,
		if (this.workQueue.length > 0) {
			this.processUpload()
		}
	}

	Fileupload.prototype.upload = function(file, fileIndex) {
		if (file.status == Fileupload.ADDED && file.status != Fileupload.UPLOADING) {
			file.processing = true
			file.status = Fileupload.UPLOADING

			// Create a new AJAX request
            const xhr = file.xhr = new XMLHttpRequest(),
                that = this;

            this.$element.trigger('upload.gt.fileupload', [file, fileIndex])

			if(this.isHTML5){
				// Add event handlers
				xhr.upload.onprogress = function(e){ that.fileProgress(e, file, fileIndex)	}
				xhr.upload.onabort    = function(e){ that.fileAbort(e, file, fileIndex) }
				xhr.upload.onerror    = function(e){ that.fileError(e, file, fileIndex) }

				xhr.onload = function(e) {
					if (xhr.readyState === 4 && xhr.status === 200) {
						try {
                            let response = xhr.responseText;
                            response = $.parseJSON(response)
							that.uploadComplete(response, file, fileIndex)
						}
						catch(ev) {
							that.fileError(e, file, fileIndex)
						}
					} else {
						that.fileError(e, file, fileIndex)
					}
				}
			}

			// Add the loading spinner
			this.loading(file)

			// IE less than 10 dose not support file.size.
			if(this.isHTML5 && this.options.chunked) {
				// Chunked upload
				this.chunkUpload(xhr, file)
			}
			else if(this.isHTML5 && this.canFormData) {
				// Use the faster FormData
				this.formDataUpload(xhr, file)
			}
		}
	}

	Fileupload.prototype.formDataUpload = function(xhr, file) {
        const formData = new FormData();

        // Add the form data
		formData.append(this.options.inputname, file)

		// Add the rest of the formData
		$.each(this.options.data, function(key, value) {
			formData.append(key, value)
		})

		file.formData = formData

		// Send the form data (multipart/form-data)
		this.send(xhr, file)
	}

	Fileupload.prototype.chunkUpload = function(xhr, file, start = 0) {
        const bpc = this.options.chunksize || 1024 * 1024;

        file.chunked = true
		file.paused  = false
		file.index   = (start == 0) ? 0 : file.index + 1
		file.slices  = Math.max(Math.ceil(file.size / bpc), 1)

		file.start   = start
		file.end     = Math.min(file.start+bpc, file.size)

		// @todo chunked upload
		this.send(xhr, file)
	}

	Fileupload.prototype.send = function(xhr, file) {
		// Open the AJAX call
		xhr.open(this.options.method, this.options.remote, this.options.async)

		// Add headers
		$.each(this.options.headers, function(k, v) {
			xhr.setRequestHeader(k, v)
		})

		this.$element.trigger('send.gt.fileupload', file, xhr)

		// set the XMLHttpRequest header
		xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest")
		xhr.setRequestHeader('Accept', 'application/json')

		// Chunked upload and optional headers
		if(file.chunked){
			xhr.overrideMimeType('application/octet-stream')
			xhr.setRequestHeader('Content-Range', 'bytes '+file.start+"-"+file.end+"/"+file.size)

			// custom header with filename and full size
			xhr.setRequestHeader("X-File-Name",   file.name)
			xhr.setRequestHeader("X-File-Size",   file.size)
			xhr.setRequestHeader("X-File-Index",  file.index)
			xhr.setRequestHeader("X-File-Type",   file.type)
			xhr.setRequestHeader("X-File-Slices", file.slices)

			// add any necessary form data as X-Form headers
			$.each(this.options.data, function(key, value) {
				xhr.setRequestHeader("X-Form-"+ key, value)
			})
		}

		// send only the file and formData
		if(typeof file.formData !== "undefined"){
			file = file.formData
		}

        if (file.chunked && file.end) {
			xhr.send( file.slice(file.start, file.end) )
		}
		else {
            // Blob or FormData or File
			xhr.send(file)
		}
	}

	Fileupload.prototype.preview = function(file) {
		if (this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
            const reader = new FileReader();
            const preview = this.$preview;
            const element = this.$element;

            reader.onload = function (re) {
                const $img = $('<img>'); // .attr('src', re.target.result)
				$img[0].src = re.target.result
				file.result = re.target.result

				element.find('.fileupload-filename').text(file.name)

				// if parent has max-height, using `(max-)height: 100%` on child doesn't take padding and border into account
				if (preview.css('max-height') != 'none') $img.css('max-height', parseInt(preview.css('max-height'), 10) - parseInt(preview.css('padding-top'), 10) - parseInt(preview.css('padding-bottom'), 10)  - parseInt(preview.css('border-top'), 10) - parseInt(preview.css('border-bottom'), 10))

				preview.html($img)
				element.addClass('fileupload-exists').removeClass('fileupload-new')

				element.trigger('change.gt.fileupload', [this.files, file, this.$element])
			}

			reader.readAsDataURL(file)
		} else {
			this.$element.find('.fileupload-filename').text(file.name)
			this.$preview.text(file.name)

			this.$element.addClass('fileupload-exists').removeClass('fileupload-new')

			this.$element.trigger('change.gt.fileupload')
		}
	}

	Fileupload.prototype.clear = function(e) {
		if (e) e.preventDefault()

		this.$hidden.val('')
		this.$hidden.attr('name', this.name)
		this.$input.attr('name', '')
        this.$input.val('')

		this.$preview.html('')
		this.$element.find('.fileupload-filename').text('')
		this.$element.addClass('fileupload-new').removeClass('fileupload-exists')
		this.$element.find('.fileupload-error').css('display', 'none')
		this.$element.find('.fileupload-success').css('display', 'none')
		this.$element.find('.fileupload-message').css('display', 'none')

		if (e !== false) {
			this.$input.trigger('change')
			this.$element.trigger('clear.gt.fileupload')
		}
	}

	Fileupload.prototype.reset = function() {
		this.clear(false)

		this.$hidden.val(this.original.hiddenVal)
		this.$preview.html(this.original.preview)
		this.$element.find('.fileupload-filename').text('')
		this.$element.find('.fileupload-error').css('display', 'none')
		this.$element.find('.fileupload-success').css('display', 'none')
		this.$element.find('.fileupload-message').css('display', 'none')

		if (this.original.exists) this.$element.addClass('fileupload-exists').removeClass('fileupload-new')
		 else this.$element.addClass('fileupload-new').removeClass('fileupload-exists')

		this.$element.trigger('reset.gt.fileupload')
	}

	Fileupload.prototype.trigger = function(e) {
		this.$input.trigger('click')
		e.preventDefault()
	}

	Fileupload.prototype.loading = function(file) {
		file.$loading = $('<div class="loading">')

        const height = this.$preview.css('max-height') || this.$element.height(),
            width = this.$preview.css('max-width') || this.$element.width(),
            offset = this.$preview.offset() || this.$element.offset(),
            that = this;

		// set height after image is loaded
		setTimeout( function(e){
			if(typeof file.$loading !== "undefined" && that.$preview.length > 0) {
                const newHeight = that.$preview.children('img').height() || height,
                    newWidth = that.$preview.children('img').width() || width;

                file.$loading.css('height', parseInt(newHeight) + 10)
							  .css('width',  parseInt(newWidth) + 10)
			}
		}, 500)

		// set default loading attributes
		file.$loading .css('height', height).css('width',  width)
		this.$element.prepend(file.$loading)

		this.$element.trigger('loading.gt.fileupload', file)
	}

	Fileupload.prototype.fileProgress = function(event, file, fileIndex) {
		if (event.lengthComputable) {
            let total = event.total,
                loaded = event.loaded,
                progress;

            if (file.chunked) {
				loaded   = parseInt(event.loaded + file.start)
				total    = file.size
			}

			progress = Math.ceil( (loaded / total) * 100 )

			file.upload = {
				progress  : progress,
				total     : total,
				bytesSent : loaded
			}

			this.$element.trigger('progress.gt.fileupload', [file, fileIndex])
		}
	}

	Fileupload.prototype.fileAbort = function(event, file, fileIndex) {
		file.status = Fileupload.CANCELED

		file.$loading.remove()
		this.$element.find('.fileupload-error').css('display', 'block')
		this.$element.trigger('abort.gt.fileupload', [file, fileIndex])
	}

	Fileupload.prototype.fileError = function(event, file, fileIndex) {
		file.status = Fileupload.ERROR

		file.$loading.remove()
		this.$element.find('.fileupload-error').css('display', 'block')
		this.$element.trigger('error.gt.fileupload', [file, fileIndex])
	}

	Fileupload.prototype.uploadComplete = function(response, file, fileIndex) {
        const that = this;

        if (file.chunked && typeof file.end !== "undefined" && file.end != file.size) {
			this.chunkUpload(file.xhr, file, file.end)
		}
		else {
			// Update processing data
			file.status    = Fileupload.SUCCESS
			file.upload.progress  = 100
			file.upload.bytesSent = file.upload.total

			if(file.iframe == true){
				this.$preview.empty()
				$('<img />').attr('src', response.file.src).appendTo(this.$preview)
			}

			// Remove from processing queue
			$.each (this.processingQueue, function (value, key) {
				if (value === fileIndex) {
					that.processingQueue.splice(key, 1)
				}
			})

	        // Add to doneQueue
			this.doneQueue.push(fileIndex)
			file.$loading.remove()

			this.$element.find('.fileupload-success').css('display', 'block')
			this.$element.trigger('uploaded.gt.fileupload', [response, file, fileIndex])
		}
	}

	Fileupload.prototype.acceptErrors = function(file, error) {
		this.$element.trigger('error.gt.fileupload', [file, error])
        const $message = this.$element.find('.fileupload-message');

        if ($message.length > 0 && error) {
			$message
				.empty()
				.css('display', 'block')
				.html(error)
		}
	}

	// Helper function to enable pause of processing to wait
	// for in process queue to complete
	Fileupload.prototype.queueWait = function(timeout) {
		setTimeout(this.processUpload, timeout)
	}

	/**
	* Pause the upload (works for chunked uploads only).
	*/
	Fileupload.prototype.pause = function(file){
		if (file.chunked && !file.paused) {
			file.paused = true
		}
	}

	/**
	* Resume the upload (works for chunked uploads only).
	*/
	Fileupload.prototype.resume = function(file){
		if (file.chunked && file.paused) {
			file.paused = false
			//this.upload()
		}
	}

	Fileupload.DEFAULTS = {
		auto         : true,
		async        : true,
		json         : true,
		method       : 'POST',
		remote       : false,
		inputname    : 'files',
		multiple     : '',
		size         : 0, // Max individual file size
		filetypes    : {}, // Allowed file extentions ex: 'image/png', 'image/jpeg'
		data         : {},
		headers      : {},
		maxfiles     : 15, // Ignored if queuefiles is set > 0
		queuefiles   : 0, // Max files before queueing (for large volume uploads)
		queuewait    : 200, // Queue wait time if full
		chunked      : false,
		chunksize    : 1048576, // Size of each chunk (default 1024*1024, 1 MiB)
		maxchunksize : undefined
	}

	Fileupload.ADDED      = "added"
	Fileupload.QUEUED     = "queued"
	Fileupload.ACCEPTED   = Fileupload.QUEUED
	Fileupload.UPLOADING  = "uploading"
	Fileupload.PROCESSING = Fileupload.UPLOADING
	Fileupload.CANCELED   = "canceled"
	Fileupload.ERROR      = "error"
	Fileupload.SUCCESS    = "success"

	// FILEUPLOAD PLUGIN DEFINITION
	// ==========================

    const old = $.fn.fileupload;

    $.fn.fileupload = function (option) {
        return this.each(function () {
            const $this = $(this);
            let data = $this.data('gt.fileupload');
            const options = $.extend({}, Fileupload.DEFAULTS, $this.data(), typeof option == 'object' && option);

            if (!data) $this.data('gt.fileupload', (data = new Fileupload(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	$.fn.fileupload.Constructor = Fileupload

	// FILEUPLOAD NO CONFLICT
	// ====================

	$.fn.fileupload.noConflict = function () {
		$.fn.fileupload = old
		return this
	}


	// FILEUPLOAD DATA-API
	// ==================

	$(document).on('click.fileupload.data-api', '[data-provides="fileupload"]', function (e) {
        const $this = $(this);
        if ($this.data('gt.fileupload')) return
		$this.fileupload($this.data())

        const $target = $(e.target).closest('[data-dismiss="fileupload"],[data-trigger="fileupload"]');
        if ($target.length > 0) {
			e.preventDefault()
			$target.trigger('click.gt.fileupload')
		}
	})

}(jQuery);