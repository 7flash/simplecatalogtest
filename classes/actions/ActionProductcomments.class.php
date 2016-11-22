<?php
/**
 * Simplecatalog plugin
 *
 * @copyright Serge Pustovit (PSNet), 2008 - 2015
 * @author    Serge Pustovit (PSNet) <light.feel@gmail.com>
 *
 * @link      http://psnet.lookformp3.net
 * @link      http://livestreet.ru/profile/PSNet/
 * @link      https://catalog.livestreetcms.com/profile/PSNet/
 * @link      http://livestreetguide.com/developer/PSNet/
 */

/*
 *
 * Обработка комментариев продуктов
 *
 */

class PluginSimplecatalog_ActionProductcomments extends ActionPlugin {

	/*
	 * Сущность текущего пользователя
	 */
	private $oUserCurrent = null;


	public function Init() {
		$this->oUserCurrent = $this->User_GetUserCurrent();
		/*
		 * экшен работает только через аякс
		 */
		$this->Viewer_SetResponseAjax('json');
	}


	protected function RegisterEvent() {
		/*
		 * аякс обработчики обработки комментариев
		 */
		$this->AddEventPreg('#^ajax-add-comment$#', 'EventAjaxAddComment');
		$this->AddEventPreg('#^ajax-response-comment$#', 'EventAjaxResponseComment');
		$this->AddEventPreg('#^ajax-online-comments$#', 'EventAjaxOnlineComments');
	}


	/**
	 * Добавить комментарий
	 * 
	 * @return bool
	 */
	public function EventAjaxAddComment() {
		/*
		 * проверка авторизации
		 */
		if (!$this->oUserCurrent) {
			$this->Message_AddError($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
			return false;
		}

		/*
		 * проверить промодерированный продукт у активной схемы
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById(getRequestStr('cmt_target_id'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * включены ли комментарии
		 */
		if (!$oProduct->getCommentsEnabled()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Comments_Are_Disabled'), $this->Lang_Get('error'));
			return false;
		}

		/*
		 * стандартная обработка комментариев
		 */
		/*
		 * может пользователь публиковать комментарии
		 */
		if (!$this->ACL_CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_acl'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * нет ли ограничений по времени
		 */
		if (!$this->ACL_CanPostCommentTime($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_limit'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * проверить текст
		 */
		$sText = $this->Text_Parser(getRequestStr('comment_text'));
		if (!func_check($sText, 'text', 2, 10000)) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * является этот комментарий ответом
		 */
		$sParentId = (int) getRequest('reply');
		if (!func_check($sParentId, 'id')) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			return false;
		}
		$oCommentParent = null;
		if ($sParentId != 0) {
			/*
			 * существует ли такой комментарий
			 */
			if (!$oCommentParent = $this->Comment_GetCommentById($sParentId)) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			/*
			 * проверить чтобы ид продуктов родителя комментария и ответа совпадали
			 */
			if ($oCommentParent->getTargetId() != $oProduct->getId()) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
		} else {
			/*
			 * корневой комментарий
			 */
			$sParentId = null;
		}
		/*
		 * проверка на дубль комментария
		 */
		if ($this->Comment_GetCommentUnique($oProduct->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT, $this->oUserCurrent->getId(), $sParentId, md5($sText))) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * создать комментарий
		 */
		$oCommentNew = Engine::GetEntity('Comment');
		$oCommentNew->setTargetId($oProduct->getId());
		$oCommentNew->setTargetType(PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT);
		$oCommentNew->setUserId($this->oUserCurrent->getId());
		$oCommentNew->setText($sText);
		$oCommentNew->setDate(date("Y-m-d H:i:s"));
		$oCommentNew->setUserIp(func_getIp());
		$oCommentNew->setPid($sParentId);
		$oCommentNew->setTextHash(md5($sText));
		$oCommentNew->setPublish(1);
		/*
		 * добавить комментарий
		 */
		$this->Hook_Run('product_comment_add_before', array('oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oProduct' => $oProduct));
		if ($this->Comment_AddComment($oCommentNew)) {
			$this->Hook_Run('product_comment_add_after', array('oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oProduct' => $oProduct));
			$this->Viewer_AssignAjax('sCommentId', $oCommentNew->getId());

			/*
			 * добавить комментарий в "прямой эфир"
			 */
			$oScheme = $oProduct->getScheme();

			if ($oScheme->getShowOnlineComments() == PluginSimplecatalog_ModuleScheme::COMPONENT_ENABLED) {
				$oCommentOnline = Engine::GetEntity('Comment_CommentOnline');
				$oCommentOnline->setTargetId($oProduct->getId());
				/*
				 * для этой схемы
				 */
				$oCommentOnline->setTargetType($this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($oScheme->getSchemeUrl()));
				$oCommentOnline->setCommentId($oCommentNew->getId());
				$this->Comment_AddCommentOnline($oCommentOnline);
			}

			/*
			 * записать дату последнего комментария для пользователя
			 */
			$this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
			$this->User_Update($this->oUserCurrent);
			/*
			 * обновить количество комментариев в продукте
			 */
			$oProduct->setCommentCount($oProduct->getCommentCount() + 1);
			$oProduct->Save();


			/*
			 * уведомить о комментариях
			 */
			$aExcludeMail = array($this->oUserCurrent->getMail());
			/*
			 * если это ответ - послать уведомление
			 */
			if ($oCommentParent and $oCommentParent->getUserId() != $oProduct->getUserId() and $oCommentNew->getUserId() != $oCommentParent->getUserId()) {
				$oUserAuthorComment = $oCommentParent->getUser();
				$aExcludeMail[] = $oUserAuthorComment->getMail();
				/**
				 * Проверяем можно ли юзеру рассылать уведомление
				 */
				if ($oUserAuthorComment->getSettingsNoticeReplyComment()) {
					$this->Notify_Send($oUserAuthorComment, 'notify.comment_reply.tpl', $this->Lang_Get('notify_subject_comment_reply'), array(
						'oUserTo'      => $oUserAuthorComment,
						'oProduct'     => $oProduct,
						'oComment'     => $oCommentNew,
						'oUserComment' => $this->oUserCurrent,
					), __CLASS__);
				}
			}
			/*
			 * послать уведомление автору
			 */
			$this->Subscribe_Send(PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT . '_new_comment', $oProduct->getId(), 'notify.comment_new.tpl', $this->Lang_Get('notify_subject_comment_new'), array(
				'oProduct'     => $oProduct,
				'oComment'     => $oCommentNew,
				'oUserComment' => $this->oUserCurrent,
			), $aExcludeMail, __CLASS__);


			// add event to stream
			//$this->Stream_write ($oCommentNew->getUserId(), 'add_comment', $oCommentNew->getId(), $oTopic->getPublish() && $oTopic->getBlog()->getType()!='close');
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
		}
	}


	/**
	 * Получить обновленный список комментариев продукта после добавления комментария
	 * 
	 * @return bool
	 */
	public function EventAjaxResponseComment() {
		/*
		 * есть ли такой промодерированный продукт у активной схемы
		 */
		if (!$oProduct = $this->PluginSimplecatalog_Product_MyGetActiveSchemeModerationDoneProductById(getRequestStr('idTarget'))) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Product_Not_Found'), $this->Lang_Get('error'));
			return false;
		}
		/*
		 * включены ли комментарии
		 */
		if (!$oProduct->getCommentsEnabled()) {
			$this->Message_AddError($this->Lang_Get('plugin.simplecatalog.Errors.Comments_Are_Disabled'), $this->Lang_Get('error'));
			return false;
		}
		
		/*
		 * стандартный ответ
		 */

		$iIdCommentLast = getRequestStr('idCommentLast');
		$iSelfIdComment = getRequestStr('selfIdComment');
		$aComments = array();
		/*
		 * если нестед сет включен (пагинация) - вернуть только добавленный комментарий
		 */
		if (getRequest('bUsePaging') and $iSelfIdComment) {
			if ($oComment = $this->Comment_GetCommentById($iSelfIdComment) and
				$oComment->getTargetId() == $oProduct->getId() and
				$oComment->getTargetType() == PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT
			) {
				$oViewerLocal = $this->Viewer_GetLocalViewer();
				$oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);
				$oViewerLocal->Assign('bOneComment', true);

				$oViewerLocal->Assign('oComment', $oComment);
				$sText = $oViewerLocal->Fetch($this->Comment_GetTemplateCommentByTarget($oProduct->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT));
				$aCmt = array();
				$aCmt[] = array('html' => $sText, 'obj' => $oComment);
			} else {
				$aCmt = array();
			}
			$aReturn['comments'] = $aCmt;
			$aReturn['iMaxIdComment'] = $iSelfIdComment;
		} else {
			$aReturn = $this->Comment_GetCommentsNewByTargetId($oProduct->getId(), PluginSimplecatalog::COMMENT_TARGET_TYPE_PRODUCT, $iIdCommentLast);
		}
		$iMaxIdComment = $aReturn['iMaxIdComment'];

		$aCmts = $aReturn['comments'];
		if ($aCmts and is_array($aCmts)) {
			foreach($aCmts as $aCmt) {
				$aComments[] = array('html' => $aCmt['html'], 'idParent' => $aCmt['obj']->getPid(), 'id' => $aCmt['obj']->getId());
			}
		}

		$this->Viewer_AssignAjax('iMaxIdComment', $iMaxIdComment);
		$this->Viewer_AssignAjax('aComments', $aComments);
	}


	/**
	 * Получение онлайн комментариев для блока "прямой эфир"
	 */
	public function EventAjaxOnlineComments() {
		if (!$sPostfix = $this->GetParam(0) or !is_string($sPostfix)) {
			return false;
		}
		/*
		 * получить комментарии схемы
		 */
		if ($aComments = $this->Comment_GetCommentsOnline($this->Comment_GetSchemeOnlineCommentsTargetTypeByUrl($sPostfix), Config::Get('block.stream.row'))) {
			$oViewer = $this->Viewer_GetLocalViewer();
			$oViewer->Assign('aComments', $aComments);
			$sTextResult = $oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__) . 'blocks/block_stream_products.tpl');
			$this->Viewer_AssignAjax('sText', $sTextResult);
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('block_stream_comments_no'), $this->Lang_Get('attention'));
			return false;
		}
	}

}

?>