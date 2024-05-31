<h1>Confirm Your Current Email Address</h1>
<p>Hello {{ Crypt::decryptString($user->username) }},</p>
<p>To confirm your current email address and proceed with changing to a new email address, please click the link below:</p>
<a href="{{ route('profile.verify.current.email', ['token' => $token]) }}">Verify Current Email Address</a>
<p>If you did not request this change, please ignore this email.</p>
