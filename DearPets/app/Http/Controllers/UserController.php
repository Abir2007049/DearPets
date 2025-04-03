<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function search(Request $request)
{
    $query = $request->input('query');
    $users = User::where('name', 'LIKE', "%$query%")->get();

    return view('searchResults', compact('users', 'query'));
}

public function show($id)
{
    $user = User::with('posts')->findOrFail($id);
    return view('user-profile', compact('user'));
}

}
