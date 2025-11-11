document.addEventListener('DOMContentLoaded', () => {

    // Set Reason in Reject Modal
    document.querySelectorAll('.reason-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = btn.closest('.modal');
            const bookingId = modal.id.replace('rejectModal', '');
            const input = document.getElementById(`reasonInput${bookingId}`);
            input.value = btn.textContent.trim();

            // Highlight selected reason
            modal.querySelectorAll('.reason-btn').forEach(b => {
                b.classList.remove('bg-red-700', 'text-white');
                b.classList.add('bg-red-100', 'text-red-700');
            });
            btn.classList.add('bg-red-700', 'text-white');
            btn.classList.remove('bg-red-100', 'text-red-700');
        });
    });

    // Toggle Modals
    document.querySelectorAll('[data-modal-toggle]').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const modalId = toggle.getAttribute('data-modal-toggle');
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.toggle('hidden');
        });
    });

    // Prevent form buttons from triggering modal
    document.querySelectorAll('form button, .action-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Stop propagation for modal content (input, buttons, etc.)
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', e => {
                e.stopPropagation();
            });
        }
    });

});
