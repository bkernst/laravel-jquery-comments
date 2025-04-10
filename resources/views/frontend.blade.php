<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Comments</title>
        <meta name="description" content="View comments data" />
        <meta name="keywords" content="comments" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta id="Viewport" name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=yes, width=465" />
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}" defer/>
        <style>
            body {
                background-color: white;
                color: black;
                font-family: Lato, "Helvetica", sans-serif;
                font-size: 1rem;
                font-weight: normal;
                margin: 0;
                padding: 0;
            }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="{{ asset('/js/shared.js') }}"></script>
        <script>
            new function viewport() {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|bada|iemobile|BB[0-9.,:_-]{2,};/i.test(navigator.userAgent)) {
                    let ww = (window.innerWidth < window.screen.width) ? window.innerWidth : window.screen.width; // get proper width
                    let mw = 465; // min width of site
                    let ratio = ww / mw; // calculate ratio
                    if (ww < mw) { // smaller than minimum size
                        document.getElementById('Viewport').setAttribute('content', 'initial-scale=' + ratio + ', maximum-scale=' + ratio + ', minimum-scale=' + ratio + ', user-scalable=yes, width=' + ww);
                    } else { // regular size
                        document.getElementById('Viewport').setAttribute('content', 'initial-scale=1.0, maximum-scale=1, minimum-scale=1.0, user-scalable=yes, width=' + ww);
                    }
                }
            };
            var apiCallInProgress = false;

            function renderComment(commentRow) {
                // Check if already found
                if ($('li[data-id="' + commentRow.id + '"]').length > 0) {
                    return false;
                }

                // Skip rejected
                if (commentRow.approved === 0) {
                    return false;
                }

                // Build the HTML
                let html = '<li class="mb-0 pt-1" data-id="' + commentRow.id + '">';
                html += '<p class="mb-0 mt-0">#' + commentRow.id + ' <strong>Name:</strong> ' + commentRow.name + '</p>';
                html += '<p class="pb-0 mb-0"><strong>Comment:</strong></p>';
                html += '<div class="mb-1 comment-text">' + commentRow.comment + '</div>';
                html += '<div class="mb-1"><a href="" class="button button-reply">Reply</a></div>';
                html += '<ul class="comments-list" data-id="' + commentRow.id + '"></ul>';
                html += '</li>';

                // Top level or sub selector
                let selector = commentRow.parent_id === 0 || commentRow.parent_id === null ? '#comments' : 'ul[data-id="' + commentRow.parent_id + '"]';

                // Append the new comment
                $(selector).append(html);
            }

            function openReplyModal(parentId) {
                setModalData(parentId);
                $('.overlay').addClass('open');
                $('.popup-add-comment').addClass('open');
            }

            function setModalData(parentId) {
                if (parentId === null || parentId === '') {
                    parentId = 0;
                }
                const prefix = 'form[name="form_reply"] ';
                $(prefix + 'input[name="parent_id"]').val(parentId);
                $(prefix + 'input[name="name"]').val('');
                $(prefix + 'input[name="email"]').val('');
                $(prefix + 'textarea[name="comment"]').val('');
            }

            $(document).ready(function() {

                function getAllComments() {
                    if (apiCallInProgress) {
                        return false;
                    }
                    apiCallInProgress = true;

                    $.ajax({
                        url: '/api/frontend/all',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            renderComments(response.data);
                        },
                        error: function(xhr, status, error) {
                            console.error(status, error);
                            showError(error);
                            if (xhr.status === 401) {
                                window.location.href = '/';
                            }
                        },
                        complete: function() {
                            apiCallInProgress = false;
                        }
                    });
                }

                function postReply(data) {
                    showError('', '');
                    if (apiCallInProgress) {
                        showError('In progress, please wait', '.notification-reply-form');
                        return false;
                    }
                    if (typeof data !== 'object' || data === null) {
                        showError('Data is required', '.notification-reply-form');
                        return false;
                    }
                    if (typeof data.parent_id === 'undefined' || typeof data.name === 'undefined' || typeof data.email === 'undefined' || typeof data.comment === 'undefined') {
                        showError('Data structure is not valid', '.notification-reply-form');
                        return false;
                    }

                    apiCallInProgress = true;
                    $.ajax({
                        url: '/api/frontend/reply',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: data,
                        success: function(response) {
                            closePopup();
                        },
                        error: function(xhr, status, error) {
                            console.error(status, error);
                            showError(error);
                            if (xhr.status === 401) {
                                window.location.href = '/';
                            }
                            closePopup();
                        },
                        complete: function() {
                            apiCallInProgress = false;
                            getAllComments();
                        }
                    });
                }

                // Add error handler for all AJAX requests
                $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
                    if (jqXHR.status === 401 ||
                        (jqXHR.responseJSON && jqXHR.responseJSON.message === 'Permission denied.')) {

                        // Clear local storage and redirect to login
                        window.location.href = '/';
                    }
                });

                // Show the reply modal window
                $('main').on('click', '.button-reply', function(e) {
                    e.preventDefault();

                    let parentId = 0;
                    if ($(this).parent().parent().data('id')) {
                        parentId = $(this).parent().parent().data('id');
                    }
                    openReplyModal(parentId);
                });

                $('.close-lightbox').on('click', function(e) {
                    closePopup();
                });

                $('form[name="form_reply"]').on('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    let prefix = 'form[name="form_reply"] ';
                    let data = {};
                    let fields = {
                        parent_id: 'input[name="parent_id"]',
                        name: 'input[name="name"]',
                        email: 'input[name="email"]',
                        comment: 'textarea[name="comment"]'
                    };

                    // Get the data and do basic validation
                    let errorCount = 0;
                    for (let index in fields) {
                        let fieldSelector = prefix + fields[index];
                        let fieldValue = $(fieldSelector).val();
                        data[index] = fieldValue;
                        if ($(fieldSelector).prop('required')) {
                            fieldValue = fieldValue.replaceAll(' ', '');
                            if (!fieldValue.length) {
                                errorCount++;
                                showError('This is required', '.error-' + index);
                            }
                        }
                    }
                    if (errorCount > 0) {
                        return false;
                    }
                    if (!validateEmail(data.email)) {
                        showError('Email address is invalid', '.error-email');
                        return false;
                    }

                    // Post the reply
                    postReply(data);
                });

                // Remove the loading text and show the first reply button
                $('.notification-main').text('');
                $('.main-reply').removeClass('hide');

                // Populate comments
                $('#comments').empty();
                getAllComments();
                let timer = setInterval(function() {
                    getAllComments();
                }, 5000);
            });
        </script>
    </head>
    <body>
        <div class="notification-bar pb-1 pt-1"><div class="center-holder text-center"></div></div>
        <div class="error-bar pb-1 pt-1"><div class="center-holder text-center"></div></div>
        <header class="header pb-1 pt-1">
            <div class="center-holder">
                <h1>Add your comments</h1>
                <p class="pb-0 mb-0">Please enter comments below.</p>
            </div>
        </header>
        <main>
            <div class="center-holder">
                <div class="mb-2 main-reply hide">
                    <a href="" class="button button-reply">Reply</a>
                </div>
                <ul id="comments" class="comments-list pl-0 mb-2">
                    <!-- Comments get added here dynamically-->
                </ul>
            </div>
        </main>
        <footer class="footer">
            <div class="center-holder">
                <p class="pt-0 mt-0"><a href="/">Frontend</a> | <a href="/cms">CMS</a></p>
                <p class="pt-0 mt-0">You have reached the current end of the world</p>
                <p class="mb-0 pb-0"><small>Created by Bernhard Ernst on 3 &amp; 4 April 2025</small></p>
            </div>
        </footer>

        <div class="popup popup-add-comment">
            <div class="popup-modal relative">
                <div class="popup-header">
                    Reply
                    <a class="close-lightbox"><span></span></a>
                </div>
                <div class="popup-content">
                    <div class="notification notification-reply-form"></div>
                    <div class="error error-reply-form"></div>
                    <form method="post" name="form_reply" action="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="parent_id" value="" />
                        <div class="mb-1">
                            <label for="name">Name *</label>
                            <input type="text" name="name" value="" maxlength="150" required />
                            <div class="error error-name"></div>
                        </div>
                        <div class="mb-1">
                            <label for="email">Email *</label>
                            <input type="email" name="email" value="" maxlength="150" required />
                            <div class="error error-email"></div>
                        </div>
                        <div class="mb-1">
                            <label for="comment">Comment *</label>
                            <textarea name="comment" cols="50" rows="10" required></textarea>
                            <div class="error error-comment"></div>
                        </div>
                        <div>
                            <button class="button" type="submit">Reply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="overlay" class="overlay"></div>
    </body>
</html>