<?php
/**
*
* Extension - Topic Descriptions
*
* @copyright (c) 2015 kinerity <http://www.acsyste.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace kinerity\topicdescriptions\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth						$auth			Authentication object
	* @param \phpbb\db\driver\driver_interface		$db				Database object
	* @param \phpbb\request\request					$request		Request object
	* @param \phpbb\template\template				$template		Template object
	* @param \phpbb\user							$user			User object
	* @access public
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'							=> 'permissions',
			'core.posting_modify_submit_post_before'	=> 'posting_modify_submit_post_before',
			'core.posting_modify_template_vars'			=> 'posting_modify_template_vars',

			'core.search_modify_tpl_ary'		=> 'modify_topicrow_tpl_ary',
			'core.submit_post_modify_sql_data'	=> 'submit_post_modify_sql_data',

			'core.user_setup'	=> 'user_setup',

			'core.viewforum_modify_topicrow'				=> 'modify_topicrow_tpl_ary',
			'core.viewtopic_assign_template_vars_before'	=> 'viewtopic_assign_template_vars_before',
		);
	}

	public function permissions($event)
	{
		$permissions = array_merge($event['permissions'], array(
			'f_topic_desc'	=> array('lang' => 'ACL_F_TOPIC_DESC', 'cat' => 'post'),
		));

		$event['permissions'] = $permissions;
	}

	public function posting_modify_submit_post_before($event)
	{
		$data = $event['data'];

		$data['topic_desc'] = $this->request->variable('topic_desc', '', true);

		$event['data'] = $data;
	}

	public function posting_modify_template_vars($event)
	{
		$post_data = $event['post_data'];
		$forum_id = $event['forum_id'];
		$page_data = $event['page_data'];

		$page_data['TOPIC_DESC'] = $post_data['topic_desc'];
		$page_data['S_TOPIC_DESC'] = $this->auth->acl_get('f_topic_desc', $forum_id) ? true : false;

		$event['page_data'] = $page_data;
	}

	public function submit_post_modify_sql_data($event)
	{
		$data = $event['data'];
		$sql_data = $event['sql_data'];

		$sql_data[TOPICS_TABLE]['sql']['topic_desc'] = $data['topic_desc'];

		$event['sql_data'] = $sql_data;
	}

	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'kinerity/topicdescriptions',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function modify_topicrow_tpl_ary($event)
	{
		$block = $event['topic_row'] ? 'topic_row' : 'tpl_ary';
		$event[$block] = $this->display_topic_desc($event['row'], $event[$block]);
	}

	public function viewtopic_assign_template_vars_before($event)
	{
		$topic_data = $event['topic_data'];

		$this->template->assign_vars(array(
			'TOPIC_DESC'	=> $topic_data['topic_desc'],
		));
	}

	private function display_topic_desc($row, $block)
	{
		$block = array_merge($block, array(
			'TOPIC_DESC'	=> $row['topic_desc'],
		));

		return $block;
	}
}
