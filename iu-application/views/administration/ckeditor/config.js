/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.skin = 'bootstrapck';
	config.allowedContent = true;
	config.extraPlugins = 'inlinesave,advanced,iusave,simpleuploads'; //,imagepaste
	config.filebrowserUploadUrl = IU_SITE_URL+'/administration/assets/quick_upload';
	config.filebrowserImageUploadUrl = IU_SITE_URL+'/administration/assets/quick_upload';
};
