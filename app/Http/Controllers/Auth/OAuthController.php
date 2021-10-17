<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\IdentityProvider;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function oauthCallback($provider)
    {

        try {
            // get socialite user info
            $socialite_user = Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')
                ->withErrors(['oauth_error' => $e->getMessage()]);
        }

        // if find user, use it. Else, create new user.
        $identity_provider = IdentityProvider::firstOrNew(
            ['id' => $socialite_user->getId(), 'name' => $provider]
        );
        $user = User::firstOrNew(['email' => $socialite_user->getEmail()]);

        // check user is already logined or not
        if (!empty($socialite_user->getEmail()) && $user->exists) {
            if ($user->identity_provider->name != $provider) {
                return redirect('login')->withErrors(['oauth_error' => 'This email address is already exists by other sns account.']);
            }
            return redirect('login')->withErrors(['oauth_error' => 'Cause Unexpected Error.']);
        } elseif ($identity_provider->exists) {
            if ($identity_provider->name != $provider) {
                return redirect('/login')->withErrors(['oauth_error' => 'This email address is already exists by other sns account.']);
            }
            $user = $identity_provider->user;
        } else {
            // set user info and identity provider
            $user->name = $socialite_user->getNickname() ?? $socialite_user->name;
            $identity_provider = new IdentityProvider([
                'id' => $socialite_user->getId(),
                'name' => $provider
            ]);
            // begin transaction
            DB::beginTransaction();
            try {
                // save user and identity provider info
                $user->save();
                $user->identity_provider()->save($identity_provider);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return redirect('login')->withErrors(['transaction_error' => $e->getMessage()]);
            }
        }

        // login
        Auth::login($user);

        // redirect to login
        return redirect(RouteServiceProvider::HOME);
    }
}
