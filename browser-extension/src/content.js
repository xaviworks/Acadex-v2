// Listen for messages from the popup
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
  if (request.action === 'fillCredentials') {
    fillCredentials(request.credentials);
    sendResponse({ success: true });
  }
});

// Function to fill credentials
function fillCredentials(credentials) {
  const emailInputs = document.querySelectorAll('input[type="email"], input[name="email"], input[id="email"]');
  const passwordInputs = document.querySelectorAll('input[type="password"], input[name="password"], input[id="password"]');

  emailInputs.forEach(input => {
    input.value = credentials.email;
    input.dispatchEvent(new Event('input', { bubbles: true }));
  });

  passwordInputs.forEach(input => {
    input.value = credentials.password;
    input.dispatchEvent(new Event('input', { bubbles: true }));
  });
}

// Check if we should auto-fill on page load
chrome.storage.sync.get(['autoFillEnabled', 'lastUsedCredentials'], function(result) {
  if (result.autoFillEnabled && result.lastUsedCredentials) {
    // Wait for the page to be fully loaded
    if (document.readyState === 'complete') {
      fillCredentials(result.lastUsedCredentials);
    } else {
      window.addEventListener('load', () => {
        fillCredentials(result.lastUsedCredentials);
      });
    }
  }
});

// --- Auto-suggestion dropdown for login forms ---
(function() {
  let dropdown;
  let lastInput;

  function removeDropdown() {
    if (dropdown) {
      dropdown.remove();
      dropdown = null;
    }
  }

  function createDropdown(accounts, input) {
    removeDropdown();
    if (!accounts.length) return;
    // Filter accounts by domain or partial match to input value
    const inputValue = input.value.toLowerCase();
    const domain = window.location.hostname.replace(/^www\./, '');
    const filtered = accounts.filter(acc => {
      // Show if username/email contains input value or if input is empty
      if (inputValue && !acc.username.toLowerCase().includes(inputValue)) return false;
      // Optionally, filter by domain (if you want to scope accounts per site)
      // return acc.domain === domain;
      return true;
    });
    if (!filtered.length) return;
    dropdown = document.createElement('div');
    // Debug: log when dropdown is created
    console.log('[ACADEX EXT] Showing account suggestions for', input);
    dropdown.style.position = 'absolute';
    dropdown.style.left = input.getBoundingClientRect().left + window.scrollX + 'px';
    dropdown.style.top = (input.getBoundingClientRect().bottom + window.scrollY) + 'px';
    dropdown.style.width = input.offsetWidth + 'px';
    dropdown.style.background = '#fff';
    dropdown.style.border = '1.5px solid #2563eb';
    dropdown.style.borderRadius = '8px';
    dropdown.style.boxShadow = '0 4px 16px rgba(31,41,55,0.10)';
    dropdown.style.zIndex = '999999';
    dropdown.style.fontFamily = 'Inter, Arial, sans-serif';
    dropdown.style.fontSize = '15px';
    dropdown.style.padding = '4px 0';
    dropdown.style.maxHeight = '220px';
    dropdown.style.overflowY = 'auto';
    // Prevent dropdown from overflowing the viewport
    const rect = input.getBoundingClientRect();
    const dropdownHeight = 220;
    if (rect.bottom + dropdownHeight > window.innerHeight) {
      dropdown.style.top = (rect.top + window.scrollY - dropdownHeight) + 'px';
    }
    filtered.forEach(acc => {
      const item = document.createElement('div');
      item.title = acc.username;
      item.textContent = acc.username;
      item.style.padding = '10px 18px';
      item.style.cursor = 'pointer';
      item.style.transition = 'background 0.15s';
      item.style.color = '#2563eb';
      item.style.overflow = 'hidden';
      item.style.textOverflow = 'ellipsis';
      item.style.whiteSpace = 'nowrap';
      item.style.maxWidth = '90%';
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
    input.value = acc.username;
    input.dispatchEvent(new Event('input', { bubbles: true }));
    // Try to find the password field in the same form
    let form = input.form || input.closest('form') || document;
    let passInput = form.querySelector('input[type="password"], input[name*="pass" i], input[id*="pass" i]');
    if (passInput) {
      passInput.value = acc.password;
      passInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }

  function onFocus(e) {
    lastInput = e.target;
    chrome.storage.sync.get(['passwords'], function(result) {
      const accounts = (result.passwords || []);
      createDropdown(accounts, e.target);
    });
  }

  function onBlur() {
    setTimeout(removeDropdown, 150); // Delay to allow click
  }

  // Attach to all username/email fields (improved, case-insensitive)
  function attachListeners() {
    const userInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[name*="user" i], input[name*="email" i], input[id*="user" i], input[id*="email" i]');
    userInputs.forEach(input => {
      input.removeEventListener('focus', onFocus);
      input.removeEventListener('blur', onBlur);
      input.addEventListener('focus', onFocus);
      input.addEventListener('blur', onBlur);
    });
  }

  // Observe DOM for dynamically added fields
  const observer = new MutationObserver(attachListeners);
  observer.observe(document.body, { childList: true, subtree: true });
  attachListeners();
})(); 