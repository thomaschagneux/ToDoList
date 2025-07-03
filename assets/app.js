import './styles/app.css'; // ton CSS perso
import $ from 'jquery';
import 'bootstrap';

// Make jQuery available globally
window.$ = window.jQuery = $;

console.log('App JS chargé');

// Test jQuery is working
$(function() {
    console.log('jQuery is loaded and working!');
});
