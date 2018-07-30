function renderDate(data) {
    let creation = new Date(data);
    let locale = 'en';
    let month = creation.toLocaleString(locale, {month:'short'});
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
            url : '/api/v1/task/',
            dataSrc : ''
        },
        columns: [
            {
                'data' : 'id',
            },
            {
                'data' : 'title',
            },
            {
                'data' : 'description',
            },
            {
                'data' : 'creation_date.date',
                "render": function (data) {
                    return renderDate(data);
                }
            },
            {
                'data' : 'status',
            }
        ]
    });


    let itemId = 0;
    let cells = null;

    $('#taskTable').click(function (event) {
        let target = event.target;

        if (target.tagName !== 'TD') return;

        if ($('#editForm, .overlay').css('opacity') === 1) return;

        itemId = target.parentNode.firstChild;
        itemId = itemId.innerText;
        cells = target.parentNode.children;
        let editCells = $('#taskTable thead th');

        $.ajax({
            url: '/api/v1/task/' + itemId,
        }).done(function (data) {
            $('#editForm, .overlay').css({'opacity': 1, 'visibility': 'visible'});

            for (let i = editCells.length - 1; 0 <= i; i--) {
                if (editCells[i].getAttribute('data-action') === 'editable') {
                    let fieldName = editCells[i].getAttribute('data-content');
                    let value = data[fieldName];
                    let input = `
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
                    if(!$(`#${editCells[i].getAttribute('data-content')}`).length) {
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
        $('#editForm, .overlay').css({'opacity': 0, 'visibility': 'hidden'});
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
                cells[0].innerText = form[0][0].value;
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
