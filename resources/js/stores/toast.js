import { defineStore } from "pinia";

export const useToastStore = defineStore("toast", {
    state: () => ({
        items: [],
    }),
    actions: {
        createItem(type, message, duration) {
            return {
                id: Math.random().toString().substr(2, 8),
                type: type,
                message: message,
                duration: duration,
            };
        },
        addFromSession(item) {
            let dur = item.duration ?? 5000;

            this.addItem(this.createItem(item.type, item.message, dur));
        },
        addItem(item) {
            if (item.duration) {
                item.timeout = setTimeout(() => {
                    this.removeItem(item);
                }, item.duration);
            }

            this.items.push(item);
        },
        removeItem(item) {
            if (item.timeout) {
                clearTimeout(item.timeout);
            }

            this.items.splice(this.items.indexOf(item), 1);
        },
        addNormalItem(message, duration = 5000) {
            this.addItem(this.createItem("normal", message, duration));
        },
        addSuccessItem(message, duration = 5000) {
            this.addItem(this.createItem("success", message, duration));
        },
        addErrorItem(message, duration = 5000) {
            this.addItem(this.createItem("error", message, duration));
        },
        addWarningItem(message, duration = 5000) {
            this.addItem(this.createItem("warning", message, duration));
        },
        addInfoItem(message, duration = 5000) {
            this.addItem(this.createItem("info", message, duration));
        },
    },
});
