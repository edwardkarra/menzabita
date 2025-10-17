class CountryCodeSelector {
    constructor(containerId, defaultCode = '+963') {
        this.container = document.getElementById(containerId);
        this.defaultCode = defaultCode;
        this.selectedCountry = countryCodes.find(c => c.code === defaultCode);
        this.isOpen = false;
        this.filteredCountries = countryCodes;
        
        this.init();
    }
    
    init() {
        this.render();
        this.bindEvents();
    }
    
    render() {
        this.container.innerHTML = `
            <div class="country-selector relative">
                <button type="button" class="country-selector-button inline-flex items-center px-3 py-2 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span class="flag mr-2">${this.selectedCountry.flag}</span>
                    <span class="code">${this.selectedCountry.code}</span>
                    <svg class="w-4 h-4 ml-2 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div class="country-selector-dropdown absolute top-full left-0 z-50 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-hidden hidden w-full min-w-0 sm:right-0 sm:w-auto sm:min-w-64">
                    <div class="p-2 border-b border-gray-200">
                        <input type="text" class="search-input w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Search countries...">
                    </div>
                    <div class="countries-list max-h-48 overflow-y-auto">
                        ${this.renderCountriesList()}
                    </div>
                </div>
            </div>
        `;
    }
    
    renderCountriesList() {
        return this.filteredCountries.map(country => `
            <button type="button" class="country-option w-full px-3 py-2 text-left hover:bg-gray-100 focus:bg-gray-100 focus:outline-none flex items-center" data-code="${country.code}" data-name="${country.name}">
                <span class="flag mr-3">${country.flag}</span>
                <span class="name flex-1">${country.name}</span>
                <span class="code text-gray-500 text-sm">${country.code}</span>
            </button>
        `).join('');
    }
    
    bindEvents() {
        const button = this.container.querySelector('.country-selector-button');
        const dropdown = this.container.querySelector('.country-selector-dropdown');
        const searchInput = this.container.querySelector('.search-input');
        const countriesList = this.container.querySelector('.countries-list');
        
        // Toggle dropdown
        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleDropdown();
        });
        
        // Search functionality
        searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });
        
        // Country selection
        countriesList.addEventListener('click', (e) => {
            const option = e.target.closest('.country-option');
            if (option) {
                e.preventDefault();
                this.selectCountry(option.dataset.code, option.dataset.name);
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.closeDropdown();
            }
        });
        
        // Keyboard navigation
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeDropdown();
            }
        });
    }
    
    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        const dropdown = this.container.querySelector('.country-selector-dropdown');
        const searchInput = this.container.querySelector('.search-input');
        const arrow = this.container.querySelector('svg');
        
        // Adjust dropdown positioning for mobile
        this.adjustDropdownPosition(dropdown);
        
        dropdown.classList.remove('hidden');
        arrow.style.transform = 'rotate(180deg)';
        this.isOpen = true;
        
        // Focus search input
        setTimeout(() => searchInput.focus(), 100);
    }
    
    closeDropdown() {
        const dropdown = this.container.querySelector('.country-selector-dropdown');
        const searchInput = this.container.querySelector('.search-input');
        const arrow = this.container.querySelector('svg');
        
        dropdown.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
        this.isOpen = false;
        
        // Clear search
        searchInput.value = '';
        this.filteredCountries = countryCodes;
        this.updateCountriesList();
    }
    
    handleSearch(query) {
        this.filteredCountries = searchCountries(query);
        this.updateCountriesList();
    }
    
    updateCountriesList() {
        const countriesList = this.container.querySelector('.countries-list');
        countriesList.innerHTML = this.renderCountriesList();
    }
    
    selectCountry(code, name) {
        const country = countryCodes.find(c => c.code === code && c.name === name);
        if (country) {
            this.selectedCountry = country;
            this.updateButton();
            this.closeDropdown();
            
            // Dispatch custom event for country selection
            document.dispatchEvent(new CustomEvent('countrySelected', {
                detail: { country: country }
            }));
        }
    }
    
    updateButton() {
        const flag = this.container.querySelector('.flag');
        const codeSpan = this.container.querySelector('.code');
        
        flag.textContent = this.selectedCountry.flag;
        codeSpan.textContent = this.selectedCountry.code;
    }
    
    adjustDropdownPosition(dropdown) {
        // Get viewport width
        const viewportWidth = window.innerWidth;
        const containerRect = this.container.getBoundingClientRect();
        
        // On mobile (screens smaller than 640px), ensure dropdown doesn't exceed screen bounds
        if (viewportWidth < 640) {
            const rightEdge = containerRect.right;
            const leftEdge = containerRect.left;
            
            // If dropdown would extend beyond right edge, adjust positioning
            if (rightEdge > viewportWidth - 20) {
                dropdown.style.right = '0';
                dropdown.style.left = 'auto';
                dropdown.style.width = Math.min(350, viewportWidth - 40) + 'px';
            } else if (leftEdge < 20) {
                // If too close to left edge, adjust
                dropdown.style.left = '0';
                dropdown.style.right = 'auto';
                dropdown.style.width = Math.min(350, viewportWidth - 40) + 'px';
            } else {
                // Default mobile positioning
                dropdown.style.left = '0';
                dropdown.style.right = 'auto';
                dropdown.style.width = Math.min(350, Math.max(280, containerRect.width * 1.5)) + 'px';
            }
        } else {
            // Desktop: set a wider minimum width
            dropdown.style.left = '';
            dropdown.style.right = '';
            dropdown.style.width = '320px';
            dropdown.style.minWidth = '320px';
        }
    }
    
    getSelectedCountryCode() {
        return this.selectedCountry ? this.selectedCountry.code : '+963';
    }
    
    getSelectedCountry() {
        return this.selectedCountry;
    }
    
    setSelectedCountry(code) {
        const country = countryCodes.find(c => c.code === code);
        if (country) {
            this.selectedCountry = country;
            this.updateButton();
        }
    }
}