<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Comments CMS</title>
        <meta name="description" content="Approve/reject comments" />
        <meta name="keywords" content="comments, cms" />
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

                // Build the HTML
                let html = '<li class="mb-0 pt-1" data-id="' + commentRow.id + '">';
                html += '<p class="mb-0 mt-0">#' + commentRow.id + ' <strong>Name:</strong> ' + commentRow.name + ' (' + commentRow.email + ')</p>';
                html += '<p class="pb-0 mb-0"><strong>Comment:</strong></p>';
                html += '<div class="mb-1 comment-text">' + commentRow.comment + '</div>';
                html += '<div class="mb-1"><a href="" class="button button-delete">Delete</a></div>';
                html += '<ul class="comments-list" data-id="' + commentRow.id + '"></ul>';
                html += '</li>';

                // Top level or sub selector
                let selector = commentRow.parent_id === 0 || commentRow.parent_id === null ? '#comments' : 'ul[data-id="' + commentRow.parent_id + '"]';

                // Append the new comment
                $(selector).append(html);
            }

            function getAllComments() {
                if (apiCallInProgress) {
                    return false;
                }
                apiCallInProgress = true;
                $.ajax({
                    url: '/api/cms/all',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        renderComments(response.data, true);
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

            function getChangedComments() {
                if (apiCallInProgress) {
                    return false;
                }
                apiCallInProgress = true;
                $.ajax({
                    url: '/api/cms/changed',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        renderComments(response.data, false);
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

            $(document).ready(function() {
                // Add error handler for all AJAX requests
                $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
                    if (jqXHR.status === 401 ||
                        (jqXHR.responseJSON && jqXHR.responseJSON.message === 'Permission denied.')) {

                        // Clear local storage and redirect to login
                        window.location.href = '/';
                    }
                });

                // Show the delete modal window
                $('main').on('click', '.button-delete', function(e) {
                    e.preventDefault();

                    // Get the ID
                    let parentId = 0;
                    if ($(this).parent().parent().data('id')) {
                        parentId = $(this).parent().parent().data('id');
                    }

                    // Show the modal
                    $('form[name="form_delete_comment"] input[name="id"]').val(parentId);
                    $('.popup-delete-comment').addClass('open');
                });

                $('form[name="form_delete_comment"]').on('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (apiCallInProgress) {
                        return;
                    }
                    apiCallInProgress = true;
                    $.ajax({
                        url: '/api/cms/delete',
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: $('form[name="form_delete_comment"] input[name="id"]').val()
                        },
                        success: function(response) {
                            closePopup();
                        },
                        error: function(xhr, status, error) {
                            console.error(status, error);
                            showError(error);
                            if (error !== '') {
                                closePopup();
                            }
                            if (xhr.status === 401) {
                                window.location.href = '/';
                            }
                        },
                        complete: function() {
                            apiCallInProgress = false;
                            getChangedComments();
                        }
                    });
                });

                $('.close-lightbox').on('click', function(e) {
                    closePopup();
                });

                // Remove the loading text and show the first delete button
                $('.notification-main').text('');
                $('.main-delete').removeClass('hide');

                // Populate comments
                $('#comments').empty();
                getAllComments();
                let timer = setInterval(function() {
                    getChangedComments();
                }, 5000);
            });
        </script>
    </head>
    <body>
        <div class="notification-bar pb-1 pt-1"><div class="center-holder text-center"></div></div>
        <div class="error-bar pb-1 pt-1"><div class="center-holder text-center"></div></div>
        <header class="header pb-1 pt-1">
            <div class="center-holder">
                <h1>Comments Moderation</h1>
                <p class="pb-0 mb-0">Please review the comments below.</p>
            </div>
        </header>
        <main>
            <div class="center-holder">
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

        <div class="popup popup-delete-comment">
            <div class="popup-modal relative">
                <div class="popup-header">
                    Delete confirmation
                    <a class="close-lightbox"><span></span></a>
                </div>
                <div class="popup-content">
                    <div class="notification notification-delete-form"></div>
                    <div class="error error-delete-form"></div>
                    <form method="post" name="form_delete_comment" action="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="id" value="" />
                        <div class="mb-1">
                            <p>Are you sure you want to delete this comment?</p>
                            <p>All child comments linked to it will be deleted as well.</p>
                        </div>
                        <div>
                            <button class="button button-delete-confirm" type="submit">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="overlay" class="overlay"></div>
    </body>
</html>