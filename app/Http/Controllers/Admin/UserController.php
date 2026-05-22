<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        // Filter by role
        if ($request->has('role') && $request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by status
        if ($request->has('status') && $request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $users = $query->paginate(15);

        return view('admin.users.index', ['users' => $users, 'roles' => UserRole::cases()]);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $subscriptions = $user->subscriptions()->latest()->paginate(10);
        $payments = $user->payments()->latest()->paginate(10);

        return view('admin.users.show', [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'payments' => $payments,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        return view('admin.users.create', ['roles' => UserRole::cases()]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $validated = $request->validated();

        $user = User::create($validated);

        return redirect()->route('admin.users.show', $user)->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('admin.users.edit', ['user' => $user, 'roles' => UserRole::cases()]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validated();

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
