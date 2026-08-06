document.querySelectorAll('.btn-remove').forEach((btn) => {
    btn.addEventListener('click', function () {
        const el = this;
        el.disabled = true;
        el.textContent = 'Removing…';
        fetch(window.__RC?.basePath + '/remove-from-wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ index: parseInt(el.dataset.index) })
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.success) location.reload();
                else { el.disabled = false; el.textContent = 'Remove'; }
            })
            .catch(() => { el.disabled = false; el.textContent = 'Remove'; });
    });
});
