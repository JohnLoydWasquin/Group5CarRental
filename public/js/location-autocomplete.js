document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const token = csrfMeta ? csrfMeta.content : '';

    const searchUrlMeta = document.querySelector('meta[name="locations-search-url"]');
    const searchUrl = searchUrlMeta ? searchUrlMeta.content : null;

    if (!searchUrl) {
        console.error('Locations search URL not found.');
        return;
    }

    document.querySelectorAll('.js-location-autocomplete').forEach(function (input) {
        let dropdown = null;

        input.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length < 2) {
                closeDropdown();
                return;
            }

            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(res => res.json())
                .then(data => {
                    console.log('Locations result:', data); 
                    showDropdown(this, data);
                })
                .catch(err => console.error('Locations search error:', err));
        });

        input.addEventListener('blur', function () {
            setTimeout(closeDropdown, 200);
        });

        function showDropdown(inputEl, items) {
            closeDropdown();

            if (!items.length) return;

            dropdown = document.createElement('div');
            dropdown.className = 'list-group position-absolute w-100 shadow-sm bg-white';
            dropdown.style.zIndex = 9999;
            dropdown.style.maxHeight = '220px';
            dropdown.style.overflowY = 'auto';

            items.forEach(function (item) {
                const div = document.createElement('button');
                div.type = 'button';
                div.className = 'list-group-item list-group-item-action small text-start';

                let label = item.name;
                if (item.type === 'airport' && item.code) {
                    label += ` (${item.code})`;
                } else if (item.province) {
                    label += `, ${item.province}`;
                }

                div.textContent = label;

                div.addEventListener('click', function () {
                    inputEl.value = label;
                    closeDropdown();
                });

                dropdown.appendChild(div);
            });

            // parent needs to be relative to position dropdown correctly
            const parent = inputEl.parentElement;
            if (getComputedStyle(parent).position === 'static') {
                parent.style.position = 'relative';
            }
            parent.appendChild(dropdown);
        }

        function closeDropdown() {
            if (dropdown && dropdown.parentNode) {
                dropdown.parentNode.removeChild(dropdown);
                dropdown = null;
            }
        }
    });
});