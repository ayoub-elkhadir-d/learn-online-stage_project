{{-- Single shared receipt viewer for the whole admin panel. Any "View
     Receipt" trigger elsewhere just needs:
       data-receipt-trigger data-receipt-url="..." data-receipt-type="image|pdf"
     — no page reload, no new tab. Included once in admin/layout.blade.php. --}}
<dialog id="receiptModal" data-admin-modal data-receipt-modal
        class="m-auto w-full max-w-3xl overflow-hidden rounded-2xl border border-(--color-border) bg-(--color-card) p-0 shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">

    <div class="flex items-center justify-between gap-3 border-b border-(--color-border) px-4 py-3 dark:border-white/10">
        <h3 class="flex items-center gap-2 text-sm font-bold text-(--color-text) dark:text-white">
            <x-icon name="image" class="h-4 w-4 text-(--color-primary)" />
            Payment Receipt
        </h3>
        <div class="flex items-center gap-1">
            <button type="button" data-receipt-zoom-out title="Zoom out"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                <x-icon name="minus" class="h-3.5 w-3.5" />
            </button>
            <span data-receipt-zoom-level class="w-10 text-center text-xs font-semibold tabular-nums text-(--color-text-secondary)">100%</span>
            <button type="button" data-receipt-zoom-in title="Zoom in"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                <x-icon name="plus" class="h-3.5 w-3.5" />
            </button>
            <button type="button" data-receipt-zoom-reset title="Fit to screen"
                    class="ml-1 hidden items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) sm:inline-flex dark:hover:bg-white/10 dark:hover:text-white">
                <x-icon name="maximize-2" class="h-3.5 w-3.5" />
                Fit
            </button>
            <a data-receipt-download href="#" download title="Download"
               class="ml-1 inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                <x-icon name="download" class="h-4 w-4" />
            </a>
            <button type="button" data-close-modal title="Close"
                    class="ml-1 inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-(--color-danger)/10 hover:text-(--color-danger)">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    </div>

    <div data-receipt-viewport class="relative flex h-[70vh] items-center justify-center overflow-auto bg-(--color-bg-light) dark:bg-black/30">
        <div data-receipt-skeleton class="absolute inset-0 flex items-center justify-center">
            <div class="skeleton h-2/3 w-2/3 rounded-xl"></div>
        </div>

        <img data-receipt-img alt="Payment receipt" class="hidden max-h-full max-w-full object-contain transition-transform duration-150 ease-out">

        <div data-receipt-pdf-fallback class="hidden flex-col items-center gap-3 p-8 text-center">
            <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                <x-icon name="file-text" class="h-8 w-8" />
            </span>
            <p class="text-sm text-(--color-text-secondary)">This receipt is a PDF file and can't be previewed here.</p>
            <a data-receipt-pdf-link href="#" target="_blank" class="btn-primary !py-2 text-sm">
                <x-icon name="download" class="h-4 w-4" />
                Open PDF
            </a>
        </div>
    </div>
</dialog>

@once
@push('scripts')
<script>
(function () {
    var dialog = document.getElementById('receiptModal');
    if (!dialog) return;

    var img = dialog.querySelector('[data-receipt-img]');
    var skeleton = dialog.querySelector('[data-receipt-skeleton]');
    var pdfFallback = dialog.querySelector('[data-receipt-pdf-fallback]');
    var pdfLink = dialog.querySelector('[data-receipt-pdf-link]');
    var downloadLink = dialog.querySelector('[data-receipt-download]');
    var zoomLevel = dialog.querySelector('[data-receipt-zoom-level]');
    var zoomInBtn = dialog.querySelector('[data-receipt-zoom-in]');
    var zoomOutBtn = dialog.querySelector('[data-receipt-zoom-out]');
    var zoomResetBtn = dialog.querySelector('[data-receipt-zoom-reset]');
    var scale = 1;

    function setScale(next) {
        scale = Math.min(3, Math.max(0.5, next));
        img.style.transform = 'scale(' + scale + ')';
        zoomLevel.textContent = Math.round(scale * 100) + '%';
    }

    function openReceipt(url, type) {
        scale = 1;
        img.style.transform = 'scale(1)';
        zoomLevel.textContent = '100%';
        downloadLink.href = url;

        if (type === 'pdf') {
            img.classList.add('hidden');
            skeleton.classList.add('hidden');
            pdfFallback.classList.remove('hidden');
            pdfFallback.classList.add('flex');
            pdfLink.href = url;
        } else {
            pdfFallback.classList.add('hidden');
            pdfFallback.classList.remove('flex');
            img.classList.add('hidden');
            skeleton.classList.remove('hidden');
            img.onload = function () {
                skeleton.classList.add('hidden');
                img.classList.remove('hidden');
            };
            img.src = url;
        }

        dialog.showModal();
    }

    document.querySelectorAll('[data-receipt-trigger]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openReceipt(btn.dataset.receiptUrl, btn.dataset.receiptType);
        });
    });

    zoomInBtn.addEventListener('click', function () { setScale(scale + 0.25); });
    zoomOutBtn.addEventListener('click', function () { setScale(scale - 0.25); });
    zoomResetBtn.addEventListener('click', function () { setScale(1); });

    dialog.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () { dialog.close(); });
    });

    // Click on the backdrop (the <dialog> element's own padding box, outside
    // the panel) closes it — native <dialog> has no built-in light-dismiss.
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) dialog.close();
    });
})();
</script>
@endpush
@endonce
