document.addEventListener('DOMContentLoaded', function() {
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const saveBtn = document.getElementById('saveBtn');
  const passwordList = document.getElementById('passwordList');
  const searchInput = document.getElementById('searchInput');
  const addAccountBtn = document.getElementById('addAccountBtn');
  const addAccountForm = document.getElementById('addAccountForm');
  const cancelAddAccount = document.getElementById('cancelAddAccount');

  // Load saved passwords
  loadPasswords();

  // Save password with validation and feedback
  saveBtn.addEventListener('click', async function() {
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (!username || !password) {
      showNotification('Please enter a username/email and password', 'error');
      return;
    }

    // Accept either email or username (basic email check)
    const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(username);
    if (!isEmail && username.length < 3) {
      showNotification('Username must be at least 3 characters', 'error');
      return;
    }
    if (isEmail && username.length < 6) {
      showNotification('Email must be at least 6 characters', 'error');
      return;
    }
    if (password.length < 8) {
      showNotification('Password must be at least 8 characters long', 'error');
      return;
    }

    try {
      const passwords = await getPasswords();
      // Check for duplicate username/email
      if (passwords.some(p => p.username === username)) {
        showNotification('This username/email is already saved', 'error');
        return;
      }
      passwords.push({ 
        username, 
        password,
        createdAt: new Date().toISOString(),
        lastUsed: null
      });
      await savePasswords(passwords);
      showNotification('Password saved successfully', 'success');
      usernameInput.value = '';
      passwordInput.value = '';
      loadPasswords();
      addAccountForm.style.display = 'none';
      addAccountBtn.style.display = 'block';
    } catch (error) {
      showNotification('Failed to save password', 'error');
      console.error('Save error:', error);
    }
  });

  // Search functionality with debounce
  let searchTimeout;
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      const searchTerm = this.value.toLowerCase();
      const passwordItems = document.querySelectorAll('.password-item');
      passwordItems.forEach(item => {
        const username = item.querySelector('.username').textContent.toLowerCase();
        if (username.includes(searchTerm)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    }, 300);
  });

  addAccountBtn.addEventListener('click', function() {
    addAccountBtn.style.display = 'none';
    addAccountForm.style.display = 'block';
    document.getElementById('username').focus();
  });
  cancelAddAccount.addEventListener('click', function() {
    addAccountForm.style.display = 'none';
    addAccountBtn.style.display = 'block';
  });

  // Change saveBtn event to handle form submit and hide form after save
  addAccountForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    saveBtn.click();
  });

  async function loadPasswords() {
    try {
      const passwords = await getPasswords();
      if (passwords.length === 0) {
        passwordList.innerHTML = `
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
      passwordList.innerHTML = '';
      passwords.forEach((item, index) => {
        const passwordItem = document.createElement('div');
        passwordItem.className = 'password-item';
        passwordItem.innerHTML = `
          <div class="password-header">
            <span class="username" title="${item.username}">${item.username}</span>
            <div class="password-actions">
              <button class="action-btn copy-btn" data-index="${index}" title="Copy password">Copy</button>
              <button class="action-btn fill-btn" data-index="${index}" title="Auto-fill credentials">Fill</button>
              <button class="action-btn delete-btn" data-index="${index}" title="Delete password">Delete</button>
            </div>
          </div>
          <div class="password-meta">
            <small>Added: ${formatDate(item.createdAt, true)}</small>
            ${item.lastUsed ? `<small>Last used: ${formatDate(item.lastUsed, true)}</small>` : ''}
          </div>
        `;
        passwordList.appendChild(passwordItem);
        // Add click-to-expand for username
        const usernameSpan = passwordItem.querySelector('.username');
        let expanded = false;
        usernameSpan.addEventListener('click', function(e) {
          expanded = !expanded;
          if (expanded) {
            usernameSpan.style.whiteSpace = 'normal';
            usernameSpan.style.overflow = 'visible';
            usernameSpan.style.textOverflow = 'clip';
            usernameSpan.style.wordBreak = 'break-all';
            usernameSpan.style.background = '#f3f4f6';
            usernameSpan.style.padding = '2px 4px';
            usernameSpan.style.borderRadius = '4px';
            usernameSpan.style.zIndex = '2';
          } else {
            usernameSpan.style.whiteSpace = 'nowrap';
            usernameSpan.style.overflow = 'hidden';
            usernameSpan.style.textOverflow = 'ellipsis';
            usernameSpan.style.wordBreak = 'normal';
            usernameSpan.style.background = '';
            usernameSpan.style.padding = '';
            usernameSpan.style.borderRadius = '';
            usernameSpan.style.zIndex = '';
          }
        });
      });
      document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
          const index = this.getAttribute('data-index');
          const passwords = await getPasswords();
          const password = passwords[index].password;
          try {
            await navigator.clipboard.writeText(password);
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            this.classList.add('copied');
            setTimeout(() => {
              this.textContent = originalText;
              this.classList.remove('copied');
            }, 1200);
            showNotification('Password copied to clipboard', 'success');
            passwords[index].lastUsed = new Date().toISOString();
            await savePasswords(passwords);
          } catch (error) {
            showNotification('Failed to copy password', 'error');
          }
        });
      });
      document.querySelectorAll('.fill-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
          const index = this.getAttribute('data-index');
          const passwords = await getPasswords();
          const credentials = passwords[index];
          try {
            const [tab] = await chrome.tabs.query({active: true, currentWindow: true});
            await chrome.scripting.executeScript({
              target: {tabId: tab.id},
              function: fillCredentials,
              args: [credentials]
            });
            passwords[index].lastUsed = new Date().toISOString();
            await savePasswords(passwords);
            showNotification('Credentials filled successfully', 'success');
          } catch (error) {
            showNotification('Failed to fill credentials', 'error');
          }
        });
      });
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
          if (!confirm('Are you sure you want to delete this password?')) return;
          const index = this.getAttribute('data-index');
          const passwords = await getPasswords();
          passwords.splice(index, 1);
          try {
            await savePasswords(passwords);
            showNotification('Password deleted successfully', 'success');
            loadPasswords();
          } catch (error) {
            showNotification('Failed to delete password', 'error');
          }
        });
      });
    } catch (error) {
      showNotification('Failed to load passwords', 'error');
      console.error('Load error:', error);
    }
  }
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