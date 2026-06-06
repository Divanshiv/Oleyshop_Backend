$(function() {
    const defaultBottomPosition = getComputedStyle(document.querySelector('.floating-ai-button'))
      .getPropertyValue('--bottom-position').trim();

    $(window).on('scroll', function() {
        const nearBottom = $(window).scrollTop() + $(window).height() >= $(document).height() - 50;
        $('.floating-ai-button').css('--bottom-position', nearBottom ? '200px' : defaultBottomPosition);
    });
});


document.querySelectorAll('.outline-wrapper').forEach(wrapper => {
    const child = wrapper.firstElementChild;
    if (child) {
        const radius = getComputedStyle(child).borderRadius;
        wrapper.style.borderRadius = radius;
    }
});
