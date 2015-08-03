CKEDITOR.plugins.setLang( 'simpleuploads', 'de',
    {
        // Tooltip for the "add file" toolbar button
        addFile    : 'Datei hinzufügen',
        // Tooltip for the "add image" toolbar button
        addImage: 'Bild hinzufügen',

        // Shown after the data has been sent to the server and we're waiting for the response
        processing: 'Wird geladen...',

        // File size is over config.simpleuploads_maxFileSize OR the server returns HTTP status 413
        fileTooBig : 'Die Datei ist zu groß. Versuchen Sie bitte eine kleinere Datei hochzuladen.',

        // The extension matches one of the blacklisted ones in config.simpleuploads_invalidExtensions
        invalidExtension : 'Die ausgewählte Datei ist nicht zugelassen. Bitte lade nur zugelassene Dateien hoch.',

        // The extension isn't included in config.simpleuploads_acceptedExtensions
        nonAcceptedExtension: 'Die ausgewählte Datei ist nicht zugelassen. Bitte lade nur zugelassene Dateien hoch:\r\n%0',

		// The file isn't an accepted type for images
		nonImageExtension: 'Sie müssen ein Bild auswählen',

		// The width of the image is over the allowed maximum
		imageTooWide: 'Das Bild ist zu breit',

		// The height of the image is over the allowed maximum
		imageTooTall: 'Das Bild ist nicht hoch genug'
    }
);