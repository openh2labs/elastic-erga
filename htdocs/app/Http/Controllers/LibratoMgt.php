<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\alerts;
use App\Librato;

class LibratoMgt extends Controller
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
    public function create($id)
    {
        $alert = alerts::find($id);
        $data = array();
        $data['type'] = 'create';

        return view('librato_form', $data)->with('alert', $alert);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $alert = alerts::find($id);

        if ($request->input('id') == '') {
            //  $alert = new alerts;
            $librato = new Librato();
        } else {
            //  $alert = alerts::find($request->input('id'));
            $librato = Librato::find($alert->librato_id);
        }

        $librato->uri = $request->input('uri');
        $librato->username = $request->input('username');
        $librato->api_key = $request->input('api_key');
        $librato->gauge_ok = $request->input('gauge_ok');
        $librato->gauge_alert = $request->input('gauge_alert');
        $librato->source = $request->input('source');

        $librato->save();

        //update the librato_id
        $alert->librato_id = $librato->id;
        $alert->save();

        //return to the alert view
        return redirect()->action('AlertMgtController@edit', $id);
        //return redirect()->route('alert/edit/{id}', $id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = array();
        $data['type'] = 'edit';
        // $data['alert'] = alerts::find($id);
        $alert = alerts::find($id);
        if ($alert == null) {
            echo 'something went wrong';
            die;
        }
        $librato = Librato::find($alert->librato_id);

        return view('librato_form', $data)->with('alert', $alert)->with('librato', $librato);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
