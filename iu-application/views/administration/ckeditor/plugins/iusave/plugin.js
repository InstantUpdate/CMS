CKEDITOR.plugins.add( 'iusave',
{
	init: function( editor )
	{
		editor.addCommand( 'iusave',
			{
				exec : function( editor )
				{
					window.save(true, editor);
				}
			});
		editor.ui.addButton( 'Iusave',
		{
			label: 'Save',
			command: 'iusave',
			icon: this.path + 'images/iusave.png'
		} );
	}
} );