 $(function () {
            const $button = $('.dateRange');
            const $label = $button.find('span');
            const placeholder = $label.data('placeholder') || 'Select Date';

            const currentUrl = new URL(window.location.href);
            const startDateUrl = currentUrl.searchParams.get('start_date');
            const endDateUrl = currentUrl.searchParams.get('end_date');

            const startDate = startDateUrl ? moment(startDateUrl, 'YYYY-MM-DD') : null;
            const endDate = endDateUrl ? moment(endDateUrl, 'YYYY-MM-DD') : null;

            if (startDate && endDate) {
                $label.text(`${startDate.format('DD MMM, YYYY')} - ${endDate.format('DD MMM, YYYY')}`);
            } else {
                currentUrl.searchParams.delete('start_date');
                currentUrl.searchParams.delete('end_date');
                window.history.replaceState({}, '', currentUrl.toString());
                $label.text(placeholder);
            }

            $button.daterangepicker({
                autoUpdateInput: false,
                startDate: startDate || undefined,
                endDate: endDate || undefined,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Confirm'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Last 2 Months': [moment().subtract(2, 'months').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $button.on('apply.daterangepicker', function (ev, picker) {
                $label.text(`${picker.startDate.format('DD MMM, YYYY')} - ${picker.endDate.format('DD MMM, YYYY')}`);

                $('#start_date_value').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date_value').val(picker.endDate.format('YYYY-MM-DD'));

                $(document).trigger('dateRangeUpdated', {
                    startDate: picker.startDate.format('YYYY-MM-DD'),
                    endDate: picker.endDate.format('YYYY-MM-DD')
                });
            });

            $button.on('cancel.daterangepicker', function (ev, picker) {
                $(document).trigger('dateRangeCleared');
            });
        });
