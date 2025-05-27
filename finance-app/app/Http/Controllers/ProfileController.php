<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('profile_updated', 'Perfil atualizado com sucesso!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function showStock(Request $request): View
    {
        return view('profile.stock-api', [
            'user' => $request->user(),
        ]);
    }

    public function updateApiKey(Request $request)
    {
        $request->validate([
            'api_key' => ['required', 'string', 'min:10'],
        ]);

        $user = Auth::user();
        $user->api_key = Crypt::encryptString($request->input('api_key'));
        $user->save();

        return redirect()->back()->with('success', 'Chave de API atualizada com sucesso!');
    }

    public function updateFavoriteSymbols(Request $request)
    {
        $request->validate([
            'symbols' => ['nullable', 'array'],
            'symbols.*' => ['string', 'regex:/^[A-Z]+$/']
        ]);

        $user = Auth::user();
        $user->favorite_symbols = implode(',', $request->input('symbols', []));
        $user->save();

        return redirect()->back()->with('success', 'Ações favoritas atualizadas com sucesso!');
    }
}