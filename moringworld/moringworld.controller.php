<?php
/*! Copyright (C) 2017 Moring World. All rights reserved. */
/**
 * @class moringworldController
 * @author Huhani (mmia268@gmail.com)
 * @brief Moring World module controller class.
 */

class moringworldController extends moringworld
{
	function init(){
	}


	function triggerBeforeModuleInit(&$obj){

		// 짧은 주소 사용을 위한 사전작업
		if(!$obj->document_srl){
			return new Object();
		}
		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoByDocumentSrl($obj->document_srl);
		if($module_info){
			$obj->mid = $module_info->mid;
			Context::set('mid', $obj->mid);
		}

		return new Object();
	}

	function triggerBeforeModuleProc(&$oModule){
		switch($oModule->act){
			case "dispBoardContent":
				$use_cpage_detecter = Context::get('cpage_detect') ? TRUE : FALSE;
				if($use_cpage_detecter){
					$oMoringWorldModel = getModel('moringworld');
					$output = $oMoringWorldModel->setMoringWorldCommentPage();
					if($output === false){
						return new Object(-1, "msg_invalid_request");
					}
				}
			break;
		}

		return new Object();
	}


}

/* End of file moringworld.controller.php */
/* Location: ./modules/moringworld/moringworld.controller.php */
