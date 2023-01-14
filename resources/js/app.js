require("./bootstrap");

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import { createPinia } from "pinia";
import VueScrollTo from "vue-scrollto";
import { VueMasonryPlugin } from "vue-masonry";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(createPinia())
            .use(VueScrollTo)
            .use(VueMasonryPlugin)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init();
