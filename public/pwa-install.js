class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.reminderInterval = null;
        this.reminderCount = 0;
        this.maxReminders = 3;
        this.reminderIntervalTime = 5 * 60 * 1000; // 5 minutes
        
        this.init();
    }

    init() {
        // Check if app is already installed
        this.checkInstallStatus();
        
        // Listen for the beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA install prompt available');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
            this.startReminderInterval();
        });

        // Listen for app installed event
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            this.isInstalled = true;
            this.hideInstallButton();
            this.stopReminderInterval();
            this.showInstalledMessage();
        });

        // Register service worker
        this.registerServiceWorker();
    }

    checkInstallStatus() {
        // Check if running in standalone mode (installed)
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.isInstalled = true;
            console.log('App is running in installed mode');
        }
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registered successfully:', registration);
            } catch (error) {
                console.log('Service Worker registration failed:', error);
            }
        }
    }

    showInstallButton() {
        if (this.isInstalled) return;

        let installButton = document.getElementById('pwa-install-btn');
        if (!installButton) {
            installButton = this.createInstallButton();
            document.body.appendChild(installButton);
        }
        installButton.style.display = 'block';
    }

    hideInstallButton() {
        const installButton = document.getElementById('pwa-install-btn');
        if (installButton) {
            installButton.style.display = 'none';
        }
    }

    createInstallButton() {
        const button = document.createElement('button');
        button.id = 'pwa-install-btn';
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Install App
        `;
        button.className = 'fixed bottom-4 right-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center text-sm font-medium transition-colors duration-200 z-50';
        button.style.display = 'none';
        
        button.addEventListener('click', () => this.installApp());
        
        return button;
    }

    async installApp() {
        if (!this.deferredPrompt) {
            console.log('No install prompt available');
            return;
        }

        try {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
                this.stopReminderInterval();
            } else {
                console.log('User dismissed the install prompt');
                this.startReminderInterval();
            }
            
            this.deferredPrompt = null;
        } catch (error) {
            console.error('Error during installation:', error);
        }
    }

    startReminderInterval() {
        if (this.isInstalled || this.reminderInterval) return;

        this.reminderInterval = setInterval(() => {
            if (this.reminderCount >= this.maxReminders) {
                this.stopReminderInterval();
                return;
            }

            this.showReminderNotification();
            this.reminderCount++;
        }, this.reminderIntervalTime);
    }

    stopReminderInterval() {
        if (this.reminderInterval) {
            clearInterval(this.reminderInterval);
            this.reminderInterval = null;
        }
    }

    showReminderNotification() {
        if (this.isInstalled) return;

        // Create a subtle reminder notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm';
        notification.innerHTML = `
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium">Install MenzaBita</p>
                    <p class="text-xs text-blue-600 mt-1">Get quick access and work offline</p>
                </div>
                <button class="ml-3 text-blue-400 hover:text-blue-600" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <button class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200" onclick="pwaInstaller.installApp(); this.parentElement.parentElement.remove();">
                Install Now
            </button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 10 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }

    showInstalledMessage() {
        const message = document.createElement('div');
        message.className = 'fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg z-50';
        message.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium">App installed successfully!</p>
            </div>
        `;

        document.body.appendChild(message);

        setTimeout(() => {
            if (message.parentElement) {
                message.remove();
            }
        }, 5000);
    }
}

// Initialize PWA installer when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pwaInstaller = new PWAInstaller();
});

// Also initialize if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.pwaInstaller = new PWAInstaller();
    });
} else {
    window.pwaInstaller = new PWAInstaller();
}