<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Global member vars Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		GDmac
 * @link		http://github.com/gdmac
 */

class Global_member_vars_ext {
	
	public $settings 		= array();
	public $description		= 'Make some member variables available as early parsed global variables';
	public $docs_url		= '';
	public $name			= 'Global member vars';
	public $settings_exist	= 'y';
	public $version			= '1.0';
	
	private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}
	
	// ----------------------------------------------------------------------
	
	/**
	 * Settings Form
	 *
	 * If you wish for ExpressionEngine to automatically create your settings
	 * page, work in this method.  If you wish to have fine-grained control
	 * over your form, use the settings_form() and save_settings() methods 
	 * instead, and delete this one.
	 *
	 * @see http://expressionengine.com/user_guide/development/extensions.html#settings
	 */
	public function settings()
	{
		$settings = array();

		$settings['early_global']   = array('c', array(
		  'member_id' => "global_member_id",
		  'group_id' => "global_group_id",
		  // defaults
		), array('member_id','group_id'));

	    $settings['early_logged_in'] = array('c', array(
		  'member_id' => "logged_in_member_id",
		  'group_id' => "logged_in_group_id",
		  // defaults
		), array('member_id','group_id'));

	    // for demo, include some others
	    $settings['include_other'] = array('c', array(
		  'include_other' => "Include other member variables",
		), array());
		
 		$settings['others'] = array('ms', array(
		  'username' => 'username',
		  'screen_name' => 'screen_name',
		  'email' => 'email',
		  // 'last_visit' => 'last_visit',
		  'access_cp' => 'access_cp',
		  // defaults
		), array());

		$settings['handy']   = array('c', array(
		  'comment_edit_time_limit' => "comment_edit_time_limit",
		), array('comment_edit_time_limit'));

		return $settings;
	}
	
	// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// fetch default settings
		$settings = $this->settings();
		
		// $value is the settings array ($type, $options, $defaults)
		foreach($settings as $key => $value)
		{
			$this->settings[$key] = $value[2];
		}
		
		$hooks = array(
			'sessions_end'   => 'sessions_end',
		);

		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class'		=> __CLASS__,
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize($this->settings),
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);

			$this->EE->db->insert('extensions', $data);			
		}
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * sessions_end
	 *
	 * @param 
	 * @return 
	 */
	public function sessions_end($SESS)
	{
		// only on the front-end
		if (REQ != 'PAGE') {
			return;
		}

		// set global_member_id and global_group_id
		// these will not collide with late parsed logged_in_member_id etc.		
		foreach ($this->settings['early_global'] as $v) {
			$this->EE->config->_global_vars['global_'.$v] = $SESS->userdata[$v];
		}
		
		// set logged_in_member_id and logged_in_group_id
		foreach ($this->settings['early_logged_in'] as $v) {
			$this->EE->config->_global_vars['logged_in_'.$v] = $SESS->userdata[$v];
		}

		// other user variables, like logged_in_screen_name etc.
		if ( ! empty($this->settings['include_other'])) {
			foreach ($this->settings['others'] as $k => $v) {
				$this->EE->config->_global_vars['logged_in_'.$v] = $SESS->userdata[$v];
			}
		}

		// other handy global variables, like comment_time_limit
		if ( ! empty($this->settings['handy'])) {		
			foreach ($this->settings['handy'] as $k => $v) {
				$this->EE->config->_global_vars['global_'.$v] = $this->EE->config->item('comment_edit_time_limit');
			}
		}
		
 		// for entries start_on= parameter :-)  format is YYYY-MM-DD HH:MM
		$this->EE->config->_global_vars['global_last_visit'] = date('Y-m-d H:i',$SESS->userdata['last_visit']);
		$this->EE->config->_global_vars['global_last_visit_e'] = date('YmdHis',$SESS->userdata['last_visit']);

		// Last_activity can be handy for idle check :-)
		// $this->EE->config->_global_vars['global_last_activity'] = date('Y-m-d H:i',$SESS->userdata['last_activity']);

		if ($SESS->userdata['member_id'] == 1) {
			// var_dump($this->settings['others']);
			// var_dump($SESS->userdata);
		}
		

	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.global_member_vars.php */
/* Location: /system/expressionengine/third_party/global_member_vars/ext.global_member_vars.php */