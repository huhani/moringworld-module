<?php
/*! Copyright (C) 2017 MORING WORLD. All rights reserved. */
/**
 * @class  moringworld
 * @author Huhani (mmia268@gmail.com)
 * @brief  MORING WORLD module high class.
 */

class moringworld extends ModuleObject
{


	private $triggers = array(
		//array( 'member.deleteMember',			'moringworld',	'controller',	'triggerDeleteMember',					'after'	),
		//array( 'document.insertDocument',	'moringworld',	'controller',	'triggerBeforeInsertDocument',      'before'	),
		//array( 'document.updateDocument',	'moringworld',	'controller',	'triggerBeforeUpdateDocument',      'before'	),
		//array( 'document.deleteDocument',	'moringworld',	'controller',	'triggerBeforeDeleteDocument',      'before'	),
		//array( 'comment.insertComment',		'moringworld',	'controller',	'triggerBeforeInsertComment',       'before'	),
		//array( 'comment.updateComment',		'moringworld',	'controller',	'triggerBeforeUpdateComment',       'before'	),
		//array( 'comment.deleteComment',		'moringworld',	'controller',	'triggerBeforeDeleteComment',       'before'	),
		array( 'moduleHandler.init',			'moringworld',	'controller',	'triggerBeforeModuleInit',				'before'	),
		array( 'moduleObject.proc',			'moringworld',	'controller',	'triggerBeforeModuleProc',				'before'	),
	);


	function moduleInstall()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		foreach ($this->triggers as $trigger) {
			$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}

		return new Object();
	}




	function moduleUninstall()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		//트리거 삭제
		foreach ($this->triggers as $trigger)
		{
			$oModuleController->deleteTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}

		return new Object();

	}




	function checkUpdate()
	{
		$oModuleModel = getModel('module');
		foreach ($this->triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				return true;
			}
		}

		return false;
	}

	function moduleUpdate()
	{

		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		foreach ($this->triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		return new Object();
	}

}

/* End of file moringworld.class.php */
/* Location: ./modules/moringworld/moringworld.class.php */
