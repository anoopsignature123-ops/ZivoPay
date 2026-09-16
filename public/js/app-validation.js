/**
 * Dex Trade / Zivo Pay Global Toast & Real-Time Form Validation Helper
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

// Function to update submit button state for a given form
function updateFormSubmitButtonState(form) {
    if (!form) return;

    // Find submit button(s) in form
    const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
    if (!submitBtns || submitBtns.length === 0) return;

    // Get all required inputs
    const requiredInputs = form.querySelectorAll("input[required], select[required], textarea[required]");
    
    let isAllValid = true;

    // 1. Check if required fields have values
    requiredInputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            if (!input.checked) {
                isAllValid = false;
            }
        } else {
            const val = input.value ? input.value.trim() : '';
            if (val === '') {
                isAllValid = false;
            }
        }
    });

    // 2. Also check if any input has active validation errors or invalid format
    const allInputs = form.querySelectorAll("input, select, textarea");
    allInputs.forEach(input => {
        if (input.classList.contains("is-invalid")) {
            isAllValid = false;
        }

        // Email regex check if value filled
        if (input.type === "email" && input.value && input.value.trim() !== "") {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value.trim())) {
                isAllValid = false;
            }
        }

        // Confirm password match check if value filled
        if (input.name === "password_confirmation" && input.value && input.value.trim() !== "") {
            const passInput = form.querySelector('input[name="password"]');
            if (passInput && input.value !== passInput.value) {
                isAllValid = false;
            }
        }
    });

    // 3. Update button disabled status and visual styles
    submitBtns.forEach(btn => {
        if (!isAllValid) {
            btn.disabled = true;
            btn.style.opacity = "0.5";
            btn.style.cursor = "not-allowed";
            btn.classList.add("opacity-50", "cursor-not-allowed");
        } else {
            btn.disabled = false;
            btn.style.opacity = "1";
            btn.style.cursor = "pointer";
            btn.classList.remove("opacity-50", "cursor-not-allowed");
        }
    });
}

// Real-Time Input Validation Handler
document.addEventListener("DOMContentLoaded", function() {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        // Initial button state check
        updateFormSubmitButtonState(form);

        // Attach real-time validation and button state listeners
        const formInputs = form.querySelectorAll("input, select, textarea");
        formInputs.forEach(input => {
            ['input', 'change', 'keyup', 'blur', 'click'].forEach(eventType => {
                input.addEventListener(eventType, function() {
                    validateField(input);
                    updateFormSubmitButtonState(form);
                });
            });
        });

        // Form submit listener
        form.addEventListener("submit", function(e) {
            let isValid = true;
            const formInputs = form.querySelectorAll("input[required], select[required], textarea[required]");

            formInputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast("Validation Error", "Please fill in all required fields accurately before submitting.", "error");
            }
        });
    });

    // Re-check after short delay to handle browser autofill & dynamic values
    setTimeout(function() {
        forms.forEach(form => updateFormSubmitButtonState(form));
    }, 300);
});

function validateField(input) {
    if (!input || input.type === 'hidden' || input.type === 'submit' || input.type === 'button') return true;

    const val = input.value ? input.value.trim() : '';
    let errorMsg = "";
    let isValid = true;

    // Required Check
    if (input.hasAttribute("required")) {
        if (input.type === 'checkbox' || input.type === 'radio') {
            if (!input.checked) {
                isValid = false;
                errorMsg = "This field is required.";
            }
        } else if (val === "") {
            isValid = false;
            errorMsg = "This field is required.";
        }
    }

    // Email Check
    if (isValid && input.type === "email" && val !== "") {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(val)) {
            isValid = false;
            errorMsg = "Please enter a valid email address.";
        }
    } 
    // Password Min Length Check
    else if (isValid && input.type === "password" && input.name === "password" && val !== "") {
        if (val.length < 6) {
            isValid = false;
            errorMsg = "Password must be at least 6 characters.";
        }
    }
    // Confirm Password Check
    else if (isValid && input.name === "password_confirmation" && val !== "") {
        const passInput = input.form ? input.form.querySelector('input[name="password"]') : null;
        if (passInput && val !== passInput.value) {
            isValid = false;
            errorMsg = "Passwords do not match.";
        }
    }
    // Mobile Check
    else if (isValid && input.name === "mobile" && val !== "") {
        if (val.length < 8) {
            isValid = false;
            errorMsg = "Enter a valid mobile phone number.";
        }
    }

    // UI Helper Feedback
    let errorContainer = input.parentElement ? input.parentElement.querySelector(".input-error-msg") : null;
    if (!isValid) {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");

        if (!errorContainer && input.parentElement && input.type !== 'checkbox' && input.type !== 'radio') {
            errorContainer = document.createElement("span");
            errorContainer.className = "input-error-msg";
            input.parentElement.appendChild(errorContainer);
        }
        if (errorContainer) errorContainer.innerText = errorMsg;
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
