<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with(['department', 'vendor']);
        $type = request('type');
        if ($type && in_array($type, ['admin', 'it', 'user', 'vendor'])) {
            $users->where('user_type', $type);
        }
        $users = $users->orderBy('id')->paginate(10)->withQueryString();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::all();
        $vendors = Vendor::all();
        return view('user.create', compact('departments', 'vendors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'user_type'    => 'required|in:admin,it,user,vendor',
            'department_id'=> 'nullable|exists:departments,id',
            'vendor_id'    => 'nullable|exists:vendors,id',
        ]);
        $data['password'] = bcrypt($data['password']);
        if ($data['user_type'] !== 'user') {
            $data['department_id'] = null;
        }
        if ($data['user_type'] !== 'vendor') {
            $data['vendor_id'] = null;
        }
        User::create($data);
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $departments = Department::all();
        $vendors = Vendor::all();
        return view('user.edit', compact('user', 'departments', 'vendors'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'password'     => 'nullable|string|min:6|confirmed',
            'user_type'    => 'required|in:admin,it,user,vendor',
            'department_id'=> 'nullable|exists:departments,id',
            'vendor_id'    => 'nullable|exists:vendors,id',
        ]);
        if ($data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        if ($data['user_type'] !== 'user') {
            $data['department_id'] = null;
        }
        if ($data['user_type'] !== 'vendor') {
            $data['vendor_id'] = null;
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        $exclude = $request->get('exclude', []);
        $userTypes = $request->get('user_types', []);
        $users = User::where('name', 'like', "%{$query}%")
            ->when($exclude, function ($q) use ($exclude) {
                $q->whereNotIn('id', (array)$exclude);
            })
            ->when($userTypes, function ($q) use ($userTypes) {
                $q->whereIn('user_type', $userTypes);
            })
            ->with(['department:id,name', 'vendor:id,name'])
            ->limit(10)
            ->get(['id', 'name', 'email', 'user_type', 'department_id', 'vendor_id']);
        return response()->json($users);
    }
}
