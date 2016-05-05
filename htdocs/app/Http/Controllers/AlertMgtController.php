<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\alerts;
use App\Librato;

class AlertMgtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data = array();
        $data['type'] = "create";
        return view ("alert_form", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->input('id') == ""){
            $alert = new alerts;
        }else{
            $alert = alerts::find($request->input('id'));
        }

        $alert->description = $request->input('description');
        $alert->criteria = $request->input('criteria');
        $alert->criteria_total = $request->input('criteria_total');
        $alert->es_host = $request->input('es_host');
        $alert->es_index = $request->input('es_index');
        $alert->es_type = $request->input('es_type');
        $alert->es_datetime_field = $request->input('es_datetime_field');
        $alert->minutes_back = $request->input('minutes_back');
        $alert->pct_of_total_threshold = $request->input('pct_of_total_threshold');
        $alert->number_of_hits = $request->input('number_of_hits');
        $alert->alert_email_sender = $request->input('alert_email_sender');
        $alert->alert_email_recipient = $request->input('alert_email_recipient');
        $alert->alert_type = $request->input('alert_type');
        $alert->consecutive_failures = $request->input('consecutive_failures');
        $alert->consecutive_failures_pct = $request->input('consecutive_failures_pct');
        $alert->alert_enabled_gt0 = $request->input('alert_enabled_gt0');
        $alert->alert_enabled_gt0_pct = $request->input('alert_enabled_gt0_pct');
        $alert->alert_enabled_e0 = $request->input('alert_enabled_e0');

        //only set alert states to false when a new alert is setup
        if($request->input('id') == ""){
            $alert->pct_alert_state = false;
            $alert->number_hit_alert_state = false;
            $alert->zero_hit_alert_state = false;
            $alert->es_config_error_state = false;
        }

        $alert->save();

        return redirect()->action('AlertController@home');
    }

    public function storeedit($id, Request $request){
        $this->store($request);
        return redirect()->action('AlertController@home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = array();
        $data['type'] = "edit";
       // $data['alert'] = alerts::find($id);
        $alert = alerts::find($id);
        if($alert == null){
            echo "something went wrong"; die;
        }
        $librato = Librato::find($alert->librato_id);
        return view ("alert_form", $data)->with('alert', $alert)->with('librato', $librato);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
