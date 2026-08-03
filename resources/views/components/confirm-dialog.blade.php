<dialog data-confirm-dialog
    class="m-auto w-[calc(100%-2rem)] max-w-md overflow-visible border-0 bg-transparent p-0 text-navy">
    <div class="dialog-panel ui-surface overflow-hidden">
        <div class="p-5 sm:p-6">
            <div class="flex items-start gap-4">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-danger/10 text-danger"
                    aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.3 3.9 2.7 17.1A2 2 0 0 0 4.4 20h15.2a2 2 0 0 0 1.7-2.9L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-navy">Hapus tugas?</h2>
                    <p data-confirm-message class="mt-2 text-sm leading-6 text-secondary">
                        Tugas dan seluruh pengumpulan terkait akan dihapus permanen.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-border bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
            <button type="button" data-confirm-cancel class="ui-button ui-button-secondary">Batal</button>
            <button type="button" data-confirm-submit class="ui-button ui-button-danger">Hapus Tugas</button>
        </div>
    </div>
</dialog>
