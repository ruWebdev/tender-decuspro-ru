<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminUsersController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('is_blocked')) {
            $query->where('is_blocked', $request->boolean('is_blocked'));
        }

        $users = $query
            ->select('id', 'name', 'email', 'role', 'locale', 'is_blocked', 'blocked_at', 'created_at')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $roles = [
            User::ROLE_CUSTOMER => 'Заказчик',
            User::ROLE_SUPPLIER => 'Поставщик',
            User::ROLE_ADMIN => 'Администратор',
        ];

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'is_blocked']),
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        $roles = [
            User::ROLE_CUSTOMER => 'Заказчик',
            User::ROLE_SUPPLIER => 'Поставщик',
            User::ROLE_ADMIN => 'Администратор',
        ];

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in([User::ROLE_CUSTOMER, User::ROLE_SUPPLIER, User::ROLE_ADMIN])],
            'locale' => ['nullable', 'string', 'in:ru,en,cn'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'locale' => $validated['locale'] ?? 'ru',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно создан');
    }

    public function edit(User $user): Response
    {
        $roles = [
            User::ROLE_CUSTOMER => 'Заказчик',
            User::ROLE_SUPPLIER => 'Поставщик',
            User::ROLE_ADMIN => 'Администратор',
        ];

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in([User::ROLE_CUSTOMER, User::ROLE_SUPPLIER, User::ROLE_ADMIN])],
            'locale' => ['nullable', 'string', 'in:ru,en,cn'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлен');
    }

    public function block(User $user): RedirectResponse
    {
        if ($user->isAdmin() && $user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Нельзя заблокировать самого себя');
        }

        $user->update([
            'is_blocked' => true,
            'blocked_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь заблокирован');
    }

    public function unblock(User $user): RedirectResponse
    {
        $user->update([
            'is_blocked' => false,
            'blocked_at' => null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь разблокирован');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin() && $user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Нельзя удалить самого себя');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь удален');
    }
}
