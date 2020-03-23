@extends('layouts.app')

@section('content')
<div class="container">
  <contact-component :user="{{Auth::user()}}" csrf="{{csrf_token()}}"></contact-component>
</div>
@endsection
