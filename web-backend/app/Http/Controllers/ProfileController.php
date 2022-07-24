<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Gate;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $idTypesList = array();
        $idTypes = explode(",",env('allowedIdTypes'));

        foreach ($idTypes as $idType){
            $idTypesList[$idType] = $idType;
        }
        return view('profile.edit',['idTypes' => $idTypesList]);
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $request->merge(['dateOfBirth' => Carbon::parse($request->input('dateOfBirth'))->format("Y-m-d")]);
        auth()->user()->update($request->all());


        return redirect()->route('home')->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus(__('Password successfully updated.'));
    }
}
