<p>Invitation from {{ $name }}</p>
<br>
<p>{{ $name }} has invited you to collaborate on the {{ $businessName }} account.</p>
<p>You can accept this tis invitation, thru this <a href='{{ $registrationLink }}' target='_blank'>link</a>.</p>
<br>

<p>Thanks,</p>
<span>{{ Config::get("config.appName") }} </span>