// Listen for installation
chrome.runtime.onInstalled.addListener(function() {
  console.log('ACADEX Password Manager installed');
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