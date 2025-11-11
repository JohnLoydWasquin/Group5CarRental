document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const container = document.getElementById('vehicleData');
    if (!container) return;
    const vehicles = JSON.parse(container.dataset.vehicles);
    const securityDeposit = 3000;

    function formatCurrency(value) {
        return `₱${Number(value).toLocaleString()}`;
    }

    const today = new Date().toISOString().split('T')[0];

    // Calculate totals
    function calculateTotals(vehicle) {
        const id = vehicle.VehicleID;
        const pickupDate = document.getElementById(`pickupDate${id}`);
        const pickupTime = document.getElementById(`pickupTime${id}`);
        const returnDate = document.getElementById(`returnDate${id}`);
        const returnTime = document.getElementById(`returnTime${id}`);
        if (!pickupDate || !pickupTime || !returnDate || !returnTime) return;

        const durationCost = document.getElementById(`durationCost${id}`);
        const addonsTotal = document.getElementById(`addonsTotal${id}`);
        const subtotal = document.getElementById(`subtotal${id}`);
        const grandTotal = document.getElementById(`grandTotal${id}`);

        const driver = document.getElementById(`driver${id}`);
        const childSeat = document.getElementById(`childSeat${id}`);
        const insurance = document.getElementById(`insurance${id}`);

        if (!pickupDate.value || !pickupTime.value || !returnDate.value || !returnTime.value) return;

        const start = new Date(`${pickupDate.value}T${pickupTime.value}`);
        const end = new Date(`${returnDate.value}T${returnTime.value}`);

        if (end < start) {
            durationCost.textContent = addonsTotal.textContent = subtotal.textContent = grandTotal.textContent = "₱0";
            return;
        }

        const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        const duration = parseFloat(vehicle.DailyPrice) * days;

        let addonsCost = 0;
        if (driver && driver.checked) addonsCost += 500 * days;
        if (childSeat && childSeat.checked) addonsCost += 200 * days;
        if (insurance && insurance.checked) addonsCost += 300;

        const subtotalAmount = duration + addonsCost;
        const grand = subtotalAmount + securityDeposit;

        durationCost.textContent = formatCurrency(duration);
        addonsTotal.textContent = formatCurrency(addonsCost);
        subtotal.textContent = formatCurrency(subtotalAmount);
        grandTotal.textContent = formatCurrency(grand);
        grandTotal.dataset.total = grand;
    }

    // Setup vehicle inputs
    vehicles.forEach(vehicle => {
        const id = vehicle.VehicleID;

        ['pickupDate', 'returnDate'].forEach(el => {
            const e = document.getElementById(`${el}${id}`);
            if (e) e.setAttribute('min', today);
        });

        ['pickupDate', 'pickupTime', 'returnDate', 'returnTime', 'driver', 'childSeat', 'insurance']
        .forEach(el => {
            const element = document.getElementById(`${el}${id}`);
            if (element) element.addEventListener("change", () => calculateTotals(vehicle));
        });

        const locationType = document.getElementById(`locationType${id}`);
        if (locationType) {
            locationType.addEventListener('change', () => {
                const type = locationType.value;
                document.getElementById(`pickupOnly${id}`).style.display = type==='different'?'none':'block';
                document.getElementById(`pickupDifferent${id}`).style.display = type==='different'?'block':'none';
                document.getElementById(`dropoffDifferent${id}`).style.display = type==='different'?'block':'none';
            });
        }
    });

    // Booking button click (prevent duplicate bindings)
document.querySelectorAll('.confirmBookingBtn').forEach(btn => {
    // Remove any existing listener first
    btn.replaceWith(btn.cloneNode(true));
});

// Re-select the freshly cloned buttons (now clean)
document.querySelectorAll('.confirmBookingBtn').forEach(btn => {
    btn.addEventListener('click', async function (event) {
        event.preventDefault();
        event.stopImmediatePropagation(); // ✅ prevent double fire
        btn.disabled = true; // block multiple clicks

        try {
            const vehicleId = this.dataset.vehicleId;
            const data = await prepareBooking(vehicleId);
            if (!data) return;
        } finally {
            setTimeout(() => btn.disabled = false, 1500); // re-enable after delay
        }
    });
});

    async function prepareBooking(vehicleId) {
    const vehicle = vehicles.find(v => v.VehicleID == vehicleId);
    if (!vehicle) return;

    // --- Pickup / Drop-off locations ---
    const locationTypeEl = document.getElementById(`locationType${vehicleId}`);
    const locationType = locationTypeEl ? locationTypeEl.value : 'same';

    let pickupLoc = '', dropoffLoc = '';
    if (locationType === 'different') {
        pickupLoc = document.getElementById(`pickupLocationDiff${vehicleId}`)?.value.trim() || '';
        dropoffLoc = document.getElementById(`dropoffLocationDiff${vehicleId}`)?.value.trim() || '';
    } else {
        pickupLoc = document.getElementById(`pickupLocation${vehicleId}`)?.value.trim() || '';
        dropoffLoc = pickupLoc;
    }

    if (!pickupLoc || !dropoffLoc) {
        return Swal.fire({
            icon: 'warning',
            title: 'Incomplete Details',
            text: 'Please fill your pickup and drop-off locations.'
        });
    }

    // --- Pickup / Return dates and times ---
    const pickupDate = document.getElementById(`pickupDate${vehicleId}`)?.value;
    const pickupTime = document.getElementById(`pickupTime${vehicleId}`)?.value;
    const returnDate = document.getElementById(`returnDate${vehicleId}`)?.value;
    const returnTime = document.getElementById(`returnTime${vehicleId}`)?.value;

    if (!pickupDate || !pickupTime || !returnDate || !returnTime) {
        return Swal.fire({
            icon: 'warning',
            title: 'Incomplete Details',
            text: 'Please fill your pickup and return dates & times.'
        });
    }

    const start = new Date(`${pickupDate}T${pickupTime}`);
    const end = new Date(`${returnDate}T${returnTime}`);
    const days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));

    // --- Set hidden inputs for form submission ---
    document.getElementById(`pickupLocationInput${vehicleId}`).value = pickupLoc;
    document.getElementById(`dropoffLocationInput${vehicleId}`).value = dropoffLoc;
    document.getElementById(`pickupDateTime${vehicleId}`).value = `${pickupDate} ${pickupTime}`;
    document.getElementById(`returnDateTime${vehicleId}`).value = `${returnDate} ${returnTime}`;

    // --- Collect Add-ons ---
    const container = document.getElementById(`addonsContainer${vehicleId}`);
    if (!container) return Swal.fire({icon:'error', title:'Error', text:'Add-ons container not found.'});
    container.innerHTML = '';

    const addons = ['driver','childSeat','insurance'];
    addons.forEach(a => {
        const el = document.getElementById(`${a}${vehicleId}`);
        if (el && el.checked) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'addons[]';
            input.value = a;
            container.appendChild(input);
        }
    });

    const driverEl = document.getElementById(`driver${vehicleId}`);
    const childSeatEl = document.getElementById(`childSeat${vehicleId}`);
    const insuranceEl = document.getElementById(`insurance${vehicleId}`);
    const securityDeposit = 3000;

    // --- Calculate totals ---
    const basePrice = parseFloat(vehicle.DailyPrice) * days;
    const driverPrice = driverEl && driverEl.checked ? 500 * days : 0;
    const childSeatPrice = childSeatEl && childSeatEl.checked ? 200 * days : 0;
    const insurancePrice = insuranceEl && insuranceEl.checked ? 300 : 0;
    const totalAmount = basePrice + driverPrice + childSeatPrice + insurancePrice + securityDeposit;

    // --- Booking form ---
    const bookingForm = document.querySelector(`.bookingForm[data-vehicle-id="${vehicleId}"]`);
    if (!bookingForm) return Swal.fire({icon:'error', title:'Error', text:'Booking form not found.'});

    try {
        const res = await fetch(bookingForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(bookingForm)
        });

        if (res.status === 401) {
            return Swal.fire({
                icon: 'warning',
                title: 'Please Login First',
                text: 'You need to login before booking a vehicle.'
            }).then(() => window.location.href = '/login');
        }

        const data = await res.json().catch(() => null);
        if (!data || !data.success) {
            return Swal.fire({
                icon: 'error',
                title: 'Booking Failed',
                text: data?.message || 'Something went wrong.'
            });
        }

        // --- Update Payment Modal ---
        const paymentModalEl = document.getElementById(`paymentModal${vehicleId}`);
        const bookingModalEl = document.getElementById(`bookModal${vehicleId}`);
        if (paymentModalEl) {
            paymentModalEl.querySelector(`#vehicleName${vehicleId}`).textContent = data.vehicleName || '';
            paymentModalEl.querySelector(`#pickupText${vehicleId}`).textContent = `${pickupDate} at ${pickupTime}`;
            paymentModalEl.querySelector(`#returnText${vehicleId}`).textContent = `${returnDate} at ${returnTime}`;

            paymentModalEl.querySelector(`#basePrice${vehicleId}`).textContent = formatCurrency(basePrice);
            paymentModalEl.querySelector(`#driverPrice${vehicleId}`).textContent = formatCurrency(driverPrice);
            paymentModalEl.querySelector(`#childSeatPrice${vehicleId}`).textContent = formatCurrency(childSeatPrice);
            paymentModalEl.querySelector(`#insurancePrice${vehicleId}`).textContent = formatCurrency(insurancePrice);
            paymentModalEl.querySelector(`#gcashTotalText${vehicleId}`).textContent = formatCurrency(totalAmount);
            paymentModalEl.querySelector(`#paymentTotal${vehicleId}`).value = totalAmount;
            paymentModalEl.querySelector(`#bookingId${vehicleId}`).value = data.booking_id;

            // Optional: show selected add-ons
            const addonsListEl = paymentModalEl.querySelector(`#addonsList${vehicleId}`);
            if (addonsListEl) {
                const selectedAddons = [];
                if (driverEl && driverEl.checked) selectedAddons.push("Driver");
                if (childSeatEl && childSeatEl.checked) selectedAddons.push("Child Seat");
                if (insuranceEl && insuranceEl.checked) selectedAddons.push("Insurance");
                addonsListEl.textContent = selectedAddons.length ? selectedAddons.join(", ") : "-";
            }

            if (bookingModalEl) bootstrap.Modal.getInstance(bookingModalEl)?.hide();
            bootstrap.Modal.getOrCreateInstance(paymentModalEl).show();
        }

        // --- Success notification ---
        Swal.fire({
            icon: 'success',
            title: 'Booking Created!',
            text: 'Please proceed to payment.',
            timer: 1500,
            showConfirmButton: false
        });

        return data;

    } catch (err) {
        console.error(err);
        Swal.fire({icon:'error', title:'Booking Error', text:'Something went wrong. Please try again.'});
    }
}
});