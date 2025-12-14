function showForm(formId){
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}
// untuk filter 
document.addEventListener('DOMContentLoaded', function() {
    // Filter dropdown functionality for book.php
    const filterBtn = document.getElementById('filter-btn');
    const filterDropdown = document.getElementById('filter-dropdown');

    if (filterBtn && filterDropdown) {
        filterBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            filterDropdown.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            if (!filterDropdown.contains(event.target))
                filterDropdown.classList.remove('active');
        });
    }
});
