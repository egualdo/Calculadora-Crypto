import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

// Global theme handling: apply preferred theme, expose toggle and emit events for listeners (charts, components)
(function() {
	function prefersDark() {
		return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	}

	function applyTheme(dark){
		if(dark) document.documentElement.classList.add('dark');
		else document.documentElement.classList.remove('dark');
		window.dispatchEvent(new CustomEvent('themeChanged', { detail: { dark } }));
		try{ localStorage.setItem('theme', dark ? 'dark' : 'light'); }catch(e){}
	}

	function init(){
		let stored = null;
		try{ stored = localStorage.getItem('theme'); }catch(e){}
		const dark = stored ? stored === 'dark' : prefersDark();
		applyTheme(dark);

		// delegate toggle clicks for any element with id 'theme-toggle'
		document.addEventListener('click', function(e){
			const t = e.target;
			if(!t) return;
			if(t.id === 'theme-toggle' || t.closest && t.closest('#theme-toggle')){
				applyTheme(!document.documentElement.classList.contains('dark'));
			}
		});

		// expose helpers
		window.toggleTheme = function(){ applyTheme(!document.documentElement.classList.contains('dark')); };
		window.setTheme = applyTheme;
	}

	if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
})();
