import Alpine from 'alpinejs'
import count from './count';
import mask from '@alpinejs/mask'
 

window.Alpine = Alpine
window.count = count;
Alpine.plugin(mask)
 
Alpine.start()