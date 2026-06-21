@php
    $site = config('comics.site');
    $title = $site['name'].' — '.$site['tagline'];
    $description = $site['description'];
@endphp

@extends('layouts.app')

@section('content')
<div class="text-center py-20">
    <h1 class="text-4xl font-extrabold mb-3" style="font-family: var(--font-display)">Startup Katt is loading…</h1>
    <p class="text-black/60 max-w-md mx-auto">
        No comics have been published yet. Drop some art in the incoming folder and run
        <code class="px-1 rounded bg-black/5">php artisan comics:import</code>.
    </p>
    <x-newsletter-signup class="mt-10 max-w-md mx-auto" />
</div>
@endsection
