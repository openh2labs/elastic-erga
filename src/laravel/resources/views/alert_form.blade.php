<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 08/01/16
 * Time: 08:59
 */

//var_dump($alert);die;
?>

@extends('master_layout')

@section('main')


    @if ($type === "create")
        <h1>create a new alert</h1>
        {!!   Form::open(array('url' => 'alert/store')) !!}
    @endif
    @if ($type === "edit")
        <h1>edit alert : {{ $alert->description }}</h1>
        {!!  Form::model($alert, array('url' => array('alert/storeedit', $alert->id))) !!}
        {!! Form::hidden('id', $alert->id) !!}
    @endif

    <div class="row description">
        <div class="col-xs-12">
            <div class="round">{!! Form::input('text', 'description', null, ['size' => '75', 'placeholder' => 'enter alert description'])  !!}</div>
        </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#elastic-search" aria-controls="elastic-search" role="tab"
                                                  data-toggle="tab">Elastic search query setup</a></li>
        <li role="presentation"><a href="#alerts" aria-controls="alerts" role="tab" data-toggle="tab">Alert setup</a>
        </li>
        <li role="presentation"><a href="#misc" aria-controls="misc" role="tab" data-toggle="tab">Misc</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="elastic-search">
            {{-- elastic search setup --}}
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-12">{!! Form::label('criteria', 'alert query (json)') !!}</div>
                        <div class="col-xs-12">{!! Form::textarea('criteria', null, ['cols'=>75, 'rows'=>20]) !!}</div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-12">{!! Form::label('criteria_total', 'total query (json)') !!}</div>
                        <div class="col-xs-12">{!! Form::textarea('criteria_total', null, ['cols'=>75, 'rows'=>20]) !!}</div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div id="prefetch" class="row">
                        <div class="col-xs-12 col-md-4">{!! Form::label('es_host', 'host') !!}</div>
                        <div class="col-xs-12 col-md-8">{!! Form::input('text', 'es_host', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}</div>
                    </div>
                    <div id="prefetch_indexes" class="row">
                        <div class="col-xs-12 col-md-4">{!! Form::label('es_index', 'index') !!}</div>
                        <div class="col-xs-12 col-md-8">{!! Form::input('es_index', 'es_index', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
                            <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="supported wildcards are %Y% (year in YYYY format), %m% (month 01 to 12) and %d% (day 01 to 31)"></i>
                        </div>
                    </div>
                    <div id="prefetch_types" class="row">
                        <div class="col-xs-12 col-md-4">{!! Form::label('es_index', 'type') !!}</div>
                        <div class="col-xs-12 col-md-8">{!! Form::input('es_index', 'es_type', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="alerts">
            <div id="prefetch_alert_email_sender" class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('alert_email_sender', 'alert email sender') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('alert_email_sender', 'alert_email_sender', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
                    (if smtp make sure its a permitted sender)
                </div>
            </div>
            <div id="prefetch_alert_email_recipient" class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('alert_email_recipient', 'alert email recipient') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('alert_email_recipient', 'alert_email_recipient', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}</div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('alert_type', 'alert type') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('alert_type', 'alert_type', null, ['size' => '50'])  !!}
                    e0
                    or gt0
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('consecutive_failures', 'consecutive failures') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('consecutive_failures', 'consecutive_failures', null, ['size'=>'5']) !!}
                    <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="number of consecutive failures before hit alerts are sent"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('consecutive_failures_pct', 'consecutive failures pct') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('consecutive_failures_pct', 'consecutive_failures_pct', null, ['size'=>'5']) !!}
                    <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="number of consecutive failures before pct alerts are sent"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('alert_enabled_gt0', 'alert enabled gt0') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('alert_enabled_gt0', 'alert_enabled_gt0', null, ['size'=>'5']) !!}
                    <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="hits greater than zero alerts (1=enabled, 0=disabled)"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('alert_enabled_gt0_pct', 'alert enabled gt0 pct') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('alert_enabled_gt0_pct', 'alert_enabled_gt0_pct', null, ['size'=>'5']) !!}
                    <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="percentage greater than zero alerts (1=enabled, 0=disabled)"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('alert_enabled_e0', 'alert enabled e0') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('alert_enabled_e0', 'alert_enabled_e0', null, ['size'=>'5']) !!}
                    <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="zero hit alerts (1=enabled, 0=disabled)"></i>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="misc">
            <div id="prefetch_es_date_time_field" class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('es_datetime_field', 'datetime_field') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('es_datetime_field', 'es_datetime_field', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}</div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('minutes_back', 'minutes back') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('minutes_back', 'minutes_back', null, ['size' => '10'])  !!}
                    <i class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="How many minutes back you want to be checking"></i>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('pct_of_total_threshold', 'pct of total threshold') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('pct_of_total_threshold', 'pct_of_total_threshold', null, ['size' => '10'])  !!}
                    %
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4">{!! Form::label('number_of_hits', 'number of hits') !!}</div>
                <div class="col-xs-12 col-md-8">{!! Form::input('number_of_hits', 'number_of_hits', null, ['size' => '10'])  !!}</div>
            </div>
        </div>
    </div>

    <div class="row submit">
        <div class="col-xs-12">
    @if ($type === "create")
            {!! Form::submit('create new alert monitor!', ['class' => 'btn btn-primary btn-lg']) !!}
    @elseif ($type === "edit")
            {!! Form::submit('save changes') !!}
    @endif
    {!!  Form::close() !!}
        </div>
    </div>
    @if(isset($librato))
        @if($librato != null)
            <a href="/librato/edit/{!! $alert->id !!}">Edit Librato integration</a>
        @endif
    @else
        @if ($type === "edit")
            <a href="/librato/create/{!! $alert->id !!}">Create Librato integration</a>
        @endif
    @endif

    <meta type="js-module" name="alert-form"/>
@endsection

@section('active_tab_alert_list') active @endsection

@section('js')
    $(".nav-tabs a").tab();
    $('[data-toggle="tooltip"]').tooltip();
@endsection

