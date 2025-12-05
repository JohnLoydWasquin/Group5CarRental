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

    // Calculate totals (BOOKING MODAL)
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

    // ⭐ NEW: Calculate totals (RESERVE MODAL)
    function calculateReserveTotals(vehicle) {
        const id = vehicle.VehicleID;
        const reserveForm = document.querySelector(`.reserveForm[data-vehicle-id="${id}"]`);
        if (!reserveForm) return;

        const pickupDateEl = reserveForm.querySelector('.reserve-pickup-date');
        const pickupTimeEl = reserveForm.querySelector('.reserve-pickup-time');
        const returnDateEl = reserveForm.querySelector('.reserve-return-date');
        const returnTimeEl = reserveForm.querySelector('.reserve-return-time');

        if (!pickupDateEl || !pickupTimeEl || !returnDateEl || !returnTimeEl) return;
        if (!pickupDateEl.value || !pickupTimeEl.value || !returnDateEl.value || !returnTimeEl.value) return;

        const durationCost = reserveForm.querySelector('.reserve-duration');
        const addonsTotal = reserveForm.querySelector('.reserve-addons');
        const subtotal = reserveForm.querySelector('.reserve-subtotal');
        const grandTotal = reserveForm.querySelector('.reserve-grandtotal');

        const driver = reserveForm.querySelector('#driver' + id);
        const childSeat = reserveForm.querySelector('#childSeat' + id);
        const insurance = reserveForm.querySelector('#insurance' + id);

        const start = new Date(`${pickupDateEl.value}T${pickupTimeEl.value}`);
        const end = new Date(`${returnDateEl.value}T${returnTimeEl.value}`);

        if (end < start) {
            if (durationCost && addonsTotal && subtotal && grandTotal) {
                durationCost.textContent = addonsTotal.textContent = subtotal.textContent = grandTotal.textContent = "₱0";
            }
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

        if (durationCost) durationCost.textContent = formatCurrency(duration);
        if (addonsTotal) addonsTotal.textContent = formatCurrency(addonsCost);
        if (subtotal) subtotal.textContent = formatCurrency(subtotalAmount);
        if (grandTotal) {
            grandTotal.textContent = formatCurrency(grand);
            grandTotal.dataset.total = grand;
        }
    }

    // Setup vehicle inputs
    vehicles.forEach(vehicle => {
        const id = vehicle.VehicleID;

        // BOOKING: min date
        ['pickupDate', 'returnDate'].forEach(el => {
            const e = document.getElementById(`${el}${id}`);
            if (e) e.setAttribute('min', today);
        });

            // If the car is currently rented, set a nicer default min for pickup
    if (vehicle.current_booking_until) {
        const until = new Date(vehicle.current_booking_until);

        const pickupDateInput = document.getElementById(`pickupDate${id}`);
        if (pickupDateInput) {
            // allow booking from the day of "until" or after – adjust to your rules
            const y = until.getFullYear();
            const m = String(until.getMonth() + 1).padStart(2, '0');
            const d = String(until.getDate()).padStart(2, '0');
            const minDate = `${y}-${m}-${d}`;

            // Make sure min date is at least today
            pickupDateInput.min = minDate > today ? minDate : today;
        }
    }


        // BOOKING: listeners
        ['pickupDate', 'pickupTime', 'returnDate', 'returnTime', 'driver', 'childSeat', 'insurance']
            .forEach(el => {
                const element = document.getElementById(`${el}${id}`);
                if (element) element.addEventListener("change", () => calculateTotals(vehicle));
            });

        // BOOKING: same/different location
        const locationType = document.getElementById(`locationType${id}`);
        if (locationType) {
            locationType.addEventListener('change', () => {
                const type = locationType.value;
                document.getElementById(`pickupOnly${id}`).style.display = type === 'different' ? 'none' : 'block';
                document.getElementById(`pickupDifferent${id}`).style.display = type === 'different' ? 'block' : 'none';
                document.getElementById(`dropoffDifferent${id}`).style.display = type === 'different' ? 'block' : 'none';
            });
        }

        // ⭐ RESERVE: min date on reserve date fields
        const reserveForm = document.querySelector(`.reserveForm[data-vehicle-id="${id}"]`);
        if (reserveForm) {
            const pickupDateReserve = reserveForm.querySelector('.reserve-pickup-date');
            const returnDateReserve = reserveForm.querySelector('.reserve-return-date');
            if (pickupDateReserve) pickupDateReserve.setAttribute('min', today);
            if (returnDateReserve) returnDateReserve.setAttribute('min', today);
        }

        // ⭐ RESERVE: same/different location toggle
        const locationTypeReserve = document.getElementById(`locationTypeReserve${id}`);
        if (locationTypeReserve) {
            locationTypeReserve.addEventListener('change', () => {
                const type = locationTypeReserve.value;
                document.getElementById(`pickupOnlyReserve${id}`).style.display = type === 'different' ? 'none' : 'block';
                document.getElementById(`pickupDifferentReserve${id}`).style.display = type === 'different' ? 'block' : 'none';
                document.getElementById(`dropoffDifferentReserve${id}`).style.display = type === 'different' ? 'block' : 'none';
            });
        }

        // ⭐ RESERVE: listeners for totals
        if (reserveForm) {
            const reserveSelectors = [
                '.reserve-pickup-date',
                '.reserve-pickup-time',
                '.reserve-return-date',
                '.reserve-return-time',
                '.reserve-addon'
            ];

            reserveSelectors.forEach(sel => {
                const elements = reserveForm.querySelectorAll(sel);
                elements.forEach(el => {
                    el.addEventListener('change', () => calculateReserveTotals(vehicle));
                });
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

    // ⭐ Reservation button click handler
    document.querySelectorAll('.btn-confirm-reserve').forEach(btn => {
        btn.addEventListener('click', function (event) {
            event.preventDefault();

            const vehicleId = this.dataset.vehicleId;
            const form = document.querySelector(`.reserveForm[data-vehicle-id="${vehicleId}"]`);
            if (!form) {
                console.error('Reserve form not found for vehicle', vehicleId);
                return;
            }

            const vehicle = vehicles.find(v => v.VehicleID == vehicleId);
            if (!vehicle) return;

            // --- LOCATION (same / different) ---
            const locationTypeReserve = document.getElementById(`locationTypeReserve${vehicleId}`);
            let pickupLoc = '';
            let dropoffLoc = '';

            if (locationTypeReserve && locationTypeReserve.value === 'different') {
                pickupLoc = document.getElementById(`pickupLocationDiffReserve${vehicleId}`)?.value.trim() || '';
                dropoffLoc = document.getElementById(`dropoffLocationDiffReserve${vehicleId}`)?.value.trim() || '';
            } else {
                pickupLoc = document.getElementById(`pickupLocationReserve${vehicleId}`)?.value.trim() || '';
                dropoffLoc = pickupLoc;
            }

            if (!pickupLoc || !dropoffLoc) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Details',
                    text: 'Please fill your pickup and drop-off locations.'
                });
            }

            const pickupInput = form.querySelector('input[name="pickup_location"]');
            const dropoffHidden = document.getElementById(`dropoffLocationHidden${vehicleId}`);
            if (pickupInput) pickupInput.value = pickupLoc;
            if (dropoffHidden) dropoffHidden.value = dropoffLoc;

            // --- DATES/TIMES (using reserve-specific inputs) ---
            const pickupDateEl = form.querySelector('.reserve-pickup-date');
            const pickupTimeEl = form.querySelector('.reserve-pickup-time');
            const returnDateEl = form.querySelector('.reserve-return-date');
            const returnTimeEl = form.querySelector('.reserve-return-time');

            const pickupDate = pickupDateEl?.value;
            const pickupTime = pickupTimeEl?.value;
            const returnDate = returnDateEl?.value;
            const returnTime = returnTimeEl?.value;

            if (!pickupDate || !pickupTime || !returnDate || !returnTime) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Details',
                    text: 'Please fill your pickup and return dates & times.'
                });
            }

            const start = new Date(`${pickupDate}T${pickupTime}`);
            const end = new Date(`${returnDate}T${returnTime}`);

            if (end <= start) {
                return Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Dates',
                    text: 'Return date/time must be after pickup date/time.'
                });
            }

            const pickupHidden = form.querySelector('input[name="pickup_datetime"]');
            const returnHidden = form.querySelector('input[name="return_datetime"]');

            if (pickupHidden) pickupHidden.value = `${pickupDate} ${pickupTime}`;
            if (returnHidden) returnHidden.value = `${returnDate} ${returnTime}`;

            // --- ADDONS (build hidden addons[] like booking) ---
            const addonsContainerReserve = document.getElementById(`addonsContainerReserve${vehicleId}`);
            if (addonsContainerReserve) {
                addonsContainerReserve.innerHTML = '';
                const reserveAddons = form.querySelectorAll('.reserve-addon');
                reserveAddons.forEach(cb => {
                    if (cb.checked) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'addons[]';
                        hidden.value = cb.value;
                        addonsContainerReserve.appendChild(hidden);
                    }
                });
            }

            // Optionally recalc totals before submit
            calculateReserveTotals(vehicle);

            form.submit(); // send to BookingController@reserve
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
        if (!container) return Swal.fire({ icon: 'error', title: 'Error', text: 'Add-ons container not found.' });
        container.innerHTML = '';

        const addons = ['driver', 'childSeat', 'insurance'];
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
        if (!bookingForm) return Swal.fire({ icon: 'error', title: 'Error', text: 'Booking form not found.' });

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
            Swal.fire({ icon: 'error', title: 'Booking Error', text: 'Something went wrong. Please try again.' });
        }
    }
        // NEW: HANDLE "PAY NOW" FROM RESERVATIONS
    document.querySelectorAll('.payNowBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const vehicleId = this.dataset.vehicleId;
            const bookingId = this.dataset.bookingId;
            const total = this.dataset.total;
            const pickup = this.dataset.pickup;
            const ret = this.dataset.return;

            const paymentModalEl = document.getElementById(`paymentModal${vehicleId}`);
            if (!paymentModalEl) {
                console.error('Payment modal not found for vehicle', vehicleId);
                return;
            }

            // Update summary section
            const pickupText = paymentModalEl.querySelector(`#pickupText${vehicleId}`);
            const returnText = paymentModalEl.querySelector(`#returnText${vehicleId}`);
            const totalTextEl = paymentModalEl.querySelector(`#gcashTotalText${vehicleId}`);
            const paymentTotalInput = paymentModalEl.querySelector(`#paymentTotal${vehicleId}`);
            const bookingIdInput = paymentModalEl.querySelector(`#bookingId${vehicleId}`);

            if (pickupText) pickupText.textContent = pickup;
            if (returnText)  returnText.textContent  = ret;
            if (totalTextEl) totalTextEl.textContent = formatCurrency(total);
            if (paymentTotalInput) paymentTotalInput.value = total;
            if (bookingIdInput) bookingIdInput.value = bookingId;

            // Show modal
            bootstrap.Modal.getOrCreateInstance(paymentModalEl).show();
        });
    });

    document.querySelectorAll('.btn-require-kyc').forEach(btn => {
        btn.addEventListener('click', () => {
            const redirectUrl = btn.dataset.redirect || '/';

            Swal.fire({
                icon: 'warning',
                title: 'Verify Your Account',
                text: 'You must complete account verification before you can book or reserve a vehicle.',
                confirmButtonText: 'Go to Verification'
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = '/profile#verify';
                }
            });
        });
    });

    document.querySelectorAll('.btn-require-login').forEach(btn => {
        btn.addEventListener('click', () => {
            const redirectUrl = btn.dataset.redirect || '/login';

            Swal.fire({
                icon: 'info',
                title: 'Login Required',
                text: 'You need to login before booking or reserving a vehicle.',
                confirmButtonText: 'Go to Login'
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = redirectUrl;
                }
            });
        });
    });
});
