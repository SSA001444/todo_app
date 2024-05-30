<h1>Confirm Your New Email Address</h1>
<p>Hello {{ Crypt::decryptString($user->username) }},</p>
<p>To confirm your new email address, please click the link below:</p>
<a href="{{ route('profile.verify.new.email', ['token' => $token]) }}">Verify New Email Address</a>
<p>If you did not request this change, please ignore this email.</p>
