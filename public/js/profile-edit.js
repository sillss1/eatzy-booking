document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-account-edit');
    const editFields = document.getElementById('account-edit-fields');
    const form = document.getElementById('profile-form');

    if (toggleBtn && editFields) {
        toggleBtn.addEventListener('click', () => {
            editFields.classList.toggle('show');
        });
    }

    if (!form) return;

    function showError(input, message) {
        let errorEl = input.nextElementSibling;
        if (!errorEl || !errorEl.classList.contains('error-message')) {
            errorEl = document.createElement('div');
            errorEl.classList.add('error-message');
            errorEl.style.color = 'red';
            input.parentNode.insertBefore(errorEl, input.nextSibling);
        }
        errorEl.textContent = message;
    }

    function clearError(input) {
        const errorEl = input.nextElementSibling;
        if (errorEl && errorEl.classList.contains('error-message')) {
            errorEl.textContent = '';
        }
    }

    const validators = {
        name: value => {
            if (!value) return "Name is required.";
            if (!/^[A-Za-z\s]+$/.test(value)) return "Name can only contain letters and spaces.";
        },
        surname: value => {
            if (!value) return "Surname is required.";
            if (!/^[A-Za-z\s]+$/.test(value)) return "Surname can only contain letters and spaces.";
        },
        username: value => {
            if (!value) return "Username is required.";
            if (!/^[A-Za-z0-9_]+$/.test(value)) return "Username can only contain letters, numbers, and underscores.";
        },
        email: value => {
            if (!value) return "Email is required.";
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return "Email must be valid.";
        },
        profile_description: value => {
            if (value.length > 500) return "Description cannot exceed 500 characters.";
        },
        profile_picture: file => {
            if (!file) return;
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) return "Profile picture must be JPG, PNG, or GIF.";
            if (file.size > 2 * 1024 * 1024) return "Profile picture must be less than 2MB.";
        }
    };

    const fields = form.querySelectorAll('input[name], textarea[name]');
    fields.forEach(field => {
        const type = field.name;
        if (!validators[type]) return;

        const eventType = type === 'profile_picture' ? 'change' : 'input';
        field.addEventListener(eventType, () => {
            let error;
            if (type === 'profile_picture') {
                error = validators[type](field.files[0]);
            } else {
                error = validators[type](field.value.trim());
            }

            if (error) showError(field, error);
            else clearError(field);
        });
    });

    form.addEventListener('submit', function(e) {
        let hasErrors = false;

        fields.forEach(field => {
            const type = field.name;
            if (!validators[type]) return;

            let error;
            if (type === 'profile_picture') {
                error = validators[type](field.files[0]);
            } else {
                error = validators[type](field.value.trim());
            }

            if (error) {
                showError(field, error);
                hasErrors = true;
            } else {
                clearError(field);
            }
        });

        if (hasErrors) e.preventDefault();
    });
});
