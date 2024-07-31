<p>{{ Config::get("config.appName") }} - Message Notification</p>
<br>
<p>Someone sent you a message. Please <a href='{{ Config::get("config.appBaseUrl") }}' target='_blank'>login</a> to view
  the message and reply.
</p>
<br>


<p>Thanks,</p>
<span>{{ Config::get("config.appName") }}</span>