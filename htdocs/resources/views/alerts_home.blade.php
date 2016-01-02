<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 02/10/15
 * Time: 21:49
 */

$a = 4;
$b = $_SERVER['HTTP_HOST'];
?>

@extends('master_layout')

@section('main')
    <h1>Alerts dashboard {{ $name }}</h1>
    object {{ $alerts }}
    <hr>
    {{
     url('', $parameters = [], $secure = null)
    }}
    more here and even more here
    <hr>
    {{

     (1+1+$a)
    }}

    {{ $b }}.

@endsection

@section('active_tab_alert_list') class="active"@endsection