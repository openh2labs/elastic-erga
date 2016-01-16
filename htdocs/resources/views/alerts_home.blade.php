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
    <table class="table table-striped table-hover table-condensed">
        <tr>
            <th>id</th>
            <th>description</th>
            <th class="text-center">alert type</th>
            <th class="text-center">greater<br>than<br>percentage</th>
            <th class="text-center">greater<br>than hit<br>count</th>
            <th class="text-center">zero results</th>
            <th class="text-center">ES connection<br>configuration</th>
        </tr>
        @foreach($alerts as $alert)
        <tr>
            <td>{{ $alert->id }}</td>
            <td><a href="/alert/edit/{{$alert->id }}">{{ $alert->description  }}</a></td>
            <td class="text-center">{{ $alert->alert_type  }}</td>
            <td class="text-center">
                @if($alert->alert_type === "gt0" )
                    @if($alert->pct_alert_state === 1)
                        <span class="label label-danger"> {{ $alert->pct_of_total_threshold  }}%</span>
                    @else
                        <span class="label label-success">{{ $alert->pct_of_total_threshold }}%</span>
                    @endif
                @endif
            </td>
            <td class="text-center">
                @if($alert->alert_type === "gt0" )
                    @if($alert->number_hit_alert_state === 1)
                        <span class="label label-danger"> {{ $alert->number_of_hits  }}</span>
                    @else
                        <span class="label label-success">{{ $alert->number_of_hits }}</span>
                    @endif
                @endif
            </td>
            <td class="text-center">
                @if($alert->alert_type === "e0" )
                    @if($alert->zero_hit_alert_state === 1)
                        <span class="label label-danger">0 found</span>
                    @else
                        <span class="label label-success">OK</span>
                    @endif

                    @else
                    N/A
                @endif
            </td>
            <td class="text-center">
                    @if($alert->es_config_error_state === 1)
                        <span class="label label-danger">error</span>
                    @else
                        <span class="label label-success">OK</span>
                    @endif
            </td>
        </tr>
        @endforeach
    </table>



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
    {{

     (1+1+$a)
    }}

    {{ $b }}.

@endsection

@section('active_tab_alert_list') active @endsection