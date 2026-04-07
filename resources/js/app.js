import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';

// Livewire v4 already bundles Alpine.js — do NOT import/start it again.
// Use the alpine:init event to register plugins and stores on Livewire's Alpine instance.
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(intersect);
    window.Alpine.plugin(focus);

    // Global Alpine store untuk sidebar state
    window.Alpine.store('sidebar', {
        open: window.innerWidth >= 1024,
        toggle() {
            this.open = !this.open;
        }
    });
});
