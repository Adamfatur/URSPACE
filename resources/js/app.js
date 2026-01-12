import hljs from 'highlight.js';

// Init Highlight.js
hljs.highlightAll();

// Expose to window for dynamic content (like improved comments)
window.hljs = hljs;
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import './forum';
import '../css/app.scss';
