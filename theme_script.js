function updateIcon(theme) {
    const icon = document.getElementById('theme-icon');
    if (!icon) return;

    if (theme === 'light') {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        icon.style.color = '#f1c40f'; 
    } else {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        icon.style.color = '#f1c40f'; 
    }
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('selected-theme', newTheme);
    updateIcon(newTheme);
}

document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('selected-theme') || 'dark';
    updateIcon(savedTheme);
});