<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->input('keyword', ''));

        $users = User::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->input('role')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['user' => new User()]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create($this->payload($request));

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($this->payload($request, $user));

        return redirect()->route('users.index')->with('success', 'อัปเดตผู้ใช้เรียบร้อยแล้ว');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้',
            ]);
        }

        if ($user->isAdmin() && $this->adminCount() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'ต้องมีผู้ดูแลระบบอย่างน้อย 1 คน',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }

    private function payload(UserRequest $request, ?User $user = null): array
    {
        $validated = $request->validated();

        if (($validated['password'] ?? '') === '') {
            unset($validated['password']);
        }

        unset($validated['password_confirmation']);

        $newRole = $validated['role'] ?? $user?->role;

        if ($user && $user->isAdmin() && $newRole !== 'admin' && $this->adminCount() <= 1) {
            throw ValidationException::withMessages([
                'role' => 'ต้องมีผู้ดูแลระบบอย่างน้อย 1 คน',
            ]);
        }

        if ($user && auth()->id() === $user->id && $newRole !== 'admin') {
            throw ValidationException::withMessages([
                'role' => 'ไม่สามารถลดสิทธิ์บัญชีที่กำลังใช้งานอยู่ได้',
            ]);
        }

        return $validated;
    }

    private function adminCount(): int
    {
        return User::where('role', 'admin')->count();
    }
}
