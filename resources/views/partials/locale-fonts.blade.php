{{-- Shared font <link> tags for every layout. Cairo is only ever used when
     html[lang="ar"] (see resources/css/app.css), but the browser only
     fetches a font file once a glyph actually needs it, so including the
     link unconditionally costs nothing on EN/FR pages. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
