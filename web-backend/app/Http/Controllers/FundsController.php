<?php

namespace App\Http\Controllers;

use App\DataTables\FundsDatatable;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FundsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param FundsDatatable $datatable
     * @return \Illuminate\Http\Response
     */
    public function index(FundsDatatable  $datatable)
    {
        return $datatable->render('funds.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function getFundingView(User  $user, Request  $request){
        $current_balance = $user->country->currency_code." ".$user->balance;
        return view('funds.create',['user' => $user, 'current_balance' => $current_balance]);
    }


    public function updateUserEValue(User  $user, Request  $request){
        try{
            $value = floatval($request->input('evalue'));
            UtilitiesController::creditUserWallet($user,$value,"system e-value credit");
            return redirect()->route('users.index')->withStatus(__('User Balance updated.'));
        }catch (\Exception $exception){
            Log::error("[FundsController][updateUserEValue]\t Error: ".$exception->getMessage());
            $request->session()->flash("error","Sorry Something Went Wrong. Please try later.");
            return redirect()->route('users.index');
        }
    }


}
