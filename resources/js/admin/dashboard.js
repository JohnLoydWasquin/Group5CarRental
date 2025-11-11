document.addEventListener("DOMContentLoaded", function() {
    const dashboardData = document.getElementById('dashboardData');

    const revenueData = JSON.parse(dashboardData.dataset.revenue);

    const revenueLabels = revenueData.map(item => item.month);
    const revenueTotals = revenueData.map(item => item.total);

    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue',
                data: revenueTotals,
                borderColor: 'rgba(34,197,94,1)',
                backgroundColor: 'rgba(34,197,94,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: true, text: 'Revenue Trends' }
            },
            scales: {
                y: {
                    stacked: true
                },
                x: {
                    stacked: true
                }
            }
        }
    });

    // Booking status chart
    const statusData = JSON.parse(dashboardData.dataset.status);
    const statusLabels = statusData.map(item => item.booking_status);
    const statusCounts = statusData.map(item => item.count);

    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: [
                    '#FBBF24', // Pending Approval
                    '#6366F1', // Payment Submitted
                    '#10B981', // Confirmed
                    '#3B82F6', // Ongoing
                    '#9CA3AF', // Completed
                    '#EF4444', // Cancelled/Rejected
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
