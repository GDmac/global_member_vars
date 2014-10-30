
# Global Member Vars

## After EE 2.9

In EE 2.9+ there is a new parser included. The neccessity for this extension 
is somewhat diminished. the if logged_in_member_id conditional won't execute tags
anymore if the condition is not met. Test your situation first with the template debugger.

## Pre EE 2.9

I never understood why (if logged_in) is parsed so late in the template. If you have 
a channel:entries tag for members, and another for visitors, both get rendered in the template, 
and one of them is discarded when advance conditionals are parsed. (see your template debugger). 

This extension adds **logged_in_member_id** and **logged_in_group_id** to the global variables 
array so they are parsed early and can be used in simple conditionals. Try the following code 
with the extension disabled and enabled and see the difference in the template debugger output.

<pre><code>
&#123;if logged_in_member_id != "0"&#125;
  &#123;exp:channel:entries status="open|members_only"&#125;
  ...
  &#123;/exp:channel:entries&#125;
&#123;/if&#125;
&#123;if logged_in_member_id == "0"&#125;
  &#123;exp:channel:entries status="open"&#125;
  ...
  &#123;/exp:channel:entries&#125;
&#123;/if&#125;
</code></pre>

If you rely, for some reason, on logged_in_member_id being parsed late, 
this extension can also add **global_member_id** and **global_group_id** for convenience. 


