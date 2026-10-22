<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $role = $request->string('role')->trim()->toString();

        $users = User::query()
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($role, function ($query, $roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->string('name')->toString(),
            'username' => $request->string('username')->toString(),
            'role' => $request->string('role')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->string('name')->toString(),
            'username' => $request->string('username')->toString(),
            'role' => $request->string('role')->toString(),
            'email' => $request->string('email')->toString(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->string('password')->toString());
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws ValidationException
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        if ($user->isSuperadmin() && User::where('role', 'superadmin')->count() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'Tidak dapat menghapus satu-satunya akun Superadmin yang tersisa di sistem.',
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
