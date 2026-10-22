@extends('errors.layout')

@section('title', 'Versi HTTP Tidak Didukung')
@section('code', '505')
@section('badge', '505 • HTTP Version Not Supported')
@section('message', 'Protokol versi HTTP yang dikirimkan oleh peramban Anda tidak didukung oleh server aplikasi.')
