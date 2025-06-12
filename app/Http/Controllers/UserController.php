<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        $exclude = $request->get('exclude', []);
        $users = User::where('name', 'like', "%{$query}%")
            ->when($exclude, function ($q) use ($exclude) {
                $q->whereNotIn('id', (array)$exclude);
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);
        return response()->json($users);
    }
}
