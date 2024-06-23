document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('th.sortable').forEach(function (header) {
        header.addEventListener('click', function () {
            const table = header.closest('table');
            const index = Array.prototype.indexOf.call(header.parentNode.children, header);
            const order = header.dataset.order === 'asc' ? 'desc' : 'asc';
            header.dataset.order = order;

            const rows = Array.from(table.querySelector('tbody').children);
            rows.sort(function (a, b) {
                const aText = a.children[index].innerText.trim();
                const bText = b.children[index].innerText.trim();
                return order === 'asc' ? aText.localeCompare(bText) : bText.localeCompare(aText);
            });

            rows.forEach(function (row) {
                table.querySelector('tbody').appendChild(row);
            });
        });
    });
});
