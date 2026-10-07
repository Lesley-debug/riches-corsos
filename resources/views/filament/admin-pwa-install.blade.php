<div
    x-data="{
        deferredPrompt: null,
        canInstall: false,

        init() {
            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault();
                this.deferredPrompt = event;
                this.canInstall = true;
            });

            window.addEventListener('appinstalled', () => {
                this.deferredPrompt = null;
                this.canInstall = false;
            });
        },

        async install() {
            if (!this.deferredPrompt) return;
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            if (outcome === 'accepted') this.canInstall = false;
            this.deferredPrompt = null;
        }
    }"
    x-show="canInstall"
    x-cloak
    style="display:flex; justify-content:flex-end; padding: 0.5rem 1rem 0;"
>
    <button
        type="button"
        x-on:click="install()"
        style="display:inline-flex; align-items:center; gap:0.375rem; padding:0.375rem 0.75rem; font-size:0.75rem; font-weight:500; color:#fff; background:#2F6B4F; border:none; border-radius:0.375rem; cursor:pointer; white-space:nowrap;"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:0.875rem;height:0.875rem;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Install Admin App
    </button>
</div>
