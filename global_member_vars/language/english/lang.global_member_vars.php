<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(

'early_global' => 
'Set early global_member_id and group variables<br><small>Note: visitors not logged in are usually assigned to the "guests" group, id 3.</small> ',

//----------------------------------------------
'early_logged_in' => 
'Set logged_in_member_id and logged_in_group_id as an early global',

//----------------------------------------------
'include_other' => 
'Include other user variables?',

//----------------------------------------------
'others' =>
'Others<br>
<small>
(extra, always set)<br>
• global_last_visit -> date(\'Y-m-d H:i\')<br>
• global_last_visit_e -> date(\'YmdHis\')

</small>',

//----------------------------------------------
'handy' =>
'The global "comment_edit_time_limit"<br>
<small>
Comments can be edited for 
{global_comment_edit_time_limit} minutes.
</small>',

//----------------------------------------------


);

/* End of file lang.global_member_vars.php */
/* Location: /system/expressionengine/third_party/global_member_vars/language/english/lang.global_member_vars.php */
