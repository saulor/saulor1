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

	config.toolbarGroups = [
	    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
	    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
	    { name: 'links' },
	    { name: 'insert' },
	    { name: 'forms' },
	    { name: 'tools' },
	    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
	    { name: 'others' },
	    '/',
	    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	    { name: 'styles' },
	    { name: 'colors' },
	    { name: 'about' }
	];

	config.toolbar_basic = [
		['Source'],
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike' ] },
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		['NumberedList', 'BulletedList'],
		['Link', 'Unlink'],
	    { name: 'document', items: [ 'NewPage', 'Preview', '-', 'Templates' ] },
	    ['Youtube', 'Image'],
	    ['RemoveFormat'],
	    ['Maximize']
	];

	config.toolbar_basic2 = [
		['Source'],
		{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
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
	config.removeDialogTabs = 'image:advanced;link:advanced';

	config.removePlugins = 'wsc,scayt';
	config.extraPlugins = 'uploadimage';
	config.imageUploadUrl = 'webtools/ckeditor/upload.php?type=Images';

    config.filebrowserBrowseUrl = 'webtools/ckeditor/fileman/index.html?langCode=pt';
    config.filebrowserImageBrowseUrl = 'webtools/ckeditor/fileman/index.html?langCode=pt&type=image';
    config.removeDialogTabs = 'link:upload;image:upload';
};
