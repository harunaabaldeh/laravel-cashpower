<?php

namespace App\Http\Controllers;

use App\Country;
use App\DataTables\UsersDataTable;
use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param UsersDataTable $usersDataTable
     * @return \Illuminate\View\View
     */
    public function index(UsersDataTable $usersDataTable)
    {
        return $usersDataTable->render('users.index');
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('users.create-agent');
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());

        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param \App\Http\Requests\UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAgentUser(UserRequest $request)
    {
        $userInput = $request->input();
        $country = Country::where('iso_3166_2',$userInput['iso_code'])->first();

        if (empty($country)){
            $request->session()->flash("error","Country Resolution Failure");
            return redirect()->back();
        }

        $msisdn = trim(str_replace("+","",$userInput['msisdn']['full']));
        $agentCredentials = Arr::except($userInput,["_token","dial_code","msisdn","password","password_confirmation"]);


        $agentCredentials['msisdn'] = $msisdn;
        $agentCredentials['country_id'] = $country->id;
        $agentCredentials['uuid'] = Uuid::uuid4()->toString();
        $agentCredentials['password'] = Hash::make($request->get('password'));

        Log::debug("[UserController][storeAgentUser]\t... credentials ",$agentCredentials);
        $user = \App\User::create($agentCredentials);

        Log::debug("[UserController][storeAgentUser]\t... done creating user");
//        self::upgradeToAgent($user);
        return redirect()->route('user.index')->withStatus(__('Agent successfully created.'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User  $user)
    {
        $hasPassword = $request->get('password');
        $user->update(
            $request->merge([
                'password' => Hash::make($request->get('password'))
                ])->except([$hasPassword ? '' : 'password'])
            );

        return redirect()->route('user.index')->withStatus(__('User successfully updated.'));
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User  $user)
    {
        $user->delete();

        return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
    }

    /**
     * change a user account type to admin
     * @param User $user
     * @return mixed
     */
    public function upgradeToAgent(User  $user){
        Log::info("[UserController][upgradeToAgent]\t properties: \t".$user->properties."\t",$user->toArray());
       $user->isAgentUser = true;
       $user->isAdminUser = false;
        $user->save();
        Log::info("[UserController][upgradeToAgent]\t properties: \t".$user->properties."\t",$user->toArray());
        return redirect()->route('users.index')->withStatus(__('User upgrade  completed Successfully.'));
    }

    /**
     * change a user account type to admin
     * @param User $user
     * @return mixed
     */
    public function upgradeToAdmin(User  $user){
        Log::info("[UserController][upgradeToAdmin]\t properties: \t".$user->properties."\t",$user->toArray());
       $user->isAgentUser = false;
       $user->isAdminUser = true;
        $user->save();
        Log::info("[UserController][upgradeToAdmin]\t properties: \t".$user->properties."\t",$user->toArray());
        return redirect()->route('users.index')->withStatus(__('User upgrade  completed Successfully.'));
    }


    /**
     * de-activate a user account
     * @param User $user
     * @return mixed
     */
    public function deActivateUser(User  $user){
        Log::info("[UserController][deActivateUser]\t properties: \t".$user->properties."\t",$user->toArray());
       $user->accountStatus = "de-activated";
        $user->save();
        Log::info("[UserController][deActivateUser]\t properties: \t".$user->properties."\t",$user->toArray());
        return redirect()->route('users.index')->withStatus(__('User Status updated Successfully.'));
    }

    /**
     * re-activate user account
     * @param User $user
     * @return mixed
     */
    public function reActivateUser(User  $user){
        Log::info("[UserController][reActivateUser]\t properties: \t".$user->properties."\t",$user->toArray());
       $user->accountStatus = "active";
       $user->save();
        Log::info("[UserController][reActivateUser]\t properties: \t".$user->properties."\t",$user->toArray());
        return redirect()->route('users.index')->withStatus(__('User Status updated Successfully.'));
    }
}
