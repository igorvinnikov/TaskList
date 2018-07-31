<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Task List</title>
    <link href="{{asset('css/tables.from.database.css')}} " rel="stylesheet">
    <link href="{{asset('libs/datatables.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/DataTables-1.10.18/css/dataTables.bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('libs/DataTables-1.10.18/css/jquery.dataTables.css')}}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>
<style>
    table.dataTable thead .sorting:after {
        content: '';
    }

    table.dataTable thead .sorting_asc:after {
        content: '';
    }

    table.dataTable thead .sorting_desc:after {
        content: '';
    }
</style>
<main class="main-content bgc-grey-100">
    <div id="mainContent">
        <div class="container-fluid">
            <h4 class="c-grey-900 mT-10 mB-30">Task List</h4>
            <div class="btn">
                <button class="newTask btn btn-success">Add +</button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="taskTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th data-action="non_editable" data-content="id">ID</th>
                            <th data-action="editable" data-content="title" data-label="Title" data-content-key="title">
                                Title
                            </th>
                            <th data-action="editable" data-content="description" data-label="Description">Description
                            </th>
                            <th data-action="non-editable" data-content="creation_date" data-content-key="date">Date of
                                Creation
                            </th>
                            <th data-action="editable" data-label="Status" data-content="status">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="overlay"></div>
<div id="taskForm" class="editForm">
    <form id="newTask">
        <div class="inputGroup">
            <div>
                <label for="title">Title</label>
            </div>
            <div id="titleInput">
                <input id="titleId" name="title" type="text" class="form-control dynamic">
            </div>
            <div>
                <label for="description">Description</label>
            </div>
            <div id="descriptionInput">
                <input id="descript" name="description" type="text" class="form-control dynamic">
            </div>
            <div>
                <label for="status">Status</label>
            </div>
            <div>
                <select name="status" id="statusTask" class="form-control">
                    <option value="open">open</option>
                    <option value="closed">closed</option>
                </select>
            </div>
        </div>
        <input type="submit" id="announce" value="Create" class="btn btn-success btn-block">
        <input type="button" value="Close" class="btn btn-danger btn-block closeForm">
    </form>
</div>
<div id="editForm" class="editForm">
    <form id="newTaskForm">
        <input type="submit" value="Edit" class="btn btn-success btn-block">
        <input type="button" id="close" value="Close" class="btn btn-danger btn-block closeForm">
    </form>
</div>

<script src="{{asset('js/tasks.from.database.js')}}" defer></script>
<script src="{{asset('js/new.task.js')}}" defer></script>
<script type="text/javascript" src="{{asset('libs/datatables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('libs/DataTables-1.10.18/js/dataTables.bootstrap.js')}}"></script>
<script type="text/javascript" src="{{asset('libs/DataTables-1.10.18/js/jquery.dataTables.js')}}"></script>
</body>
</html>
