@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="card table-card">
    <div class="card-body">
        <form method="POST" action="{{ route('vendors.update', $vendor) }}">
            @method('PUT')
            @include('vendors._form')
        </form>
    </div>
</div>
@endsection
