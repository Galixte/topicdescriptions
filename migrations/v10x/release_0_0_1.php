<?php
/**
*
* Extension - Topic Descriptions
*
* @copyright (c) 2015 kinerity <http://www.acsyste.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace kinerity\topicdescriptions\migrations\v10x;

class release_0_0_1 extends \phpbb\db\migration\migration
{
	/**
	* Add or update database schema
	*/
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'topics'	=> array(
					'topic_desc'	=> array('VCHAR_UNI', ''),
				),
			),
		);
	}

	/**
	* Add or update data in the database
	*/
	public function update_data()
	{
		return array(
			// Add permissions
			array('permission.add', array('f_topic_desc', false)),

			// Set permissions
			array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_topic_desc')),
		);
	}

	/**
	* Drop database schema
	*/
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'topics'	=> array(
					'topic_desc',
				),
			),
		);
	}
}
