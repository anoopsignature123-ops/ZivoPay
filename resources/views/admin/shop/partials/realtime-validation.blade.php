@push('scripts')
<script>
    (() => {
        const serverErrors = @json($errors->getMessages());

        document.querySelectorAll('[data-shop-form]').forEach((form) => {
            form.noValidate = true;

            const fields = [...form.querySelectorAll('input, select, textarea')]
                .filter((field) => !['hidden', 'checkbox', 'submit', 'button'].includes(field.type));

            const getFieldMessage = (field) => {
                if (field.dataset.edited === 'true') return '';

                const name = field.name.replace(/\[\]$/, '');
                const exact = serverErrors[name]?.[0];
                if (exact) return exact;

                const arrayPrefix = `${name}.`;
                const matchingKey = Object.keys(serverErrors).find((key) => key.startsWith(arrayPrefix));
                return matchingKey ? serverErrors[matchingKey][0] : '';
            };

            const feedbackFor = (field) => {
                const container = field.closest('[data-field]') || field.closest('label') || field.parentElement;
                let feedback = container.querySelector('[data-validation-feedback]');

                if (!feedback) {
                    feedback = document.createElement('p');
                    feedback.dataset.validationFeedback = '';
                    feedback.className = 'mt-1.5 hidden text-[11px] font-semibold';
                    container.append(feedback);
                }

                return feedback;
            };

            const setCrossFieldValidity = () => {
                fields.forEach((field) => field.setCustomValidity(''));

                const price = form.querySelector('[name="price"]');
                const mrp = form.querySelector('[name="mrp"]');
                if (price && mrp && price.value && mrp.value && Number(mrp.value) < Number(price.value)) {
                    mrp.setCustomValidity('MRP must be equal to or higher than the selling price.');
                }

                const discountType = form.querySelector('[name="discount_type"]');
                const discountValue = form.querySelector('[name="discount_value"]');
                if (discountType?.value === 'percent' && discountValue?.value && Number(discountValue.value) > 100) {
                    discountValue.setCustomValidity('Percentage discount cannot exceed 100%.');
                }

                const start = form.querySelector('[name="starts_at"]');
                const end = form.querySelector('[name="ends_at"]');
                if (start?.value && end?.value && end.value <= start.value) {
                    end.setCustomValidity('End date must be after the start date.');
                }

                const images = form.querySelector('[name="images[]"]');
                if (images?.files?.length) {
                    if (images.files.length > 6) {
                        images.setCustomValidity('A product can have up to 6 images.');
                    } else if ([...images.files].some((file) => file.size > 5 * 1024 * 1024)) {
                        images.setCustomValidity('Each product image must be 5 MB or smaller.');
                    } else if ([...images.files].some((file) => !['image/jpeg', 'image/png', 'image/webp'].includes(file.type))) {
                        images.setCustomValidity('Upload JPG, PNG, or WebP images only.');
                    }
                }

                const image = form.querySelector('[name="image"]');
                if (image?.files?.[0]) {
                    if (image.files[0].size > 5 * 1024 * 1024) {
                        image.setCustomValidity('Image must be 5 MB or smaller.');
                    } else if (!['image/jpeg', 'image/png', 'image/webp'].includes(image.files[0].type)) {
                        image.setCustomValidity('Upload a JPG, PNG, or WebP image.');
                    }
                }

                const flashDeal = form.querySelector('[name="is_flash_deal"]');
                const flashEnd = form.querySelector('[name="flash_deal_ends_at"]');
                if (flashDeal && flashEnd) {
                    flashEnd.required = flashDeal.checked;
                    if (flashDeal.checked && !flashEnd.value) {
                        flashEnd.setCustomValidity('Choose when the flash deal ends.');
                    } else if (flashDeal.checked && new Date(flashEnd.value) <= new Date()) {
                        flashEnd.setCustomValidity('Flash deal end time must be in the future.');
                    }
                }
            };

            const validateField = (field, showMessage = false) => {
                setCrossFieldValidity();
                const serverMessage = getFieldMessage(field);
                const message = serverMessage || (field.validity.valid ? '' : field.validationMessage);
                const feedback = feedbackFor(field);
                const shouldShow = showMessage || field.dataset.touched === 'true' || Boolean(serverMessage);

                feedback.textContent = shouldShow ? message : '';
                feedback.classList.toggle('hidden', !shouldShow || !message);
                feedback.classList.toggle('text-rose-300', Boolean(message));
                feedback.classList.toggle('text-emerald-300', shouldShow && !message && field.value !== '');
                field.classList.toggle('border-rose-400', shouldShow && Boolean(message));
                field.classList.toggle('border-emerald-400/70', shouldShow && !message && field.value !== '');

                return !message;
            };

            fields.forEach((field) => {
                const revalidate = () => {
                    field.dataset.edited = 'true';
                    field.dataset.touched = 'true';
                    validateField(field);
                    fields.filter((candidate) => candidate.dataset.touched === 'true')
                        .forEach((candidate) => validateField(candidate));
                };

                field.addEventListener('input', revalidate);
                field.addEventListener('change', revalidate);
                field.addEventListener('blur', () => {
                    field.dataset.touched = 'true';
                    validateField(field, true);
                });
                if (getFieldMessage(field)) validateField(field, true);
            });

            form.querySelector('[name="is_flash_deal"]')?.addEventListener('change', () => {
                const flashEnd = form.querySelector('[name="flash_deal_ends_at"]');
                if (flashEnd) validateField(flashEnd, true);
            });

            form.addEventListener('submit', (event) => {
                setCrossFieldValidity();
                const invalidField = fields.find((field) => !validateField(field, true));
                if (invalidField) {
                    event.preventDefault();
                    invalidField.focus();
                    invalidField.reportValidity();
                }
            });
        });

        document.querySelectorAll('[data-shop-live-filter]').forEach((form) => {
            let timer;
            const search = form.querySelector('input[name="search"]');
            search?.addEventListener('input', () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(() => form.requestSubmit(), 350);
            });
            form.querySelectorAll('select').forEach((select) => {
                select.addEventListener('change', () => form.requestSubmit());
            });
        });
    })();
</script>
@endpush
