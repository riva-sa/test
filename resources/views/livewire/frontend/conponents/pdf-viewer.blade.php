@push('styles')
    <style scoped>
        .modal-open {
            overflow: hidden;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
@endpush
<div>

    {{-- Modal --}}
    <div class="modal fade @if($showModal) show @endif"
         style="display: @if($showModal) block @else none @endif"
         tabindex="-1"
         role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <button type="button" class="btn-close me-auto ms-0 p-0" wire:click="closeModal"></button>
                    <h5 class="modal-title">عرض ملف المشروع</h5>
                </div>
                {{-- Modal Body with PDF Viewer --}}
<div class="modal-body p-0">
    @if($showModal && $pdfUrl)
        <div id="pdf-container" style="width: 100%; height: 600px; overflow: auto;">
            <iframe
                src="{{ $pdfUrl }}#toolbar=0&view=FitH"
                width="100%"
                height="100%"
                frameborder="0"
                style="overflow: hidden; width: 100%; height: 100%;"
                allowfullscreen
            ></iframe>
        </div>
    @endif
</div>
                <div class="modal-footer py-2 px-3">
                    <a href="{{ $pdfUrl }}" class="btn btn-primary" download>
                        <i class="uil uil-download-alt me-1"></i> تحميل الملف
                    </a>
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Backdrop --}}
    @if($showModal)
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to adjust iframe for mobile
        function adjustPdfDisplay() {
            const container = document.getElementById('pdf-container');
            if (container) {
                // Set height based on viewport for better mobile experience
                const viewportHeight = window.innerHeight;
                container.style.height = (viewportHeight * 0.7) + 'px';
            }
        }

        // Run on load and resize
        window.addEventListener('resize', adjustPdfDisplay);
        adjustPdfDisplay();

        // Make sure this runs when Livewire updates the DOM
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', function() {
                adjustPdfDisplay();
            });
        });
    });
</script>
@endpush