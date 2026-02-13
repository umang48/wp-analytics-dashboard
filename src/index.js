import { render } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import App from './App';
import './style.css';

// Configure API
const settings = window.wadSettings || {};
if (settings.nonce) {
    apiFetch.use(apiFetch.createNonceMiddleware(settings.nonce));
}
if (settings.apiUrl) {
    apiFetch.use(apiFetch.createRootURLMiddleware(settings.apiUrl));
}

const root = document.getElementById('wad-dashboard-root');
if (root) {
    render(<App />, root);
}
