// Listen for installation
chrome.runtime.onInstalled.addListener(function() {
  console.log('ACADEX Password Manager installed');
  
  // Default accounts from UserSeeder
  const defaultAccounts = [
    {
      username: 'admin@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'chairperson.bsit@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'instructor.bsit@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'chairperson.bsba@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'instructor.bsba@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'chairperson.bspsy@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'dean@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    },
    {
      username: 'instructor.bspsy@brokenshire.edu.ph',
      password: 'password',
      createdAt: new Date().toISOString(),
      lastUsed: null
    }
  ];

  // Save default accounts to storage
  chrome.storage.sync.set({ passwords: defaultAccounts }, function() {
    console.log('Default accounts seeded successfully');
  });
});

// Listen for messages from popup
chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
  if (request.action === "getPasswords") {
    chrome.storage.sync.get(['passwords'], function(result) {
      sendResponse(result.passwords || {});
    });
    return true; // Required for async sendResponse
  }
}); 