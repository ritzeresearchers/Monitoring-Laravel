<p>Hello,</p>
<br>
<p>You recently update your email to {{ $email }}. To verify this email
  address belongs to you, please enter the code below.
</p>
<p>
  <strong>{{ $verificationCode }}</strong>
</p>
<br>
<p>Thanks,</p>
<span>{{ Config::get("config.appName") }} </span>