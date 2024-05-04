<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class massegemailController extends Controller
{

    public function index()
    {
        return view('auth.verify');
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        if ($request->input('code') == $user->code) {
            $user->resetCode();
            return redirect()->route('dashboard');
        }

        return redirect()->back()->withErrors(['code' => 'Check Your code']);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
