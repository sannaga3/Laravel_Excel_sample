<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    public function store(Request $request)
    {
        $validated = $this->validator($request);

        if ($validated->fails()) {
            return redirect()
                ->back()
                ->withErrors($validated);
        }

        $request->password = Hash::make($request->password);
        User::create($request->all());

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        return view('users.edit', compact('user'));
    }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $validated = $this->validator($request, $id);

        if ($validated->fails()) {
            return redirect()
                ->back()
                ->withErrors($validated);
        }

        $user->fill($request->all())->save();

        return view('users.show',  compact('user'));
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroy($id)
    {
        User::find($id)->delete();

        return redirect()->route('users.index');
    }

    public function validator($request, $id = null)
    {
        if (isset($id)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:20',
                // その他省略
                'email' => 'required|string|max:50|unique:users,email,' . $id . ',id' // idを無視することで、emailがユニークの条件が外れる
                // 'email' => ['required', 'string', 'max:50', 'email', Rule::unique('users')->ignore($user->id)]   メモ
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:20',
                // その他省略
                'email' => 'required|string|email|unique:users|max:50',
                'password' => 'required|string:min6|confirmed',
            ]);
        }

        return $validator;
    }

    public function export()
    {
        // new UsersExport では headings -> collection の順番で呼び出す
        return Excel::download(new UsersExport, 'users.xlsx'); // ダウンロード先を指定しなければデフォルトはPCのダウンロード
    }
}