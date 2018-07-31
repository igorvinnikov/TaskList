function renderDate(data) {
    let creation = new Date(data);
    let locale = 'en';
    let month = creation.toLocaleString(locale, {month: 'short'});
    let formatDate = creation.getDate() + " " + month + " " + creation.getFullYear();

    let time = new Date(data);
    time = time.getTime();

    return '<span class="invisible">' + time + '</span><span>' + formatDate + '</span>';
}

$(document).ready(function () {
    $('#taskTable').DataTable({
        scrollX: true,
        columnDefs: [
            {className: "hide_column", "targets": [0]}
        ],
        ajax: {
            url: '/api/v1/task/',
            dataSrc: ''
        },
        columns: [
            {
                'data': 'id',
            },
            {
                'data': 'title',
            },
            {
                'data': 'description',
            },
            {
                'data': 'creation_date.date',
                "render": function (data) {
                    return renderDate(data);
                }
            },
            {
                'data': 'status',
            },
            {
                'data': 'id',
                'orderable': false,
                "render": function (data) {
                    return '<button value="' + data + '" class="btn btn-danger btn-block delete">Delete</button>';
                }
            },
        ]
    });


    let itemId = 0;
    let cells = null;

    $('#taskTable').click(function (event) {
        let target = event.target;

        if (target.tagName == 'BUTTON') {
                $.ajax({
                    type: 'DELETE',
                    url: '/api/v1/task/' + target.value,
                    success: function () {
                        target.parentNode.parentNode.remove();
                    }
                });
        }

        if (target.tagName !== 'TD') return;

        if ($('#editTaskForm, .overlay').css('opacity') === 1) return;

        itemId = target.parentNode.firstChild;
        itemId = itemId.innerText;
        cells = target.parentNode.children;
        let editCells = $('#taskTable thead th');

        $.ajax({
            url: '/api/v1/task/' + itemId,
        }).done(function (data) {
            $('#editTaskForm, .overlay').css({'opacity': 1, 'visibility': 'visible'});

            for (let i = editCells.length - 1; 0 <= i; i--) {
                if (editCells[i].getAttribute('data-action') === 'editable') {
                    let fieldName = editCells[i].getAttribute('data-content');
                    let value = data[fieldName];
                    let input = null;
                    if (fieldName === 'status'){
                        input = `<div class="inputGroup dynamic">
                                    <div>
                                        <label for="${editCells[i].getAttribute('data-content')}">
                                            ${editCells[i].getAttribute('data-label')}
                                        </label>
                                    </div>
                                    <div>
                                       <select 
                                            name="${editCells[i].getAttribute('data-content')}" 
                                            id="${editCells[i].getAttribute('data-content')}" 
                                            class="form-control" wrap="soft"
                                       >
                                            <option value="open">open</option>
                                            <option value="closed">closed</option>
                                       </select>
                                </div>`;
                    }else{
                        input = `
                                <div class="inputGroup dynamic">
                                    <div>
                                        <label for="${editCells[i].getAttribute('data-content')}">
                                            ${editCells[i].getAttribute('data-label')}
                                        </label>
                                    </div>
                                    <div>
                                        <input 
                                            type="text" 
                                            id="${editCells[i].getAttribute('data-content')}" 
                                            name="${editCells[i].getAttribute('data-content')}" 
                                            value="${value}" class="form-control" wrap="soft">
                                    </div>
                                </div>`;
                    };

                    if (!$(`#${editCells[i].getAttribute('data-content')}`).length) {
                        $('.editForm form').prepend(input);
                    }
                }
            }
        });
    });

    let close = function (e) {
        e.preventDefault();
        let dynamic = $('.dynamic');
        for (let i = 0; i < dynamic.length; i++) {
            dynamic[i].remove();
        }
        $('#editTaskForm, .overlay').css({'opacity': 0, 'visibility': 'hidden'});
    };

    $('#close').click(function (e) {
        close(e)
    });

    $('#newTaskForm').submit(function (e) {
        e.preventDefault();

        let form = $('#newTaskForm');
        let formData = $('#newTaskForm').serialize();

        $.ajax({
            type: 'PATCH',
            url: '/api/v1/task/' + itemId,
            data: formData,
            success: function () {
                cells[1].innerText = form[0][0].value;
                cells[2].innerText = form[0][1].value;
                cells[4].innerText = form[0][2].value;
                close(e);
            },
            error: function (error) {
                printErrorMsg(error.responseJSON.errors.detail);
            }
        });

        function printErrorMsg(msg) {
            let input = document.querySelector('#status');
            $('.errorMessage').remove();

            let p = input.parentNode;
            let errorMessage = document.createElement('div');
            errorMessage.setAttribute('class', 'errorMessage alert alert-danger');
            errorMessage.innerText = msg;
            p.append(errorMessage);
        }

    });
});
