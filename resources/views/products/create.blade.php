@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('products.store') }}">@include('products._form')</form></div></div>
@endsection
