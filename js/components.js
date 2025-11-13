// Component loader
class ComponentLoader {
  static async loadComponent(elementId, componentPath) {
    try {
      const response = await fetch(componentPath);
      const html = await response.text();
      document.getElementById(elementId).innerHTML = html;

      // Re-initialize Feather icons after loading component
      if (window.feather) {
        feather.replace();
      }

      // Fix paths based on current page location
      ComponentLoader.fixPaths();
      
      // Initialize mobile menu
      ComponentLoader.initMobileMenu();
    } catch (error) {
      console.error('Error loading component:', error);
    }
  }

  static fixPaths() {
    // Get the base URL for the current page
    const isInPages = window.location.pathname.includes('/pages/');
    const basePath = isInPages ? '..' : '.';

    // Update navbar logo link
    const homeLink = document.querySelector('#navbar-home');
    if (homeLink) {
      homeLink.href = `${basePath}/index.html`;
    }

    // Update navigation links
    const navLinks = document.querySelectorAll('.navbar-nav a');
    navLinks.forEach(link => {
      if (link.getAttribute('href').startsWith('/#')) {
        // For hash links (home, about, contact)
        link.href = `${basePath}/index.html${link.getAttribute('href').substring(1)}`;
      } else if (link.getAttribute('href').startsWith('/pages/')) {
        // For page links (menu.html, merch.html)
        link.href = `${basePath}${link.getAttribute('href')}`;
      }
    });
  }

  static initMobileMenu() {
    const hamburgerBtn = document.querySelector('#hamburger-menu');
    const navMenu = document.querySelector('.navbar-nav');

    if (!hamburgerBtn || !navMenu) return;

    // Remove any existing event listeners
    hamburgerBtn.replaceWith(hamburgerBtn.cloneNode(true));
    const newHamburger = document.querySelector('#hamburger-menu');

    // Set initial state
    navMenu.classList.add('hidden');
    newHamburger.setAttribute('aria-expanded', 'false');
    newHamburger.setAttribute('aria-controls', 'navbar-nav');

    // Add click event listener
    newHamburger.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      navMenu.classList.toggle('hidden');
      newHamburger.setAttribute('aria-expanded', navMenu.classList.contains('hidden') ? 'false' : 'true');
    });

    // Handle click outside
    const handleClickOutside = (e) => {
      if (!newHamburger.contains(e.target) && !navMenu.contains(e.target)) {
        navMenu.classList.add('hidden');
        newHamburger.setAttribute('aria-expanded', 'false');
      }
    };

    // Remove any existing click listeners
    document.removeEventListener('click', handleClickOutside);
    document.addEventListener('click', handleClickOutside);

    // Handle escape key
    const handleEscape = (e) => {
      if (e.key === 'Escape' && !navMenu.classList.contains('hidden')) {
        navMenu.classList.add('hidden');
        newHamburger.setAttribute('aria-expanded', 'false');
      }
    };

    // Remove any existing keydown listeners
    document.removeEventListener('keydown', handleEscape);
    document.addEventListener('keydown', handleEscape);
    
    if (!hamburgerBtn || !navMenu) return;

    // Set initial state and ARIA attributes
    hamburgerBtn.setAttribute('aria-expanded', 'false');
    hamburgerBtn.setAttribute('aria-controls', 'navbar-nav');
    navMenu.setAttribute('id', 'navbar-nav');
    navMenu.setAttribute('role', 'navigation');
    
    // Toggle menu when hamburger is clicked
    hamburgerBtn.onclick = (e) => {
      e.preventDefault();
      navMenu.classList.toggle('hidden');
      const isExpanded = !navMenu.classList.contains('hidden');
      hamburgerBtn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
    };

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!hamburgerBtn.contains(e.target) && 
          !navMenu.contains(e.target) && 
          !navMenu.classList.contains('hidden')) {
        navMenu.classList.add('hidden');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      }
    });

    // Close menu when pressing Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !navMenu.classList.contains('hidden')) {
        navMenu.classList.add('hidden');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      }
    });

    // Close menu when clicking a navigation link
    navMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.add('hidden');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      });
    });
    
    if (hamburgerBtn && navMenu) {
      // Toggle menu on hamburger click
      hamburgerBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isOpen = !navMenu.classList.contains('hidden');
        
        // Toggle menu visibility with smooth transition
        navMenu.style.display = 'flex';
        navMenu.style.transition = 'opacity 0.3s ease-in-out';
        
        if (isOpen) {
          navMenu.style.opacity = '0';
          setTimeout(() => {
            navMenu.classList.add('hidden');
            navMenu.style.display = '';
          }, 300);
        } else {
          navMenu.classList.remove('hidden');
          setTimeout(() => {
            navMenu.style.opacity = '1';
          }, 10);
        }

        // Update hamburger icon
        const icon = hamburgerBtn.querySelector('i');
        if (icon) {
          icon.setAttribute('data-feather', isOpen ? 'menu' : 'x');
          feather.replace();
        }
      });

      // Close menu when clicking outside
      document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !hamburgerBtn.contains(e.target) && !navMenu.classList.contains('hidden')) {
          hamburgerBtn.click();
        }
      });

      // Close menu when clicking a link
      navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          if (!navMenu.classList.contains('hidden')) {
            hamburgerBtn.click();
          }
        });
      });

      // Close menu when screen is resized to desktop size
      window.addEventListener('resize', () => {
        if (window.innerWidth >= 768 && !navMenu.classList.contains('hidden')) {
          navMenu.classList.add('hidden');
          navMenu.style.opacity = '';
          navMenu.style.display = '';
          const icon = hamburgerBtn.querySelector('i');
          if (icon) {
            icon.setAttribute('data-feather', 'menu');
            feather.replace();
          }
        }
      });
    }
  }
}

// Add event listener for after the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  const componentPath = window.location.pathname.includes('/pages/') 
    ? '../components/navbar.html' 
    : 'components/navbar.html';
  
  ComponentLoader.loadComponent('navbar-container', componentPath);
});