CKEDITOR.plugins.setLang( 'simpleuploads', 'pl',
    {
        // Tooltip for the "add file" toolbar button
        addFile    : 'Dodanie pliku',
        // Tooltip for the "add image" toolbar button
        addImage: 'Dodanie obrazka',

        // Shown after the data has been sent to the server and we're waiting for the response
        processing: 'Wczytuję...',

        // File size is over config.simpleuploads_maxFileSize OR the server returns HTTP status 413
        fileTooBig : 'Wybrany plik jest zbyt duży. Proszę wybierz mniejszy plik.',

        // The extension matches one of the blacklisted ones in config.simpleuploads_invalidExtensions
        invalidExtension : 'Nieprawidłowy typ pliku. Proszę wybrać plik o dopuszczalnym typie.',

        // The extension isn't included in config.simpleuploads_acceptedExtensions
        nonAcceptedExtension: 'Nieprawidłowy typ pliku. Proszę wybrać plik o dopuszczalnym typie:\r\n%0',

		// The file isn't an accepted type for images
		nonImageExtension: 'Musisz wybrać zdjęcie',

		// The width of the image is over the allowed maximum
		imageTooWide: 'The image is too wide',

		// The height of the image is over the allowed maximum
		imageTooTall: 'The image is too tall'
    }
);
