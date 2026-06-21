{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
    <title>{{ config('comics.site.name') }}</title>
    <link>{{ route('home') }}</link>
    <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
    <description>{{ config('comics.site.description') }}</description>
    <language>en</language>
    @if($comics->isNotEmpty())
    <lastBuildDate>{{ $comics->first()->published_at->toRfc822String() }}</lastBuildDate>
    @endif
    @foreach($comics as $comic)
    <item>
        <title>{{ $comic->title }}</title>
        <link>{{ $comic->url }}</link>
        <guid isPermaLink="true">{{ $comic->url }}</guid>
        <pubDate>{{ $comic->published_at->toRfc822String() }}</pubDate>
        <description><![CDATA[
            <p><img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}" /></p>
            @if($comic->caption)<p>{{ $comic->caption }}</p>@endif
            <p><a href="{{ $comic->url }}">Read on {{ config('comics.site.name') }}</a></p>
        ]]></description>
        <content:encoded><![CDATA[
            <p><img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}" /></p>
            @if($comic->caption)<p>{{ $comic->caption }}</p>@endif
            <p><a href="{{ $comic->url }}">Read on {{ config('comics.site.name') }}</a></p>
        ]]></content:encoded>
    </item>
    @endforeach
</channel>
</rss>
