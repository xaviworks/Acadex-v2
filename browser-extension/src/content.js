// Cache DOM queries and common values
const DOM_CACHE = {
  emailInputs: null,
  passwordInputs: null,
  lastQueryTime: 0,
  queryThrottle: 100 // ms
};

// Debounce function for performance
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Optimized credential filling
function fillCredentials(credentials) {
  try {
    // Update cache if needed
    const now = Date.now();
    if (!DOM_CACHE.emailInputs || !DOM_CACHE.passwordInputs || now - DOM_CACHE.lastQueryTime > DOM_CACHE.queryThrottle) {
      DOM_CACHE.emailInputs = document.querySelectorAll('input[type="email"], input[name="email"], input[id="email"]');
      DOM_CACHE.passwordInputs = document.querySelectorAll('input[type="password"], input[name="password"], input[id="password"]');
      DOM_CACHE.lastQueryTime = now;
    }

    const inputEvent = new Event('input', { bubbles: true });
    
    DOM_CACHE.emailInputs.forEach(input => {
      input.value = credentials.email;
      input.dispatchEvent(inputEvent);
    });

    DOM_CACHE.passwordInputs.forEach(input => {
      input.value = credentials.password;
      input.dispatchEvent(inputEvent);
    });
  } catch (error) {
    console.error('[ACADEX EXT] Error filling credentials:', error);
  }
}

// Optimized message listener
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
  if (request.action === 'fillCredentials') {
    try {
      fillCredentials(request.credentials);
      sendResponse({ success: true });
    } catch (error) {
      console.error('[ACADEX EXT] Error handling message:', error);
      sendResponse({ success: false, error: error.message });
    }
  }
  return true; // Keep the message channel open for async response
});

// Optimized auto-fill check
const autoFillCheck = debounce(() => {
  chrome.storage.sync.get(['autoFillEnabled', 'lastUsedCredentials'], function(result) {
    if (result.autoFillEnabled && result.lastUsedCredentials) {
      if (document.readyState === 'complete') {
        fillCredentials(result.lastUsedCredentials);
      } else {
        window.addEventListener('load', () => fillCredentials(result.lastUsedCredentials), { once: true });
      }
    }
  });
}, 100);

autoFillCheck();

// --- Optimized Auto-suggestion dropdown ---
(function() {
  let dropdown = null;
  let lastInput = null;
  let dropdownTimeout = null;
  const DROPDOWN_DELAY = 150;
  const MAX_DROPDOWN_HEIGHT = 220;

  // Cached styles for dropdown
  const DROPDOWN_STYLES = {
    position: 'absolute',
    background: '#fff',
    border: '1.5px solid #2563eb',
    borderRadius: '8px',
    boxShadow: '0 4px 16px rgba(31,41,55,0.10)',
    zIndex: '999999',
    fontFamily: 'Inter, Arial, sans-serif',
    fontSize: '15px',
    padding: '4px 0',
    maxHeight: `${MAX_DROPDOWN_HEIGHT}px`,
    overflowY: 'auto'
  };

  function removeDropdown() {
    if (dropdown) {
      dropdown.remove();
      dropdown = null;
    }
    if (dropdownTimeout) {
      clearTimeout(dropdownTimeout);
      dropdownTimeout = null;
    }
  }

  function createDropdown(accounts, input) {
    removeDropdown();
    if (!accounts.length) return;

    const inputValue = input.value.toLowerCase();
    const domain = window.location.hostname.replace(/^www\./, '');
    
    // Optimize filtering
    const filtered = accounts.filter(acc => 
      !inputValue || acc.username.toLowerCase().includes(inputValue)
    );

    if (!filtered.length) return;

    dropdown = document.createElement('div');
    Object.assign(dropdown.style, DROPDOWN_STYLES);

    // Optimize positioning
    const rect = input.getBoundingClientRect();
    const scrollX = window.scrollX;
    const scrollY = window.scrollY;
    
    dropdown.style.left = `${rect.left + scrollX}px`;
    dropdown.style.width = `${input.offsetWidth}px`;

    // Smart positioning to prevent viewport overflow
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;
    
    if (spaceBelow < MAX_DROPDOWN_HEIGHT && spaceAbove > spaceBelow) {
      dropdown.style.top = `${rect.top + scrollY - MAX_DROPDOWN_HEIGHT}px`;
    } else {
      dropdown.style.top = `${rect.bottom + scrollY}px`;
    }

    // Create dropdown items
    filtered.forEach(acc => {
      const item = document.createElement('div');
      item.title = acc.username;
      item.textContent = acc.username;
      
      Object.assign(item.style, {
        padding: '10px 18px',
        cursor: 'pointer',
        transition: 'background 0.15s',
        color: '#2563eb',
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        whiteSpace: 'nowrap',
        maxWidth: '90%'
      });

      item.addEventListener('mouseenter', () => item.style.background = '#e0e7ff');
      item.addEventListener('mouseleave', () => item.style.background = 'transparent');
      item.addEventListener('mousedown', e => {
        e.preventDefault();
        fillAccount(acc, input);
        removeDropdown();
      });

      dropdown.appendChild(item);
    });

    document.body.appendChild(dropdown);
  }

  function fillAccount(acc, input) {
    try {
      input.value = acc.username;
      input.dispatchEvent(new Event('input', { bubbles: true }));

      const form = input.form || input.closest('form') || document;
      const passInput = form.querySelector('input[type="password"], input[name*="pass" i], input[id*="pass" i]');
      
      if (passInput) {
        passInput.value = acc.password;
        passInput.dispatchEvent(new Event('input', { bubbles: true }));
      }
    } catch (error) {
      console.error('[ACADEX EXT] Error filling account:', error);
    }
  }

  const onFocus = debounce((e) => {
    lastInput = e.target;
    chrome.storage.sync.get(['passwords'], function(result) {
      if (chrome.runtime.lastError) {
        console.error('[ACADEX EXT] Storage error:', chrome.runtime.lastError);
        return;
      }
      createDropdown(result.passwords || [], e.target);
    });
  }, 100);

  const onBlur = () => {
    dropdownTimeout = setTimeout(removeDropdown, DROPDOWN_DELAY);
  };

  // Optimized listener attachment
  function attachListeners() {
    const userInputs = document.querySelectorAll(
      'input[type="text"], input[type="email"], input[name*="user" i], input[name*="email" i], input[id*="user" i], input[id*="email" i]'
    );
    
    userInputs.forEach(input => {
      input.removeEventListener('focus', onFocus);
      input.removeEventListener('blur', onBlur);
      input.addEventListener('focus', onFocus);
      input.addEventListener('blur', onBlur);
    });
  }

  // Optimized DOM observer
  const observer = new MutationObserver(debounce(attachListeners, 100));
  observer.observe(document.body, { 
    childList: true, 
    subtree: true,
    attributes: false,
    characterData: false
  });

  // Initial attachment
  attachListeners();
})(); 