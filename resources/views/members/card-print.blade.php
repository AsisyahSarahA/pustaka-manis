@extends('layouts.print')

@section('title', 'Kartu Anggota - ' . $member->name)

@section('content')
    @include('members.partials.card-single', ['member' => $member])
@endsection
