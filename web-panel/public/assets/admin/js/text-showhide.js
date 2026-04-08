// description text show hide
$(document).ready(function () {
    const wordLimit = 20;

    $('.des_text').each(function () {
        const $paragraph = $(this);
        const $btn = $paragraph.find('.see_more_btn');

        const fullText = $paragraph.clone()
            .children('.see_more_btn').remove().end()
            .text().trim();

        const words = fullText.split(/\s+/);

        if (words.length > wordLimit) {
            const visibleText = words.slice(0, wordLimit).join(' ');
            const hiddenText = words.slice(wordLimit).join(' ');

            $paragraph.contents().filter(function () {
                return this.nodeType === 3;
            }).remove();

            $btn.before(`
                        <span class="visible_text">${visibleText}</span>
                        <span class="more_text d-none">${' ' + hiddenText}</span>
                    `);

            $btn.on('click', function () {
                const $moreText = $paragraph.find('.more_text');
                const $visibleText = $paragraph.find('.visible_text');

                $moreText.toggleClass('d-none');
                $(this).text($moreText.hasClass('d-none') ? 'See More' : 'See Less');
            });
        } else {
            $btn.hide();
        }
    });
});
