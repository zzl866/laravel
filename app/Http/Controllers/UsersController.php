<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;


class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store']
        ]);
    }

    public function create()
    {
        return view('users.create');
    }
    /*
     * @User: Eloquent 模型 User
     * @$user: 会匹配路由片段中的 {user}
     * Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例
     */
    public function show(User $user)
    {
        /*
         *用户对象 $user 通过 compact 方法转化为一个关联数组，并作为第二个参数传递给 view 方法，将数据与视图进行绑定。
         */
        return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6',
        ]);
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request-> email,
            'password'=> bcrypt($request->password),
        ]);
        Auth::login($user);
        session()->flash('success','欢迎,来到laravel55');
        return redirect()->route('users.show',[$user]);

    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }
}
