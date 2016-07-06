<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/01/16
 * Time: 22:10
 */
?>
@extends('master_layout')


@section('main')
    <h1>System log</h1>
    <table class="table table-striped table-hover table-condensed">
        <tr>
            <th>date</th>
            <th>description</th>
            <th class="text-center">duration</th>
            <th class="text-center">hits alerts</th>
            <th class="text-center">percentage alerts</th>
            <th class="text-center">zero hits alerts</th>
        </tr>
        @foreach($ae as $a)
            <tr>
                <td>
                    {{ $a->created_at }}
                </td>
                <td>
                    {{ $a->description }}
                </td>
                <td class="text-center">
                    {{ $a->duration }} secs
                </td>
                <td class="text-center">
                    {{ $a->total_alerts_absolute }}
                </td>
                <td class="text-center">
                    {{ $a->total_alerts_pct }}
                </td>
                <td class="text-center">
                    {{ $a->total_alerts_equal_zero }}
                </td>
            </tr>
        @endforeach
    </table>
@endsection

@section('active_tab_system_log') class="active"@endsection