<p>Service provider {{ $first_name }} {{ $last_name }} registered</p>
<br>
<p>
    Service provider {{ $first_name }} {{ $last_name }}  registered <a href='{{ Config::get("config.appBaseUrl") . 'business/' . $businessId }}' target='_blank'>view profile</a>.
</p>
<br>

<p>Thanks,</p>
<span>{{ Config::get("config.appName") }}</span>