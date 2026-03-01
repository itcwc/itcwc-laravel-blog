import './bootstrap';
import EasyMDE from 'easymde';
import hljs from 'highlight.js';

window.EasyMDE = EasyMDE;
window.hljs = hljs;

document.addEventListener('DOMContentLoaded', () => {
    hljs.highlightAll();
});
