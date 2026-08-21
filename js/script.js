document.addEventListener('DOMContentLoaded', function () {

    const doctorGrid = document.getElementById('doctorGrid');
    const searchInput = document.getElementById('searchInput');
    const specializationFilter = document.getElementById('specializationFilter');
    const resetBtn = document.getElementById('resetFilters');
    const template = document.getElementById('doctorCardTemplate');

    loadDoctors();

    let debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadDoctors, 300);
    });

    specializationFilter.addEventListener('change', loadDoctors);

    resetBtn.addEventListener('click', function () {
        searchInput.value = '';
        specializationFilter.value = '';
        loadDoctors();
    });

    function loadDoctors() {
        const search = encodeURIComponent(searchInput.value.trim());
        const spec = encodeURIComponent(specializationFilter.value);

        doctorGrid.innerHTML = '<p class="loading-text">Loading doctors...</p>';

        fetch(`search.php?search=${search}&specialization=${spec}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(doctors => renderDoctors(doctors))
            .catch(err => {
                doctorGrid.innerHTML = '<p class="no-results">Could not load doctors. Please try again.</p>';
                console.error(err);
            });
    }

    function renderDoctors(doctors) {
        doctorGrid.innerHTML = '';

        if (doctors.length === 0) {
            doctorGrid.innerHTML = '<p class="no-results">No doctors match your search.</p>';
            return;
        }

        doctors.forEach(doc => {
            const card = template.content.cloneNode(true);

            card.querySelector('.doctor-img').src = 'uploads/' + doc.profile_image;
            card.querySelector('.doctor-img').alt = doc.name;
            card.querySelector('.doctor-name').textContent = doc.name;
            card.querySelector('.doctor-spec').textContent = doc.specialization;
            card.querySelector('.doctor-days span').textContent = doc.available_days;
            card.querySelector('.doctor-times span').textContent = doc.available_times;
            card.querySelector('.doctor-fee span').textContent = Number(doc.channeling_fee).toFixed(2);
            card.querySelector('.book-btn').href = 'booking.php?doctor_id=' + doc.doctor_id;

            doctorGrid.appendChild(card);
        });
    }
});