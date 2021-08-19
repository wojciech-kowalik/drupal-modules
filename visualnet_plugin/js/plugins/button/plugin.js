/**
 * @file
 * Functionality to enable block functionality in CKEditor.
 */

(function ()
{
	'use strict';

	CKEDITOR.plugins.add('vtbutton', {

		hidpi: true,
		icons: 'vtbutton',

		init: function (editor)
		{
			editor.ui.addButton('Button', {
				command: 'addButtonCmd',
				icon: this.path + 'icons/button.png',
				label: Drupal.t('Insert button')
			});

			var cssPath = this.path + 'button.css';

			editor.on('mode', function ()
			{
				if (editor.mode === 'wysiwyg') {
					this.document.appendStyleSheet(cssPath);
				}
			});

			editor.on('selectionChange', function (evt)
			{
				if (editor.readOnly) {
					return;
				}
				var command = editor.getCommand('addButtonCmd');
				var element = evt.data.path.lastElement && evt.data.path.lastElement.getAscendant('div', true);
				if (element) {
					command.setState(CKEDITOR.TRISTATE_DISABLED);
				}
				else {
					command.setState(CKEDITOR.TRISTATE_OFF);
				}
			});

			var allowedContent = 'div(!ckeditor-button)';

			editor.addCommand('addButtonCmd', {

				allowedContent: allowedContent,

				exec: function (editor)
				{
					var button = new CKEDITOR.dom.element.createFromHtml (
						'<div class="ckeditor-button">' + Drupal.t('Button name') + '</div>');

					editor.insertElement(button);
				}
			});

			editor.addCommand('removeButton', {

				exec: function (editor)
				{
					var selected = editor.getSelection().getStartElement(),
						element, ascendant;

					ascendant = selected.getAscendant('div', true);

					if (ascendant) {
						ascendant.remove();
					}
				}
			});

			editor.addCommand('changeButtonColor', {

				exec: function (editor)
				{
					var selected = editor.getSelection().getStartElement();

					if(selected.hasClass('colorized')){
						selected.removeClass('colorized');
					}else{
						selected.addClass('colorized');
					}
				}
			});

			if (editor.contextMenu) {

				editor.addMenuGroup('buttonGroup');

				editor.addMenuItem('buttonRemove', {
					label: Drupal.t('Remove button'),
					icon: this.path + 'icons/button.png',
					command: 'removeButton',
					group: 'buttonGroup'
				});

				editor.addMenuItem('buttonChangeColor', {
					label: Drupal.t('Change button color'),
					icon: this.path + 'icons/button.png',
					command: 'changeButtonColor',
					group: 'buttonGroup'
				});

				editor.contextMenu.addListener(function (element)
				{
					var parentEl = element.getAscendant('div', true);
					if (parentEl && parentEl.hasClass('ckeditor-button')) {
						return {
							buttonRemove: CKEDITOR.TRISTATE_OFF,
							buttonChangeColor: CKEDITOR.TRISTATE_OFF
						};
					}
				});
			}
		}
	});

}) ();
