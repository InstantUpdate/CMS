CKEDITOR.plugins.add( 'inlinesave',
{
	init: function( editor )
	{
		editor.addCommand( 'inlinesave',
			{
				exec : function( editor )
				{

					iu_quick_save(editor.editable().$);
				}
			});
		editor.ui.addButton( 'Inlinesave',
		{
			label: 'Quick save',
			command: 'inlinesave',
			icon: this.path + 'images/inlinesave.png'
		} );
	}
} );