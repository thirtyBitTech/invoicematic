// resources/js/cp.js

import Example from './components/Example.vue'

Statamic.booting(() => {
    Statamic.component('example-component', Example)
})