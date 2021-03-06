/**
 * Creates a TinyMCE plugin which adds a special button to the editor that 
 * facilitate the insertion of rows with a full width inside the editor's text. 
 * Usually the editor's text is confined to a certain convenient width. With 
 * this element the enclosed contents may span the whole width of the page.
 */

tinymce.create( 'tinymce.plugins.MnltrTinymceShortcodes', {

	/**
	 * The TinyMCE editor instance the plugin instance is associated with.
	 */
	
	// editor: null,


	/**
	 * Initializes the TinyMCE plugin by declaring the desired command and the
	 * button that activates it. The command adds a full width content shortcode
	 * to the text.
	 */

	init : function( editor, url ) {

		this.editor = editor;

		// Get the shortcodes
		var shortcodes = mnltr_skin_shortcodes;

		// Add a button for each one, handled by the same command
		for( var i = 0; i < shortcodes.length; i++ ) {

			var shortcode = shortcodes[i];
			
			editor.addButton( shortcodes[i], {
				text  : '[' + shortcodes[i] + ']',
                title : 'Shortcode: ' + shortcodes[i],
				onclick : function() {

					return function( value ) {

						return function() {
							editor.execCommand( 'mceInsertContent', false, '[' + value + '][/' + value + ']' );
						};

					}(shortcode);

				}()
			});
		}
	},

	

	/**
	 * Returns information about this plugin.
	 */

	getInfo : function () {

		return {
			longname  : 'Nevma Newsletters Skin Shortcodes',
			author    : 'Nevma',
			authorurl : 'http://www.nevma.gr',
			infourl   : 'http://www.nevma.gr',
			version   : '1.0'
		};

	}

});

// Add the plugin which was just created to the plugin manager.

tinymce.PluginManager.add( 'mnltrskinshortcodes', tinymce.plugins.MnltrTinymceShortcodes );