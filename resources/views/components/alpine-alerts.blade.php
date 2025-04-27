<div x-data="{
    alerts: [],
    addAlert(data) {
        const type = data.type || 'success';

        // Remove all alerts of different types
        this.alerts = this.alerts.filter(alert => alert.type === type);

        const newAlert = {
            id: Date.now() + Math.random(),
            message: data.message,
            type: type
        };

        this.alerts.push(newAlert);

        // Auto-remove this specific alert after 30 seconds
        setTimeout(() => {
            this.removeAlert(newAlert.id);
        }, 30000);
    },
    removeAlert(id) {
        this.alerts = this.alerts.filter(alert => alert.id !== id);
    }
}" x-on:show-alert.window="addAlert($event.detail.data)" class="mt-2 space-y-2">
    <template x-for="alert in alerts" :key="alert.id">
        <div x-show="true" x-transition class="alert"
            :class="{
                'alert-success': alert.type === 'success',
                'alert-danger': alert.type === 'error',
                'alert-warning': alert.type === 'info',
                'alert-secondary': alert.type === 'notice'
            }">
            <strong x-text="alert.type.charAt(0).toUpperCase() + alert.type.slice(1) + '!'"></strong>
            <span x-html="alert.message" class="ml-2"></span>
            <button class="btn-close float-end" @click="removeAlert(alert.id)"></button>
        </div>
    </template>
</div>
