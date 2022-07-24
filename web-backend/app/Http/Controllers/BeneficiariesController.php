<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Beneficiary;
use App\DataTables\BeneficiariesDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BeneficiariesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param BeneficiariesDataTable $beneficiariesDataTable
     * @return void
     */
    public function index(BeneficiariesDataTable $beneficiariesDataTable)
    {
        return $beneficiariesDataTable->render('beneficiaries.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $supportCountries = \Countries::whereIn('iso_3166_2',explode(',',env('supportedCountryList')))->pluck('name','id');

        return  view('beneficiaries.create',['countries' => $supportCountries]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Log::info("[BeneficiariesController][store]\t..",$request->toArray());
        try{
            $beneficiary = \App\Beneficiary::create(['account_type' => $request->input('service_type'),
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'nickname' => $request->input('nickname'),
                'othernames' => $request->input('othernames'),
                'country_id' => $request->input('country_id'),
                'user_id' => \Auth::user()->id,
            ]);

            if ($request->has('msisdn')){
                //TODO MNO resolution and account validation
                $beneficiary->update(['msisdn' => $request->input('msisdn')['full']]);
            }


            if ($beneficiary->account_type == "Bank")
            {
                //TODO do account validation
                $bank = \App\Bank::find($request->input('banks'));

                if (!empty($bank)){
                    $beneficiary->update(['account_number' => $request->input('account_number'), 'bank_name' => $bank->name, 'account_routing_number' => $bank->routing_code]);
                }
            }

            $request->session()->flash("success","Beneficiary Saved Successfully..");

        }catch (\Exception $exception){

            Log::error("[BeneficiariesController][store]\t..Error: ".$exception->getMessage());
            Log::error("[BeneficiariesController][store]\t..Error: ".$exception->getTraceAsString());

            $request->session()->flash("error","Sorry Something Went Wrong");

        }
        return  redirect()->route('beneficiaries.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Beneficiary $beneficiary
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Beneficiary $beneficiary, Request $request)
    {
        return  view('beneficiaries.show',['beneficiary' => $beneficiary, 'transactions' => $beneficiary->transactions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Beneficiary  $beneficiary
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Beneficiary $beneficiary)
    {

        $beneficiaryCountry = \Countries::find($beneficiary->country_id);
        $banks = Bank::where('country_code',$beneficiaryCountry->iso_3166_3)->pluck('name','id');
        $supportCountries = \Countries::whereIn('iso_3166_2',explode(',',env('supportedCountryList')))->pluck('name','id');

        if ($beneficiary->account_type == "Bank"){
            $beneficiaryBank = \App\Bank::where('routing_code',$beneficiary->account_routing_number)->first();
        }



        return view('beneficiaries.edit',['beneficiary' => $beneficiary,'countries' => $supportCountries, 'banks' => $banks, 'beneficiaryBank' => (!empty($beneficiaryBank) ? $beneficiaryBank : null)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Beneficiary  $beneficiary
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Beneficiary $beneficiary)
    {
        Log::info("[BeneficiariesController][update]\t..",$request->toArray());
        try{
            $beneficiary->update(['account_type' => $request->input('service_type'),
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'nickname' => $request->input('nickname'),
                'othernames' => $request->input('othernames'),
                'country_id' => $request->input('country_id'),
            ]);



            if ($beneficiary->account_type == "Bank")
            {
                //TODO do account validation
                $bank = \App\Bank::find($request->input('banks'));

                if (!empty($bank)){
                    $beneficiary->update(['bank_name' => $bank->name, 'account_routing_number' => $bank->routing_code]);
                }
            }

            if ($request->has('msisdn')){
                //TODO MNO resolution and account validation
                $beneficiary->update(['msisdn' => $request->input('msisdn')['full']]);
            }


            $beneficiary->save();
            $request->session()->flash("success","Beneficiary Updated Successfully..");

        }catch (\Exception $exception){

            Log::error("[BeneficiariesController][update]\t..Error: ".$exception->getMessage());
            Log::error("[BeneficiariesController][update]\t..Error: ".$exception->getTraceAsString());

            $request->session()->flash("error","Sorry Something Went Wrong");

        }
        return  redirect()->route('beneficiaries.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Beneficiary  $beneficiary
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Beneficiary $beneficiary,Request $request)
    {
        try {
            Log::info("[BeneficiariesController][destroy][".$beneficiary->id."]\t... called with ", $beneficiary->toArray());
            $beneficiary->delete();
            $request->session()->flash("success","Beneficiary Removed Successfully");


        }catch (\Exception $exception){
            $request->session()->flash("error","Sorry Something Went Wrong");
            Log::error("[BeneficiariesController][destroy][".$beneficiary->id."]\t... Error:  ".$exception->getMessage());
            Log::error("[BeneficiariesController][destroy][".$beneficiary->id."]\t... Error:  ".$exception->getTraceAsString());
        }
        return  redirect()->route('beneficiaries.index');
    }
}
