document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchCustomer');
    const tableBody = document.getElementById('customerTableBody');

    function formatDate(dateString) {
        const date = dateString.split('T')[0];
        const [year, month, day] = date.split('-');

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        return `${monthNames[parseInt(month)-1]} ${day}, ${year}`;
    }

    searchInput.addEventListener('input', function () {
        const query = this.value;

        fetch(`/admin/customers/search?query=${query}`)
            .then(res => res.json())
            .then(customers => {
                tableBody.innerHTML = '';

                customers.forEach(customer => {
                    const statusClass = customer.status === 'active'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700';

                    tableBody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">${customer.name}</td>
                            <td class="p-3 border-b">${customer.email}</td>
                            <td class="p-3 border-b">${customer.phone ?? 'N/A'}</td>
                            <td class="p-3 border-b text-center">${customer.bookings_count}</td>
                            <td class="p-3 border-b">
                                <span class="px-2 py-1 rounded text-sm ${statusClass}">
                                    ${customer.status ? customer.status.charAt(0).toUpperCase() + customer.status.slice(1) : 'Inactive'}
                                </span>
                            </td>
                            <td class="p-3 border-b">${formatDate(customer.created_at)}</td>
                            <td class="p-3 border-b text-center">
                                <button class="text-blue-600 hover:underline">View</button>
                                <button class="text-red-600 hover:underline ml-2">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            });
    });
});
