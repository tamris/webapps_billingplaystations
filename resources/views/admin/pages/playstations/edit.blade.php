@extends('admin.layouts.base')
@section('content')
<div class="container">
  <h4 class="mb-3">Edit PlayStation</h4>
  <form action="{{ route('playstations.update',$playstation) }}" method="post">
    @method('PUT')
    @include('admin.pages.playstations._form', ['playstation'=>$playstation])
  </form>
</div>
@endsection
