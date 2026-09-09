/**
 * Dex Trade Global Toast & Real-Time Form Validation Helper
 */

// Global Toast Notification Function
function showToast(title, message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-msg toast-${type}`;
    
    let iconSvg = '';
    if (type === 'success') {
        iconSvg = `<svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`;
    } else if (type === 'error') {
        iconSvg = `<svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
    } else {
        iconSvg = `<svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
    }

    toast.innerHTML = `
        ${iconSvg}
        <div class="flex-1">
            <p class="font-bold text-amber-300 text-xs uppercase tracking-wider">${title}</p>
            <p class="text-neutral-200 text-xs mt-0.5">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-neutral-400 hover:text-white font-bold">&times;</button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastOut 0.35s ease forwards';
        setTimeout(() => toast.remove(), 350);
    }, 4500);
}

// Real-Time Input Validation Handler
document.addEventListener("DOMContentLoaded", function() {
    const inputs = document.querySelectorAll("form input, form select");

    inputs.forEach(input => {
        ['input', 'blur'].forEach(eventType => {
            input.addEventListener(eventType, function() {
                validateField(input);
            });
        });
    });

    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", function(e) {
            let isValid = true;
            const formInputs = form.querySelectorAll("input[required], select[required]");

            formInputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast("Validation Error", "Please correct the highlighted fields before submitting.", "error");
            }
        });
    });
});

function validateField(input) {
    if (!input || input.type === 'hidden' || input.type === 'submit' || input.type === 'checkbox') return true;

    const val = input.value.trim();
    let errorMsg = "";
    let isValid = true;

    // Required Check
    if (input.hasAttribute("required") && val === "") {
        isValid = false;
        errorMsg = "This field is required.";
    } 
    // Email Check
    else if (input.type === "email" && val !== "") {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(val)) {
            isValid = false;
            errorMsg = "Please enter a valid email address.";
        }
    } 
    // Password Min Length Check
    else if (input.type === "password" && input.name === "password" && val !== "") {
        if (val.length < 6) {
            isValid = false;
            errorMsg = "Password must be at least 6 characters.";
        }
    }
    // Confirm Password Check
    else if (input.name === "password_confirmation" && val !== "") {
        const passInput = input.form ? input.form.querySelector('input[name="password"]') : null;
        if (passInput && val !== passInput.value) {
            isValid = false;
            errorMsg = "Passwords do not match.";
        }
    }
    // Mobile Check
    else if (input.name === "mobile" && val !== "") {
        if (val.length < 8) {
            isValid = false;
            errorMsg = "Enter a valid mobile phone number.";
        }
    }

    // UI Helper Feedback
    let errorContainer = input.parentElement.querySelector(".input-error-msg");
    if (!isValid) {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");

        if (!errorContainer) {
            errorContainer = document.createElement("span");
            errorContainer.className = "input-error-msg";
            input.parentElement.appendChild(errorContainer);
        }
        errorContainer.innerText = errorMsg;
    } else if (val !== "") {
        input.classList.remove("is-invalid");
        input.classList.add("is-valid");
        if (errorContainer) errorContainer.remove();
    } else {
        input.classList.remove("is-invalid", "is-valid");
        if (errorContainer) errorContainer.remove();
    }

    return isValid;
}
