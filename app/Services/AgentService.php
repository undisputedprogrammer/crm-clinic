<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class AgentService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = User::class;
    }

    public function processAndStore($request)
    {
        $details = $request->validate([
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|confirmed'
        ]);

        $agent = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        $agent->assignRole('agent');

        return ['success'=>true, 'message'=>'Agent Added'];
    }

    public function processAndUpdate($id, $request)
    {
        $details = $request->validate([
            'name'=>'required|string',
            'email'=>'required|email'
        ]);

        $agent = User::find($id);

        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->save();

        return ['success'=>true, 'message'=>'Agent details updated'];

    }

    public function changePassword($request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return ['success'=>true, 'message'=>'Password Updated'];
    }

}
