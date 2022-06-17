<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Imports\UsersImport;
use Exception;

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

    public function export(Request $request)
    {
        $date = Carbon::today('Asia/Tokyo')->format('Y-m-d_');
        if ($request->exportType === "excel")
            return Excel::download(new UsersExport, $date . 'users.xlsx'); // new UsersExport では headings -> collection の順番で呼び出す。ダウンロード先を指定しなければデフォルトはPCのダウンロード
        return Excel::download(new UsersExport, $date . 'users.csv');
    }

    public function importIndex()
    {
        return view('users.import_index');
    }

    public function importStore(Request $request)
    {
        try {
            $file = $request->file('csvFile');
            $params = $request->request->all();

            // 登録前の時点でのユーザーの数を取得。countが０から始まる為 +1
            $before_last_id = User::all()->last()->id;

            Excel::import(new UsersImport($params), $file);

            // 再度最後のユーザーidを取得し、新規登録したユーザーのみをviewに返す
            $after_last_id = User::all()->last()->id;
            $users = User::whereBetween('id', [$before_last_id + 1, $after_last_id])->get();

            return view('users.import_index', compact('users'));
        } catch (Exception $e) {
            $error = $e->getMessage();
            return view('users.import_index', compact('error'));
        }
    }
}