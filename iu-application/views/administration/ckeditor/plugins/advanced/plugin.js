CKEDITOR.plugins.add( 'advanced',
{
	init: function( editor )
	{
		editor.addCommand( 'advanced',
			{
				exec : function( editor )
				{

					iu_advanced_edit(editor);
				}
			});
		editor.ui.addButton( 'Advanced',
		{
			label: 'Advanced editor',
			command: 'advanced',
			icon: this.path + 'images/advanced.png'
		} );
	}
} );