{{-- Must run inline, before first paint, to avoid a flash of the wrong theme.
     Kept out of the bundled JS deliberately. --}}
<script>
    (function () {
        var stored = localStorage.getItem('theme');
        var isDark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', isDark);
    })();
</script>
