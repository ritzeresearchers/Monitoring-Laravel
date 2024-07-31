<p>{{ Config::get("config.appName") }} - New Lead Notification</p>
<br>
<p>You have got new lead. Please <a href='{{ Config::get("config.appBaseUrl") }}' target='_blank'>login</a> to view
    new leads.
</p>
<br>

<p>Thanks,</p>
<span>{{ Config::get("config.appName") }}</span>
