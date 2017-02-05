/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.

	config.toolbar_basic = [
		['Source'],
		['Undo', 'Redo'],
		['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-'],
		['Bold', 'Italic', 'Underline', 'Strike'],
		['NumberedList', 'BulletedList'],
		['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
		['Link', 'Unlink'],
	    ['Youtube', 'Image'],
	    ['RemoveFormat'],
	    ['Maximize']
	];

	config.toolbar_basic2 = [
		['Source'],
		['Bold', 'Italic', 'Underline', 'Strike'],
		['NumberedList', 'BulletedList'],
	    ['RemoveFormat']
	];

	config.toolbar_none =
	[
		['Source', '-', 'RemoveFormat', '-', 'Maximize']
	];

	config.removeButtons = 'About,Font,Styles,CreateDiv,Format,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced;link:upload;image:upload';

	config.extraPlugins = 'uploadimage,justify';
	config.imageUploadUrl = 'webtools/ckeditor/upload.php?type=Images';

    config.filebrowserBrowseUrl = 'webtools/ckeditor/fileman/index.html?langCode=pt';
    config.filebrowserImageBrowseUrl = 'webtools/ckeditor/fileman/index.html?langCode=pt&type=image';
};
