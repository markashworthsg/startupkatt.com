@php($title = 'Comics')

@extends('admin.layout')

@section('content')
    <div class="flex items-baseline justify-between mb-6">
        <h1 class="text-2xl font-bold">Comics</h1>
        <p class="text-sm text-black/60">{{ $comics->total() }} total</p>
    </div>

    @if ($comics->isEmpty())
        <p class="text-black/60">No comics yet. Drop art in <code>storage/app/comics/incoming/</code> and run
            <code>php artisan comics:import</code>.</p>
    @else
        <div class="overflow-x-auto border border-black/10 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-black/5 text-left">
                    <tr>
                        <th class="px-3 py-2 font-semibold">#</th>
                        <th class="px-3 py-2 font-semibold">Title</th>
                        <th class="px-3 py-2 font-semibold">Release date</th>
                        <th class="px-3 py-2 font-semibold">Status</th>
                        <th class="px-3 py-2 font-semibold text-right">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comics as $comic)
                        @php($isLive = ! $comic->published_at->isFuture())
                        <tr class="border-t border-black/10">
                            <td class="px-3 py-2 text-black/60">{{ $comic->number }}</td>
                            <td class="px-3 py-2 font-medium">{{ $comic->title }}</td>
                            <td class="px-3 py-2 tabular-nums">{{ $comic->published_at->toDateString() }}</td>
                            <td class="px-3 py-2">
                                @if ($isLive)
                                    <span class="text-green-700">Live</span>
                                @else
                                    <span class="text-black/50">Scheduled</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('admin.comics.edit', $comic) }}"
                                   class="font-medium text-[var(--color-katt-accent)] hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $comics->links() }}
        </div>
    @endif
@endsection
