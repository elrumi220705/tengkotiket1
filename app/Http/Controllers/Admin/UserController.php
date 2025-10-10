<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /admin/users
    public function index(Request $request)
    {
        $search = $request->query('q');
        $role   = $request->query('role'); // 'admin' / 'pengguna'
        $from   = $request->query('from'); // YYYY-MM-DD
        $to     = $request->query('to');   // YYYY-MM-DD

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('id', $search);
                });
            })
            ->when($role, fn ($q) => $q->where('role', $role))
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.users.index', compact('users', 'search', 'role', 'from', 'to'));

    }

    // (opsional) Detail
    public function show(User $user)
    {
        return view('admin.pages.users.show', compact('user'));

    }

    // (opsional) Hapus
    public function destroy(User $user)
    {
        // proteksi jangan hapus diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return back()->with('success', 'Pengguna dihapus.');
    }
}
