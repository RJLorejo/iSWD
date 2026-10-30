@extends('layouts.guest')

@section('content')

<div class="min-h-screen flex flex-col">


{{-- Navbar --}}
@include('landing.components.landing.navbar')

{{-- Hero --}}
@include('landing.components.landing.hero')

{{-- About --}}
@include('landing.components.landing.about')

{{-- Features --}}
@include('landing.components.landing.features')

{{-- Complaint Workflow --}}
@include('landing.components.landing.workflow')

{{-- Water Application Information (Informational Only) --}}
@include('landing.components.landing.water-application')

{{-- Contact --}}
@include('landing.components.landing.contact')

{{-- Footer --}}
@include('landing.components.landing.footer')


</div>

@endsection
