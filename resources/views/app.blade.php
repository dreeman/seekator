@extends('layouts.app')

@section('scripts')
    <script src="{{ asset('js/app.js') }}?{{ random_int(100000, 999999) }}" defer></script>
@endsection

@section('content')
<div id="app" data-userid="@guest{{ 0 }}@else{{ auth()->id() }}@endguest">
    <div class="container theme-showcase" role="main">
        <div class="row">
            <video-form @formsent="sendForm" @hidealert="hideAlert" :alert="alert"></video-form>
            <video-list :files="files"></video-list>
        </div>
    </div>
</div>
@endsection
