<header class="bg-[#024DDF] text-white pb-6 px-4 md:px-8">
    <div class="max-w-4xl mx-auto mt-4 relative">
        <div class="bg-white text-gray-900 rounded-xl shadow-2xl flex flex-col md:flex-row items-stretch overflow-hidden divide-y md:divide-y-0 md:divide-x divide-gray-200">
            <div class="flex items-center gap-3 px-4 py-3 flex-1">
                <i class="fas fa-search text-[#024DDF] text-xl"></i>
                <input type="text" id="global-search-input" autocomplete="off" placeholder="Search for artist, event presentation or venue..." class="bg-transparent outline-none w-full text-base font-medium">
            </div>
            <div class="hidden md:flex items-center gap-3 px-5 py-3 w-64">
                <i class="fas fa-map-marker-alt text-[#024DDF] text-xl"></i>
                <input type="text" readonly value="All Venues & Cities" class="bg-transparent outline-none w-full text-sm font-medium text-gray-600">
            </div>
        </div>
        
        <div id="search-results-dropdown" class="absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 hidden z-50 overflow-hidden max-h-96 overflow-y-auto text-gray-900"></div>
    </div>
</header>

<script>
document.getElementById('global-search-input').addEventListener('input', function() {
    const query = this.value.trim();
    const dropdown = document.getElementById('search-results-dropdown');
    
    if (query.length < 2) {
        dropdown.innerHTML = '';
        dropdown.classList.add('hidden');
        return;
    }
    
    fetch('search_api.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            dropdown.innerHTML = '';
            if (data.length === 0) {
                dropdown.innerHTML = '<div class="p-4 text-sm text-gray-500 text-center">No matches found</div>';
                dropdown.classList.remove('hidden');
                return;
            }
            
            data.forEach(item => {
                const element = document.createElement('div');
                element.className = 'p-4 border-b border-gray-50 flex items-center justify-between cursor-pointer transition-colors';
                
                let icon = 'fa-music';
                if(item.type === 'event') icon = 'fa-ticket-alt';
                if(item.type === 'venue') icon = 'fa-building';
                
                element.innerHTML = `
                    <div class="flex items-center gap-3">
                        <i class="fas ${icon} text-blue-600"></i>
                        <div>
                            <p class="font-semibold text-sm text-gray-900">${item.label}</p>
                            <p class="text-xs text-gray-400 capitalize">${item.type}</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                `;
                
                element.addEventListener('click', () => {
                    if (item.type === 'venue') {
                        window.location.href = 'event.php?type=venue&query=' + encodeURIComponent(item.label);
                    } else {
                        window.location.href = 'event.php?type=' + item.type + '&id=' + item.id;
                    }
                });
                dropdown.appendChild(element);
            });
            dropdown.classList.remove('hidden');
        });
});

// Structural UI Auto-Close Event Context
document.addEventListener('click', function(e) {
    if (e.target.id !== 'global-search-input') {
        document.getElementById('search-results-dropdown').classList.add('hidden');
    }
});
</script>
