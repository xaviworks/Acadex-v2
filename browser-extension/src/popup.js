// State management
const state = {
  passwords: [],
  isSearching: false,
  searchTimeout: null,
  validationCache: new Map()
};

// Cache DOM elements
const elements = {
  usernameInput: document.getElementById('username'),
  passwordInput: document.getElementById('password'),
  saveBtn: document.getElementById('saveBtn'),
  passwordList: document.getElementById('passwordList'),
  searchInput: document.getElementById('searchInput'),
  addAccountBtn: document.getElementById('addAccountBtn'),
  addAccountForm: document.getElementById('addAccountForm'),
  cancelAddAccount: document.getElementById('cancelAddAccount')
};

// Validation patterns
const VALIDATION = {
  email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  minUsernameLength: 3,
  minEmailLength: 6,
  minPasswordLength: 8
};

// Utility functions
const utils = {
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  async getPasswords() {
    try {
      const result = await chrome.storage.sync.get(['passwords']);
      return result.passwords || [];
    } catch (error) {
      console.error('[ACADEX EXT] Error getting passwords:', error);
      showNotification('Failed to load passwords', 'error');
      return [];
    }
  },

  async savePasswords(passwords) {
    try {
      await chrome.storage.sync.set({ passwords });
      state.passwords = passwords;
    } catch (error) {
      console.error('[ACADEX EXT] Error saving passwords:', error);
      showNotification('Failed to save passwords', 'error');
      throw error;
    }
  },

  formatDate(dateString, friendly = false) {
    const date = new Date(dateString);
    if (friendly) {
      const now = new Date();
      const diff = now - date;
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      
      if (days === 0) {
        return `Today at ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
      }
      if (days === 1) {
        return `Yesterday at ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
      }
      if (days < 7) {
        return `${days} days ago`;
      }
    }
    return date.toLocaleDateString();
  },

  validateInput(username, password) {
    const cacheKey = `${username}:${password}`;
    if (state.validationCache.has(cacheKey)) {
      return state.validationCache.get(cacheKey);
    }

    const isEmail = VALIDATION.email.test(username);
    const validation = {
      isValid: true,
      message: ''
    };

    if (!username || !password) {
      validation.isValid = false;
      validation.message = 'Please enter a username/email and password';
    } else if (isEmail && username.length < VALIDATION.minEmailLength) {
      validation.isValid = false;
      validation.message = 'Email must be at least 6 characters';
    } else if (!isEmail && username.length < VALIDATION.minUsernameLength) {
      validation.isValid = false;
      validation.message = 'Username must be at least 3 characters';
    } else if (password.length < VALIDATION.minPasswordLength) {
      validation.isValid = false;
      validation.message = 'Password must be at least 8 characters long';
    }

    state.validationCache.set(cacheKey, validation);
    return validation;
  }
};

// UI functions
const ui = {
  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    Object.assign(notification.style, {
      position: 'fixed',
      top: '20px',
      right: '20px',
      padding: '12px 24px',
      borderRadius: '8px',
      background: type === 'error' ? '#fee2e2' : '#dcfce7',
      color: type === 'error' ? '#991b1b' : '#166534',
      boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
      zIndex: '9999',
      transition: 'opacity 0.3s ease-in-out'
    });

    document.body.appendChild(notification);
    setTimeout(() => {
      notification.style.opacity = '0';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  },

  async loadPasswords() {
    try {
      state.passwords = await utils.getPasswords();
      
      if (state.passwords.length === 0) {
        elements.passwordList.innerHTML = `
          <div class="no-passwords">
            <div class="no-passwords-icon">
              <svg width="48" height="48" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                <rect x="5" y="11" width="14" height="8" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </div>
            <p>No saved passwords yet</p>
          </div>
        `;
        return;
      }

      elements.passwordList.innerHTML = '';
      state.passwords.forEach((item, index) => {
        const passwordItem = document.createElement('div');
        passwordItem.className = 'password-item';
        
        passwordItem.innerHTML = `
          <div class="account-info">
            <div class="username-row">
              <span class="username" title="${item.username}">${item.username}</span>
            </div>
            <div class="password-meta">
              Added ${utils.formatDate(item.createdAt, true)}
            </div>
          </div>
          <div class="password-actions">
            <button class="action-btn copy-btn" data-index="${index}" title="Copy password">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
              </svg>
            </button>
            <button class="action-btn fill-btn" data-index="${index}" title="Auto-fill credentials">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button class="action-btn delete-btn" data-index="${index}" title="Delete password">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        `;

        // Add click-to-expand for username
        const usernameSpan = passwordItem.querySelector('.username');
        let expanded = false;
        usernameSpan.addEventListener('click', function() {
          expanded = !expanded;
          Object.assign(this.style, expanded ? {
            whiteSpace: 'normal',
            overflow: 'visible',
            textOverflow: 'clip',
            wordBreak: 'break-all',
            background: '#f3f4f6',
            padding: '4px 8px',
            borderRadius: '4px',
            zIndex: '2',
            maxWidth: 'none'
          } : {
            whiteSpace: 'nowrap',
            overflow: 'hidden',
            textOverflow: 'ellipsis',
            wordBreak: 'normal',
            background: '',
            padding: '',
            borderRadius: '',
            zIndex: '',
            maxWidth: '200px'
          });
        });

        elements.passwordList.appendChild(passwordItem);
      });

      // Add button interactions
      document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => btn.style.transform = 'translateY(-1px)');
        btn.addEventListener('mouseleave', () => btn.style.transform = 'translateY(0)');
      });

      // Add button click handlers
      this.attachButtonHandlers();
    } catch (error) {
      console.error('[ACADEX EXT] Error loading passwords:', error);
      ui.showNotification('Failed to load passwords', 'error');
    }
  },

  attachButtonHandlers() {
    // Copy button handler
    document.querySelectorAll('.copy-btn').forEach(btn => {
      btn.addEventListener('click', async function() {
        const index = this.getAttribute('data-index');
        const password = state.passwords[index].password;
        
        try {
          await navigator.clipboard.writeText(password);
          const originalHTML = this.innerHTML;
          this.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M5 13l4 4L19 7"/>
            </svg>
          `;
          this.classList.add('copied');
          
          setTimeout(() => {
            this.innerHTML = originalHTML;
            this.classList.remove('copied');
          }, 1200);

          ui.showNotification('Password copied to clipboard', 'success');
          
          // Update last used timestamp
          state.passwords[index].lastUsed = new Date().toISOString();
          await utils.savePasswords(state.passwords);
        } catch (error) {
          console.error('[ACADEX EXT] Error copying password:', error);
          ui.showNotification('Failed to copy password', 'error');
        }
      });
    });

    // Fill button handler
    document.querySelectorAll('.fill-btn').forEach(btn => {
      btn.addEventListener('click', async function() {
        const index = this.getAttribute('data-index');
        const credentials = state.passwords[index];
        
        try {
          const [tab] = await chrome.tabs.query({active: true, currentWindow: true});
          await chrome.scripting.executeScript({
            target: {tabId: tab.id},
            function: fillCredentials,
            args: [credentials]
          });

          state.passwords[index].lastUsed = new Date().toISOString();
          await utils.savePasswords(state.passwords);
          ui.showNotification('Credentials filled successfully', 'success');
        } catch (error) {
          console.error('[ACADEX EXT] Error filling credentials:', error);
          ui.showNotification('Failed to fill credentials', 'error');
        }
      });
    });

    // Delete button handler
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', async function() {
        if (!confirm('Are you sure you want to delete this password?')) return;
        
        const index = this.getAttribute('data-index');
        state.passwords.splice(index, 1);
        
        try {
          await utils.savePasswords(state.passwords);
          ui.showNotification('Password deleted successfully', 'success');
          ui.loadPasswords();
        } catch (error) {
          console.error('[ACADEX EXT] Error deleting password:', error);
          ui.showNotification('Failed to delete password', 'error');
        }
      });
    });
  }
};

// Event handlers
document.addEventListener('DOMContentLoaded', function() {
  // Load initial passwords
  ui.loadPasswords();

  // Save button handler
  elements.saveBtn.addEventListener('click', async function() {
    const username = elements.usernameInput.value.trim();
    const password = elements.passwordInput.value.trim();

    const validation = utils.validateInput(username, password);
    if (!validation.isValid) {
      ui.showNotification(validation.message, 'error');
      return;
    }

    try {
      // Check for duplicate username/email
      if (state.passwords.some(p => p.username === username)) {
        ui.showNotification('This username/email is already saved', 'error');
        return;
      }

      state.passwords.push({ 
        username, 
        password,
        createdAt: new Date().toISOString(),
        lastUsed: null
      });

      await utils.savePasswords(state.passwords);
      ui.showNotification('Password saved successfully', 'success');
      
      elements.usernameInput.value = '';
      elements.passwordInput.value = '';
      ui.loadPasswords();
      elements.addAccountForm.style.display = 'none';
      elements.addAccountBtn.style.display = 'block';
    } catch (error) {
      console.error('[ACADEX EXT] Error saving password:', error);
      ui.showNotification('Failed to save password', 'error');
    }
  });

  // Search functionality with debounce
  elements.searchInput.addEventListener('input', utils.debounce(function() {
    const searchTerm = this.value.toLowerCase();
    const passwordItems = document.querySelectorAll('.password-item');
    
    passwordItems.forEach(item => {
      const username = item.querySelector('.username').textContent.toLowerCase();
      item.style.display = username.includes(searchTerm) ? 'flex' : 'none';
    });
  }, 300));

  // Add account button handlers
  elements.addAccountBtn.addEventListener('click', function() {
    elements.addAccountBtn.style.display = 'none';
    elements.addAccountForm.style.display = 'block';
    elements.usernameInput.focus();
  });

  elements.cancelAddAccount.addEventListener('click', function() {
    elements.addAccountForm.style.display = 'none';
    elements.addAccountBtn.style.display = 'block';
  });

  // Form submit handler
  elements.addAccountForm.addEventListener('submit', function(e) {
    e.preventDefault();
    elements.saveBtn.click();
  });
});

function getPasswords() {
  return new Promise((resolve) => {
    chrome.storage.sync.get(['passwords'], function(result) {
      resolve(result.passwords || []);
    });
  });
}
function savePasswords(passwords) {
  return new Promise((resolve) => {
    chrome.storage.sync.set({ passwords }, resolve);
  });
}
function formatDate(dateString, friendly = false) {
  const date = new Date(dateString);
  if (!friendly) {
    return date.toLocaleDateString('en-US', {
      year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
  }
  const now = new Date();
  const diffMs = now - date;
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  if (diffDays === 0) {
    // Today
    return `Today, ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
  } else if (diffDays === 1) {
    return `Yesterday, ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
  } else if (diffDays < 7) {
    return `${diffDays} days ago, ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
  } else {
    return date.toLocaleDateString('en-US', {
      year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
  }
}
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.textContent = message;
  notification.style.position = 'fixed';
  notification.style.top = '24px';
  notification.style.left = '50%';
  notification.style.transform = 'translateX(-50%)';
  notification.style.background = type === 'success' ? '#059669' : type === 'error' ? '#dc2626' : '#2563eb';
  notification.style.color = '#fff';
  notification.style.padding = '12px 28px';
  notification.style.borderRadius = '8px';
  notification.style.fontWeight = '600';
  notification.style.fontSize = '15px';
  notification.style.boxShadow = '0 4px 16px rgba(31,41,55,0.10)';
  notification.style.zIndex = '9999';
  notification.style.opacity = '0';
  notification.style.transition = 'opacity 0.2s';
  document.body.appendChild(notification);
  setTimeout(() => { notification.style.opacity = '1'; }, 100);
  setTimeout(() => {
    notification.style.opacity = '0';
    setTimeout(() => { notification.remove(); }, 300);
  }, 2600);
}
// Function to be injected into the page for auto-filling
function fillCredentials(credentials) {
  // Try to fill both username and email fields
  const userInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[name*="user"], input[name*="email"], input[id*="user"], input[id*="email"]');
  const passwordInputs = document.querySelectorAll('input[type="password"], input[name*="pass"], input[id*="pass"]');
  userInputs.forEach(input => {
    input.value = credentials.username;
    input.dispatchEvent(new Event('input', { bubbles: true }));
  });
  passwordInputs.forEach(input => {
    input.value = credentials.password;
    input.dispatchEvent(new Event('input', { bubbles: true }));
  });
} 