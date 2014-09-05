<?php

class Popup extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		while(ob_get_level())
			ob_end_clean();


	}

	public function close()
	{
?>
<script type="text/javascript">
	window.parent.$(".jackbox-close").trigger("click.jackbox");
</script>
<?php
	}


} // class

?>