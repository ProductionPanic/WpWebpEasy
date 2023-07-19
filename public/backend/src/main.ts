import './app.scss'
import "toastify-js/src/toastify.css"
import App from './App.svelte'
import { StatsHandler } from './lib/ts/Stats';
import WebpEasy from './lib/ts/WebpEasy'

window.addEventListener('load', () => {


  const root_element = document.getElementById('webp-easy');
  if (!root_element) return;
  const is_settings = root_element.classList.contains('webp-easy-settings');
  WebpEasy.init();
  StatsHandler.init();

  const app = new App({
    target: root_element,
    props: {
      is_settings_page: is_settings,
    }
  })

  // on navigate
  window.addEventListener('click', async (e) => {
    const target = e.target as HTMLElement;
    if (target.tagName !== 'A') return;
    const links = WebpEasy.links();
    if (!Object.values(links).includes(target.getAttribute('href'))) return;
    e.preventDefault();
    app.$set({
      is_settings_page: target.getAttribute('href') === links.settings,
    });
    window.history.pushState({}, '', target.getAttribute('href'));

    document.dispatchEvent(new Event('hashchange'));

    // fix for wp-admin sidebar
    const parent = document.querySelector('ul#adminmenu li.wp-menu-open');
    if (parent) {
      const items = parent.querySelectorAll('.wp-submenu li');
      items.forEach((item) => {
        item.classList.remove('current');
        const link = item.querySelector('a');
        if (!link) return;
        link.classList.remove('current');
        link.removeAttribute('aria-current');

        const link_url = new URL(link.href);
        const link_page_param = link_url.searchParams.get('page');
        const current_url = new URL(window.location.href);
        const current_page_param = current_url.searchParams.get('page');

        if (link_page_param === current_page_param) {
          item.classList.add('current');
          link.classList.add('current');
          link.setAttribute('aria-current', 'page');
        }
      });
    }
  });

  // on back/forward
  window.addEventListener('popstate', () => {
    app.$set({
      is_settings_page: window.location.href === WebpEasy.links().settings,
    });
  });
})