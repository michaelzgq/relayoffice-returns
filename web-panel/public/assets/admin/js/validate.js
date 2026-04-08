"use strict"
$(document).on('submit','#store-or-update-data', function (e) {
    e.preventDefault();
    const $form = $(this);
    let formData = new FormData(this);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.post({
        url: $(this).attr('action'),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#loading').removeClass('d-none');
            $form.find('button[type="submit"]').attr('disabled', true)
        },
        success: function (response) {
            if (response?.errors) {
                $('#loading').addClass('d-none');
                $form.find('button[type="submit"]').removeAttr('disabled');
                let firstErrorElement = null;
                $.each(response.errors, function (key, value){
                    let errorSpan = $form.find(`.error-text[data-error="${value.code}"]`);
                    errorSpan.text(value.message);
                    let input = $form.find(`[name="${value.code}"]`);

                    if (input.attr('type') === 'hidden')
                    {
                        let container = input.closest('.iti');
                        input = container.find('.iti__tel-input');
                    }

                    if (input.length) {
                        input.addClass('is-invalid');
                        if (input.hasClass('js-select2-custom')) {
                            input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                        }
                    }
                    if (!firstErrorElement) {
                        firstErrorElement = input.length ? input : errorSpan;
                    }
                });

                if (firstErrorElement && firstErrorElement.length) {
                    firstErrorElement[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            } else {
                sessionStorage.setItem('formSubmittedSuccessfully', response.success_message ?? 'Data saved successfully!');
                location.href = response.redirect_url;
            }
        },
        error: function (response) {
            $('#loading').addClass('d-none');
            $form.find('button[type="submit"]').removeAttr('disabled');
            toastr.error('An error occurred while processing your request. Please try again.', {
                CloseButton: true,
                ProgressBar: true
            });
        },
    });
});

$(document).on('input', '#store-or-update-data input, #store-or-update-data select, #store-or-update-data textarea', function () {
    const $form = $(this).closest('form');
    const fieldName = $(this).attr('name');
    $form.find(`.error-text[data-error="${fieldName}"]`).text('');
    $(this).removeClass('is-invalid');
    if ($(this).hasClass('js-select2-custom')) {
        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
    }
});


