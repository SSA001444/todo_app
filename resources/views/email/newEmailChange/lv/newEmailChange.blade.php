<h1>Apstipriniet savu jauno e-pasta adresi</h1>
<p>Sveiki {{ Crypt::decryptString($user->username) }},</p>
<p>Lai apstiprinātu savu jauno e-pasta adresi, lūdzu, noklikšķiniet uz zemāk esošās saites:</p>
<a href="{{ route('profile.verify.new.email', ['token' => $token]) }}">Apstiprināt jauno e-pasta adresi</a>
<p>Ja neesat pieprasījis šo izmaiņu, lūdzu, ignorējiet šo e-pastu.</p>
