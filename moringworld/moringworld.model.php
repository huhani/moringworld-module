<?php
/*! Copyright (C) 2017 Moring World. All rights reserved. */
/**
 * @class moringworldModel
 * @author Huhani (mmia268@gmail.com)
 * @brief Moring World module model class.
 */

class moringworldModel extends moringworld
{
	function init(){
	}

	function getConfig(){
		static $config = null;
		if(is_null($config))	{
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('moringworld');
			if(!$config){
				$config = new stdClass;
			}

			unset($config->body);
			unset($config->_filter);
			unset($config->error_return_url);
			unset($config->act);
			unset($config->module);
		}

		return $config;
	}

	function getMoringWorldMemberPage() {
		$logged_info = Context::get('logged_info');

		if($logged_info) {
			$file = 'member_info';
			$member_srl = $logged_info->member_srl;
			$oPointModel = getModel('point');
			$oModuleModel = getModel('module');

			$point_config = $oModuleModel->getModuleConfig('point');
			$point = $oPointModel->getPoint($member_srl);
			$level = $oPointModel->getLevel($point, $point_config->level_step);
			$level_max = count($point_config->level_step);

			$level_step = $point_config->level_step[$level];
			$next_level_step = $point_config->level_step[$level+1 > $level_max ? $level_max : $level+1];
			$percent = $level+1 <= $level_max ? round(($point-$level_step)/($next_level_step-$level_step)*100, 0) : 100;

			Context::set('point', $point);
			Context::set('level', $level);
			Context::set('level_max', $level_max);
			Context::set('percent', $percent);

		} else {
			$file = 'member_login';
		}

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', $file);

		$this->add('html', $tpl);
	}

	function getMoringWorldMemberScrapPage() {
		$logged_info = Context::get('logged_info');
		$page = Context::get('page');
		if(!$logged_info) {
			return new Object(-1, 'msg_invalid_request');
		}

		$args = new stdClass();
		$args->member_srl = $logged_info->member_srl;
		$args->page = (int)Context::get('page');
		$output = executeQuery('member.getScrapDocumentList', $args);

		$oDocumentModel = getModel('document');
		$oModuleModel = getModel('module');
		$oPointModel = getModel('point');
		$point_config = $oModuleModel->getModuleConfig('point');
		$level_max = count($point_config->level_step);
		foreach($output->data as &$val) {
			$oDocument = $oDocumentModel->getDocument($val->document_srl);
			if(!$oDocument->isExists()){
				$val->document_srl = 0;
				$val->module_srl = 0;
				$val->module = 'unknown';

				continue;
			}

			$module_srl = $oDocument->get('module_srl');
			$oModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if(!$oModuleInfo) {
				$val->module_srl = 0;
				$val->module = 'unknown';

				continue;
			}

			$val->browser_title = $oModuleInfo->browser_title;

			$val->point = $point = $oPointModel->getPoint($val->target_member_srl);
			$val->level = $level = $oPointModel->getLevel($point, $point_config->level_step);

			$level_step = $point_config->level_step[$level];
			$next_level_step = $point_config->level_step[$level+1 > $level_max ? $level_max : $level+1];
			$val->percent = $level+1 <= $level_max ? round(($point-$level_step)/($next_level_step-$level_step)*100, 0) : 100;
		}

		Context::set('level_max', $level_max);

		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('document_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', 'member_scrap');
		$this->add('html', $tpl);
	}

	function getMoringWorldMemberWriteDocument() {
		$logged_info = Context::get('logged_info');
		$page = Context::get('page');
		if(!$logged_info) {
			return new Object(-1, 'msg_invalid_request');
		}

		$member_srl = $logged_info->member_srl;

		Context::set('search_target','member_srl');
		Context::set('search_keyword',$member_srl);

		$oModuleModel = getModel('module');
		$oDocumentAdminView = getAdminView('document');
		$oDocumentAdminView->dispDocumentAdminList();

		$document_list = Context::get('document_list');
		foreach($document_list as &$val) {
			$module_srl = $val->get('module_srl');
			$oModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if($oModuleInfo) {
				$val->browser_title = $oModuleInfo->browser_title;
			}
		}

		Context::set('document_list', $document_list);


		$oPointModel = getModel('point');
		$oModuleModel = getModel('module');

		$point_config = $oModuleModel->getModuleConfig('point');
		$point = $oPointModel->getPoint($member_srl);
		$level = $oPointModel->getLevel($point, $point_config->level_step);
		$level_max = count($point_config->level_step);

		$level_step = $point_config->level_step[$level];
		$next_level_step = $point_config->level_step[$level+1 > $level_max ? $level_max : $level+1];
		$percent = $level+1 <= $level_max ? round(($point-$level_step)/($next_level_step-$level_step)*100, 0) : 100;

		Context::set('point', $point);
		Context::set('level', $level);
		Context::set('level_max', $level_max);
		Context::set('percent', $percent);


		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', 'member_write_document');
		$this->add('html', $tpl);
	}

	function getMoringWorldMemberWriteComment() {
		$logged_info = Context::get('logged_info');
		$page = Context::get('page');
		if(!$logged_info) {
			return new Object(-1, 'msg_invalid_request');
		}

		$member_srl = $logged_info->member_srl;

		Context::set('search_target','member_srl');
		Context::set('search_keyword',$member_srl);

		$oModuleModel = getModel('module');
		$oCommentAdminView = getAdminView('comment');
		$oCommentAdminView->dispCommentAdminList();

		$comment_list = Context::get('comment_list');
		foreach($comment_list as &$val) {
			$module_srl = $val->get('module_srl');
			$oModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if($oModuleInfo) {
				$val->browser_title = $oModuleInfo->browser_title;
				$val->mid = $oModuleInfo->mid;
			}
		}

		Context::set('comment_list', $comment_list);

		$oPointModel = getModel('point');
		$oModuleModel = getModel('module');

		$point_config = $oModuleModel->getModuleConfig('point');
		$point = $oPointModel->getPoint($member_srl);
		$level = $oPointModel->getLevel($point, $point_config->level_step);
		$level_max = count($point_config->level_step);

		$level_step = $point_config->level_step[$level];
		$next_level_step = $point_config->level_step[$level+1 > $level_max ? $level_max : $level+1];
		$percent = $level+1 <= $level_max ? round(($point-$level_step)/($next_level_step-$level_step)*100, 0) : 100;

		Context::set('point', $point);
		Context::set('level', $level);
		Context::set('level_max', $level_max);
		Context::set('percent', $percent);

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', 'member_write_comment');
		$this->add('html', $tpl);
	}

	function _getModifyMemberInfoBefore() {
		$_SESSION['rechecked_password_step'] = 'INPUT_PASSWORD';

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', 'member_check_password');
		$this->add('html', $tpl);
	}

	function getMoringWorldModifyMemberInfo() {
		$logged_info = Context::get('logged_info');
		if(!$logged_info) {
			return new Object(-1, 'msg_invalid_request');
		}

		if($_SESSION['rechecked_password_step'] != 'VALIDATE_PASSWORD' && $_SESSION['rechecked_password_step'] != 'INPUT_DATA')
		{
			$this->_getModifyMemberInfoBefore();
			return;
		}

		$_SESSION['rechecked_password_step'] = 'INPUT_DATA';

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', 'member_change_info');
		$this->add('html', $tpl);
	}

	function getMoringWorldModifyMemberPassword() {
		$logged_info = Context::get('logged_info');
		if(!$logged_info) {
			return new Object(-1, 'msg_invalid_request');
		}
		$oMemberModel = getModel('member');
		$member_config = $oMemberModel->getMemberConfig();

		Context::set('member_config', $member_config);

		$oTemplate = TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl/layout', 'member_change_password');
		$this->add('html', $tpl);
	}

	function setMoringWorldCommentPage() {
		$document_srl = Context::get('document_srl');
		$cpage = Context::get('cpage');
		$comment_srl = Context::get('comment_srl');
		if(!($document_srl && !$cpage && $comment_srl)){
			return false;
		}

		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($document_srl);
		if(!$oDocument->isExists()){
			return false;
		}

		$comment_list = $oDocument->getComments();
		$cpage = $oDocument->comment_page_navigation->cur_page;
		$comment_page = 1;
		if($comment_list){
			if(array_key_exists($comment_srl, $comment_list)){
				$comment_page = Context::get('cpage');
			} else {
				if($cpage > 1)	{
					$count = 0;
					while(++$count <= $cpage){
						Context::set($document_srl.'_cpage', $count);
						if(array_key_exists($comment_srl, $oDocument->getComments())) {
							$comment_page = $count;
							break;
						}
					}
				}

			}
		}
		Context::set('comment_srl', '');
		Context::set('cpage_detect', '');
		Context::set('cpage', $comment_page);
	}

	function getMoringWorldCommentGrant(){
		$mid = Context::get('mid');
		$comment_srl = Context::get('comment_srl');
		$logged_info = Context::get('logged_info');

		if(!$comment_srl){
			return new Object(-1, "msg_invalid_request");
		}
		
		$is_manager = $logged_info ? $this->checkIsBoardAdmin($mid) : false;
		if($comment_srl){
			$oCommentModel = getModel('comment');
			$oComment = $oCommentModel->getComment($comment_srl, $is_manager);
		}

		if(!$oComment->isExists())	{
			return new Object(-1, "msg_invalid_request");
		}

		$this->add("grant", !$oComment->isGranted() ? 0 : 1);
	}


	function checkIsBoardAdmin($mid = false){
		$mid = $mid ? $mid : Context::get('mid');
		if(!$mid){
			return false;
		}

		$logged_info = Context::get('logged_info');
		if(!$logged_info){
			return false;
		}

		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoByMid($mid);
		$admin_member = $oModuleModel->getAdminId($module_info->module_srl);
		$is_module_admin = false;

		if($logged_info->is_admin == 'Y'){
			$is_module_admin = true;
		} else {

			if(!empty($admin_member)){
				foreach($admin_member as $value){
					if($value->member_srl === $logged_info->member_srl){
						$is_module_admin = true;
						break;
					}
				}
			}
			if(!$is_module_admin) {
				$getGrant = $this->_getBoardAdminGroup($module_info);
				$member_group_list = $logged_info->group_list;
				foreach($getGrant as $value){
					if(isset($member_group_list[$value])){
						$is_module_admin = true;
					}
				}
			}

		}

		return $is_module_admin;
	}

	function _getBoardAdminGroup($module_info){

		if(!$module_info){
			$mid = Context::get('mid');
			$oModuleModel = getModel('module');
			$module_info = $oModuleModel->getModuleInfoByMid($mid);
		}

		$args = new stdClass();
		$args->module_srl = $module_info->module_srl;
		$output = executeQueryArray('module.getModuleGrants', $args);

		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups($module_info->site_srl);

		$adminGroup_array = array();

		foreach($output->data as $manager_group){
			if($manager_group->name === "manager"){
				foreach($group_list as $val){
					if($val->group_srl === $manager_group->group_srl){
						array_push($adminGroup_array, $manager_group->group_srl);
					}
				}
			}
		}

		return $adminGroup_array;
	}


}

/* End of file moringworld.model.php */
/* Location: ./modules/moringworld/moringworld.model.php */
