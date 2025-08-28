@extends('admin.layouts.masterData') {{-- sesuaikan dengan layout mu --}}
@section('content')
<div class="container">
  <h4 class="mb-3">Tambah PlayStation</h4>
  <form action="{{ route('playstations.store') }}" method="post">
    @include('admin.pages.playstations._form')
  </form>
</div>
@endsection
