export default function notificationDropdown() {
    return {
        show: false,
        notifications: [],
        toggleDropdown() {
            this.show = !this.show;
        },
        async fetchNotifications() {
            const res = await fetch('/notifications');
            this.notifications = await res.json();
        },
        formatDate(datetime) {
            const d = new Date(datetime);
            return `${d.getFullYear()}/${d.getMonth() + 1}/${d.getDate()} ${d.getHours()}:${String(d.getMinutes()).padStart(2, '0')}`;
        },
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 30000); // 30秒ごとに更新
        }
    }
}
