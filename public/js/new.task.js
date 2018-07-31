let add = document.querySelector('.newTask');

add.onclick = function () {
    if ($('#taskForm, .overlay').css('opacity') === 1) return;

    $('#taskForm, .overlay').css({'opacity': 1, 'visibility': 'visible'});
}

/**
 * Insert new task into data base.
 */
$('#newTask').submit(function (e) {
    e.preventDefault();

    let title = document.querySelector('#titleId').value;
    let description = document.querySelector('#descript').value;
    let formData = $('#newTask').serialize();

    $.ajax({
        type: 'POST',
        url: '/api/v1/task/',
        data: formData,
        success: function () {
            document.location.href = '/';
        },
        error: function (error) {
            alert(error.responseJSON.errors.detail);
        }
    });
});

/**
 * Event listener for close button. Will close edit form without saving any changes.
 */
$('.closeForm').click(function (e) {
    close(e)
});

let close = function (e) {
    e.preventDefault();
    let dynamic = $('.dynamic');
    for (let i = 0; i < dynamic.length; i++) {
        dynamic[i].remove();
    }
    $('#taskForm, #editForm, .overlay').css({'opacity': 0, 'visibility': 'hidden'});
};
