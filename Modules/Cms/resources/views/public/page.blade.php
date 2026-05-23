<!DOCTYPE html>
<html lang="{{ $site->default_locale ?? 'id' }}">
<head>
    <meta charset="utf-8">
    <title>{{ $page->meta_title ?? $page->title_id }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
</head>
<body>
    <header>
        <h1>{{ $page->title_id }}</h1>
    </header>
    <main>
        @foreach($page->blocks as $block)
            @include('cms::public.blocks.'.$block->block_type, ['block' => $block])
        @endforeach
    </main>
</body>
</html>
