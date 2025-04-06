// Function to switch between tabs
function openTab(tabName) {
    // Hide all tab content
    const tabContent = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContent.length; i++) {
        tabContent[i].classList.remove('active');
    }
    
    // Remove active class from all tab buttons
    const tabButtons = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }
    
    // Show the selected tab content and mark the button as active
    document.getElementById(tabName).classList.add('active');
    
    // Find and activate the button that opened this tab
    const activeButton = document.querySelector(`.tab-btn[onclick="openTab('${tabName}')"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
}

// Event listener to ensure the default tab is open when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // The first tab (organic) is set as active by default in the HTML
    // This ensures the JavaScript behavior matches the initial HTML state
});