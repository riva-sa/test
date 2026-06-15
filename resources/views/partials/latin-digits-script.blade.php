{{-- Forces inputs marked with [data-latin-digits] to use Latin (English) digits.
     Any Arabic-Indic or Persian digits the user types are converted on the fly,
     and the change is dispatched so Livewire picks up the normalised value. --}}
<script>
    (function () {
        const map = {
            '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9',
            '۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9'
        };

        function toLatin(value) {
            return value.replace(/[٠-٩۰-۹]/g, d => map[d] ?? d);
        }

        document.addEventListener('input', function (e) {
            const el = e.target;
            if (!el || !el.matches || !el.matches('[data-latin-digits]')) {
                return;
            }

            const converted = toLatin(el.value);
            if (converted !== el.value) {
                const start = el.selectionStart;
                el.value = converted;
                try { el.setSelectionRange(start, start); } catch (_) {}
                // Re-dispatch so Livewire's wire:model syncs the cleaned value.
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }, true);
    })();
</script>
