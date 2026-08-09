<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    use \App\Http\Controllers\Concerns\HandlesLiveTables;

    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        if ($this->isLiveTable($request)) {
            return view('users.partials.results', compact('users'));
        }

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['password'] = $data['password'] ?? 'pustaka123';

        User::create($data);

        return redirect()->route('users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Pengguna berhasil ditambahkan.']);
    }

    public function edit(User $user): View
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateData($request, $user->id);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Data pengguna berhasil diperbarui.']);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('toast', ['type' => 'error', 'message' => 'Tidak dapat menghapus akun sendiri.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('users.index')
                ->with('toast', ['type' => 'error', 'message' => 'Minimal harus ada satu admin aktif.']);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Pengguna berhasil dihapus.']);
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $ignoreId],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,pustakawan,viewer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}