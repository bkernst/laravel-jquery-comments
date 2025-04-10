function validateEmail(email) {
    return email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
}

function closePopup() {
    $('.overlay').removeClass('open');
    $('.popup').removeClass('open');
}

function renderComments(data) {
    if (data === null) {
        console.error('Data is null');
        return false;
    }
    if (typeof data !== 'object') {
        console.error('Data is not an object');
        return false;
    }
    if (data.length === 0) {
        $('#comments').empty();
        $('#comments').html('<li class="no-comments-found pb-1 pt-1"><p class="mt-1 mb-1 pb-0 text-center">No comments found</p></li>');
        return false;
    }
    $('li.no-comments-found').remove();

    // Get comment IDs to check which comments need to be removed later
    let commentIdsExisting = [];
    $('li').each(function() {
        commentIdsExisting.push($(this).data('id'));
    });

    // Render comments
    let commentIdsData = [];
    for (let index in data) {
        renderComment(data[index]);
        commentIdsData.push(data[index].id);
    }

    // Remove existing comments not found in the new list
    let removedIds = commentIdsExisting.filter(item => !commentIdsData.includes(item));
    if (removedIds.length > 0) {
        for (let index in removedIds) {
            $('li[data-id="' + removedIds[index] + '"]').remove();
        }
    }
}

function setBarDisplay(message, className) {
    let selector = '.' + className + '-bar';
    if (message !== '') {
        $(selector + ' > .center-holder').text(message);
        $(selector).addClass('open');
    } else {
        $(selector).removeClass('open');
        $(selector + ' > .center-holder').text('');
    }
}

function showNotification(message, selector) {
    // if (typeof selector !== 'string') {
    //     selector = '.notification-main';
    // }
    // if (selector === '') {
    //     selector = '.notification-main';
    // }
    // $('.notification' + selector).text(message);
    setBarDisplay(message, 'notification');
}

function showError(message, selector) {
    // if (typeof selector !== 'string') {
    //     selector = '.error-main';
    // }
    // if (selector === '') {
    //     selector = '.error-main';
    // }
    // $('.error' + selector).text(message);
    setBarDisplay(message, 'error');
}
