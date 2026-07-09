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

    const FileUpload = function (element, options) {
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

        this.$preview = this.$element.find('.file-upload-preview')
        const height = this.$preview.css('height');
        if (this.$preview.css('display') !== 'inline' && height !== '0px' && height !== 'none')
            this.$preview.css('line-height', height)

        this.original = {
            exists: this.$element.hasClass('file-upload-exists'),
            preview: this.$preview.html(),
            hiddenVal: this.$hidden.val()
        }

        this.listen()
        this.$element.trigger('init.gt.fileUpload', this)
    };

    FileUpload.prototype.listen = function () {
        this.$input.on('change.gt.fileUpload', $.proxy(this.change, this))
        $(this.$input[0].form).on('reset.gt.fileUpload', $.proxy(this.reset, this))

        this.$element.find('[data-trigger="file-upload"]').on('click.gt.fileUpload', $.proxy(this.trigger, this))
        this.$element.find('[data-dismiss="file-upload"]').on('click.gt.fileUpload', $.proxy(this.clear, this))
	}

    FileUpload.prototype.accept = function (file) {
		//restrict number of uploaded files when queue is 0
        if (this.options.maxFiles > 0 && this.total >= this.options.maxFiles && this.options.queueFiles === 0) {
            this.acceptErrors(file, 'maxFiles')
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

    FileUpload.prototype.addFile = function (file, i) {
		file.upload = {
			progress    : 0
			, total     : file.size
			, bytesSent : 0
		}

		// Set some defaults
		file.iframe  = false
		file.chunked = false
		file.errors  = false

        file.status = FileUpload.ADDED
		this.files.push(file)

		// Show image preview
		this.preview(file)

		if(this.accept(file)) {
			this.workQueue.push(i)
            this.$element.trigger('add.gt.fileUpload', [file, i])
		}
	}

    FileUpload.prototype.change = function (e) {
		if (e.target.files === undefined) e.target.files = e.target && e.target.value ? [ {name: e.target.value.replace(/^.+\\/, '')} ] : []
		if (e.target.files.length === 0) return

		this.$hidden.val('')
		this.$hidden.attr('name', '')
		this.$input.attr('name', this.name)
        this.$element.find('.file-upload-error').css('display', 'none')
        this.$element.find('.file-upload-success').css('display', 'none')
        this.$element.find('.file-upload-message').css('display', 'none')

        let files = e.target.files || [],
            i,
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

    FileUpload.prototype.processUpload = function () {
        let fileIndex,
            that = this;

        // Check to see if are in queue mode
        if (this.options.queueFiles > 0 && this.processingQueue.length >= this.options.queueFiles) {
            this.queueWait(this.options.queueWait)
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

    FileUpload.prototype.upload = function (file, fileIndex) {
        if (file.status === FileUpload.ADDED && file.status !== FileUpload.UPLOADING) {
			file.processing = true
            file.status = FileUpload.UPLOADING

			// Create a new AJAX request
            const xhr = file.xhr = new XMLHttpRequest(),
                that = this;

            this.$element.trigger('upload.gt.fileUpload', [file, fileIndex])

			if(this.isHTML5){
				// Add event handlers
				xhr.upload.onprogress = function(e){ that.fileProgress(e, file, fileIndex)	}
				xhr.upload.onabort    = function(e){ that.fileAbort(e, file, fileIndex) }
				xhr.upload.onerror    = function(e){ that.fileError(e, file, fileIndex) }

				xhr.onload = function(e) {
					if (xhr.readyState === 4 && xhr.status === 200) {
						try {
                            let response = xhr.responseText;
                            response = JSON.parse(response)
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

            // IE less than 10 does not support file.size.
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

    FileUpload.prototype.formDataUpload = function (xhr, file) {
        const formData = new FormData();

        // Add the form data
        formData.append(this.options.inputName, file)

		// Add the rest of the formData
		$.each(this.options.data, function(key, value) {
			formData.append(key, value)
		})

		file.formData = formData

		// Send the form data (multipart/form-data)
		this.send(xhr, file)
	}

    FileUpload.prototype.chunkUpload = function (xhr, file, start = 0) {
        const bpc = this.options.chunkSize || 1024 * 1024;

        file.chunked = true
		file.paused  = false
        file.index = (start === 0) ? 0 : file.index + 1
		file.slices  = Math.max(Math.ceil(file.size / bpc), 1)

		file.start   = start
		file.end     = Math.min(file.start+bpc, file.size)

		// @todo chunked upload
		this.send(xhr, file)
	}

    FileUpload.prototype.send = function (xhr, file) {
		// Open the AJAX call
		xhr.open(this.options.method, this.options.remote, this.options.async)

		// Add headers
		$.each(this.options.headers, function(k, v) {
			xhr.setRequestHeader(k, v)
		})

        this.$element.trigger('send.gt.fileUpload', file, xhr)

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

    FileUpload.prototype.preview = function (file) {
		if (this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
            const reader = new FileReader();
            const preview = this.$preview;
            const element = this.$element;
            const that = this;

            reader.onload = function (re) {
                const $img = $('<img>'); // .attr('src', re.target.result)
				$img[0].src = re.target.result
				file.result = re.target.result

                element.find('.file-upload-filename').text(file.name)

				// if parent has max-height, using `(max-)height: 100%` on child doesn't take padding and border into account
                if (preview.css('max-height') !== 'none')
                    $img.css('max-height', parseInt(preview.css('max-height'), 10) - parseInt(preview.css('padding-top'), 10) - parseInt(preview.css('padding-bottom'), 10) - parseInt(preview.css('border-top'), 10) - parseInt(preview.css('border-bottom'), 10))

				preview.html($img)
                element.addClass('file-upload-exists').removeClass('file-upload-new')

                element.trigger('change.gt.fileUpload', [that.files, file, that.$element])
			}

			reader.readAsDataURL(file)
		} else {
            this.$element.find('.file-upload-filename').text(file.name)
			this.$preview.text(file.name)

            this.$element.addClass('file-upload-exists').removeClass('file-upload-new')

            this.$element.trigger('change.gt.fileUpload')
		}
	}

    FileUpload.prototype.clear = function (e) {
		if (e) e.preventDefault()

		this.$hidden.val('')
		this.$hidden.attr('name', this.name)
		this.$input.attr('name', '')
        this.$input.val('')

		this.$preview.html('')
        this.$element.find('.file-upload-filename').text('')
        this.$element.addClass('file-upload-new').removeClass('file-upload-exists')
        this.$element.find('.file-upload-error').css('display', 'none')
        this.$element.find('.file-upload-success').css('display', 'none')
        this.$element.find('.file-upload-message').css('display', 'none')

		if (e !== false) {
			this.$input.trigger('change')
            this.$element.trigger('clear.gt.fileUpload')
		}
	}

    FileUpload.prototype.reset = function () {
		this.clear(false)

		this.$hidden.val(this.original.hiddenVal)
		this.$preview.html(this.original.preview)
        this.$element.find('.file-upload-filename').text('')
        this.$element.find('.file-upload-error').css('display', 'none')
        this.$element.find('.file-upload-success').css('display', 'none')
        this.$element.find('.file-upload-message').css('display', 'none')

        if (this.original.exists)
            this.$element.addClass('file-upload-exists').removeClass('file-upload-new')
        else
            this.$element.addClass('file-upload-new').removeClass('file-upload-exists')

        this.$element.trigger('reset.gt.fileUpload')
	}

    FileUpload.prototype.trigger = function (e) {
		this.$input.trigger('click')
		e.preventDefault()
	}

    FileUpload.prototype.loading = function (file) {
		file.$loading = $('<div class="loading">')

        const height = this.$preview.css('max-height') || this.$element.height(),
            width = this.$preview.css('max-width') || this.$element.width(),
            that = this;

		// set height after image is loaded
        setTimeout(function () {
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

        this.$element.trigger('loading.gt.fileUpload', file)
	}

    FileUpload.prototype.fileProgress = function (event, file, fileIndex) {
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

            this.$element.trigger('progress.gt.fileUpload', [file, fileIndex])
		}
	}

    FileUpload.prototype.fileAbort = function (event, file, fileIndex) {
        file.status = FileUpload.CANCELED

		file.$loading.remove()
        this.$element.find('.file-upload-error').css('display', 'block')
        this.$element.trigger('abort.gt.fileUpload', [file, fileIndex])
	}

    FileUpload.prototype.fileError = function (event, file, fileIndex) {
        file.status = FileUpload.ERROR

		file.$loading.remove()
        this.$element.find('.file-upload-error').css('display', 'block')
        this.$element.trigger('error.gt.fileUpload', [file, fileIndex])
	}

    FileUpload.prototype.uploadComplete = function (response, file, fileIndex) {
        const that = this;

        if (file.chunked && typeof file.end !== "undefined" && file.end !== file.size) {
			this.chunkUpload(file.xhr, file, file.end)
		}
		else {
			// Update processing data
            file.status = FileUpload.SUCCESS
			file.upload.progress  = 100
			file.upload.bytesSent = file.upload.total

            if (file.iframe === true) {
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

            this.$element.find('.file-upload-success').css('display', 'block')
            this.$element.trigger('uploaded.gt.fileUpload', [response, file, fileIndex])
		}
	}

    FileUpload.prototype.acceptErrors = function (file, error) {
        this.$element.trigger('error.gt.fileUpload', [file, error])
        const $message = this.$element.find('.file-upload-message');

        if ($message.length > 0 && error) {
			$message
				.empty()
				.css('display', 'block')
				.html(error)
		}
	}

	// Helper function to enable pause of processing to wait
	// for in process queue to complete
    FileUpload.prototype.queueWait = function (timeout) {
		setTimeout(this.processUpload, timeout)
	}

	/**
	* Pause the upload (works for chunked uploads only).
	*/
    FileUpload.prototype.pause = function (file) {
		if (file.chunked && !file.paused) {
			file.paused = true
		}
	}

	/**
	* Resume the upload (works for chunked uploads only).
	*/
    FileUpload.prototype.resume = function (file) {
		if (file.chunked && file.paused) {
			file.paused = false
			//this.upload()
		}
	}

    FileUpload.DEFAULTS = {
		auto         : true,
		async        : true,
		json         : true,
		method       : 'POST',
		remote       : false,
        inputName: 'files',
		multiple     : '',
		size         : 0, // Max individual file size
        filetypes: {}, // Allowed file extensions. Example: 'image/png', 'image/jpeg'
		data         : {},
		headers      : {},
        maxFiles: 15, // Ignored if queueFiles is set > 0
        queueFiles: 0, // Max files before queueing (for large volume uploads)
        queueWait: 200, // Queue wait time if full
		chunked      : false,
        chunkSize: 1048576, // Size of each chunk (default 1024*1024, 1 MiB)
        maxChunkSize: undefined
	}

    FileUpload.ADDED = "added"
    FileUpload.QUEUED = "queued"
    FileUpload.ACCEPTED = FileUpload.QUEUED
    FileUpload.UPLOADING = "uploading"
    FileUpload.PROCESSING = FileUpload.UPLOADING
    FileUpload.CANCELED = "canceled"
    FileUpload.ERROR = "error"
    FileUpload.SUCCESS = "success"

	// FILEUPLOAD PLUGIN DEFINITION
	// ==========================

    const old = $.fn.fileUpload;

    $.fn.fileUpload = function (option) {
        const args = Array.prototype.slice.call(arguments, 1);
        return this.each(function () {
            const $this = $(this);
            let data = $this.data('gt.fileUpload');
            const options = $.extend({}, FileUpload.DEFAULTS, $this.data(), typeof option == 'object' && option);

            if (!data)
                $this.data('gt.fileUpload', (data = new FileUpload(this, options)))
            if (typeof option == 'string') {
                data[option].apply(data, args);
            }
		})
	}

    $.fn.fileUpload.Constructor = FileUpload

	// FILEUPLOAD NO CONFLICT
	// ====================

    $.fn.fileUpload.noConflict = function () {
        $.fn.fileUpload = old
		return this
	}


	// FILEUPLOAD DATA-API
	// ==================

    $(document).on('click.fileUpload.data-api', '[data-provides="file-upload"]', function (e) {
        const $this = $(this);
        if ($this.data('gt.fileUpload'))
            return
        $this.fileUpload($this.data())

        const $target = $(e.target).closest('[data-dismiss="file-upload"],[data-trigger="file-upload"]');
        if ($target.length > 0) {
			e.preventDefault()
            $target.trigger('click.gt.fileUpload')
		}
	})

}(jQuery);