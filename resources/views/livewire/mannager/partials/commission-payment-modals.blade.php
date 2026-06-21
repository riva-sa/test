<!-- Payment modal: reference + receipt are both mandatory proof of payment. -->
@if ($showPayModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:key="pay-modal">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-1">تسجيل دفع العمولة</h3>
            <p class="text-sm text-gray-500 mb-4">إجراء مالي موثّق: أدخل رقم الحوالة وأرفق إيصال التحويل لإثبات صرف العمولة.</p>

            <label class="block text-xs font-semibold text-gray-600 mb-1">رقم الحوالة / المرجع <span class="text-red-500">*</span></label>
            <input type="text" wire:model="paymentReference"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                placeholder="مثال: TRX-928374">
            @error('paymentReference') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <label class="block text-xs font-semibold text-gray-600 mb-1 mt-4">إيصال التحويل (صورة أو PDF) <span class="text-red-500">*</span></label>
            <input type="file" wire:model="receipt" accept=".jpg,.jpeg,.png,.pdf"
                class="block w-full text-sm text-gray-600 file:ml-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            <div wire:loading wire:target="receipt" class="text-xs text-gray-400 mt-1">جارٍ رفع الإيصال...</div>
            @error('receipt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" wire:click="closePayModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">إلغاء</button>
                <button type="button" wire:click="confirmPayment" wire:loading.attr="disabled" wire:target="confirmPayment,receipt"
                    class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmPayment">تأكيد الدفع</span>
                    <span wire:loading wire:target="confirmPayment">جارٍ التسجيل...</span>
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Void modal: cancelling a commission requires a recorded reason. -->
@if ($showVoidModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:key="void-modal">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-1">إلغاء العمولة</h3>
            <p class="text-sm text-gray-500 mb-4">سيُلغى احتساب هذه العمولة ولن تظهر للوسيط. اذكر سبب الإلغاء (يُسجَّل في سجلّ النشاط).</p>

            <label class="block text-xs font-semibold text-gray-600 mb-1">سبب الإلغاء <span class="text-red-500">*</span></label>
            <textarea wire:model="voidReason" rows="3"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                placeholder="مثال: الصفقة أُلغيت من العميل / خطأ في تسجيل الطلب"></textarea>
            @error('voidReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" wire:click="closeVoidModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">تراجع</button>
                <button type="button" wire:click="confirmVoid" wire:loading.attr="disabled" wire:target="confirmVoid"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmVoid">تأكيد الإلغاء</span>
                    <span wire:loading wire:target="confirmVoid">جارٍ التنفيذ...</span>
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Reversal modal: most sensitive action — reason is mandatory and audited. -->
@if ($showReverseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:key="reverse-modal">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-red-700 mb-1">عكس دفعة العمولة</h3>
            <p class="text-sm text-gray-500 mb-4">سيُعاد تصنيف العمولة إلى "معتمدة" ويُسجَّل هذا العكس بشكل دائم في سجلّ التدقيق. اذكر السبب بوضوح.</p>

            <label class="block text-xs font-semibold text-gray-600 mb-1">سبب العكس <span class="text-red-500">*</span></label>
            <textarea wire:model="reversalReason" rows="3"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                placeholder="مثال: حُوّل المبلغ لحساب خاطئ وسيُعاد صرفه"></textarea>
            @error('reversalReason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" wire:click="closeReverseModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">إلغاء</button>
                <button type="button" wire:click="confirmReversal" wire:loading.attr="disabled" wire:target="confirmReversal"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="confirmReversal">تأكيد العكس</span>
                    <span wire:loading wire:target="confirmReversal">جارٍ التنفيذ...</span>
                </button>
            </div>
        </div>
    </div>
@endif
