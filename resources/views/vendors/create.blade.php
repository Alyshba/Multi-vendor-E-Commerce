@extends('layouts.app')

@section('title', 'Add Vendor')

@section('content')
<div class="card table-card">
    <div class="card-body">
        <form method="POST" action="{{ route('vendors.store') }}">
            @include('vendors._form')
        </form>
    </div>
</div>
@endsection
