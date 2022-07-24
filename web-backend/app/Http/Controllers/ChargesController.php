<?php

namespace App\Http\Controllers;

use App\Charge;
use App\DataTables\ChargesDataTable;
use App\ServiceTypeCountryConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ChargesDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ChargesDataTable  $dataTable)
    {
        return  $dataTable->render('charges.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $services = ServiceTypeCountryConfiguration::distinct('service_name')->pluck('service_name','service_name')
            ->toArray();

        $services['Monthly Charges'] = 'Monthly Charges';
//        $countryIds = ServiceTypeCountryConfiguration::pluck('countries')->toArray();
//        $countryIds = Arr::flatten(array_unique(Arr::collapse($countryIds)));
        $countries = \Countries::pluck('name','iso_3166_3');

        return  view('charges.create',['countries' => $countries,'services' =>  $services]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            Log::info("[ChargesController][store]\t called ", $request->all());
            Charge::create($request->all());
            return redirect()->route('charges.index')->withStatus(__('System Charge Added Successfully'));
        } catch (\Exception $e) {
            Log::error("[ChargesController][store]\t Error: ".$e->getMessage());
            $request->session()->flash("error","Sorry Something Went Wrong. Please try later.");
            return redirect()->route('charges.index');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Charge  $charge
     * @return \Illuminate\Http\Response
     */
    public function show(Charge $charge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Charge  $charge
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Charge $charge)
    {
        $services = ServiceTypeCountryConfiguration::distinct('service_name')->pluck('service_name','service_name')
            ->toArray();


        $countryIds = ServiceTypeCountryConfiguration::pluck('countries')->toArray();
        $countryIds = Arr::flatten(array_unique(Arr::collapse($countryIds)));
        $countries = \Countries::whereIn('id',$countryIds)->pluck('name','iso_3166_3');

        return  view('charges.edit',['charge' => $charge,'countries' => $countries,'services' =>  $services]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Charge  $charge
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Charge $charge)
    {
        try {
            Log::info("[ChargesController][update]\t called ", $request->all());
            $update_data = $request->input();

            if (!(in_array('service_name',$update_data) && !empty($update_data['service_name']))){
                $update_data['service_name'] = $charge->service_name;
            }

            $charge->update($update_data);
            return redirect()->route('charges.index')->withStatus(__('System Charge Updated Successfully'));
        } catch (\Exception $e) {
            Log::error("[ChargesController][update]\t Error: ".$e->getMessage());
            $request->session()->flash("error","Sorry Something Went Wrong. Please try later.");
            return redirect()->route('charges.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Charge $charge
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Charge $charge, Request  $request)
    {
        try {
            Log::info("[ChargesController][destroy][".$charge->id."]\t called ", $request->all());
            $charge->delete();
            return redirect()->route('charges.index')->withStatus(__('System Charge removed Successfully'));
        } catch (\Exception $e) {
            Log::error("[ChargesController][destroy]\t Error: ".$e->getMessage());
            $request->session()->flash("error","Sorry Something Went Wrong. Please try later.");
            return redirect()->route('charges.index');
        }
    }

}
