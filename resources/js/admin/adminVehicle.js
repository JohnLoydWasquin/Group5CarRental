const searchInput = document.getElementById('searchVehicle');
const tableBody = document.getElementById('vehicleTableBody');

searchInput.addEventListener('keyup', function() {
    const query = this.value.trim().toLowerCase();
    const rows = tableBody.getElementsByTagName('tr');

    Array.from(rows).forEach(row => {
        const rowText = row.textContent.toLowerCase();
        const words = query.split(/\s+/);
        const isMatch = words.every(word => rowText.includes(word));
        row.style.display = isMatch ? '' : 'none';
    });
});

    const editModal = document.getElementById('editModal');
    const closeModal = document.getElementById('closeModal');
    const editForm = document.getElementById('editForm');

    document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;
        editForm.action = `/admin/vehicles/${id}`;

        document.getElementById('editPlateNo').value = button.dataset.plate;
        document.getElementById('editBrand').value = button.dataset.brand;
        document.getElementById('editModel').value = button.dataset.model;
        document.getElementById('editDailyPrice').value = button.dataset.price;
        document.getElementById('editAvailability').value = button.dataset.availability;
        document.getElementById('editCondition').value = button.dataset.condition;

        editModal.classList.remove('hidden');
    });
});

    closeModal.addEventListener('click', () => editModal.classList.add('hidden'));

    const addModal = document.getElementById('addVehicleModal');
    const addVehicleBtn = document.getElementById('addVehicleBtn');
    const closeAddModal = document.getElementById('closeAddModal');

    addVehicleBtn.addEventListener('click', () => {
        addModal.classList.remove('hidden');
    });

    closeAddModal.addEventListener('click', () => {
        addModal.classList.add('hidden');
    });

    // Image Preview
    const addImageInput = document.getElementById('addImage');
    const addImagePreview = document.getElementById('addImagePreview');

    addImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                addImagePreview.src = e.target.result;
                addImagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

editBtn.addEventListener('click', function() {
    document.getElementById('editPassengers').value = this.dataset.passengers ?? 4;
});


