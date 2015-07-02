/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.skin = 'bootstrapck';
	config.height = '460px';
	config.allowedContent = true;
	config.extraPlugins = 'inlinesave,advanced,iusave,simpleuploads,youtube'; 
	config.filebrowserUploadUrl = IU_SITE_URL+'/administration/assets/quick_upload';
	config.filebrowserImageUploadUrl = IU_SITE_URL+'/administration/assets/quick_upload';
	CKEDITOR.config.toolbar = [
   ['Styles','Format','Font','FontSize'],
   '/',
   ['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','Find','Replace','-','Outdent','Indent','-','Print'],
   ['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
   ['Image', 'addImage', 'addFile', 'Table','-','Link', 'Unlink', 'Smiley','TextColor','BGColor','Source', 'Youtube']
] ;
};
