import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';

Alpine.plugin(intersect);
Alpine.plugin(focus);

// Global Alpine store untuk sidebar state
Alpine.store('sidebar', {
    open: window.innerWidth >= 1024,
    toggle() {
        this.open = !this.open;
    }
});

window.Alpine = Alpine;
Alpine.start();
