<?php

/**
 * @class  moringworldAdminView
 * @author Huhani (mmia268@gmail.com)
 * @brief  Moring World module admin view class.
 **/

class moringworldAdminView extends moringworld
{
	function init(){
		$this->setTemplatePath($this->module_path . 'tpl/');
	}
}

/* End of file moringworld.admin.view.php */
/* Location: ./modules/moringworld/moringworld.admin.view.php */
