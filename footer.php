<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    //Toggle tema dan disimpan cookie selama 30 hari
    const temaSwitch = document.getElementById('temaSwitch');
    const temaIcon = document.getElementById('temaIcon');

    if (temaSwitch) {
        temaSwitch.addEventListener('change', function () {
            let tema = 'light';
            if (temaIcon) {
                temaIcon.className = 'bi bi-moon-stars-fill'; // terang
            }
            if (temaSwitch.checked) {
                tema = 'dark';
                if (temaIcon) {
                    temaIcon.className = 'bi bi-sun-fill'; // gelap
                }
            }
            document.documentElement.setAttribute('data-bs-theme', tema);
            document.cookie = 'tema=' + tema + '; max-age=2592000; path=/';
        });
    }
</script>