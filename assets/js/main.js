document.addEventListener('DOMContentLoaded', function () {
    const eventSearch = document.getElementById('eventSearch');
    const eventCategory = document.getElementById('eventCategory');
    const cards = document.querySelectorAll('.event-card');

    if (eventSearch && eventCategory) {
        const applyFilter = () => {
            const searchValue = eventSearch.value.trim().toLowerCase();
            const categoryValue = eventCategory.value.trim().toLowerCase();

            cards.forEach(card => {
                const title = card.dataset.title.toLowerCase();
                const category = card.dataset.category.toLowerCase();
                const matchesSearch = title.includes(searchValue);
                const matchesCategory = categoryValue === '' || category === categoryValue;
                card.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        };

        eventSearch.addEventListener('input', applyFilter);
        eventCategory.addEventListener('change', applyFilter);
    }

    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function (event) {
            let valid = true;
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const message = document.getElementById('message');

            [name, email, message].forEach((field) => {
                field.classList.remove('is-invalid');
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                }
            });

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value && !emailPattern.test(email.value)) {
                email.classList.add('is-invalid');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }

    const shareButton = document.getElementById('shareEvent');
    if (shareButton) {
        shareButton.addEventListener('click', async function () {
            const shareData = {
                title: document.title,
                text: 'استكشف هذه الفعالية معنا',
                url: window.location.href,
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    window.prompt('انسخ الرابط', window.location.href);
                }
            } catch (error) {
                console.error(error);
            }
        });
    }
});
