@php($title = 'Edit: '.$comic->title)

@extends('admin.layout')

@section('content')
    <div class="flex items-baseline justify-between mb-6">
        <h1 class="text-2xl font-bold">Edit comic #{{ $comic->number }}</h1>
        <a href="{{ route('admin.index') }}" class="text-sm font-medium text-black/60 hover:underline">&larr; Back</a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-8 md:grid-cols-[1fr_280px]">
        <form method="POST" action="{{ route('admin.comics.update', $comic) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-semibold mb-1">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $comic->title) }}"
                       class="w-full rounded-md border border-black/20 px-3 py-2" required>
            </div>

            <div>
                <label for="alt_text" class="block text-sm font-semibold mb-1">Alt text
                    <span class="font-normal text-black/50">(accessibility + SEO, required)</span>
                </label>
                <textarea id="alt_text" name="alt_text" rows="2"
                          class="w-full rounded-md border border-black/20 px-3 py-2" required>{{ old('alt_text', $comic->alt_text) }}</textarea>
            </div>

            <div>
                <label for="caption" class="block text-sm font-semibold mb-1">Caption / transcript
                    <span class="font-normal text-black/50">(shown under the strip)</span>
                </label>
                <textarea id="caption" name="caption" rows="4"
                          class="w-full rounded-md border border-black/20 px-3 py-2">{{ old('caption', $comic->caption) }}</textarea>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold mb-1">Meta description
                    <span class="font-normal text-black/50">(falls back to caption → alt text)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-md border border-black/20 px-3 py-2">{{ old('description', $comic->description) }}</textarea>
            </div>

            <div>
                <label for="published_at" class="block text-sm font-semibold mb-1">Release date
                    <span class="font-normal text-black/50">(one comic per day — stays unique)</span>
                </label>
                <input type="date" id="published_at" name="published_at"
                       value="{{ old('published_at', $comic->published_at->toDateString()) }}"
                       class="rounded-md border border-black/20 px-3 py-2" required>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-md bg-[var(--color-katt-accent)] px-5 py-2.5 font-semibold text-white hover:opacity-90">
                    Save changes
                </button>
                @if (! $comic->published_at->isFuture())
                    <a href="{{ $comic->url }}" class="text-sm font-medium text-black/60 hover:underline">View live →</a>
                @endif
            </div>
        </form>

        <aside class="space-y-3">
            <p class="text-sm font-semibold">Strip preview</p>
            <img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}"
                 class="w-full rounded-lg border border-black/10">
            <dl class="text-xs text-black/60 space-y-1">
                <div><dt class="inline font-semibold">Slug:</dt> <dd class="inline">{{ $comic->slug }}</dd></div>
                <div><dt class="inline font-semibold">File:</dt> <dd class="inline">{{ $comic->original_filename }}</dd></div>
            </dl>
        </aside>
    </div>
@endsection
