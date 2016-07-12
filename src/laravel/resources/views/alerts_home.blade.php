<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/10/15
 * Time: 21:49
 */

$a = 4;
$b = $_SERVER['HTTP_HOST'];

//@todo check the updated_at timestamp and the page load and if greater than five minutes display a warning

?>

@extends('master_layout')

@section('main')
    <h1>Alerts dashboard : {{ $title }} </h1>
    <section class="hide-in-desktop hide-in-handheld">
        <h2>legend</h2>
        <dl class="clearfix">
            <dt><i class="fa fa-exclamation-triangle" aria-hidden="true" title="alert type"></i></dt>
            <dd>alert type</dd>
            <dt><i class="fa fa-percent" aria-hidden="true" title="greater than percentage"></i></dt>
            <dd>greater than percentage</dd>
            <dt><i class="fa fa-heartbeat" aria-hidden="true" title="greater than hit count"></i></dt>
            <dd>greater than hit count</dd>
            <dt><i class="fa fa-line-chart" aria-hidden="true" title="zero results"></i></dt>
            <dd>zero results</dd>
            <dt><i class="fa fa-cogs" aria-hidden="true" title="ES connection configuration"></i></dt>
            <dd>ES connection configuration</dd>
        </dl>
    </section>

    <div class="dashboard container-fluid">
        <div class="row head">
            <div class="col-xs-6 col-sm-4">
                <div class="row">
                    <div class="col-xs-3 center">id</div>
                    <div class="col-xs-9 left">description</div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-8">
                <div class="row">
                    <div class="col-xs-2 relative">
                        <span class="hide-in-mobile">alert type</span>
                        <span class="bc"><i class="fa fa-exclamation-triangle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="alert type"></i></span>
                    </div>
                    <div class="col-xs-2 col-md-3 relative">
                        <span class="hide-in-mobile">greater than percentage</span>
                        <span class="bc"><i class="fa fa-percent" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="greater than percentage"></i></span>
                    </div>
                    <div class="col-xs-2 relative">
                        <span class="hide-in-mobile">greater than hit count</span>
                        <span class="bc"><i class="fa fa-heartbeat" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="greater than hit count"></i></span>
                    </div>
                    <div class="col-xs-2 relative">
                        <span class="hide-in-mobile">zero results</span>
                        <span class="bc"><i class="fa fa-line-chart" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="zero results"></i></span>
                    </div>
                    <div class="col-xs-3 relative">
                        <span class="hide-in-mobile">ES connection configuration</span>
                        <span class="bc"><i class="fa fa-cogs" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="ES connection configuration"></i></span>
                    </div>
                </div>
            </div>
        </div>

        @foreach($alerts as $alert)
            <div class="row list">
                <div class="col-xs-6 col-sm-4">
                    <div class="row">
                        <div class="col-xs-3 center"><span class="badge number">{{ $alert->id }}</span></div>
                        <div class="col-xs-9 left">
                            @if ($alert->description === "")
                                <a href="/alert/edit/{{$alert->id }}">no description</a>
                            @else
                                <a href="/alert/edit/{{$alert->id }}">{{ $alert->description  }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-8">
                    <div class="row">
                        <div class="col-xs-2">
                            {{ $alert->alert_type }}
                        </div>
                        <div class="col-xs-2 col-md-3 relative">
                            @if($alert->alert_type === "gt0" )
                                @if($alert->pct_alert_state === 1)
                                    <span class="bc"><span class="badge red">{{ $alert->pct_of_total_threshold  }}%</span></span>
                                @else
                                    <span class="bc"><span class="badge green">{{ $alert->pct_of_total_threshold  }}%</span></span>
                                @endif
                            @else
                                <span class="bc"><i class="fa fa-circle" aria-hidden="true"></i></span>
                            @endif
                        </div>
                        <div class="col-xs-2 relative">
                            @if($alert->alert_type === "gt0" && $alert->number_of_hits > 0)
                                @if($alert->number_hit_alert_state === 1)
                                    <span class="bc"><span class="badge red">{{ $alert->number_of_hits  }}</span></span>
                                @else
                                    <span class="bc"><span class="badge green">{{ $alert->number_of_hits  }}</span></span>
                                @endif
                            @else
                                <span class="bc"><span class="badge grey">N/A</span></span>
                            @endif
                        </div>
                        <div class="col-xs-2 relative">
                            @if($alert->alert_type === "e0" )
                                @if($alert->zero_hit_alert_state === 1)
                                    <span class="bc"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                @else
                                    <span class="bc"><i class="fa fa-check-circle" aria-hidden="true"></i></span>
                                @endif

                            @else
                                N/A
                            @endif
                        </div>
                        <div class="col-xs-3 col-md-3 relative">
                            @if($alert->es_config_error_state === 1)
                                <span class="bc"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                            @else
                                <span class="bc"><i class="fa fa-check-circle" aria-hidden="true"></i></span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <hr>
    <ul>
        <li>
            alert type
            <ul>
            <li>gt0: greater than zero alert; check and alert if the document count exceeds a threshold (either as percentage, or absolute hit count)</li>
            <li>e0: equals zero alert; check and alert if the document count is zero</li>
            </ul>
        </li>
    </ul>



    {{
     url('', $parameters = [], $secure = null)
    }}


    <hr>


@endsection

@section('active_tab_alert_list') active @endsection