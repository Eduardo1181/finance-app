<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin) {
                abort(403, 'Acesso não autorizado');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $totalUsers = \App\Models\User::count();
        $users = User::query()
        ->when($request->name, fn($q) => $q->where('name', 'like', '%'.$request->name.'%'))
        ->when($request->email, fn($q) => $q->where('email', 'like', '%'.$request->email.'%'))
        ->paginate(10);

        return view('profile.admin', compact('users', 'search', 'totalUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->back()->with('success', 'Usuário criado com sucesso!');
    }

    public function update(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode alterar seu próprio status de admin.');
        }

        $request->validate([
            'is_admin' => ['required', 'boolean'],
        ]);

        $user->is_admin = $request->is_admin;
        $user->save();

        $msg = $user->is_admin 
            ? "Usuário “{$user->name}” promovido a administrador." 
            : "Usuário “{$user->name}” rebaixado de administrador.";

        return back()->with('success', $msg);
    }

    public function toggleBlock(User $user)
    {
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        return redirect()->back()->with('success', 'Status de acesso atualizado.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode excluir a si mesmo.');
        }

        $user->delete();
        return back()->with('success', 'Usuário excluído com sucesso.');
    }
}