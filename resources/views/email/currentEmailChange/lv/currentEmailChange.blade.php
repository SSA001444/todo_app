<h1>Apstipriniet savu pašreizējo e-pasta adresi</h1>
<p>Sveiki {{ Crypt::decryptString($user->username) }},</p>
<p>Lai apstiprinātu savu pašreizējo e-pasta adresi un turpinātu mainīt uz jaunu e-pasta adresi, lūdzu, noklikšķiniet uz zemāk esošās saites:</p>
<a href="{{ route('profile.verify.current.email', ['token' => $token]) }}">Apstiprināt pašreizējo e-pasta adresi</a>
<p>Ja neesat pieprasījis šo izmaiņu, lūdzu, ignorējiet šo e-pastu.</p>
