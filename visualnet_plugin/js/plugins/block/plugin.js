/**
 * @file
 * Functionality to enable block functionality in CKEditor.
 */

(function ()
{
	'use strict';

	CKEDITOR.plugins.add('block', {

		hidpi: true,
		icons: 'block',

		init: function (editor)
		{
			editor.ui.addButton('Block', {
				command: 'addBlockCmd',
				icon: this.path + 'icons/block.png',
				label: Drupal.t('Insert block')
			});

			var cssPath = this.path + 'block.css';

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
				var command = editor.getCommand('addBlockCmd');
				var element = evt.data.path.lastElement && evt.data.path.lastElement.getAscendant('section', true);
				if (element) {
					command.setState(CKEDITOR.TRISTATE_DISABLED);
				}
				else {
					command.setState(CKEDITOR.TRISTATE_OFF);
				}
			});

			var allowedContent = 'div section(!ckeditor-block)';

			editor.addCommand('addBlockCmd', {

				allowedContent: allowedContent,

				exec: function (editor)
				{
					var div = new CKEDITOR.dom.element.createFromHtml (
						'<section class="ckeditor-block">' +
						'<div class="col-xs-6">' + Drupal.t('Block element') + ' 1</div>' +
						'<div class="col-xs-6">' + Drupal.t('Block element') + ' 2</div>' +
						'</section class="col-xs-6">');

					editor.insertElement(div);
				}
			});

			editor.addCommand('addBlockBefore', {

				allowedContent: allowedContent,

				exec: function (editor)
				{
					var element    = editor.getSelection().getStartElement();
					var newElement  = new CKEDITOR.dom.element.createFromHtml ('<div class="col-xs-6">' + Drupal.t('New element') + '</div>');

					if (element.getAscendant('div', true)) {
						element = element.getAscendant('div', true);
					}

					newElement.insertBefore(element);
				}
			});

			editor.addCommand('addBlockAfter', {

				allowedContent: allowedContent,

				exec: function (editor)
				{
					var element    = editor.getSelection().getStartElement();
					var newElement  = new CKEDITOR.dom.element.createFromHtml ('<div class="col-xs-6">' + Drupal.t('New element') + '</div>');

					if (element.getAscendant('div', true)) {
						element = element.getAscendant('div', true);
					}

					newElement.insertAfter(element);
				}
			});

			editor.addCommand('removeBlock', {

				exec: function (editor)
				{
					var selected = editor.getSelection().getStartElement(),
						element, ascendant;

					if(selected.getName() == 'section') {
						selected.remove();
					}

					ascendant = selected.getAscendant('div', true);

					if (ascendant) {
						ascendant.remove();
					}
				}
			});

			if (editor.contextMenu) {

				editor.addMenuGroup('blockGroup');

				editor.addMenuItem('blockBeforeItem', {
					label: Drupal.t('Add block before'),
					icon: this.path + 'icons/block.png',
					command: 'addBlockBefore',
					group: 'blockGroup'
				});

				editor.addMenuItem('blockAfterItem', {
					label: Drupal.t('Add block after'),
					icon: this.path + 'icons/block.png',
					command: 'addBlockAfter',
					group: 'blockGroup'
				});

				editor.addMenuItem('blockRemove', {
					label: Drupal.t('Remove block'),
					icon: this.path + 'icons/block.png',
					command: 'removeBlock',
					group: 'blockGroup'
				});

				editor.contextMenu.addListener(function (element)
				{
					var parentEl = element.getAscendant('section', true);
					if (parentEl && parentEl.hasClass('ckeditor-block')) {
						return {
							blockBeforeItem: CKEDITOR.TRISTATE_OFF,
							blockAfterItem: CKEDITOR.TRISTATE_OFF,
							blockRemove: CKEDITOR.TRISTATE_OFF
						};
					}
				});
			}
		}
	});

	CKEDITOR.dtd.$removeEmpty.div = 0;

}) ();
