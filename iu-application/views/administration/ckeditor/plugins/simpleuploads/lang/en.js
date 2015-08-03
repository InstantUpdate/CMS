CKEDITOR.plugins.setLang( 'simpleuploads', 'en',
	{
		// Tooltip for the "add file" toolbar button
		addFile	: 'Add a file',
		// Tooltip for the "add image" toolbar button
		addImage: 'Add an image',

		// Shown after the data has been sent to the server and we're waiting for the response
		processing: 'Processing...',

		// File size is over config.simpleuploads_maxFileSize OR the server returns HTTP status 413
		fileTooBig : 'The file is too big, please use a smaller one.',

		// The extension matches one of the blacklisted ones in config.simpleuploads_invalidExtensions
		invalidExtension : 'Invalid file type, please use only valid files.',

		// The extension isn't included in config.simpleuploads_acceptedExtensions
		nonAcceptedExtension: 'The file type is not valid, please use only valid files:\r\n%0',

		// The file isn't an accepted type for images
		nonImageExtension: 'You must select an image',

		// The width of the image is over the allowed maximum
		imageTooWide: 'The image is too wide',

		// The height of the image is over the allowed maximum
		imageTooTall: 'The image is too tall'
	}
);
