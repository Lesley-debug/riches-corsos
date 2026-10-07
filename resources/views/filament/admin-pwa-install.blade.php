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
            if (!this.deferredPrompt) {
                return;
            }

            this.deferredPrompt.prompt();

            const { outcome } = await this.deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                this.canInstall = false;
            }

            this.deferredPrompt = null;
        }
    }"
    x-show="canInstall"
    x-cloak
>
    <button
        type="button"
        x-on:click="install()"
        class="fi-btn fi-btn-size-md fi-btn-color-primary"
    >
        <x-heroicon-o-arrow-down-tray class="fi-btn-icon" />

        <span>
            Install Admin App
        </span>
    </button>
</div>
